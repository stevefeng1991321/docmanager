@extends('layouts.admin')
@section('title', 'Edit Employee')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Edit {{ $employee->full_name }}</h1>
        <a href="{{ route('admin.employees.show', $employee) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Profile</a>
    </div>

    <form method="POST" action="{{ route('admin.employees.update', $employee) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        @include('admin.employees._form', ['employee' => $employee])

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Save Changes
            </button>
            <a href="{{ route('admin.employees.show', $employee) }}" class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
