<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BuildLaravelOfflineDocs extends Command
{
    protected $signature   = 'docs:build-laravel-offline {--branch=12.x : The docs branch to build}';
    protected $description = 'Convert Laravel markdown docs into a single offline HTML file';

    private string $branch;
    private string $sourceDir;

    public function handle(): int
    {
        $this->branch    = $this->option('branch');
        $this->sourceDir = storage_path("app/private/documents/laravel-docs/{$this->branch}");

        if (! is_dir($this->sourceDir)) {
            $this->error("Source not found: {$this->sourceDir}");
            $this->error('Run php artisan docs:import-laravel first.');
            return self::FAILURE;
        }

        $nav = $this->buildNav();

        // Convert all markdown files → HTML
        $this->info("Converting markdown files…");
        $mdFiles = glob("{$this->sourceDir}/*.md") ?: [];
        $bar     = $this->output->createProgressBar(count($mdFiles));
        $pages   = [];

        foreach ($mdFiles as $mdFile) {
            $key = basename($mdFile, '.md');
            if ($key === 'documentation') {
                $bar->advance();
                continue;
            }
            $markdown    = file_get_contents($mdFile);
            $pages[$key] = $this->convertMarkdown($markdown);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Build and write the HTML file
        $this->info('Generating documentation/laravel/index.html…');

        $outDir  = base_path('documentation/laravel');
        $outFile = "{$outDir}/index.html";
        @mkdir($outDir, 0755, true);

        file_put_contents($outFile, $this->buildHtml($nav, $pages));

        $kb    = number_format(filesize($outFile) / 1024, 0);
        $count = count($pages);
        $this->info("✓ Built {$outFile} ({$kb} KB, {$count} pages)");

        return self::SUCCESS;
    }

    private function buildNav(): array
    {
        $navFile = "{$this->sourceDir}/documentation.md";

        if (! file_exists($navFile)) {
            // Fallback: alphabetical list from filenames
            $files = glob("{$this->sourceDir}/*.md") ?: [];
            $items = [];
            foreach ($files as $file) {
                $key = basename($file, '.md');
                if ($key === 'documentation') {
                    continue;
                }
                $items[ucwords(str_replace('-', ' ', $key))] = $key;
            }
            return ['Pages' => $items];
        }

        $nav     = [];
        $section = null;

        foreach (explode("\n", file_get_contents($navFile)) as $line) {
            // Section heading: "- ## Section Name"
            if (preg_match('/^-\s+##\s+(.+)/', trim($line), $m)) {
                $section        = trim($m[1]);
                $nav[$section]  = [];
                continue;
            }

            // Nav item: "    - [Title](/docs/{{version}}/key)"
            if ($section && preg_match('/\[(.+?)\]\(\/docs\/\{\{version\}\}\/([^)]+)\)/', $line, $m)) {
                $title = trim($m[1]);
                $key   = trim($m[2]);
                // Only add the item if we actually downloaded that page
                if (file_exists("{$this->sourceDir}/{$key}.md")) {
                    $nav[$section][$title] = $key;
                }
            }
        }

        // Remove sections whose files were not downloaded (e.g. some Packages pages)
        return array_filter($nav, fn($items) => ! empty($items));
    }

    private function convertMarkdown(string $markdown): string
    {
        // Strip YAML frontmatter
        $markdown = preg_replace('/\A---\s*\n.*?\n---\s*\n/s', '', $markdown);

        $html = Str::markdown($markdown, [
            'html_input'         => 'allow',
            'allow_unsafe_links' => false,
        ]);

        // Rewrite internal doc links to JS show() calls
        $html = preg_replace_callback('/<a href="([^"#][^"]*)"/', function ($m) {
            $href = $m[1];
            if (str_starts_with($href, 'http') || str_starts_with($href, 'mailto')) {
                return "<a target=\"_blank\" rel=\"noopener\" href=\"{$href}\"";
            }
            $page = preg_replace('/\.md(#.*)?$/', '$1', $href);
            $page = preg_replace('/#.*$/', '', $page);
            $page = trim($page, './');
            return "<a href=\"javascript:show('{$page}')\"";
        }, $html);

        // Style GitHub-flavoured admonitions
        $html = preg_replace(
            '/<blockquote>\s*<p>\[!(NOTE|TIP)\](.*?)<\/blockquote>/si',
            '<div class="info-box">$2</div>',
            $html
        );
        $html = preg_replace(
            '/<blockquote>\s*<p>\[!(WARNING|IMPORTANT|CAUTION)\](.*?)<\/blockquote>/si',
            '<div class="warn-box">$2</div>',
            $html
        );

        return $html;
    }

    private function buildHtml(array $nav, array $pages): string
    {
        $branch   = htmlspecialchars($this->branch);
        $navHtml  = $this->buildNavHtml($nav);
        $pagesJs  = $this->buildPagesJs($pages);
        $firstKey = $this->firstKey($nav);
        $css      = $this->css();
        $ts       = date('Y-m-d');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Laravel {$branch} — Offline Documentation</title>
<style>{$css}</style>
</head>
<body>
<div id="app">

<div id="sidebar">
  <div id="sidebar-header">
    <div class="logo"><span>⚡</span> Laravel</div>
    <div class="sub">{$branch} &nbsp;·&nbsp; offline · {$ts}</div>
  </div>
  <div id="sidebar-search">
    <input id="search" type="text" placeholder="Filter pages…" oninput="filterNav(this.value)" autocomplete="off">
  </div>
  <nav id="nav">{$navHtml}</nav>
</div>

<div id="content-wrap">
  <div id="content"></div>
</div>

</div><!-- #app -->

<script>
var docs={};
{$pagesJs}

var navItems=document.querySelectorAll('.nav-item');

function show(key){
  var page=docs[key];
  if(!page){page='<p style="color:#94a3b8">Page not found: '+key+'</p>';}
  document.getElementById('content').innerHTML=page;
  document.getElementById('content-wrap').scrollTop=0;
  navItems.forEach(function(el){
    var active=el.dataset.key===key;
    el.classList.toggle('active',active);
  });
  location.hash=key;
}

function filterNav(q){
  var lq=q.toLowerCase();
  var sections=document.querySelectorAll('.nav-section');
  sections.forEach(function(sec){
    var next=sec.nextElementSibling,vis=false;
    while(next&&!next.classList.contains('nav-section')){
      if(next.classList.contains('nav-item')){
        var match=!lq||next.textContent.toLowerCase().includes(lq);
        next.style.display=match?'':'none';
        if(match)vis=true;
      }
      next=next.nextElementSibling;
    }
    sec.style.display=vis||!lq?'':'none';
  });
}

var hash=location.hash.replace('#','');
show(hash&&docs[hash]?hash:'{$firstKey}');

window.addEventListener('hashchange',function(){
  var h=location.hash.replace('#','');
  if(h&&docs[h])show(h);
});
</script>
</body>
</html>
HTML;
    }

    private function buildNavHtml(array $nav): string
    {
        $html = '';
        foreach ($nav as $section => $items) {
            $html .= '<div class="nav-section">' . htmlspecialchars($section) . '</div>';
            foreach ($items as $title => $key) {
                $sk    = htmlspecialchars($key);
                $st    = htmlspecialchars($title);
                $html .= "<div class=\"nav-item\" onclick=\"show('{$sk}')\" data-key=\"{$sk}\">{$st}</div>";
            }
        }
        return $html;
    }

    private function buildPagesJs(array $pages): string
    {
        $js = '';
        foreach ($pages as $key => $html) {
            $sk  = addslashes($key);
            $enc = json_encode($html, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $js .= "docs['{$sk}']={$enc};\n";
        }
        return $js;
    }

    private function firstKey(array $nav): string
    {
        foreach ($nav as $items) {
            foreach ($items as $key) {
                return $key;
            }
        }
        return 'installation';
    }

    private function css(): string
    {
        return '
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#0f172a;--surface:#1e293b;--border:#334155;--text:#e2e8f0;--muted:#94a3b8;--dim:#64748b;--accent:#6366f1;--accent2:#818cf8;--sidebar:270px}
html{scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:var(--bg);color:var(--text);line-height:1.6;font-size:15px}
a{color:var(--accent2);text-decoration:none}
a:hover{text-decoration:underline}
code{background:#0f172a;border:1px solid var(--border);padding:1px 5px;border-radius:4px;font-family:"SFMono-Regular",Consolas,monospace;font-size:.82em;color:#f472b6}
pre{background:#0f172a;border:1px solid var(--border);border-radius:8px;padding:1rem;overflow-x:auto;font-size:.82em;line-height:1.7;margin:1rem 0}
pre code{background:none;border:none;padding:0;color:#e2e8f0}
#app{display:flex;height:100vh;overflow:hidden}
#sidebar{width:var(--sidebar);min-width:var(--sidebar);background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden}
#sidebar-header{padding:.9rem 1rem;border-bottom:1px solid var(--border);flex-shrink:0}
.logo{font-size:1rem;font-weight:700;color:#f8fafc;display:flex;align-items:center;gap:.4rem}
.logo span{font-size:1.2rem}
.sub{font-size:.7rem;color:var(--dim);margin-top:2px}
#sidebar-search{padding:.5rem .8rem;border-bottom:1px solid var(--border);flex-shrink:0}
#search{width:100%;background:#0f172a;border:1px solid var(--border);border-radius:6px;padding:5px 10px;color:var(--text);font-size:.8rem;outline:none}
#search:focus{border-color:var(--accent)}
#nav{overflow-y:auto;flex:1;padding:.4rem 0}
#nav::-webkit-scrollbar{width:4px}
#nav::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.nav-section{padding:.4rem 1rem .2rem;font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dim)}
.nav-item{display:block;padding:.3rem 1rem;font-size:.82rem;color:var(--muted);cursor:pointer;transition:color .12s,background .12s;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.nav-item:hover{color:var(--text);background:rgba(99,102,241,.08)}
.nav-item.active{color:#fff;background:rgba(99,102,241,.2);border-right:2px solid var(--accent)}
#content-wrap{flex:1;overflow-y:auto;padding:0}
#content{padding:2rem 2.5rem;max-width:880px}
h1{font-size:1.75rem;font-weight:800;color:#f8fafc;margin-bottom:.4rem}
h2{font-size:1.2rem;font-weight:700;color:#f1f5f9;margin:2rem 0 .75rem;padding-bottom:.4rem;border-bottom:1px solid var(--border)}
h3{font-size:1rem;font-weight:600;color:#cbd5e1;margin:1.5rem 0 .5rem}
h4{font-size:.9rem;font-weight:600;color:var(--muted);margin:1rem 0 .4rem}
p{color:#cbd5e1;margin-bottom:.75rem;font-size:.9rem}
ul,ol{color:#cbd5e1;padding-left:1.4rem;margin-bottom:.75rem;font-size:.9rem}
li{margin-bottom:.25rem}
blockquote{border-left:3px solid var(--border);padding:.5rem 1rem;margin:1rem 0;color:var(--muted);font-size:.9rem}
table{width:100%;border-collapse:collapse;font-size:.82rem;margin:1rem 0}
th{text-align:left;padding:6px 10px;background:#162032;color:var(--muted);font-weight:600;font-size:.75rem;text-transform:uppercase;border-bottom:1px solid var(--border)}
td{padding:7px 10px;border-bottom:1px solid #1e293b;vertical-align:top;color:#cbd5e1}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(99,102,241,.04)}
hr{border:none;border-top:1px solid var(--border);margin:1.5rem 0}
img{max-width:100%;border-radius:6px;margin:.5rem 0}
.info-box{background:#0c2a4e;border:1px solid #1d4ed8;border-radius:8px;padding:.8rem 1rem;margin:1rem 0;font-size:.85rem;color:#bfdbfe}
.warn-box{background:#3b1a00;border:1px solid #92400e;border-radius:8px;padding:.8rem 1rem;margin:1rem 0;font-size:.85rem;color:#fde68a}
@media(max-width:768px){#sidebar{display:none}#content{padding:1.2rem}}
';
    }
}
