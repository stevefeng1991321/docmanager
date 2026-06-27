@extends('layouts.admin')
@section('title', 'Project Documentation')

@push('head')
<style>
#doc-pane{background:#0f172a}
#doc-content{padding:2rem 2.5rem;max-width:900px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#e2e8f0;line-height:1.6;font-size:15px}
#doc-content a{color:#818cf8;text-decoration:none}
#doc-content a:hover{text-decoration:underline}
#doc-content code{background:#0f172a;border:1px solid #334155;padding:1px 5px;border-radius:4px;font-family:'SFMono-Regular',Consolas,monospace;font-size:.82em;color:#f472b6}
#doc-content pre{background:#0f172a;border:1px solid #334155;border-radius:8px;padding:1rem;overflow-x:auto;font-family:'SFMono-Regular',Consolas,monospace;font-size:.82em;line-height:1.7;color:#e2e8f0;margin:1rem 0}
#doc-content pre code{background:none;border:none;padding:0;font-size:inherit;color:inherit}
#doc-content .doc-section{display:none;animation:docFadeIn .2s ease}
#doc-content .doc-section.active{display:block}
@keyframes docFadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}
#doc-content h1{font-size:1.75rem;font-weight:800;color:#f8fafc;margin-bottom:.3rem}
#doc-content h2{font-size:1.2rem;font-weight:700;color:#f1f5f9;margin:2rem 0 .75rem;padding-bottom:.4rem;border-bottom:1px solid #334155}
#doc-content h3{font-size:1rem;font-weight:600;color:#cbd5e1;margin:1.5rem 0 .5rem}
#doc-content h4{font-size:.9rem;font-weight:600;color:#94a3b8;margin:1rem 0 .4rem}
#doc-content .lead{color:#94a3b8;margin-bottom:1.5rem;font-size:.95rem}
#doc-content p{color:#cbd5e1;margin-bottom:.75rem;font-size:.9rem}
#doc-content ul,#doc-content ol{color:#cbd5e1;padding-left:1.4rem;margin-bottom:.75rem;font-size:.9rem}
#doc-content li{margin-bottom:.25rem}
#doc-content .badge{display:inline-block;padding:1px 7px;border-radius:999px;font-size:.7rem;font-weight:600;vertical-align:middle}
#doc-content .badge-green{background:#14532d;color:#86efac}
#doc-content .badge-blue{background:#1e3a5f;color:#93c5fd}
#doc-content .badge-yellow{background:#422006;color:#fde68a}
#doc-content .badge-red{background:#450a0a;color:#fca5a5}
#doc-content .badge-purple{background:#2e1065;color:#c4b5fd}
#doc-content .badge-gray{background:#1e293b;color:#94a3b8}
#doc-content .badge-cyan{background:#0c4a6e;color:#a5f3fc}
#doc-content .badge-orange{background:#431407;color:#fdba74}
#doc-content table{width:100%;border-collapse:collapse;font-size:.82rem;margin:1rem 0}
#doc-content th{text-align:left;padding:6px 10px;background:#162032;color:#94a3b8;font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #334155}
#doc-content td{padding:7px 10px;border-bottom:1px solid #1e293b;vertical-align:top;color:#cbd5e1}
#doc-content tr:last-child td{border-bottom:none}
#doc-content tr:hover td{background:rgba(99,102,241,.04)}
#doc-content td code{font-size:.75rem}
#doc-content .card-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin:1rem 0}
#doc-content .card{background:#1e293b;border:1px solid #334155;border-radius:10px;padding:1rem}
#doc-content .card h4{margin:0 0 .4rem;color:#f1f5f9;font-size:.9rem}
#doc-content .card p{margin:0;font-size:.8rem;color:#94a3b8}
#doc-content .card .card-icon{font-size:1.5rem;margin-bottom:.5rem}
#doc-content .feature-block{background:#1e293b;border:1px solid #334155;border-radius:10px;padding:1.2rem;margin:.75rem 0}
#doc-content .feature-block h4{margin:0 0 .5rem;color:#f1f5f9}
#doc-content .feature-block p{margin:0;font-size:.85rem;color:#94a3b8}
#doc-content .tree{font-family:'SFMono-Regular',Consolas,monospace;font-size:.82em;line-height:1.8;color:#e2e8f0;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:1rem;overflow-x:auto;margin:1rem 0;white-space:pre}
#doc-content .tree .dir{color:#93c5fd;font-weight:600}
#doc-content .tree .file{color:#e2e8f0}
#doc-content .tree .comment{color:#64748b}
#doc-content .method{display:inline-block;padding:1px 6px;border-radius:4px;font-size:.7rem;font-weight:700;font-family:monospace;margin-right:4px}
#doc-content .get{background:#0c4a6e;color:#7dd3fc}
#doc-content .post{background:#14532d;color:#86efac}
#doc-content .patch{background:#422006;color:#fde68a}
#doc-content .delete{background:#450a0a;color:#fca5a5}
#doc-content .put{background:#2e1065;color:#c4b5fd}
#doc-content .info-box{background:#0c2a4e;border:1px solid #1d4ed8;border-radius:8px;padding:.8rem 1rem;margin:1rem 0;font-size:.85rem;color:#bfdbfe}
#doc-content .warn-box{background:#3b1a00;border:1px solid #92400e;border-radius:8px;padding:.8rem 1rem;margin:1rem 0;font-size:.85rem;color:#fde68a}
</style>
@endpush

@section('content')
<div class="-m-4 sm:-m-6 flex" style="height:calc(100vh - 3.5rem)">

    {{-- ── Sidebar ── --}}
    <aside id="doc-sidebar"
           class="w-56 flex-shrink-0 flex flex-col overflow-hidden border-r border-gray-200 bg-gray-50">

        <div class="px-3 py-2.5 border-b border-gray-200 flex-shrink-0">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="doc-search" type="text" placeholder="Filter…"
                       class="w-full pl-7 pr-2 py-1.5 text-xs bg-white border border-gray-200 rounded-md
                              focus:outline-none focus:border-blue-400 text-gray-700 placeholder-gray-400">
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-2 px-2 space-y-3">
            @php
            $navGroups = [
                'Introduction' => [
                    ['id'=>'overview',   'label'=>'Overview'],
                    ['id'=>'quickstart', 'label'=>'Quick Start'],
                    ['id'=>'tech-stack', 'label'=>'Technology Stack'],
                ],
                'Folder Structure' => [
                    ['id'=>'folder-root',          'label'=>'Root Directory'],
                    ['id'=>'folder-app',           'label'=>'app/'],
                    ['id'=>'folder-routes',        'label'=>'routes/'],
                    ['id'=>'folder-resources',     'label'=>'resources/'],
                    ['id'=>'folder-database',      'label'=>'database/'],
                    ['id'=>'folder-storage',       'label'=>'storage/'],
                    ['id'=>'folder-bootstrap',     'label'=>'bootstrap/'],
                    ['id'=>'folder-public',        'label'=>'public/'],
                    ['id'=>'folder-config',        'label'=>'config/'],
                    ['id'=>'folder-vendor',        'label'=>'vendor/'],
                    ['id'=>'folder-documentation', 'label'=>'documentation/'],
                ],
                'Application Layer' => [
                    ['id'=>'models',             'label'=>'Models'],
                    ['id'=>'controllers-client', 'label'=>'Client Controllers'],
                    ['id'=>'controllers-admin',  'label'=>'Admin Controllers'],
                    ['id'=>'controllers-auth',   'label'=>'Auth Controllers'],
                    ['id'=>'controllers-api',    'label'=>'API Controllers'],
                    ['id'=>'controllers-chat',   'label'=>'Chat Controllers'],
                    ['id'=>'services',           'label'=>'Services'],
                    ['id'=>'jobs',               'label'=>'Jobs (Queue)'],
                    ['id'=>'commands',           'label'=>'Artisan Commands'],
                    ['id'=>'middleware',         'label'=>'Middleware'],
                ],
                'Features' => [
                    ['id'=>'feat-documents',    'label'=>'Document Management'],
                    ['id'=>'feat-search',       'label'=>'Search System'],
                    ['id'=>'feat-users',        'label'=>'User &amp; Roles'],
                    ['id'=>'feat-upload',       'label'=>'File Upload'],
                    ['id'=>'feat-sharing',      'label'=>'Sharing'],
                    ['id'=>'feat-notifications','label'=>'Notifications'],
                    ['id'=>'feat-problems',     'label'=>'Problems'],
                    ['id'=>'feat-tests',        'label'=>'Developer Tests'],
                    ['id'=>'feat-chat',         'label'=>'Chat &amp; Messaging'],
                    ['id'=>'feat-api',          'label'=>'REST API'],
                    ['id'=>'feat-schedule',     'label'=>'Scheduled Tasks'],
                    ['id'=>'feat-hr',           'label'=>'HR &amp; People'],
                    ['id'=>'feat-attendance',   'label'=>'Attendance &amp; Leave'],
                    ['id'=>'feat-work-reports', 'label'=>'Work Reports'],
                    ['id'=>'feat-plans',        'label'=>'Plans'],
                ],
            ];
            @endphp

            @foreach ($navGroups as $groupLabel => $items)
            <div class="doc-nav-group">
                <p class="px-1.5 mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                    {{ $groupLabel }}
                </p>
                @foreach ($items as $item)
                <button type="button"
                        onclick="docNav('{{ $item['id'] }}')"
                        data-section="{{ $item['id'] }}"
                        class="doc-nav-item w-full text-left flex items-center px-2 py-1 rounded text-xs
                               text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-colors duration-100 truncate">
                    {!! $item['label'] !!}
                </button>
                @endforeach
            </div>
            @endforeach
        </nav>
    </aside>

    {{-- ── Content pane ── --}}
    <div id="doc-pane" class="flex-1 overflow-y-auto">
        <div id="doc-content">
