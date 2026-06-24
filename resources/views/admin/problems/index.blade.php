@extends('layouts.admin')
@section('title', 'Programming Problems')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
@endpush

@section('content')
<div x-data="problemExplorer()" class="flex gap-0 -m-4 sm:-m-6" style="height: calc(100vh - 4rem);">

    {{-- ── Left Panel: Problem List ─────────────────────────────────────────── --}}
    <div class="w-72 xl:w-80 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col overflow-hidden">

        {{-- Search + Filter --}}
        <div class="p-3 border-b border-gray-100 space-y-2">
            <input
                type="text"
                x-model="search"
                placeholder="Search problems…"
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            {{-- Difficulty filter --}}
            <select
                x-model="filter"
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            >
                <option value="">All Difficulties</option>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
            </select>
            {{-- Category filter --}}
            <select
                x-model="categoryFilter"
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            >
                <option value="">All Categories</option>
                <option value="JavaScript">JavaScript</option>
                <option value="Math">Math</option>
                <option value="Algorithms">Algorithms</option>
                <option value="AI">AI</option>
            </select>
        </div>

        {{-- Count --}}
        <div class="px-3 py-1.5 text-xs text-gray-400 border-b border-gray-50">
            <span x-text="filteredProblems.length"></span> problems
        </div>

        {{-- Problem list --}}
        <div class="flex-1 overflow-y-auto">
            <template x-for="p in filteredProblems" :key="p.id">
                <button
                    @click="select(p)"
                    :class="selected && selected.id === p.id
                        ? 'bg-blue-50 border-l-4 border-blue-500'
                        : 'border-l-4 border-transparent hover:bg-gray-50'"
                    class="w-full text-left px-3 py-2.5 flex items-start gap-2.5 transition">
                    <span class="text-xs text-gray-400 w-6 flex-shrink-0 mt-0.5 font-mono" x-text="p.order_index"></span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate" x-text="p.title"></div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-xs text-gray-400" x-text="p.category"></span>
                            <span
                                :class="{
                                    'bg-green-100 text-green-700': p.difficulty === 'easy',
                                    'bg-yellow-100 text-yellow-700': p.difficulty === 'medium',
                                    'bg-red-100 text-red-700': p.difficulty === 'hard',
                                }"
                                class="text-xs px-1.5 py-0.5 rounded font-medium capitalize"
                                x-text="p.difficulty">
                            </span>
                        </div>
                    </div>
                </button>
            </template>

            {{-- Empty state --}}
            <div x-show="filteredProblems.length === 0" class="px-4 py-10 text-center text-gray-400 text-sm">
                No problems match your search.
            </div>
        </div>
    </div>

    {{-- ── Right Panel: Problem Detail + Code ──────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">

        {{-- Empty state --}}
        <div x-show="!selected" class="flex-1 flex items-center justify-center">
            <div class="text-center text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                <p class="text-lg font-medium">Select a problem</p>
                <p class="text-sm mt-1">Choose a problem from the left panel to view its solution</p>
            </div>
        </div>

        {{-- Problem detail --}}
        <div x-show="selected" x-cloak class="flex-1 flex flex-col overflow-hidden">

            {{-- Loading overlay --}}
            <div x-show="loading" class="flex-1 flex items-center justify-center">
                <div class="flex items-center gap-2 text-gray-500">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-sm">Loading…</span>
                </div>
            </div>

            <div x-show="!loading && detail" x-cloak class="flex-1 flex flex-col overflow-hidden">

                {{-- Header --}}
                <div class="px-5 py-4 bg-white border-b border-gray-200 flex-shrink-0">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs text-gray-400 font-mono" x-text="'#' + detail.id"></span>
                                <span
                                    :class="{
                                        'bg-green-100 text-green-700': detail.difficulty === 'easy',
                                        'bg-yellow-100 text-yellow-700': detail.difficulty === 'medium',
                                        'bg-red-100 text-red-700': detail.difficulty === 'hard',
                                    }"
                                    class="text-xs px-2 py-0.5 rounded-full font-semibold capitalize"
                                    x-text="detail.difficulty">
                                </span>
                                <span
                                    :class="{
                                        'bg-blue-100 text-blue-700': detail.category === 'JavaScript',
                                        'bg-emerald-100 text-emerald-700': detail.category === 'Math',
                                        'bg-purple-100 text-purple-700': detail.category === 'Algorithms',
                                        'bg-orange-100 text-orange-700': detail.category === 'AI',
                                        'bg-gray-100 text-gray-700': !['JavaScript','Math','Algorithms','AI'].includes(detail.category),
                                    }"
                                    class="text-xs px-2 py-0.5 rounded-full font-medium"
                                    x-text="detail.category"></span>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900" x-text="detail.title"></h2>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed" x-text="detail.description"></p>
                </div>

                {{-- Code + Output area --}}
                <div class="flex-1 overflow-y-auto">
                    <div class="p-5 space-y-4">

                        {{-- Code viewer --}}
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2.5 bg-gray-800 border-b border-gray-700">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                    <span class="ml-2 text-xs text-gray-400 font-mono">solution.js</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="copyCode()"
                                            class="text-xs text-gray-400 hover:text-white transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                                    </button>
                                    <button @click="runCode()"
                                            :disabled="running"
                                            class="flex items-center gap-1.5 bg-green-600 hover:bg-green-700 disabled:opacity-60 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                        <svg x-show="!running" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        <svg x-show="running" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Run
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <pre class="!m-0 !rounded-none text-sm leading-relaxed" style="max-height: 420px; overflow-y: auto;"><code id="code-block" class="language-javascript !bg-white"></code></pre>
                            </div>
                        </div>

                        {{-- Output console --}}
                        <div x-show="output.length > 0 || hasError" class="bg-gray-900 rounded-xl border border-gray-700 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2 border-b border-gray-700">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Console Output</span>
                                <button @click="output = []; hasError = false"
                                        class="text-xs text-gray-500 hover:text-gray-300 transition">Clear</button>
                            </div>
                            <div class="p-4 font-mono text-sm space-y-1" style="max-height: 250px; overflow-y: auto;">
                                <template x-for="(line, i) in output" :key="i">
                                    <div :class="line.type === 'error' ? 'text-red-400' : line.type === 'warn' ? 'text-yellow-400' : 'text-white'"
                                         class="flex gap-2">
                                        <span class="text-gray-600 select-none">›</span>
                                        <span x-text="line.text" class="whitespace-pre-wrap break-all"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Idle output hint --}}
                        <div x-show="output.length === 0 && !hasError && !running" class="text-center text-xs text-gray-400 py-2">
                            Press <kbd class="bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded text-xs font-mono">Run</kbd> to execute the solution and see console output
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden sandbox iframe --}}
    <iframe id="js-sandbox" sandbox="allow-scripts" style="display:none" title="JS Sandbox"></iframe>
