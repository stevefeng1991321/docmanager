@extends('layouts.admin')
@section('title', 'Search Analytics')

@section('content')
<div class="space-y-5">

    <div class="flex justify-end">
        <form method="POST" action="{{ route('admin.search.reindex') }}">
            @csrf
            <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                Re-index All Documents
            </button>
        </form>
    </div>

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