@verbatim
<!-- OVERVIEW -->
<div class="doc-section active" id="s-overview">
  <h1>DocManager</h1>
  <p class="lead">A full-featured document management system built with Laravel 12. Supports file upload, content extraction, TF-IDF semantic search, role-based access control, real-time chat, developer assessments, and a REST API — all served from a single application.</p>
  <h2>What it does</h2>
  <div class="card-grid">
    <div class="card"><div class="card-icon">📄</div><h4>Document Store</h4><p>Upload, version, categorise and tag documents of any type.</p></div>
    <div class="card"><div class="card-icon">🔍</div><h4>Dual Search</h4><p>Keyword (MySQL FULLTEXT) and TF-IDF semantic search over extracted content.</p></div>
    <div class="card"><div class="card-icon">👥</div><h4>Role-based Access</h4><p>Admin, Editor, and Viewer roles with per-user storage quotas.</p></div>
    <div class="card"><div class="card-icon">🔗</div><h4>Share Links</h4><p>Time-limited public share tokens. No login required for recipients.</p></div>
    <div class="card"><div class="card-icon">🔒</div><h4>Lock &amp; Archive</h4><p>Admins can lock documents to block downloads/previews, or auto-archive on a date.</p></div>
    <div class="card"><div class="card-icon">🌐</div><h4>REST API</h4><p>Sanctum token-based API for programmatic document access and search.</p></div>
    <div class="card"><div class="card-icon">💬</div><h4>Real-Time Chat</h4><p>1:1 messaging over a self-hosted WebSocket server (Laravel Reverb).</p></div>
    <div class="card"><div class="card-icon">🧪</div><h4>Developer Tests</h4><p>Build a timed coding test from the problem bank, send a no-account invite link, grade manually.</p></div>
    <div class="card"><div class="card-icon">👤</div><h4>HR &amp; People</h4><p>Employee profiles, departments, positions, documents, and manager hierarchy.</p></div>
    <div class="card"><div class="card-icon">🕐</div><h4>Attendance</h4><p>Daily check-in/out records, work hours, and leave requests with approval workflow.</p></div>
    <div class="card"><div class="card-icon">📋</div><h4>Work Reports</h4><p>Daily/weekly/monthly reports with task tracking, manager review, and analytics.</p></div>
    <div class="card"><div class="card-icon">🗂️</div><h4>Plans</h4><p>Full plan lifecycle with task lists, employee assignment, progress tracking, and comments.</p></div>
  </div>
  <h2>Key numbers</h2>
  <table>
    <tr><th>Item</th><th>Count</th></tr>
    <tr><td>Database tables</td><td>~58</td></tr>
    <tr><td>Models</td><td>47</td></tr>
    <tr><td>Controllers</td><td>~66</td></tr>
    <tr><td>Blade views</td><td>~120</td></tr>
    <tr><td>Migrations</td><td>65</td></tr>
    <tr><td>Broadcast events</td><td>3 (MessageSent, MessagesDelivered, MessagesRead)</td></tr>
    <tr><td>Artisan commands</td><td>6 custom</td></tr>
  </table>
</div>

<!-- QUICK START -->
<div class="doc-section" id="s-quickstart">
  <h1>Quick Start</h1>
  <p class="lead">From clone to running server in four steps.</p>
  <h2>One-command setup</h2>
  <pre><code>composer run setup</code></pre>
  <p>This single script runs: <code>composer install</code>, copies <code>.env.example → .env</code>, generates the app key, runs migrations, <code>npm install</code>, and <code>npm run build</code>.</p>
  <h2>Start the dev stack</h2>
  <pre><code>composer run dev</code></pre>
  <p>Starts four concurrent processes via <code>concurrently</code>:</p>
  <table>
    <tr><th>Process</th><th>Command</th><th>Purpose</th></tr>
    <tr><td><span class="badge badge-blue">server</span></td><td><code>php artisan serve</code></td><td>Laravel dev server on port 8000</td></tr>
    <tr><td><span class="badge badge-purple">queue</span></td><td><code>php artisan queue:listen</code></td><td>Processes ExtractDocumentContent &amp; IndexDocumentTfidf jobs</td></tr>
    <tr><td><span class="badge badge-cyan">vite</span></td><td><code>npm run dev</code></td><td>Vite HMR for CSS/JS assets</td></tr>
    <tr><td><span class="badge badge-gray">reverb</span></td><td><code>php artisan reverb:start</code></td><td>Self-hosted WebSocket server for real-time chat</td></tr>
  </table>
  <h2>Environment variables (.env)</h2>
  <table>
    <tr><th>Key</th><th>Default</th><th>Notes</th></tr>
    <tr><td><code>APP_NAME</code></td><td>DocManager</td><td>Shown in the UI header</td></tr>
    <tr><td><code>DB_CONNECTION</code></td><td>sqlite</td><td>Change to <code>mysql</code> for production</td></tr>
    <tr><td><code>QUEUE_CONNECTION</code></td><td>database</td><td><code>redis</code> recommended for production</td></tr>
    <tr><td><code>FILESYSTEM_DISK</code></td><td>local</td><td>Private uploads stored under <code>storage/app/private/</code></td></tr>
    <tr><td><code>SHARE_LINK_EXPIRY_HOURS</code></td><td>24</td><td>Default share link lifetime</td></tr>
    <tr><td><code>TRASH_RETENTION_DAYS</code></td><td>30</td><td>Days before soft-deleted files are permanently purged</td></tr>
    <tr><td><code>BROADCAST_CONNECTION</code></td><td>reverb</td><td>Chat transport — self-hosted</td></tr>
    <tr><td><code>REVERB_HOST / REVERB_PORT</code></td><td>localhost / 8080</td><td>Where the WebSocket server listens</td></tr>
  </table>
  <h2>First admin account</h2>
  <pre><code>php artisan tinker
>>> App\Models\User::create([
    'name'     => 'Admin',
    'username' => 'admin',
    'email'    => 'admin@example.com',
    'password' => bcrypt('secret'),
    'role'     => 'admin',
    'status'   => 'active',
]);</code></pre>
  <h2>Build search index</h2>
  <pre><code># Extract text from all uploaded files (queued)
php artisan search:reindex

# Build TF-IDF index (runs in-process)
php artisan search:build-tfidf</code></pre>
</div>

<!-- TECH STACK -->
<div class="doc-section" id="s-tech-stack">
  <h1>Technology Stack</h1>
  <p class="lead">All dependencies — server-side and front-end.</p>
  <h2>Backend (PHP / Composer)</h2>
  <table>
    <tr><th>Package</th><th>Version</th><th>Purpose</th></tr>
    <tr><td><strong>laravel/framework</strong></td><td>^12.0</td><td>Core MVC framework — Eloquent, Blade, routing, queues, auth</td></tr>
    <tr><td><strong>laravel/sanctum</strong></td><td>^4.3</td><td>API token authentication (SPA + REST)</td></tr>
    <tr><td><strong>laravel/reverb</strong></td><td>^1.10</td><td>Self-hosted WebSocket server — real-time chat</td></tr>
    <tr><td><strong>laravel/breeze</strong></td><td>^2.4</td><td>Auth scaffolding (login, register, password pages)</td></tr>
    <tr><td><strong>smalot/pdfparser</strong></td><td>^2.12</td><td>Extract plain text from PDF files</td></tr>
    <tr><td><strong>phpoffice/phpword</strong></td><td>^1.1</td><td>Extract text from DOCX files</td></tr>
    <tr><td><strong>phpoffice/phpspreadsheet</strong></td><td>^3.10</td><td>Extract text from XLSX/XLS spreadsheets</td></tr>
    <tr><td><strong>phpoffice/phppresentation</strong></td><td>^1.2</td><td>Extract text from PPTX presentations</td></tr>
  </table>
  <h2>Frontend (Node / npm)</h2>
  <table>
    <tr><th>Package</th><th>Purpose</th></tr>
    <tr><td><strong>Vite 7</strong> + <code>laravel-vite-plugin</code></td><td>Asset bundler — compiles CSS and JS</td></tr>
    <tr><td><strong>Tailwind CSS 3</strong></td><td>Utility-first CSS — all UI styling, responsive breakpoints</td></tr>
    <tr><td><strong>Alpine.js v3</strong></td><td>Inline reactivity — dropdowns, modals, drawers, star ratings</td></tr>
    <tr><td><strong>Flowbite 2</strong></td><td>Prebuilt Tailwind components (modals, badges, tables)</td></tr>
    <tr><td><strong>laravel-echo + pusher-js</strong></td><td>WebSocket client — connects to Reverb for live chat events</td></tr>
    <tr><td><strong>PDF.js</strong></td><td>Client-side PDF worker at <code>public/vendor/pdfjs/pdf.worker.min.js</code></td></tr>
  </table>
  <h2>Runtime requirements</h2>
  <table>
    <tr><th>Requirement</th><th>Notes</th></tr>
    <tr><td>PHP 8.2+</td><td>Named args, fibers, readonly props</td></tr>
    <tr><td>MySQL 8 or SQLite 3.35+</td><td>SQLite for dev; MySQL 8 needed for FULLTEXT search in prod</td></tr>
    <tr><td>Node 18+</td><td>Required for Vite build only</td></tr>
    <tr><td>Composer 2</td><td>Dependency management</td></tr>
  </table>
</div>

<!-- ROOT DIR -->
<div class="doc-section" id="s-folder-root">
  <h1>Root Directory</h1>
  <p class="lead">Top-level layout of the project. Only <code>public/</code> is exposed to the web.</p>
  <div class="tree"><span class="dir">docmanager/</span>
├── <span class="dir">app/</span>                <span class="comment"># ★ All PHP application code</span>
├── <span class="dir">bootstrap/</span>          <span class="comment"># Framework startup files and provider/binding cache</span>
├── <span class="dir">config/</span>             <span class="comment"># Laravel config files</span>
├── <span class="dir">database/</span>           <span class="comment"># Migrations, seeders, factories</span>
├── <span class="dir">node_modules/</span>       <span class="comment"># npm packages — generated</span>
├── <span class="dir">public/</span>             <span class="comment"># ★ WEB ROOT — the only folder the web server exposes</span>
├── <span class="dir">resources/</span>          <span class="comment"># ★ Blade views, CSS source, JS source</span>
├── <span class="dir">routes/</span>             <span class="comment"># URL → controller mappings</span>
├── <span class="dir">storage/</span>            <span class="comment"># ★ Uploaded files, logs, cache, TF-IDF index</span>
├── <span class="dir">tests/</span>              <span class="comment"># PHPUnit feature &amp; unit tests</span>
├── <span class="dir">vendor/</span>             <span class="comment"># Composer packages — generated</span>
├── <span class="file">.env</span>                <span class="comment"># ⚠ Secrets &amp; local config — never commit to git</span>
├── <span class="file">.env.example</span>        <span class="comment"># Safe template for .env</span>
├── <span class="file">artisan</span>             <span class="comment"># Laravel CLI entry point</span>
├── <span class="file">composer.json</span>       <span class="comment"># PHP dependencies</span>
├── <span class="file">package.json</span>        <span class="comment"># Node/npm dependencies</span>
├── <span class="file">tailwind.config.js</span>  <span class="comment"># Tailwind CSS configuration</span>
└── <span class="file">vite.config.js</span>      <span class="comment"># Vite asset bundler configuration</span></div>
  <h2>Key root-level files</h2>
  <table>
    <tr><th>File</th><th>Purpose</th><th>Commit?</th></tr>
    <tr><td><code>.env</code></td><td>DB credentials, API keys, app URL, queue driver</td><td style="color:#f87171">No — contains secrets</td></tr>
    <tr><td><code>.env.example</code></td><td>Template with all keys listed but no real values</td><td style="color:#4ade80">Yes</td></tr>
    <tr><td><code>artisan</code></td><td>Laravel CLI entry point</td><td style="color:#4ade80">Yes</td></tr>
    <tr><td><code>composer.json</code></td><td>PHP dependency manifest</td><td style="color:#4ade80">Yes</td></tr>
    <tr><td><code>composer.lock</code></td><td>Exact resolved versions</td><td style="color:#4ade80">Yes</td></tr>
    <tr><td><code>package.json</code></td><td>Node dependency manifest</td><td style="color:#4ade80">Yes</td></tr>
    <tr><td><code>vite.config.js</code></td><td>Asset bundler entry points</td><td style="color:#4ade80">Yes</td></tr>
  </table>
  <div class="warn-box"><strong>Web server setup:</strong> Point the document root to <code>public/</code>, not the project root. Uploaded files live in <code>storage/app/private/</code> and are only accessible through authenticated controller routes.</div>
