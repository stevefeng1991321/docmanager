@extends('layouts.admin')
@section('title', 'Compare Documents')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-xl font-semibold text-gray-800">Compare Documents</h1>
        <p class="text-sm text-gray-500 mt-1">Select two documents to compare their metadata and content side-by-side.</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="GET" action="{{ route('admin.compare.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Document A</label>
                <select name="a" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">— Select document —</option>
                    @foreach($documents as $doc)
                    <option value="{{ $doc->id }}" @selected($selectedA === $doc->id)>
                        {{ $doc->title }} ({{ ucfirst(str_replace('_',' ',$doc->status)) }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Document B</label>
                <select name="b" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">— Select document —</option>
                    @foreach($documents as $doc)
                    <option value="{{ $doc->id }}" @selected($selectedB === $doc->id)>
                        {{ $doc->title }} ({{ ucfirst(str_replace('_',' ',$doc->status)) }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Compare
            </button>
            <span class="text-xs text-gray-400">Both documents must be different.</span>
        </div>
    </form>

</div>
@endsection
