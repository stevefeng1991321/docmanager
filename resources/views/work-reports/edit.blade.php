@extends('layouts.app')
@section('title', 'Edit Work Report')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Edit Report</h1>
        <a href="{{ route('work-reports.show', $workReport) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Report</a>
    </div>

    @if($workReport->isRejected())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg px-4 py-3">
            This report was rejected. Make your changes and re-submit it for review.
        </div>
    @endif

    <form method="POST" action="{{ route('work-reports.update', $workReport) }}">
        @csrf @method('PUT')
        @include('work-reports._form', ['workReport' => $workReport])
    </form>
</div>
@endsection