</div>
@endsection

@push('head')
<style>
/* Override default hljs style inside the dark toolbar container */
#code-block.hljs,
pre code.hljs { background: #fff !important; padding: 1.25rem !important; }
.hljs-keyword { color: #d73a49; }
.hljs-string  { color: #032f62; }
.hljs-number  { color: #005cc5; }
.hljs-comment { color: #6a737d; font-style: italic; }
.hljs-function .hljs-title,
.hljs-title.function_ { color: #6f42c1; }
.hljs-params  { color: #24292e; }
.hljs-built_in { color: #e36209; }
.hljs-literal  { color: #005cc5; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
function problemExplorer() {
    return {
        all: @json($problems),
        search: '',
        filter: '',
        categoryFilter: '',
        selected: null,
        detail: null,
        loading: false,
        running: false,
        output: [],
        hasError: false,
        copied: false,

        get filteredProblems() {
            const q = this.search.toLowerCase();
            return this.all.filter(p => {
                const matchDifficulty = !this.filter || p.difficulty === this.filter;
                const matchCategory   = !this.categoryFilter || p.category === this.categoryFilter;
                const matchSearch     = !q
                    || p.title.toLowerCase().includes(q)
                    || p.category.toLowerCase().includes(q);
                return matchDifficulty && matchCategory && matchSearch;
            });
        },

        async select(problem) {
            if (this.selected && this.selected.id === problem.id) return;
            this.selected = problem;
            this.detail = null;
            this.loading = true;
            this.output = [];
            this.hasError = false;

            try {
                const res = await fetch(`/admin/problems/${problem.id}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.detail = await res.json();
                this.$nextTick(() => this.highlight());
            } catch (e) {
                this.output = [{ type: 'error', text: 'Failed to load problem.' }];
            } finally {
                this.loading = false;
            }
        },

        highlight() {
            const el = document.getElementById('code-block');
            if (!el || !this.detail) return;
            el.removeAttribute('data-highlighted');
            el.textContent = this.detail.solution_code;
            hljs.highlightElement(el);
        },

        runCode() {
            if (!this.detail) return;
            this.output = [];
            this.hasError = false;
            this.running = true;

            const sandbox = document.getElementById('js-sandbox');
            const code = this.detail.solution_code;
            const alpine = this;

            // One-time message listener
            const handler = (event) => {
                const data = event.data;
                if (!data || data.__sandbox !== true) return;
                if (data.type === 'done') {
                    window.removeEventListener('message', handler);
                    alpine.running = false;
                    return;
                }
                alpine.output.push({ type: data.type, text: data.text });
            };
            window.addEventListener('message', handler);

            // Safety: remove handler after 10s
            setTimeout(() => {
                window.removeEventListener('message', handler);
                alpine.running = false;
            }, 10000);

            const html = `<!DOCTYPE html><html><body><script>
(function() {
    function fmt(args) {
        return Array.from(args).map(a => {
            try { return typeof a === 'object' ? JSON.stringify(a, null, 2) : String(a); }
            catch(e) { return String(a); }
        }).join(' ');
    }
    function post(type, text) {
        window.parent.postMessage({ __sandbox: true, type, text }, '*');
    }
    var origLog   = console.log;
    var origError = console.error;
    var origWarn  = console.warn;
    console.log   = function() { post('log',   fmt(arguments)); origLog.apply(console, arguments); };
    console.error = function() { post('error', fmt(arguments)); origError.apply(console, arguments); };
    console.warn  = function() { post('warn',  fmt(arguments)); origWarn.apply(console, arguments); };
    window.onerror = function(msg, src, line, col, err) {
        post('error', (err ? err.message : msg) + ' (line ' + line + ')');
        post('done', '');
        return true;
    };
    try {
        ${code.replace(/<\/script>/gi, '<\\/script>')}
    } catch(e) {
        post('error', e.message);
    } finally {
        post('done', '');
    }
})();
<\/script></body></html>`;

            sandbox.srcdoc = html;
        },

        copyCode() {
            if (!this.detail) return;
            navigator.clipboard.writeText(this.detail.solution_code).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        },

        init() {
            // Listen for future highlight calls after x-show transitions
            this.$watch('detail', () => {
                if (this.detail) this.$nextTick(() => this.highlight());
            });
        }
    };
}
</script>
@endpush