</div>

<!-- APP/ -->
<div class="doc-section" id="s-folder-app">
  <h1>app/</h1>
  <p class="lead">All application PHP code lives here, following Laravel's standard PSR-4 namespacing under <code>App\</code>.</p>
  <div class="tree">
<span class="dir">app/</span>
├── <span class="dir">Console/Commands/</span>   <span class="comment"># 6 custom Artisan commands</span>
├── <span class="dir">Events/</span>             <span class="comment"># Broadcast events — MessageSent, MessagesDelivered, MessagesRead</span>
├── <span class="dir">Http/</span>
│   ├── <span class="dir">Controllers/</span>
│   │   ├── <span class="dir">Admin/</span>       <span class="comment"># Admin panel controllers</span>
│   │   ├── <span class="dir">Api/</span>         <span class="comment"># REST API controllers</span>
│   │   ├── <span class="dir">Auth/</span>        <span class="comment"># Auth flow controllers</span>
│   │   ├── <span class="dir">Chat/</span>        <span class="comment"># Real-time chat controllers</span>
│   │   └── <span class="dir">Client/</span>      <span class="comment"># User-facing controllers</span>
│   ├── <span class="dir">Middleware/</span>     <span class="comment"># RequireRole, RequireActiveAccount, SecurityHeaders</span>
│   ├── <span class="dir">Requests/</span>       <span class="comment"># Form request validators</span>
│   └── <span class="dir">Resources/</span>      <span class="comment"># API resource transformers (JSON)</span>
├── <span class="dir">Jobs/</span>               <span class="comment"># Queued jobs for text extraction + TF-IDF</span>
├── <span class="dir">Models/</span>             <span class="comment"># 32 Eloquent models</span>
├── <span class="dir">Services/</span>           <span class="comment"># ContentExtractorService, TfidfService</span>
└── <span class="dir">View/Components/</span>    <span class="comment"># AppLayout, GuestLayout Blade components</span>
  </div>
  <h2>Subfolders at a glance</h2>
  <table>
    <tr><th>Folder</th><th>Responsibility</th></tr>
    <tr><td><code>Models/</code></td><td>Eloquent ORM — database access, relationships, scopes</td></tr>
    <tr><td><code>Http/Controllers/Client/</code></td><td>Handles requests from logged-in users plus the public test-taking flow</td></tr>
    <tr><td><code>Http/Controllers/Admin/</code></td><td>Admin panel — document CRUD, user management, bulk actions, logs</td></tr>
    <tr><td><code>Http/Controllers/Auth/</code></td><td>Login, registration, password management</td></tr>
    <tr><td><code>Http/Controllers/Api/</code></td><td>JSON REST endpoints authenticated by Sanctum tokens</td></tr>
    <tr><td><code>Http/Controllers/Chat/</code></td><td>Conversations, messages, read/delivered receipts, presence</td></tr>
    <tr><td><code>Services/</code></td><td>Reusable business logic that doesn't fit in a controller</td></tr>
    <tr><td><code>Jobs/</code></td><td>Queued background work (heavy text extraction, indexing)</td></tr>
    <tr><td><code>Console/Commands/</code></td><td><code>php artisan</code> maintenance commands</td></tr>
  </table>
</div>

<!-- ROUTES/ -->
<div class="doc-section" id="s-folder-routes">
  <h1>routes/</h1>
  <p class="lead">Five route files, each with a distinct purpose.</p>
  <div class="tree">
<span class="dir">routes/</span>
├── <span class="file">web.php</span>      <span class="comment"># Browser routes (session auth, CSRF protection)</span>
├── <span class="file">auth.php</span>     <span class="comment"># Login / register / password routes</span>
├── <span class="file">api.php</span>      <span class="comment"># REST API routes (/api prefix, Sanctum token auth)</span>
├── <span class="file">channels.php</span> <span class="comment"># Broadcasting channel authorization</span>
└── <span class="file">console.php</span>  <span class="comment"># Scheduled Artisan command definitions</span>
  </div>
  <h2>web.php — Client routes (selection)</h2>
  <table>
    <tr><th>Method</th><th>URI</th><th>Name</th></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/</code></td><td>home</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/search</code></td><td>search.index</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/documents/{id}</code></td><td>documents.show</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/documents/{id}/download</code></td><td>documents.download</td></tr>
    <tr><td><span class="method post">POST</span></td><td><code>/documents/{id}/share</code></td><td>documents.share</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/share/{token}</code></td><td>share.show</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/chat</code></td><td>chat.index</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/notifications</code></td><td>notifications.index</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/attendance</code></td><td>attendance.index</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/work-reports</code></td><td>work-reports.index</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/plans</code></td><td>plans.index</td></tr>
  </table>
  <h2>web.php — Admin routes (selection)</h2>
  <p>All admin routes require <code>auth + active + role:admin,editor</code> middleware and are prefixed <code>/admin</code>.</p>
  <table>
    <tr><th>Method</th><th>URI</th><th>Action</th></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/admin</code></td><td>Dashboard</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/admin/documents</code></td><td>Document list</td></tr>
    <tr><td><span class="method post">POST</span></td><td><code>/admin/documents</code></td><td>Upload new document</td></tr>
    <tr><td><span class="method patch">PATCH</span></td><td><code>/admin/documents/{id}/lock</code></td><td>Lock document</td></tr>
    <tr><td><span class="method patch">PATCH</span></td><td><code>/admin/documents/{id}/approve</code></td><td>Approve document</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/admin/users</code></td><td>User list</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/admin/settings</code></td><td>Global settings</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/admin/jobs</code></td><td>Queue job monitor</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/admin/plans</code></td><td>Plan list</td></tr>
  </table>
  <h2>api.php — REST routes</h2>
  <table>
    <tr><th>Method</th><th>URI</th><th>Auth</th><th>Action</th></tr>
    <tr><td><span class="method post">POST</span></td><td><code>/api/auth/login</code></td><td>none</td><td>Issue Sanctum token</td></tr>
    <tr><td><span class="method post">POST</span></td><td><code>/api/auth/logout</code></td><td>token</td><td>Revoke current token</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/api/resources</code></td><td>token</td><td>List published documents</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/api/resources/{id}</code></td><td>token</td><td>Single document detail</td></tr>
    <tr><td><span class="method post">POST</span></td><td><code>/api/resources</code></td><td>token</td><td>Upload new document</td></tr>
    <tr><td><span class="method get">GET</span></td><td><code>/api/search</code></td><td>token</td><td>Keyword search</td></tr>
  </table>
  <h2>console.php — Scheduled tasks</h2>
  <table>
    <tr><th>Command</th><th>Schedule</th></tr>
    <tr><td><code>dms:prune-trash</code></td><td>Daily at 02:00</td></tr>
    <tr><td><code>dms:prune-shares</code></td><td>Daily at 02:15</td></tr>
    <tr><td><code>dms:prune-tokens</code></td><td>Daily at 02:30</td></tr>
    <tr><td><code>dms:archive-expired</code></td><td>Hourly</td></tr>
    <tr><td><code>queue:prune-batches --hours=48</code></td><td>Weekly</td></tr>
  </table>
</div>

<!-- RESOURCES/ -->
<div class="doc-section" id="s-folder-resources">
  <h1>resources/</h1>
  <p class="lead">All source files that Vite compiles, plus the Blade view templates.</p>
  <div class="tree">
