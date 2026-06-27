<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BuildTailwindOfflineDocs extends Command
{
    protected $signature   = 'docs:build-tailwind-offline {--branch=main : The branch that was imported}';
    protected $description = 'Convert Tailwind CSS MDX docs into a single offline HTML file';

    private string $branch;
    private string $sourceDir;

    public function handle(): int
    {
        $this->branch    = $this->option('branch');
        $this->sourceDir = storage_path("app/private/documents/tailwind-docs/{$this->branch}");

        if (! is_dir($this->sourceDir)) {
            $this->error("Source not found: {$this->sourceDir}");
            $this->error('Run php artisan docs:import-tailwind first.');
            return self::FAILURE;
        }

        $nav = $this->buildNav();

        $this->info('Converting MDX files…');
        $mdxFiles = glob("{$this->sourceDir}/*.mdx") ?: [];
        $bar      = $this->output->createProgressBar(count($mdxFiles));
        $pages    = [];

        foreach ($mdxFiles as $file) {
            $key         = basename($file, '.mdx');
            $pages[$key] = $this->convertMdx(file_get_contents($file));
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('Generating documentation/tailwind/index.html…');

        $outDir  = base_path('documentation/tailwind');
        $outFile = "{$outDir}/index.html";
        @mkdir($outDir, 0755, true);

        file_put_contents($outFile, $this->buildHtml($nav, $pages));

        $kb    = number_format(filesize($outFile) / 1024, 0);
        $count = count($pages);
        $this->info("✓ Built {$outFile} ({$kb} KB, {$count} pages)");

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Navigation
    // -------------------------------------------------------------------------

    private function buildNav(): array
    {
        $files     = glob("{$this->sourceDir}/*.mdx") ?: [];
        $available = [];
        foreach ($files as $f) {
            $available[basename($f, '.mdx')] = true;
        }

        $structure = $this->navStructure();
        $nav       = [];
        $used      = [];

        foreach ($structure as $section => $items) {
            $section_items = [];
            foreach ($items as $title => $key) {
                if (isset($available[$key])) {
                    $section_items[$title] = $key;
                    $used[$key]            = true;
                }
            }
            if (! empty($section_items)) {
                $nav[$section] = $section_items;
            }
        }

        // Anything not in the known structure goes into "Other"
        $extra = [];
        foreach ($available as $key => $_) {
            if (! isset($used[$key])) {
                $extra[ucwords(str_replace('-', ' ', $key))] = $key;
            }
        }
        if (! empty($extra)) {
            ksort($extra);
            $nav['Other'] = $extra;
        }

        return $nav;
    }

    private function navStructure(): array
    {
        return [
            'Getting Started' => [
                'Installation'               => 'installation',
                'Editor Setup'               => 'editor-setup',
                'Using with Preprocessors'   => 'using-with-preprocessors',
                'Optimizing for Production'  => 'optimizing-for-production',
                'Browser Support'            => 'browser-support',
                'Upgrade Guide'              => 'upgrade-guide',
                'Compatibility'              => 'compatibility',
            ],
            'Core Concepts' => [
                'Utility-First Fundamentals'      => 'utility-first',
                'Hover, Focus & Other States'     => 'hover-focus-and-other-states',
                'Responsive Design'               => 'responsive-design',
                'Dark Mode'                       => 'dark-mode',
                'Reusing Styles'                  => 'reusing-styles',
                'Adding Custom Styles'            => 'adding-custom-styles',
                'Functions & Directives'          => 'functions-and-directives',
                'Theme Variables'                 => 'theme',
                'Detecting Classes in Source'     => 'content-configuration',
                'Preflight'                       => 'preflight',
            ],
            'Layout' => [
                'Aspect Ratio'         => 'aspect-ratio',
                'Container'            => 'container',
                'Columns'              => 'columns',
                'Break After'          => 'break-after',
                'Break Before'         => 'break-before',
                'Break Inside'         => 'break-inside',
                'Box Decoration Break' => 'box-decoration-break',
                'Box Sizing'           => 'box-sizing',
                'Display'              => 'display',
                'Floats'               => 'floats',
                'Clear'                => 'clear',
                'Isolation'            => 'isolation',
                'Object Fit'           => 'object-fit',
                'Object Position'      => 'object-position',
                'Overflow'             => 'overflow',
                'Overscroll Behavior'  => 'overscroll-behavior',
                'Position'             => 'position',
                'Inset'                => 'top-right-bottom-left',
                'Visibility'           => 'visibility',
                'Z-Index'              => 'z-index',
            ],
            'Flexbox & Grid' => [
                'Flex Basis'            => 'flex-basis',
                'Flex Direction'        => 'flex-direction',
                'Flex Wrap'             => 'flex-wrap',
                'Flex'                  => 'flex',
                'Flex Grow'             => 'flex-grow',
                'Flex Shrink'           => 'flex-shrink',
                'Order'                 => 'order',
                'Grid Template Columns' => 'grid-template-columns',
                'Grid Column Span'      => 'grid-column',
                'Grid Template Rows'    => 'grid-template-rows',
                'Grid Row Span'         => 'grid-row',
                'Grid Auto Flow'        => 'grid-flow',
                'Grid Auto Columns'     => 'grid-auto-columns',
                'Grid Auto Rows'        => 'grid-auto-rows',
                'Gap'                   => 'gap',
                'Justify Content'       => 'justify-content',
                'Justify Items'         => 'justify-items',
                'Justify Self'          => 'justify-self',
                'Align Content'         => 'align-content',
                'Align Items'           => 'align-items',
                'Align Self'            => 'align-self',
                'Place Content'         => 'place-content',
                'Place Items'           => 'place-items',
                'Place Self'            => 'place-self',
            ],
            'Spacing' => [
                'Padding'       => 'padding',
                'Margin'        => 'margin',
                'Space Between' => 'space',
            ],
            'Sizing' => [
                'Width'      => 'width',
                'Min-Width'  => 'min-width',
                'Max-Width'  => 'max-width',
                'Height'     => 'height',
                'Min-Height' => 'min-height',
                'Max-Height' => 'max-height',
                'Size'       => 'size',
            ],
            'Typography' => [
                'Font Family'              => 'font-family',
                'Font Size'                => 'font-size',
                'Font Smoothing'           => 'font-smoothing',
                'Font Style'               => 'font-style',
                'Font Weight'              => 'font-weight',
                'Font Variant Numeric'     => 'font-variant-numeric',
                'Letter Spacing'           => 'letter-spacing',
                'Line Clamp'               => 'line-clamp',
                'Line Height'              => 'line-height',
                'List Style Image'         => 'list-style-image',
                'List Style Position'      => 'list-style-position',
                'List Style Type'          => 'list-style-type',
                'Text Align'               => 'text-align',
                'Text Color'               => 'text-color',
                'Text Decoration'          => 'text-decoration',
                'Text Decoration Color'    => 'text-decoration-color',
                'Text Decoration Style'    => 'text-decoration-style',
                'Text Decoration Thickness'=> 'text-decoration-thickness',
                'Text Underline Offset'    => 'text-underline-offset',
                'Text Transform'           => 'text-transform',
                'Text Overflow'            => 'text-overflow',
                'Text Wrap'                => 'text-wrap',
                'Text Indent'              => 'text-indent',
                'Vertical Align'           => 'vertical-align',
                'Whitespace'               => 'whitespace',
                'Word Break'               => 'word-break',
                'Hyphens'                  => 'hyphens',
                'Content'                  => 'content',
            ],
            'Backgrounds' => [
                'Background Attachment' => 'background-attachment',
                'Background Clip'       => 'background-clip',
                'Background Color'      => 'background-color',
                'Background Origin'     => 'background-origin',
                'Background Position'   => 'background-position',
                'Background Repeat'     => 'background-repeat',
                'Background Size'       => 'background-size',
                'Background Image'      => 'background-image',
                'Gradient Color Stops'  => 'gradient-color-stops',
            ],
            'Borders' => [
                'Border Radius'      => 'border-radius',
                'Border Width'       => 'border-width',
                'Border Color'       => 'border-color',
                'Border Style'       => 'border-style',
                'Divide Width'       => 'divide-width',
                'Divide Color'       => 'divide-color',
                'Divide Style'       => 'divide-style',
                'Outline Width'      => 'outline-width',
                'Outline Color'      => 'outline-color',
                'Outline Style'      => 'outline-style',
                'Outline Offset'     => 'outline-offset',
                'Ring Width'         => 'ring-width',
                'Ring Color'         => 'ring-color',
                'Ring Offset Width'  => 'ring-offset-width',
                'Ring Offset Color'  => 'ring-offset-color',
            ],
            'Effects' => [
                'Box Shadow'           => 'box-shadow',
                'Box Shadow Color'     => 'box-shadow-color',
                'Opacity'              => 'opacity',
                'Mix Blend Mode'       => 'mix-blend-mode',
                'Background Blend Mode'=> 'background-blend-mode',
            ],
            'Filters' => [
                'Blur'               => 'blur',
                'Brightness'         => 'brightness',
                'Contrast'           => 'contrast',
                'Drop Shadow'        => 'drop-shadow',
                'Grayscale'          => 'grayscale',
                'Hue Rotate'         => 'hue-rotate',
                'Invert'             => 'invert',
                'Saturate'           => 'saturate',
                'Sepia'              => 'sepia',
                'Backdrop Blur'      => 'backdrop-blur',
                'Backdrop Brightness'=> 'backdrop-brightness',
                'Backdrop Contrast'  => 'backdrop-contrast',
                'Backdrop Grayscale' => 'backdrop-grayscale',
                'Backdrop Hue Rotate'=> 'backdrop-hue-rotate',
                'Backdrop Invert'    => 'backdrop-invert',
                'Backdrop Opacity'   => 'backdrop-opacity',
                'Backdrop Saturate'  => 'backdrop-saturate',
                'Backdrop Sepia'     => 'backdrop-sepia',
            ],
            'Tables' => [
                'Border Collapse' => 'border-collapse',
                'Border Spacing'  => 'border-spacing',
                'Table Layout'    => 'table-layout',
                'Caption Side'    => 'caption-side',
            ],
            'Transitions & Animation' => [
                'Transition Property'        => 'transition-property',
                'Transition Duration'        => 'transition-duration',
                'Transition Timing Function' => 'transition-timing-function',
                'Transition Delay'           => 'transition-delay',
                'Animation'                  => 'animation',
            ],
            'Transforms' => [
                'Scale'            => 'scale',
                'Rotate'           => 'rotate',
                'Translate'        => 'translate',
                'Skew'             => 'skew',
                'Transform Origin' => 'transform-origin',
            ],
            'Interactivity' => [
                'Accent Color'      => 'accent-color',
                'Appearance'        => 'appearance',
                'Cursor'            => 'cursor',
                'Caret Color'       => 'caret-color',
                'Pointer Events'    => 'pointer-events',
                'Resize'            => 'resize',
                'Scroll Behavior'   => 'scroll-behavior',
                'Scroll Margin'     => 'scroll-margin',
                'Scroll Padding'    => 'scroll-padding',
                'Scroll Snap Align' => 'scroll-snap-align',
                'Scroll Snap Stop'  => 'scroll-snap-stop',
                'Scroll Snap Type'  => 'scroll-snap-type',
                'Touch Action'      => 'touch-action',
                'User Select'       => 'user-select',
                'Will Change'       => 'will-change',
            ],
            'SVG' => [
                'Fill'         => 'fill',
                'Stroke'       => 'stroke',
                'Stroke Width' => 'stroke-width',
            ],
            'Accessibility' => [
                'Screen Readers'       => 'screen-readers',
                'Forced Color Adjust'  => 'forced-color-adjust',
            ],
            'Customization' => [
                'Configuration' => 'configuration',
                'Theme'         => 'customizing-colors',
                'Screens'       => 'screens',
                'Colors'        => 'colors',
                'Spacing'       => 'customizing-spacing',
                'Plugins'       => 'plugins',
                'Presets'       => 'presets',
            ],
            'Official Plugins' => [
                'Typography'       => 'typography-plugin',
                'Forms'            => 'forms',
                'Container Queries'=> 'container-queries',
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // MDX → HTML conversion
    // -------------------------------------------------------------------------

    private function convertMdx(string $mdx): string
    {
        // Strip frontmatter
        $mdx = preg_replace('/\A---\s*\n.*?\n---\s*\n/s', '', $mdx);

        // Remove import / export lines
        $mdx = preg_replace('/^(import|export)\s+.*$/m', '', $mdx);

        // Remove JSX block comments: {/* ... */}
        $mdx = preg_replace('/\{\/\*.*?\*\/\}/s', '', $mdx);

        // Remove JSX self-closing components (uppercase first letter)
        $mdx = preg_replace('/<[A-Z][A-Za-z.]*(\s[^>]*)?\s*\/>/s', '', $mdx);

        // Remove JSX opening/closing tags (keep text content between them)
        $mdx = preg_replace('/<[A-Z][A-Za-z.]*(?:\s[^>]*)?>/', '', $mdx);
        $mdx = preg_replace('/<\/[A-Z][A-Za-z.]*>/', '', $mdx);

        // Remove remaining {expression} blocks (template vars, etc.)
        $mdx = preg_replace('/^\s*\{[^}]*\}\s*$/m', '', $mdx);

        // Normalise blank lines
        $mdx = preg_replace('/\n{3,}/', "\n\n", $mdx);

        // Convert markdown → HTML
        $html = Str::markdown(trim($mdx), [
            'html_input'         => 'allow',
            'allow_unsafe_links' => false,
        ]);

        // External links open in a new tab
        $html = preg_replace_callback('/<a href="([^"]+)"/', function ($m) {
            $href = $m[1];
            if (str_starts_with($href, 'http') || str_starts_with($href, 'mailto')) {
                return "<a target=\"_blank\" rel=\"noopener\" href=\"{$href}\"";
            }
            return $m[0];
        }, $html);

        return $html;
    }

    // -------------------------------------------------------------------------
    // HTML assembly
    // -------------------------------------------------------------------------

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
<title>Tailwind CSS — Offline Documentation</title>
<style>{$css}</style>
</head>
<body>
<div id="app">

<div id="sidebar">
  <div id="sidebar-header">
    <div class="logo"><span>🎨</span> Tailwind CSS</div>
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
    el.classList.toggle('active',el.dataset.key===key);
  });
  location.hash=key;
}

function filterNav(q){
  var lq=q.toLowerCase();
  document.querySelectorAll('.nav-section').forEach(function(sec){
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
#content-wrap{flex:1;overflow-y:auto}
#content{padding:2rem 2.5rem;max-width:880px}
h1{font-size:1.75rem;font-weight:800;color:#f8fafc;margin-bottom:.4rem}
h2{font-size:1.2rem;font-weight:700;color:#f1f5f9;margin:2rem 0 .75rem;padding-bottom:.4rem;border-bottom:1px solid var(--border)}
h3{font-size:1rem;font-weight:600;color:#cbd5e1;margin:1.5rem 0 .5rem}
h4{font-size:.9rem;font-weight:600;color:var(--muted);margin:1rem 0 .4rem}
p{color:#cbd5e1;margin-bottom:.75rem;font-size:.9rem}
ul,ol{color:#cbd5e1;padding-left:1.4rem;margin-bottom:.75rem;font-size:.9rem}
li{margin-bottom:.25rem}
blockquote{border-left:3px solid var(--border);padding:.5rem 1rem;margin:1rem 0;color:var(--muted);font-size:.9rem}
table{width:100%;border-collapse:collapse;font-size:.82rem;margin:1rem 0;display:block;overflow-x:auto}
th{text-align:left;padding:6px 10px;background:#162032;color:var(--muted);font-weight:600;font-size:.75rem;text-transform:uppercase;border-bottom:1px solid var(--border)}
td{padding:7px 10px;border-bottom:1px solid #1e293b;vertical-align:top;color:#cbd5e1}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(99,102,241,.04)}
hr{border:none;border-top:1px solid var(--border);margin:1.5rem 0}
img{max-width:100%;border-radius:6px;margin:.5rem 0}
@media(max-width:768px){#sidebar{display:none}#content{padding:1.2rem}}
';
    }
}
