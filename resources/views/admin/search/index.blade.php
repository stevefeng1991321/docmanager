@extends('layouts.admin')
@section('title', 'Search Analytics')

@section('content')
<div class="space-y-5">

    {{-- Action buttons --}}
    <div class="flex items-center justify-end gap-3 flex-wrap">
        <form method="POST" action="{{ route('admin.search.build-tfidf') }}">
            @csrf
            <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                <span>✦</span>
                Build AI Index
            </button>
        </form>
        <form method="POST" action="{{ route('admin.search.reindex') }}">
            @csrf
            <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                Re-index All Documents
            </button>
        </form>
    </div>

    {{-- AI Index status --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <span>✦</span> AI Search Index (TF-IDF)
        </h3>
        <div class="flex items-center gap-6 text-sm">
            <div class="flex items-center gap-2">
                @if($tfidfReady)
                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                <span class="text-green-700 font-medium">Index ready</span>
                @else
                <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                <span class="text-gray-500">Not built yet</span>
                @endif
            </div>
            <div class="text-gray-600">
                <strong>{{ number_format($tfidfIndexed) }}</strong> document{{ $tfidfIndexed !== 1 ? 's' : '' }} indexed
            </div>
        </div>
        @if(!$tfidfReady)
        <p class="text-xs text-gray-400 mt-2">
            Click "Build AI Index" to generate TF-IDF vectors for all documents,
            or run <code class="bg-gray-100 px-1 rounded font-mono">php artisan search:build-tfidf</code> from the terminal.
        </p>
        @else
        <p class="text-xs text-gray-400 mt-2">
            Rebuild after uploading many new documents to improve AI search accuracy.
            New documents are indexed automatically after content extraction.
        </p>
        @endif
    </div>

    {{-- Top search queries --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Top Search Queries</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Query</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Count</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($topQueries as $i => $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $row->query }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $row->count }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">No searches recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