<span class="dir">resources/</span>
├── <span class="dir">css/</span>
│   └── <span class="file">app.css</span>          <span class="comment"># Tailwind CSS directives</span>
├── <span class="dir">js/</span>
│   ├── <span class="file">app.js</span>           <span class="comment"># Alpine.js bootstrap + global JS</span>
│   └── <span class="file">bootstrap.js</span>     <span class="comment"># Axios defaults, CSRF header</span>
└── <span class="dir">views/</span>
    ├── <span class="dir">layouts/</span>         <span class="comment"># Master layouts (app, admin, guest)</span>
    ├── <span class="dir">components/</span>      <span class="comment"># Reusable Blade components</span>
    ├── <span class="dir">admin/</span>           <span class="comment"># Admin panel views</span>
    ├── <span class="dir">auth/</span>            <span class="comment"># login, register, confirm-password</span>
    ├── <span class="dir">home/</span>            <span class="comment"># Document grid + category drawer</span>
    ├── <span class="dir">documents/</span>       <span class="comment"># show, preview views</span>
    ├── <span class="dir">search/</span>          <span class="comment"># Search results + filter drawer</span>
    └── <span class="dir">share/</span>           <span class="comment"># Public share page (no auth required)</span>
  </div>
  <h2>Layout system</h2>
  <table>
    <tr><th>Layout</th><th>Used by</th><th>Key features</th></tr>
    <tr><td><code>layouts/app.blade.php</code></td><td>All client pages</td><td>Responsive nav, user dropdown, unread notification badge, flash toasts</td></tr>
    <tr><td><code>layouts/admin.blade.php</code></td><td>All <code>/admin/*</code> pages</td><td>Collapsible sidebar (Alpine.js), mobile backdrop overlay, per-link active highlight</td></tr>
    <tr><td><code>layouts/guest.blade.php</code></td><td>Login, register</td><td>Centred card, no navigation</td></tr>
  </table>
</div>

<!-- DATABASE/ -->
<div class="doc-section" id="s-folder-database">
  <h1>database/</h1>
  <p class="lead">Migrations define the schema in chronological order. Run <code>php artisan migrate</code> to apply all.</p>
  <div class="tree">
<span class="dir">database/</span>
├── <span class="dir">factories/</span>    <span class="comment"># Model factories for test data (Faker-based)</span>
├── <span class="dir">migrations/</span>   <span class="comment"># 65 migration files</span>
└── <span class="dir">seeders/</span>      <span class="comment"># DatabaseSeeder.php</span>
  </div>
  <h2>Key tables</h2>
  <table>
    <tr><th>Table</th><th>Purpose</th></tr>
    <tr><td><code>users</code></td><td>Accounts — username, role, status, failed_login_attempts, storage_quota_mb</td></tr>
    <tr><td><code>resources</code></td><td>Documents/files — soft deletable, locked_by, expires_at, status</td></tr>
    <tr><td><code>document_versions</code></td><td>Version history per document</td></tr>
    <tr><td><code>resource_embeddings</code></td><td>TF-IDF vectors stored as JSON</td></tr>
    <tr><td><code>shares</code></td><td>Time-limited public share tokens</td></tr>
    <tr><td><code>audit_logs</code></td><td>Admin action trail</td></tr>
    <tr><td><code>employees</code></td><td>Employee profiles — EMP-00001 codes, manager hierarchy, salary</td></tr>
    <tr><td><code>attendances</code></td><td>Daily attendance records</td></tr>
    <tr><td><code>work_reports</code></td><td>Daily/weekly/monthly work reports with status workflow</td></tr>
    <tr><td><code>plans</code></td><td>Plans — full lifecycle, JSON tags, progress %, soft-deletes</td></tr>
    <tr><td><code>conversations + messages</code></td><td>Chat — normalized 1:1 pair, message history</td></tr>
    <tr><td><code>tests + test_invites + test_answers</code></td><td>Developer assessment tables</td></tr>
    <tr><td><code>settings</code></td><td>Global key-value config store (cached)</td></tr>
  </table>
</div>

<!-- STORAGE/ -->
<div class="doc-section" id="s-folder-storage">
  <h1>storage/</h1>
  <p class="lead">All runtime-generated files. Nothing in <code>storage/</code> is committed to git.</p>
  <div class="tree">
<span class="dir">storage/</span>
├── <span class="dir">app/</span>
│   ├── <span class="dir">chunks/</span>              <span class="comment"># Temporary upload chunks (deleted after assembly)</span>
│   ├── <span class="dir">private/</span>
│   │   ├── <span class="dir">resources/</span>           <span class="comment"># Private uploaded files (UUID-named)</span>
│   │   └── <span class="dir">search/</span>
│   │       └── <span class="file">tfidf_idf.json</span>   <span class="comment"># IDF dictionary (8,125 terms, 105 docs)</span>
│   ├── <span class="dir">public/avatars/</span>          <span class="comment"># User profile pictures (publicly accessible via symlink)</span>
│   └── <span class="dir">temp/</span>                    <span class="comment"># Bulk download ZIPs</span>
├── <span class="dir">framework/cache/</span>             <span class="comment"># Application cache</span>
└── <span class="dir">logs/laravel.log</span>             <span class="comment"># Application log</span>
  </div>
  <div class="warn-box"><strong>Storage disk root:</strong> The <code>local</code> disk resolves to <code>storage/app/private/</code>. When calling <code>Storage::disk('local')->put('resources/file.pdf', …)</code> the actual path is <code>storage/app/private/resources/file.pdf</code>. Never prefix paths with <code>private/</code> when using the local disk.</div>
</div>

<!-- BOOTSTRAP/ -->
<div class="doc-section" id="s-folder-bootstrap">
  <h1>bootstrap/</h1>
  <p class="lead">Bootstraps the Laravel application instance on every request and caches resolved bindings.</p>
  <div class="tree">
<span class="dir">bootstrap/</span>
├── <span class="file">app.php</span>       <span class="comment"># Creates the Application instance, registers middleware and routes</span>
├── <span class="file">providers.php</span> <span class="comment"># Auto-discovered service providers list</span>
└── <span class="dir">cache/</span>
    ├── <span class="file">packages.php</span> <span class="comment"># Cached package list</span>
    └── <span class="file">services.php</span> <span class="comment"># Cached service container bindings</span>
  </div>
  <p>The <code>app.php</code> file registers the three global middleware groups: <strong>web</strong> (sessions, CSRF, cookies), <strong>api</strong> (stateless Sanctum auth, throttle), and global aliases — <code>role</code> → <code>RequireRole</code>, <code>active</code> → <code>RequireActiveAccount</code>.</p>
  <div class="info-box">If you add a new service provider or a package is misbehaving, run <code>php artisan optimize:clear</code> to wipe all cached bootstrap files and rebuild on next request.</div>
</div>

<!-- PUBLIC/ -->
<div class="doc-section" id="s-folder-public">
  <h1>public/</h1>
  <p class="lead">The only folder exposed directly by the web server. Everything else is inaccessible by URL.</p>
  <div class="tree">
<span class="dir">public/</span>
├── <span class="file">index.php</span>             <span class="comment"># Laravel front controller — every request starts here</span>
├── <span class="dir">storage/</span>              <span class="comment"># Symlink → storage/app/public/ (run php artisan storage:link)</span>
├── <span class="dir">vendor/pdfjs/</span>
│   └── <span class="file">pdf.worker.min.js</span> <span class="comment"># PDF.js worker for browser-side PDF preview</span>
└── <span class="dir">build/</span>                <span class="comment"># Vite output (regenerated by npm run build)</span>
    └── <span class="dir">assets/</span>
        ├── <span class="file">app-*.css</span>
        └── <span class="file">app-*.js</span>
  </div>
  <h2>Storage symlink</h2>
  <pre><code>php artisan storage:link</code></pre>
  <p>Creates <code>public/storage → storage/app/public</code>. User avatars become reachable at <code>http://localhost/storage/avatars/</code>.</p>
</div>

<!-- CONFIG/ -->
<div class="doc-section" id="s-folder-config">
  <h1>config/</h1>
  <p class="lead">All configuration files. Values are read from <code>.env</code> via <code>env()</code>. Never read <code>.env</code> directly in code — always use <code>config('key')</code>.</p>
  <table>
    <tr><th>File</th><th>Key settings</th></tr>
    <tr><td><code>app.php</code></td><td><code>APP_NAME</code>, <code>APP_ENV</code>, <code>APP_KEY</code>, service providers, aliases</td></tr>
    <tr><td><code>auth.php</code></td><td>Guards (<code>web</code> = session, <code>api</code> = sanctum)</td></tr>
    <tr><td><code>database.php</code></td><td>DB connections — <code>sqlite</code> default, <code>mysql</code> available</td></tr>
    <tr><td><code>filesystems.php</code></td><td>Disks: <code>local</code> (private), <code>public</code> (publicly linked), <code>s3</code> (optional)</td></tr>
    <tr><td><code>queue.php</code></td><td><code>QUEUE_CONNECTION</code> — <code>database</code> by default, <code>redis</code> for production</td></tr>
    <tr><td><code>cache.php</code></td><td><code>CACHE_STORE</code> — <code>file</code> by default</td></tr>
    <tr><td><code>session.php</code></td><td><code>SESSION_DRIVER</code> — <code>file</code> default</td></tr>
    <tr><td><code>logging.php</code></td><td>Log channel — <code>stack</code> (daily + stderr)</td></tr>
  </table>
  <div class="info-box">After changing any <code>.env</code> value in production, run <code>php artisan config:cache</code>. In development, clear it with <code>php artisan config:clear</code>.</div>
</div>

<!-- VENDOR/ -->
<div class="doc-section" id="s-folder-vendor">
  <h1>vendor/</h1>
  <p class="lead">Composer-managed PHP packages. This folder is generated — never edit files inside it. Run <code>composer install</code> to recreate it from <code>composer.lock</code>.</p>
  <h2>Production dependencies</h2>
  <table>
    <tr><th>Package</th><th>What it provides</th></tr>
    <tr><td><code>laravel/framework</code></td><td>The entire Laravel core — Eloquent, Blade, routing, queue, auth</td></tr>
    <tr><td><code>laravel/sanctum</code></td><td>API token authentication</td></tr>
    <tr><td><code>smalot/pdfparser</code></td><td>Pure-PHP PDF text extraction</td></tr>
    <tr><td><code>phpoffice/phpword</code></td><td>DOCX text extraction</td></tr>
    <tr><td><code>phpoffice/phpspreadsheet</code></td><td>XLSX/XLS text extraction</td></tr>
    <tr><td><code>phpoffice/phppresentation</code></td><td>PPTX text extraction</td></tr>
  </table>
  <h2>Dev-only dependencies</h2>
  <table>
    <tr><th>Package</th><th>Purpose</th></tr>
    <tr><td><code>laravel/breeze</code></td><td>Auth scaffolding</td></tr>
    <tr><td><code>laravel/pail</code></td><td>Real-time log streaming in the terminal</td></tr>
    <tr><td><code>laravel/pint</code></td><td>PHP code style fixer (PSR-12)</td></tr>
    <tr><td><code>fakerphp/faker</code></td><td>Fake data for factories and seeders</td></tr>
    <tr><td><code>phpunit/phpunit</code></td><td>Unit and feature test runner</td></tr>
  </table>
</div>

<!-- DOCUMENTATION/ -->
<div class="doc-section" id="s-folder-documentation">
  <h1>documentation/</h1>
  <p class="lead">Self-contained offline HTML reference pages for every tool in the project's stack. Open directly in a browser — no server needed.</p>
  <p>The offline documentation set covers Laravel 12, Tailwind CSS v3, Alpine.js v3, Flowbite v2, Vite v7, and this project reference. Each is a single self-contained HTML file with embedded search, navigation, and content.</p>
  <h2>How the Laravel offline doc was built</h2>
  <p>Two Artisan commands created and populated the Laravel offline doc:</p>
  <pre><code># 1. Download 103 .md files from github.com/laravel/docs
php artisan docs:import-laravel --branch=12.x

# 2. Convert markdown → embedded JS data → single HTML file
php artisan docs:build-laravel-offline --branch=12.x</code></pre>
  <p>The generated file embeds all page content as JavaScript objects with hash-based navigation, client-side full-text search, and a collapsible sidebar — no server needed.</p>
</div>

<!-- MODELS -->
<div class="doc-section" id="s-models">
  <h1>Models</h1>
  <p class="lead">32 Eloquent models in <code>app/Models/</code>. Each maps to a database table and defines relationships, scopes, and domain logic.</p>
  <h2>Core document models</h2>
  <table>
    <tr><th>Model</th><th>Table</th><th>Notable methods</th></tr>
    <tr><td><strong>Resource</strong></td><td><code>resources</code></td><td><code>isLocked()</code>, <code>isPublished()</code>, <code>scopePublished()</code>, <code>averageRating()</code></td></tr>
    <tr><td><strong>Category</strong></td><td><code>categories</code></td><td>Self-referential: <code>parent()</code>, <code>children()</code></td></tr>
    <tr><td><strong>Tag</strong></td><td><code>tags</code></td><td><code>resources()</code> many-to-many</td></tr>
    <tr><td><strong>DocumentVersion</strong></td><td><code>document_versions</code></td><td>Belongs to Resource</td></tr>
    <tr><td><strong>ResourceEmbedding</strong></td><td><code>resource_embeddings</code></td><td>Stores TF-IDF vectors; model="tfidf-v1"</td></tr>
  </table>
  <h2>User models</h2>
  <table>
    <tr><th>Model</th><th>Table</th><th>Notable methods</th></tr>
    <tr><td><strong>User</strong></td><td><code>users</code></td><td><code>isAdmin()</code>, <code>isEditor()</code>, <code>isActive()</code>, <code>storageUsedBytes()</code>, <code>wouldExceedQuota()</code></td></tr>
    <tr><td><strong>UserPreference</strong></td><td><code>user_preferences</code></td><td>One-to-one with User</td></tr>
    <tr><td><strong>AccountRequest</strong></td><td><code>account_requests</code></td><td><code>isPending()</code>, <code>isApproved()</code>, <code>isRejected()</code></td></tr>
  </table>
  <h2>HR &amp; People models</h2>
  <table>
    <tr><th>Model</th><th>Table</th><th>Notable methods</th></tr>
    <tr><td><strong>Employee</strong></td><td><code>employees</code></td><td><code>isActive()</code>, <code>nextCode()</code>, <code>manager()</code>, <code>subordinates()</code>, <code>plans()</code></td></tr>
    <tr><td><strong>Department</strong></td><td><code>departments</code></td><td><code>employees()</code>, <code>manager()</code></td></tr>
    <tr><td><strong>Position</strong></td><td><code>positions</code></td><td><code>department()</code>, <code>employees()</code></td></tr>
    <tr><td><strong>WorkReport</strong></td><td><code>work_reports</code></td><td><code>canBeViewedBy($user)</code>, <code>canBeEditedBy($user)</code>, <code>canBeReviewedBy($user)</code></td></tr>
    <tr><td><strong>Plan</strong></td><td><code>plans</code></td><td><code>nextNumber()</code>, <code>updateProgress()</code>, <code>is_overdue</code>, <code>employees()</code>, SoftDeletes</td></tr>
  </table>
  <h2>Chat models</h2>
  <table>
    <tr><th>Model</th><th>Table</th><th>Notable methods</th></tr>
    <tr><td><strong>Conversation</strong></td><td><code>conversations</code></td><td><code>Conversation::between($a,$b)</code>, <code>otherUser()</code>, <code>unreadCountFor()</code></td></tr>
    <tr><td><strong>Message</strong></td><td><code>messages</code></td><td>Read status derived from <code>ConversationRead.last_read_at</code></td></tr>
    <tr><td><strong>ConversationRead</strong></td><td><code>conversation_reads</code></td><td>One upsert marks the whole thread read</td></tr>
  </table>
  <h2>System models</h2>
  <table>
    <tr><th>Model</th><th>Table</th><th>Purpose</th></tr>
    <tr><td><strong>Setting</strong></td><td><code>settings</code></td><td><code>Setting::get($key)</code>, <code>Setting::set($key,$val)</code>, <code>Setting::allKeyed()</code> (cached)</td></tr>
    <tr><td><strong>AuditLog</strong></td><td><code>audit_logs</code></td><td><code>AuditLog::record($action, $resourceId, $details)</code></td></tr>
    <tr><td><strong>Notification</strong></td><td><code>notifications</code></td><td><code>Notification::send($userId, $type, $title, $msg)</code></td></tr>
  </table>
</div>

<!-- CLIENT CONTROLLERS -->
<div class="doc-section" id="s-controllers-client">
  <h1>Client Controllers</h1>
  <p class="lead">Located in <code>app/Http/Controllers/Client/</code>. Handle all user-facing (non-admin) page requests.</p>
  <table>
    <tr><th>Controller</th><th>What it does</th></tr>
    <tr><td><strong>HomeController</strong></td><td>Doc grid with category tree (cached 1 h), sort and grid/list toggle. Browse endpoint responds to both page load and AJAX fetch.</td></tr>
    <tr><td><strong>DocumentController</strong></td><td>show, preview, stream (lock-checked), download (increments count, writes download_log), share (creates Share token).</td></tr>
    <tr><td><strong>SearchController</strong></td><td>Keyword (MySQL FULLTEXT) or TF-IDF semantic search based on <code>mode</code> param. Logs every non-empty query to <code>search_logs</code>.</td></tr>
    <tr><td><strong>ProfileController</strong></td><td>Profile settings. Username changes go through an admin-approval workflow.</td></tr>
    <tr><td><strong>WorkReportController</strong></td><td>Full work report lifecycle. Managers see a "Team" tab listing direct subordinates' submissions. Comments support types: comment, feedback, revision_request.</td></tr>
    <tr><td><strong>AttendanceController</strong></td><td>Employee self-service: check-in, check-out, monthly history.</td></tr>
    <tr><td><strong>PlanController (Client)</strong></td><td>Read-only plan access for assigned employees. 403 unless assigned or admin/editor.</td></tr>
    <tr><td><strong>TestSessionController</strong></td><td>Public, no-account developer-test flow via <code>/test/{token}</code>. Auto-finalizes expired invites on load.</td></tr>
  </table>
</div>

<!-- ADMIN CONTROLLERS -->
<div class="doc-section" id="s-controllers-admin">
  <h1>Admin Controllers</h1>
  <p class="lead">Located in <code>app/Http/Controllers/Admin/</code>. All require <code>role:admin,editor</code> middleware unless noted.</p>
  <table>
    <tr><th>Controller</th><th>Key actions</th></tr>
    <tr><td><strong>DashboardController</strong></td><td>5-card stat grid (total docs, active users, downloads today, storage used, failed jobs). Upload/download trends (7 days), top searches (30 days). Stats cached 300 s.</td></tr>
    <tr><td><strong>DocumentController</strong></td><td>Full lifecycle: list, create, edit, soft-delete, restore, force-delete, lock/unlock, approve/reject, archive/unarchive. Bulk: approve, trash, download as ZIP. Chunked upload: receive chunk, assemble.</td></tr>
    <tr><td><strong>UserController</strong></td><td><em>(admin only)</em> List, create, edit, activate/deactivate, reset password, bulk-activate pending registrations.</td></tr>
    <tr><td><strong>CategoryController</strong></td><td>CRUD with hierarchical nesting. Inline edit modal with circular-reference guard. Delete protection if docs or subcategories exist. Clears <code>home.categories.tree</code> cache on change.</td></tr>
    <tr><td><strong>SearchIndexController</strong></td><td>TF-IDF status, top queries, zero-result queries (content gap panel). Triggers reindex and rebuild artisan commands.</td></tr>
    <tr><td><strong>EmployeeController</strong></td><td>Full employee profile CRUD. Tabbed show page: details, documents, attendance, work reports, plans.</td></tr>
    <tr><td><strong>PlanController (Admin)</strong></td><td>Full CRUD. archive(), duplicate() (new PLN number, status=draft, tasks copied, assignments not copied).</td></tr>
    <tr><td><strong>TestController</strong></td><td>Developer Tests CRUD. Accepts existing bank problems and inline new problems. At least one problem required.</td></tr>
    <tr><td><strong>TestInviteController</strong></td><td>Generates unguessable token per candidate. Grading screen: candidate code vs reference solution, score clamped to problem's point value.</td></tr>
    <tr><td><strong>JobMonitorController</strong></td><td>Pending and failed queue jobs. retry($id) and retryAll().</td></tr>
  </table>
</div>

<!-- AUTH CONTROLLERS -->
<div class="doc-section" id="s-controllers-auth">
  <h1>Auth Controllers</h1>
  <p class="lead">Located in <code>app/Http/Controllers/Auth/</code>. Generated by Laravel Breeze, lightly customised.</p>
  <table>
    <tr><th>Controller</th><th>What it does</th></tr>
    <tr><td><strong>AuthenticatedSessionController</strong></td><td>Login form, credential validation with account-lock check, role-based redirect, logout.</td></tr>
    <tr><td><strong>RegisteredUserController</strong></td><td>New user registration. Newly registered accounts have <code>status = 'pending'</code> and cannot log in until an admin activates them.</td></tr>
    <tr><td><strong>PasswordController</strong></td><td>Changes the authenticated user's password — requires old password confirmation.</td></tr>
  </table>
</div>

<!-- API CONTROLLERS -->
<div class="doc-section" id="s-controllers-api">
  <h1>API Controllers</h1>
  <p class="lead">Located in <code>app/Http/Controllers/Api/</code>. Return JSON. Authenticated with Sanctum bearer tokens.</p>
  <table>
    <tr><th>Controller</th><th>What it does</th></tr>
    <tr><td><strong>AuthController</strong></td><td><code>login</code>: validates credentials, enforces throttle (<code>api-login</code>), handles lockout, returns token + user. <code>logout</code>: deletes current token. <code>me</code>: returns authenticated user.</td></tr>
    <tr><td><strong>ResourceController</strong></td><td>Full REST CRUD. Responses wrapped in <code>DocumentResource</code> API resource transformers. <code>download</code> throttled via <code>api-download</code> limiter.</td></tr>
    <tr><td><strong>SearchController</strong></td><td>Single-action controller. Keyword search, logs query to <code>search_logs</code>, returns paginated <code>DocumentResource</code> results.</td></tr>
  </table>
  <h2>API resource transformers</h2>
  <table>
    <tr><th>Transformer</th><th>Transforms</th></tr>
    <tr><td><code>DocumentResource</code></td><td>Resource model → JSON (excludes file_path, file_hash; includes category, tags)</td></tr>
    <tr><td><code>CategoryResource</code></td><td>Category model → JSON</td></tr>
    <tr><td><code>TagResource</code></td><td>Tag model → JSON</td></tr>
  </table>
</div>

<!-- CHAT CONTROLLERS -->
<div class="doc-section" id="s-controllers-chat">
  <h1>Chat Controllers</h1>
  <p class="lead">Located in <code>app/Http/Controllers/Chat/</code>.</p>
  <table>
    <tr><th>Controller</th><th>What it does</th></tr>
    <tr><td><strong>ChatController</strong></td><td><code>index</code>: renders chat page with conversation list pre-hydrated server-side; supports <code>?user=</code> to find-or-create and auto-open a conversation. <code>unreadCount</code>: backs the live nav badge.</td></tr>
    <tr><td><strong>ConversationController</strong></td><td><code>index</code>: list + search by participant name or message body. <code>store</code>: find-or-create a 1:1 conversation. <code>show</code>: returns message history and marks thread read + delivered, broadcasting both. Every action checks <code>involves($user->id)</code> — 403 otherwise.</td></tr>
    <tr><td><strong>MessageController</strong></td><td>Saves the message, bumps <code>last_message_at</code>, broadcasts <code>MessageSent</code>. Wrapped in try/catch — a Reverb outage logs a warning but never fails the send.</td></tr>
  </table>
</div>

<!-- SERVICES -->
<div class="doc-section" id="s-services">
  <h1>Services</h1>
  <p class="lead">Located in <code>app/Services/</code>. Reusable business logic injected into controllers and jobs.</p>
  <h2>ContentExtractorService</h2>
  <p>Extracts plain text from uploaded files so they can be indexed for search.</p>
  <table>
    <tr><th>Method</th><th>Library</th><th>File types</th></tr>
    <tr><td><code>extractPdf()</code></td><td>smalot/pdfparser</td><td><code>application/pdf</code></td></tr>
    <tr><td><code>extractDocx()</code></td><td>phpoffice/phpword</td><td><code>.docx</code></td></tr>
    <tr><td><code>extractXlsx()</code></td><td>phpoffice/phpspreadsheet</td><td><code>.xlsx</code>, <code>.xls</code></td></tr>
    <tr><td><code>extractPptx()</code></td><td>phpoffice/phppresentation</td><td><code>.pptx</code></td></tr>
    <tr><td><code>extractText()</code></td><td>PHP built-in</td><td><code>.txt</code>, <code>.md</code></td></tr>
  </table>
  <div class="info-box"><code>ContentExtractorService::extract($resource)</code> reads <code>$resource->file_type</code> (a full MIME type like <code>application/pdf</code>) and routes to the correct private method.</div>
  <h2>TfidfService</h2>
  <p>Implements TF-IDF (Term Frequency–Inverse Document Frequency) for semantic search without a vector database.</p>
  <table>
    <tr><th>Method</th><th>Purpose</th></tr>
    <tr><td><code>tokenize($text)</code></td><td>Lowercase → strip punctuation → remove stop words → filter &lt;3 chars</td></tr>
    <tr><td><code>computeTf($tokens)</code></td><td>Term frequency map for one document</td></tr>
    <tr><td><code>buildIdfFromDf($df, $N)</code></td><td>Smooth IDF: <code>log((N+1)/(df+1)) + 1</code></td></tr>
    <tr><td><code>computeTfidfVector($tokens, $idf)</code></td><td>TF × IDF for each term, L2-normalised</td></tr>
    <tr><td><code>cosineSimilarity($a, $b)</code></td><td>Dot product of two normalised vectors</td></tr>
    <tr><td><code>loadIdf() / saveIdf()</code></td><td>Persist IDF dictionary to <code>storage/private/search/tfidf_idf.json</code></td></tr>
  </table>
</div>

<!-- JOBS -->
<div class="doc-section" id="s-jobs">
  <h1>Jobs (Queue)</h1>
  <p class="lead">Located in <code>app/Jobs/</code>. Heavy operations are queued so they don't block the HTTP response.</p>
  <h2>ExtractDocumentContent</h2>
  <div class="feature-block">
    <h4>Triggered by</h4>
    <p>Admin document upload (via DocumentController or ChunkedUploadController). Also by <code>php artisan search:reindex</code>.</p>
  </div>
  <table>
    <tr><th>Property</th><th>Value</th></tr>
    <tr><td>Retries</td><td>3</td></tr>
    <tr><td>Timeout</td><td>120 seconds</td></tr>
    <tr><td>What it does</td><td>Calls <code>ContentExtractorService::extract()</code>, stores result in <code>resources.content</code>, then dispatches <code>IndexDocumentTfidf</code></td></tr>
  </table>
  <h2>IndexDocumentTfidf</h2>
  <div class="feature-block">
    <h4>Triggered by</h4>
    <p>Dispatched automatically after <code>ExtractDocumentContent</code> finishes. Also by <code>php artisan search:build-tfidf</code>.</p>
  </div>
  <table>
    <tr><th>Property</th><th>Value</th></tr>
    <tr><td>Retries</td><td>3</td></tr>
    <tr><td>Timeout</td><td>60 seconds</td></tr>
    <tr><td>What it does</td><td>Combines <code>title + description + content</code>, computes TF-IDF vector, stores in <code>resource_embeddings</code> (model="tfidf-v1")</td></tr>
    <tr><td>Guard</td><td>Skips silently if IDF index hasn't been built yet</td></tr>
  </table>
  <div class="info-box">The queue worker must be running for uploads to become searchable: <code>php artisan queue:listen</code>. In production use <code>php artisan queue:work --daemon</code> supervised by Supervisor.</div>
</div>

<!-- COMMANDS -->
<div class="doc-section" id="s-commands">
  <h1>Artisan Commands</h1>
  <p class="lead">Located in <code>app/Console/Commands/</code>. Run with <code>php artisan &lt;signature&gt;</code>.</p>
  <h2>Search commands</h2>
  <table>
    <tr><th>Signature</th><th>What it does</th></tr>
    <tr><td><code>search:reindex</code></td><td>Queues <code>ExtractDocumentContent</code> for every non-deleted document. Use after adding new content-extraction support or when content is missing.</td></tr>
    <tr><td><code>search:build-tfidf</code></td><td>2-pass in-process build: Pass 1 computes document frequencies → saves IDF JSON. Pass 2 computes TF-IDF vectors → stores in <code>resource_embeddings</code>.</td></tr>
  </table>
  <h2>Maintenance commands</h2>
  <table>
    <tr><th>Signature</th><th>Options</th><th>What it does</th></tr>
    <tr><td><code>dms:prune-trash</code></td><td><code>--dry-run</code></td><td>Permanently deletes documents soft-deleted longer than <code>TRASH_RETENTION_DAYS</code>. Also deletes file from disk.</td></tr>
    <tr><td><code>dms:prune-shares</code></td><td><code>--dry-run</code></td><td>Deletes share tokens expired more than 7 days ago.</td></tr>
    <tr><td><code>dms:prune-tokens</code></td><td>—</td><td>Deletes expired Sanctum personal access tokens.</td></tr>
    <tr><td><code>dms:archive-expired</code></td><td><code>--dry-run</code></td><td>Changes status of documents with <code>expires_at &lt; now()</code> to <code>'archived'</code>. Writes AuditLog entry.</td></tr>
  </table>
</div>

<!-- MIDDLEWARE -->
<div class="doc-section" id="s-middleware">
  <h1>Middleware</h1>
  <p class="lead">Located in <code>app/Http/Middleware/</code>. Applied in route groups or globally.</p>
  <table>
    <tr><th>Class</th><th>Alias</th><th>Behaviour</th></tr>
    <tr><td><strong>RequireRole</strong></td><td><code>role</code></td><td>Accepts comma-separated role names: <code>role:admin,editor</code>. Aborts 403 if user's role is not in the list.</td></tr>
    <tr><td><strong>RequireActiveAccount</strong></td><td><code>active</code></td><td>Checks <code>user->status</code>. Pending → "waiting for activation" page; inactive → 403. Also touches <code>last_seen_at</code> (throttled to once per minute) as fallback "last seen" for chat.</td></tr>
    <tr><td><strong>SecurityHeaders</strong></td><td><em>global</em></td><td>Adds X-Frame-Options, X-Content-Type-Options, Referrer-Policy etc. on every response.</td></tr>
  </table>
  <h2>How middleware is applied</h2>
  <pre><code>// Client pages
Route::middleware(['auth', 'active'])->group(function () { ... });

// Admin area
Route::middleware(['auth', 'active', 'role:admin,editor'])->prefix('admin')->group(function () { ... });

// REST API
Route::middleware(['auth:sanctum', 'active'])->group(function () { ... });</code></pre>
</div>

<!-- FEATURE: DOCUMENTS -->
<div class="doc-section" id="s-feat-documents">
  <h1>Document Management</h1>
  <p class="lead">The core feature. Documents (called <em>Resources</em> in the codebase) move through a lifecycle from upload to archive.</p>
  <h2>Status lifecycle</h2>
  <pre><code>draft → pending → published → archived
                ↓
             rejected</code></pre>
  <table>
    <tr><th>Status</th><th>Visible to users</th><th>How to reach</th></tr>
    <tr><td><span class="badge badge-gray">draft</span></td><td>No</td><td>Default on upload</td></tr>
    <tr><td><span class="badge badge-yellow">pending</span></td><td>No</td><td>Submitted for review</td></tr>
    <tr><td><span class="badge badge-green">published</span></td><td>Yes</td><td>Admin approves</td></tr>
    <tr><td><span class="badge badge-orange">archived</span></td><td>No</td><td>Admin clicks Archive, or <code>expires_at</code> passes. Unarchive reverts to draft.</td></tr>
    <tr><td><span class="badge badge-red">rejected</span></td><td>No</td><td>Admin rejects</td></tr>
  </table>
  <h2>Lock feature</h2>
  <p>When a document is locked (<code>locked_by</code> set): download returns error flash, stream returns 403, preview returns 403. The detail page shows a yellow "Document locked" banner and the download button is replaced with a greyed-out "Locked" badge.</p>
  <h2>Soft delete &amp; trash</h2>
  <p>Deleting performs a soft delete (<code>deleted_at</code> timestamp). The file remains in storage and can be restored. Permanent deletion removes the DB record and file from disk. <code>dms:prune-trash</code> auto-force-deletes documents soft-deleted longer than <code>TRASH_RETENTION_DAYS</code>.</p>
  <h2>Supported file types for preview</h2>
  <table>
    <tr><th>MIME type</th><th>Preview method</th></tr>
    <tr><td><code>application/pdf</code></td><td><code>&lt;iframe&gt;</code> pointing to <code>/documents/{id}/stream</code></td></tr>
    <tr><td><code>image/*</code></td><td><code>&lt;img&gt;</code> pointing to <code>/documents/{id}/stream</code></td></tr>
    <tr><td>All other types</td><td>No preview — download only</td></tr>
  </table>
</div>

<!-- FEATURE: SEARCH -->
<div class="doc-section" id="s-feat-search">
  <h1>Search System</h1>
  <p class="lead">Two search modes: fast keyword search via MySQL FULLTEXT, and semantic TF-IDF similarity search.</p>
  <h2>Keyword search</h2>
  <ul>
    <li>Uses MySQL <code>MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE)</code></li>
    <li>Fast, exact, supports boolean operators (<code>+word -word "phrase"</code>)</li>
    <li>Works out of the box with no pre-processing</li>
  </ul>
  <h2>TF-IDF semantic search ("AI Search")</h2>
  <ol>
    <li><strong>Build phase</strong> — <code>search:build-tfidf</code> runs two passes: compute DF for every term → build IDF dictionary → compute TF-IDF vector per document → store in <code>resource_embeddings</code></li>
    <li><strong>Query phase</strong> — Tokenize and vectorize the query using the same IDF dictionary → compute cosine similarity against each stored embedding → return top-N matches</li>
  </ol>
  <h2>Content extraction coverage</h2>
  <table>
    <tr><th>Format</th><th>Extractor</th><th>Fallback</th></tr>
    <tr><td>PDF</td><td>smalot/pdfparser</td><td>Title + description only</td></tr>
    <tr><td>DOCX</td><td>phpoffice/phpword</td><td>Title + description only</td></tr>
    <tr><td>XLSX / XLS</td><td>phpoffice/phpspreadsheet</td><td>Title + description only</td></tr>
    <tr><td>PPTX</td><td>phpoffice/phppresentation</td><td>Title + description only</td></tr>
    <tr><td>TXT / MD</td><td>PHP file_get_contents</td><td>—</td></tr>
    <tr><td>Images / others</td><td>No extraction</td><td>Title + description only</td></tr>
  </table>
</div>

<!-- FEATURE: USERS -->
<div class="doc-section" id="s-feat-users">
  <h1>User System &amp; Roles</h1>
  <p class="lead">Three roles, account approval workflow, storage quotas, and brute-force lockout protection.</p>
  <h2>Roles</h2>
  <table>
    <tr><th>Role</th><th>Permissions</th></tr>
    <tr><td><span class="badge badge-red">admin</span></td><td>Full access — all admin panel pages, user management, settings, account request approvals</td></tr>
    <tr><td><span class="badge badge-blue">editor</span></td><td>Admin panel access for documents, categories, tags — cannot manage users or settings</td></tr>
    <tr><td><span class="badge badge-green">viewer</span></td><td>Read-only — browse, search, download published documents; manage own favorites/lists/bookmarks</td></tr>
  </table>
  <h2>Account statuses</h2>
  <table>
    <tr><th>Status</th><th>Can log in?</th><th>How to set</th></tr>
    <tr><td><span class="badge badge-yellow">pending</span></td><td>No — redirected to "waiting" page</td><td>Default after self-registration</td></tr>
    <tr><td><span class="badge badge-green">active</span></td><td>Yes</td><td>Admin activates in user list</td></tr>
    <tr><td><span class="badge badge-gray">inactive</span></td><td>No — 403</td><td>Admin deactivates</td></tr>
  </table>
  <h2>Storage quotas</h2>
  <p><code>User::storageUsedBytes()</code> sums <code>file_size</code> for all documents uploaded by the user. <code>User::wouldExceedQuota($newBytes)</code> is checked before accepting a new upload.</p>
  <h2>Account requests</h2>
  <p>Users can request a username change or account deletion via the profile page. These create <code>AccountRequest</code> records with <code>status='pending'</code>. Admins review them at <code>/admin/account-requests</code>.</p>
</div>

<!-- FEATURE: UPLOAD -->
<div class="doc-section" id="s-feat-upload">
  <h1>File Upload</h1>
  <p class="lead">Two upload paths: standard single-request upload and chunked upload for large files.</p>
  <h2>Standard upload</h2>
  <ol>
    <li>Validate (file required, MIME type, max size)</li>
    <li>Generate UUID filename and store at <code>storage/app/private/resources/{uuid}.ext</code></li>
    <li>Create <code>Resource</code> record with <code>file_hash</code> (MD5) for deduplication</li>
    <li>Dispatch <code>ExtractDocumentContent</code> job to queue</li>
  </ol>
  <h2>Chunked upload</h2>
  <ol>
    <li><code>POST /admin/documents/upload/chunk</code> — receives each binary slice, stores in <code>storage/app/chunks/{upload_id}/</code></li>
    <li><code>POST /admin/documents/upload/assemble</code> — concatenates all chunks, verifies final hash, moves to <code>resources/{uuid}.ext</code>, creates Resource record, dispatches extraction job</li>
  </ol>
</div>

<!-- FEATURE: SHARING -->
<div class="doc-section" id="s-feat-sharing">
  <h1>Sharing</h1>
  <p class="lead">Time-limited public share links that don't require the recipient to log in.</p>
  <h2>How it works</h2>
  <ol>
    <li>User clicks "Generate Share Link" on a document's detail page</li>
    <li><code>DocumentController@share</code> creates a <code>Share</code> record with a 40-char random token and <code>expires_at = now() + SHARE_LINK_EXPIRY_HOURS</code></li>
    <li>The URL <code>/share/{token}</code> is shown to the user</li>
    <li><code>ShareController@show</code> validates <code>Share::isValid()</code> and shows the document read-only</li>
  </ol>
  <h2>Share validation</h2>
  <pre><code>public function isValid(): bool
{
    return is_null($this->revoked_at) && $this->expires_at->isFuture();
}</code></pre>
  <p>The scheduled command <code>dms:prune-shares</code> (daily at 02:15) deletes Share records that expired more than 7 days ago.</p>
</div>

<!-- FEATURE: NOTIFICATIONS -->
<div class="doc-section" id="s-feat-notifications">
  <h1>Notifications</h1>
  <p class="lead">In-app notification system — no email required.</p>
  <h2>Creating notifications</h2>
  <pre><code>Notification::send($userId, 'info', 'Title', 'Message text here.');</code></pre>
  <h2>Broadcasting (admin)</h2>
  <p>The admin notification panel includes a broadcast form that creates a Notification record for every active user simultaneously.</p>
  <h2>User interface</h2>
  <ul>
    <li>Unread count badge appears in the nav bar (<code>$navUnread</code> pre-computed in the layout)</li>
    <li>Individual mark-read and delete actions per notification</li>
    <li><strong>Mark all as read</strong> — bulk action shown only when at least one unread notification exists</li>
  </ul>
</div>

<!-- FEATURE: PROBLEMS -->
<div class="doc-section" id="s-feat-problems">
  <h1>Problems (Programming Challenges)</h1>
  <p class="lead">Browse, create, edit, and delete programming problems with runnable JavaScript solution code.</p>
  <h2>Data model</h2>
  <table>
    <tr><th>Field</th><th>Type</th><th>Notes</th></tr>
    <tr><td><code>title</code></td><td>string</td><td>Problem title</td></tr>
    <tr><td><code>description</code></td><td>text</td><td>Problem statement</td></tr>
    <tr><td><code>difficulty</code></td><td>enum</td><td><code>easy</code> / <code>medium</code> / <code>hard</code></td></tr>
    <tr><td><code>category</code></td><td>string</td><td>e.g. JavaScript, Math, Algorithms, AI</td></tr>
    <tr><td><code>solution_code</code></td><td>text</td><td>JavaScript code — runs in a sandboxed <code>&lt;iframe&gt;</code></td></tr>
    <tr><td><code>order_index</code></td><td>integer</td><td>Sort order in the left panel list</td></tr>
  </table>
  <h2>Explorer interface</h2>
  <p>Split-panel layout: left panel lists problems with search and difficulty/category filters; right panel shows the selected problem's description, syntax-highlighted code (highlight.js), and a sandboxed Run button that executes the code and streams <code>console.log</code> / <code>console.error</code> output.</p>
  <h2>Seeding problems</h2>
  <pre><code>php artisan db:seed --class=JavaScriptSeeder
php artisan db:seed --class=MathSeeder
php artisan db:seed --class=AlgorithmsSeeder
php artisan db:seed --class=AISeeder</code></pre>
</div>

<!-- FEATURE: DEVELOPER TESTS -->
<div class="doc-section" id="s-feat-tests">
  <h1>Developer Tests</h1>
  <p class="lead">Turns the problem bank into a timed coding assessment that an external candidate can take with no DocManager account, via a single invite link.</p>
  <h2>Building a test</h2>
  <p>An admin/editor picks any mix of existing bank problems (via an "Add Existing Problem" search modal) and/or types brand-new problems inline. Each inline problem becomes a real <code>Problem</code> row (category defaults to "Custom"). A test has a title, optional description, a time limit in minutes, and a status: <code>draft</code>, <code>active</code>, or <code>archived</code>.</p>
  <h2>Taking the test</h2>
  <p>The candidate sees a start screen (title, description, problem count, time limit), then a single page with all problems and a countdown timer that auto-submits when it hits zero. If the candidate abandons the tab, the server notices the timer has expired on next load and force-finalizes the submission.</p>
  <h2>Grading</h2>
  <p>The grading screen shows the candidate's submitted code next to the problem's reference <code>solution_code</code>, with a score input (clamped server-side to the problem's point value) and a free-text feedback field. Saving computes <code>total_score / max_score</code> and flips the invite to <code>graded</code>.</p>
  <h2>Data model</h2>
  <table>
    <tr><th>Table</th><th>Purpose</th></tr>
    <tr><td><code>tests</code></td><td>Title, description, time_limit_minutes, status</td></tr>
    <tr><td><code>test_problems</code></td><td>Pivot: which problems belong to a test, in what order, worth how many points</td></tr>
    <tr><td><code>test_invites</code></td><td>One row per candidate — token, status, timing, computed score</td></tr>
    <tr><td><code>test_answers</code></td><td>One row per problem per invite — submitted code, score, feedback</td></tr>
  </table>
</div>

<!-- FEATURE: CHAT -->
<div class="doc-section" id="s-feat-chat">
  <h1>Chat &amp; Messaging</h1>
  <p class="lead">Real-time 1:1 messaging built on <strong>Laravel Reverb</strong> — a self-hosted WebSocket server with no third-party broadcaster.</p>
  <h2>Real-time pipeline</h2>
  <table>
    <tr><th>Event</th><th>Broadcast on</th><th>Triggers when</th></tr>
    <tr><td><code>MessageSent</code></td><td><code>private-conversation.{id}</code> + <code>private-App.Models.User.{id}</code></td><td>A message is created — the second channel lets the nav badge update even if that conversation isn't open</td></tr>
    <tr><td><code>MessagesDelivered</code></td><td><code>private-conversation.{id}</code></td><td>The recipient's client fetches the thread and pending messages get a <code>delivered_at</code> timestamp</td></tr>
    <tr><td><code>MessagesRead</code></td><td><code>private-conversation.{id}</code></td><td>The recipient opens the thread — upserts their <code>conversation_reads.last_read_at</code> pointer</td></tr>
  </table>
  <p>All three use <code>ShouldBroadcastNow</code> (synchronous, no queue worker needed for chat) and are wrapped in try/catch — if Reverb is briefly down, the underlying save still succeeds.</p>
  <h2>Read &amp; delivered indicators</h2>
  <p>Status ticks are <em>derived</em>, not stored per message: a sent message shows "read" if its timestamp is at or before the other participant's <code>last_read_at</code>, "delivered" if <code>delivered_at</code> is set, otherwise "sent". Marking an entire thread read is one upsert on <code>conversation_reads</code>, not N message updates.</p>
  <h2>Data model</h2>
  <table>
    <tr><th>Table</th><th>Purpose</th></tr>
    <tr><td><code>conversations</code></td><td>Normalized 1:1 pair (<code>user_one_id</code> &lt; <code>user_two_id</code>, unique) + <code>last_message_at</code></td></tr>
    <tr><td><code>messages</code></td><td>conversation_id, sender_id, body, delivered_at</td></tr>
    <tr><td><code>conversation_reads</code></td><td>One row per participant per conversation — their read pointer</td></tr>
  </table>
</div>

<!-- FEATURE: API -->
<div class="doc-section" id="s-feat-api">
  <h1>REST API</h1>
  <p class="lead">Token-based JSON API for programmatic access. Uses Laravel Sanctum for authentication.</p>
  <h2>Authentication flow</h2>
  <pre><code># 1. Get a token
POST /api/auth/login
Content-Type: application/json
{ "email": "user@example.com", "password": "secret" }

# Response: { "token": "1|abc123...", "user": { "id": 1, "name": "..." } }

# 2. Use the token
GET /api/resources
Authorization: Bearer 1|abc123...</code></pre>
  <h2>Rate limiting</h2>
  <table>
    <tr><th>Rate limiter</th><th>Applied to</th><th>Limit</th></tr>
    <tr><td><code>api-login</code></td><td><code>POST /api/auth/login</code></td><td>5 attempts / minute per IP</td></tr>
    <tr><td><code>api-download</code></td><td><code>GET /api/resources/{id}/download</code></td><td>10 requests / minute per user</td></tr>
  </table>
  <h2>Response format</h2>
  <p>All resource responses use API Resource transformers that expose <code>id</code>, <code>title</code>, <code>description</code>, <code>file_type</code>, <code>file_size</code>, <code>status</code>, <code>download_count</code>, <code>category</code>, <code>tags</code> — and omit sensitive fields (<code>file_path</code>, <code>file_hash</code>).</p>
</div>

<!-- FEATURE: SCHEDULE -->
<div class="doc-section" id="s-feat-schedule">
  <h1>Scheduled Tasks</h1>
  <p class="lead">Defined in <code>routes/console.php</code>. Requires the Laravel scheduler running via cron: <code>* * * * * php /path/to/artisan schedule:run</code></p>
  <table>
    <tr><th>Command</th><th>Schedule</th><th>What it cleans up</th></tr>
    <tr><td><code>dms:prune-trash</code></td><td>Daily 02:00</td><td>Permanently deletes soft-deleted documents older than <code>TRASH_RETENTION_DAYS</code></td></tr>
    <tr><td><code>dms:prune-shares</code></td><td>Daily 02:15</td><td>Deletes share tokens expired &gt;7 days ago</td></tr>
    <tr><td><code>dms:prune-tokens</code></td><td>Daily 02:30</td><td>Deletes expired Sanctum personal access tokens</td></tr>
    <tr><td><code>dms:archive-expired</code></td><td>Hourly</td><td>Sets status → <code>archived</code> for documents whose <code>expires_at</code> has passed</td></tr>
    <tr><td><code>queue:prune-batches --hours=48</code></td><td>Weekly</td><td>Removes old batch records from <code>job_batches</code></td></tr>
  </table>
  <h2>Development tip</h2>
  <pre><code># Test a command now without waiting for its schedule
php artisan dms:archive-expired --dry-run
php artisan dms:prune-trash --dry-run</code></pre>
</div>

<!-- FEATURE: HR -->
<div class="doc-section" id="s-feat-hr">
  <h1>HR &amp; People Management</h1>
  <p class="lead">Manage employee profiles, org structure, and HR documents. The hierarchy drives the Work Reports team tab and plan assignment.</p>
  <h2>Org structure</h2>
  <pre><code>Employee::manager()       → BelongsTo Employee (direct manager)
Employee::subordinates()  → HasMany Employee (direct reports)</code></pre>
  <p>This hierarchy is used by the Work Reports Team tab (managers see their direct reports' submissions) and by work report review authorization.</p>
  <h2>Employee codes</h2>
  <p><code>Employee::nextCode()</code> generates sequential codes in the format <code>EMP-00001</code>. It queries <code>MAX(CAST(SUBSTRING(employee_code,5) AS UNSIGNED))</code> with <code>withTrashed()</code> so codes are never reused after soft-deletion.</p>
  <h2>Employee profile tabs (admin)</h2>
  <table>
    <tr><th>Tab</th><th>Content</th></tr>
    <tr><td>Details</td><td>Personal info, employment info, emergency contact</td></tr>
    <tr><td>Documents</td><td>Upload and list employee documents (contracts, certificates)</td></tr>
    <tr><td>Attendance</td><td>Recent attendance records and leave requests</td></tr>
    <tr><td>Work Reports</td><td>All work reports submitted by this employee</td></tr>
    <tr><td>Plans</td><td>Plans this employee is assigned to</td></tr>
  </table>
</div>

<!-- FEATURE: ATTENDANCE -->
<div class="doc-section" id="s-feat-attendance">
  <h1>Attendance &amp; Leave</h1>
  <p class="lead">Employees record their own attendance; admins manage and report on it. Leave requests go through an approval workflow.</p>
  <h2>Attendance flow (client)</h2>
  <ol>
    <li>Employee visits <code>/attendance</code> — sees today's record and monthly calendar</li>
    <li>Clicks Check In → stamps <code>check_in</code> on today's record</li>
    <li>Clicks Check Out → stamps <code>check_out</code>, calculates <code>work_hours</code></li>
  </ol>
  <p>One record per employee per date. Status values: <code>present</code>, <code>absent</code>, <code>half_day</code>, <code>leave</code>, <code>holiday</code>.</p>
  <h2>Leave request flow</h2>
  <pre><code>Employee submits → status: pending
Admin approves   → status: approved
Admin rejects    → status: rejected (with optional note)
Employee cancels → destroy() (pending only)</code></pre>
  <h2>Admin capabilities</h2>
  <ul>
    <li>View and edit any employee's attendance records</li>
    <li>Bulk mark status (e.g. mark all absent for a date)</li>
    <li>Generate attendance reports by date range and department</li>
    <li>Export to CSV</li>
    <li>Approve or reject leave requests with a note</li>
  </ul>
</div>

<!-- FEATURE: WORK REPORTS -->
<div class="doc-section" id="s-feat-work-reports">
  <h1>Work Reports</h1>
  <p class="lead">Structured reporting with a manager review workflow. Employees submit daily, weekly, or monthly reports; managers review and respond.</p>
  <h2>Report lifecycle</h2>
  <pre><code>draft → submitted → under_review → approved
                                  → rejected  (employee can resubmit)</code></pre>
  <table>
    <tr><th>Status</th><th>Who sets it</th><th>Notes</th></tr>
    <tr><td><span class="badge badge-gray">draft</span></td><td>Default on create</td><td>Editable; not visible to manager</td></tr>
    <tr><td><span class="badge badge-blue">submitted</span></td><td>Employee</td><td>Locked for editing; notifies manager</td></tr>
    <tr><td><span class="badge badge-yellow">under_review</span></td><td>Manager</td><td>Signals review in progress</td></tr>
    <tr><td><span class="badge badge-green">approved</span></td><td>Manager</td><td>Final — notifies employee</td></tr>
    <tr><td><span class="badge badge-red">rejected</span></td><td>Manager</td><td>Employee can edit and resubmit</td></tr>
  </table>
  <h2>Comment types</h2>
  <table>
    <tr><th>Type</th><th>Who can set it</th><th>Meaning</th></tr>
    <tr><td><code>comment</code></td><td>Anyone</td><td>General discussion</td></tr>
    <tr><td><code>feedback</code></td><td>Reviewer only</td><td>Reviewer feedback</td></tr>
    <tr><td><code>revision_request</code></td><td>Reviewer only</td><td>Explicit request for changes</td></tr>
  </table>
  <h2>Notifications sent</h2>
  <table>
    <tr><th>Event</th><th>Sent to</th></tr>
    <tr><td>Report submitted</td><td>Employee's manager</td></tr>
    <tr><td>Report approved / rejected</td><td>Employee</td></tr>
    <tr><td>Comment posted</td><td>Other party</td></tr>
  </table>
</div>

<!-- FEATURE: PLANS -->
<div class="doc-section" id="s-feat-plans">
  <h1>Plans</h1>
  <p class="lead">Full-lifecycle plan management with task tracking, employee assignment, progress calculation, and a client-side view for assigned employees.</p>
  <h2>Plan lifecycle</h2>
  <pre><code>draft → pending → in_progress → completed
                → on_hold    → in_progress (resumable)
                → cancelled
                → archived</code></pre>
  <h2>Plan categories</h2>
  <p><code>daily</code>, <code>weekly</code>, <code>monthly</code>, <code>quarterly</code>, <code>annual</code>, <code>personal</code>, <code>team</code>, <code>project</code>, <code>strategic</code></p>
  <h2>Progress tracking</h2>
  <p><code>Plan::updateProgress()</code> recalculates <code>progress</code> as <code>floor(completed_tasks / total_tasks * 100)</code>. Called automatically by <code>PlanTaskController@toggle</code> whenever a task is checked or unchecked.</p>
  <h2>Employee assignment</h2>
  <p>Plans use a <code>plan_employees</code> pivot table (BelongsToMany). On create/update: <code>$plan->employees()->sync($employeeIds)</code>.</p>
  <h2>Duplicate action</h2>
  <p><code>PlanController@duplicate</code> creates a copy with a new <code>plan_number</code>, status reset to <code>draft</code>, and all tasks copied (statuses reset to <code>pending</code>). Employee assignments are <em>not</em> copied — the duplicate starts unassigned.</p>
  <h2>Client view (assigned employees)</h2>
  <ul>
    <li><strong>index</strong> — shows only plans where the authenticated user's employee is assigned</li>
    <li><strong>show</strong> — aborts 403 unless assigned, or the user is admin/editor</li>
    <li><strong>storeComment</strong> — same assignment check before allowing a comment</li>
  </ul>
  <h2>Notifications sent</h2>
  <table>
    <tr><th>Event</th><th>Sent to</th></tr>
    <tr><td>Plan created (with assigned employees)</td><td>Each assigned employee's user account</td></tr>
  </table>
</div>
@endverbatim
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function docNav(id) {
    document.querySelectorAll('.doc-section').forEach(function(s) {
        s.classList.remove('active');
    });
    var sec = document.getElementById('s-' + id);
    if (sec) {
        sec.classList.add('active');
        document.getElementById('doc-pane').scrollTop = 0;
    }
    setActive(id);
}

function setActive(sectionId) {
    document.querySelectorAll('.doc-nav-item').forEach(function(el) {
        var isActive = el.dataset.section === sectionId;
        el.classList.toggle('bg-blue-600',       isActive);
        el.classList.toggle('text-white',        isActive);
        el.classList.toggle('font-medium',       isActive);
        el.classList.toggle('text-gray-600',    !isActive);
        el.classList.toggle('hover:bg-gray-200', !isActive);
        el.classList.toggle('hover:text-gray-900',!isActive);
    });
}

document.getElementById('doc-search').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    document.querySelectorAll('.doc-nav-group').forEach(function(group) {
        var visible = 0;
        group.querySelectorAll('.doc-nav-item').forEach(function(item) {
            var match = !q || item.textContent.toLowerCase().includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        group.style.display = visible ? '' : 'none';
    });
});

docNav('overview');
</script>
@endpush
