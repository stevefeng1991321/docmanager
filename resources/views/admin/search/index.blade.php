@extends('layouts.admin')
@section('title', __('admin.search.heading'))

@section('content')
<div class="space-y-5">

    {{-- Action buttons --}}
    <div class="flex items-center justify-end gap-3 flex-wrap">
        <form method="POST" action="{{ route('admin.search.build-tfidf') }}">
            @csrf
            <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                <span>✦</span>
                {{ __('admin.search.rebuild_index') }}
            </button>
        </form>
        <form method="POST" action="{{ route('admin.search.reindex') }}">
            @csrf
            <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                {{ __('admin.search.index_document') }}
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
                @if($tfidfReady && $tfidfIndexed > 0)
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    <span class="text-green-700 font-medium">{{ __('admin.search.index_status') }}</span>
                @elseif($tfidfReady && $tfidfIndexed === 0)
                    <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                    <span class="text-yellow-600 font-medium">{{ __('admin.search.index_status') }}</span>
                @else
                    <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                    <span class="text-gray-500">{{ __('admin.search.index_status') }}</span>
                @endif
            </div>
            <div class="text-gray-600">
                <strong>{{ number_format($tfidfIndexed) }}</strong> {{ __('admin.search.total_indexed') }}
            </div>
        </div>
        @if(!$tfidfReady || $tfidfIndexed === 0)
        <p class="text-xs text-gray-400 mt-2">
            Click "Build AI Index" to generate TF-IDF vectors for all documents.
            Make sure documents have content extracted first ("Re-index All Documents"), then build the AI index.
        </p>
        @else
        <p class="text-xs text-gray-400 mt-2">
            Rebuild after uploading many new documents to improve AI search accuracy.
            New documents are indexed automatically after content extraction.
        </p>
        @endif
    </div>

    {{-- Two-column grid for query tables --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- Top search queries --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">{{ __('admin.search.test_search') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Most frequent searches (all results)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('common.number') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.search.test_search') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('common.count') }}</th>
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
                        <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">{{ __('admin.search.no_results') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Zero-result queries --}}
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-amber-100 bg-amber-50">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <h3 class="font-semibold text-gray-800">{{ __('admin.search.test_results') }}</h3>
                </div>
                <p class="text-xs text-gray-500 mt-0.5 ml-6">Searches that returned no documents — content gaps to address</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-amber-50/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('common.number') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.search.test_search') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('common.count') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($zeroResultQueries as $i => $row)
                        <tr class="hover:bg-amber-50/30">
                            <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800">{{ $row->query }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-amber-700 font-semibold">
                                    {{ $row->count }}
                                    @if($row->count >= 3)
                                    <span class="text-xs font-normal text-amber-500">● high priority</span>
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center">
                                <p class="text-green-600 font-medium text-sm">All searches return results!</p>
                                <p class="text-gray-400 text-xs mt-1">No content gaps detected.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($zeroResultQueries->isNotEmpty())
            <div class="px-5 py-3 bg-amber-50/30 border-t border-amber-100 text-xs text-gray-500">
                Consider uploading documents that cover these topics to improve search coverage.
            </div>
            @endif
        </div>

    </div>{{-- /grid --}}

</div>
@endsection
