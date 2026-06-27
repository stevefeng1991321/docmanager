@extends('layouts.admin')
@section('title', 'Alpine.js Documentation')

@section('content')
<div class="-m-4 sm:-m-6" style="height:calc(100vh - 3.5rem)">

    @if($available)

        <iframe src="{{ route('admin.help.alpine.doc') }}"
                class="w-full h-full border-0"
                title="Alpine.js Offline Documentation">
        </iframe>

    @else

        <div class="flex items-center justify-center h-full bg-gray-50">
            <div class="max-w-lg text-center px-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Offline Alpine.js docs not found</h2>
                <p class="text-sm text-gray-500 mb-6">
                    The file <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">documentation/alpine/index.html</code>
                    does not exist yet. Run the two commands below to generate it, then reload this page.
                </p>
                <div class="text-left bg-gray-900 text-green-300 rounded-xl px-5 py-4 font-mono text-sm space-y-1 mb-6">
                    <p><span class="text-gray-500"># 1. Download Alpine.js markdown files from GitHub</span></p>
                    <p>php artisan docs:import-alpine --branch=main</p>
                    <p class="pt-1"><span class="text-gray-500"># 2. Build the self-contained HTML file</span></p>
                    <p>php artisan docs:build-alpine-offline --branch=main</p>
                </div>
                <a href="https://alpinejs.dev" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:underline">
                    Open online docs instead
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>

    @endif

</div>
@endsection
