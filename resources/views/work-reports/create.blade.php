@extends('layouts.app')
@section('title', 'New Work Report')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">New Work Report</h1>
        <a href="{{ route('work-reports.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Work Reports</a>
    </div>

    <form method="POST" action="{{ route('work-reports.store') }}">
        @csrf
        @include('work-reports._form', ['workReport' => null])
    </form>
</div>
@endsection
