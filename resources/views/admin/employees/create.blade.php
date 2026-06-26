@extends('layouts.admin')
@section('title', 'New Employee')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">New Employee</h1>
        <a href="{{ route('admin.employees.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Employees</a>
    </div>

    <form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        @include('admin.employees._form', ['employee' => null])

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Create Employee
            </button>
            <a href="{{ route('admin.employees.index') }}" class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
