@extends('layouts.admin')
@section('title', $employee->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex items-start justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            @if($employee->photoUrl())
                <img src="{{ $employee->photoUrl() }}" class="w-16 h-16 rounded-full object-cover" alt="">
            @else
                <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase text-xl">
                    {{ substr($employee->full_name, 0, 1) }}
                </div>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-gray-900">{{ $employee->full_name }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                        {{ match($employee->employment_status) {
                            'active' => 'bg-green-100 text-green-700',
                            'inactive' => 'bg-gray-100 text-gray-600',
                            'resigned' => 'bg-amber-100 text-amber-700',
                            'terminated' => 'bg-red-100 text-red-700',
                        } }}">
                        {{ $employee->employment_status }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $employee->position?->title ?? 'No position' }} · {{ $employee->department?->name ?? 'No department' }}
                </p>
                <p class="text-xs text-gray-400 font-mono mt-1">{{ $employee->employee_code }}</p>
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            @if($employee->isActive())
                <form action="{{ route('admin.employees.deactivate', $employee) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">Deactivate</button>
                </form>
            @else
                <form action="{{ route('admin.employees.activate', $employee) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="text-xs px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition">Activate</button>
                </form>
            @endif
            <a href="{{ route('admin.employees.edit', $employee) }}" class="text-xs px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Edit</a>
            <a href="{{ route('admin.employees.index') }}" class="text-xs px-3 py-1.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">← Employees</a>
        </div>
    </div>

    {{-- Info cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Personal Information</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-400">Email</dt><dd class="text-gray-700">{{ $employee->email ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Phone</dt><dd class="text-gray-700">{{ $employee->phone ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Date of Birth</dt><dd class="text-gray-700">{{ $employee->date_of_birth?->format('M j, Y') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Gender</dt><dd class="text-gray-700 capitalize">{{ $employee->gender ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Marital Status</dt><dd class="text-gray-700 capitalize">{{ $employee->marital_status ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Nationality</dt><dd class="text-gray-700">{{ $employee->nationality ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Address</dt><dd class="text-gray-700 text-right">{{ $employee->address ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Emergency Contact</dt><dd class="text-gray-700 text-right">{{ $employee->emergency_contact_name ?? '—' }} {{ $employee->emergency_contact_phone ? '('.$employee->emergency_contact_phone.')' : '' }}</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Employment Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-400">Department</dt><dd class="text-gray-700">{{ $employee->department?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Position</dt><dd class="text-gray-700">{{ $employee->position?->title ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Manager</dt><dd class="text-gray-700">{{ $employee->manager?->full_name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Date of Joining</dt><dd class="text-gray-700">{{ $employee->date_of_joining?->format('M j, Y') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Employment Type</dt><dd class="text-gray-700">{{ $employee->employment_type ? ucwords(str_replace('_',' ',$employee->employment_type)) : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Work Location</dt><dd class="text-gray-700">{{ $employee->work_location ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-400">Office Branch</dt><dd class="text-gray-700">{{ $employee->office_branch ?? '—' }}</dd></div>
                @if(auth()->user()->isAdmin())
                <div class="flex justify-between"><dt class="text-gray-400">Salary</dt><dd class="text-gray-700">{{ $employee->salary !== null ? number_format($employee->salary, 2) : '—' }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-gray-400">Linked Account</dt><dd class="text-gray-700">{{ $employee->user?->name ?? '— No login access —' }}</dd></div>
            </dl>
        </div>
    </div>

    {{-- Login Account --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Login Account</h2>

        @if(!$employee->user)
            <p class="text-sm text-gray-500">
                This employee has no login access.
                <a href="{{ route('admin.employees.edit', $employee) }}" class="text-blue-600 hover:underline">Link a user account</a> to manage one.
            </p>
        @else
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm">
                    <span class="text-gray-700 font-medium">{{ $employee->user->name }}</span>
                    <span class="text-gray-400">({{ '@' . $employee->user->username }})</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize ml-2
                        {{ $employee->user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $employee->user->status === 'active' ? 'Login enabled' : 'Login disabled' }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize bg-indigo-100 text-indigo-700 ml-1">
                        {{ $employee->user->role }}
                    </span>
                </div>
                <a href="{{ route('admin.users.show', $employee->user) }}" class="text-xs text-blue-600 hover:underline">View Account →</a>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="grid sm:grid-cols-3 gap-4 pt-3 border-t border-gray-100">
                {{-- Role --}}
                <form action="{{ route('admin.users.update', $employee->user) }}" method="POST" class="space-y-2">
                    @csrf @method('PUT')
                    <input type="hidden" name="username" value="{{ $employee->user->username }}">
                    <input type="hidden" name="name" value="{{ $employee->user->name }}">
                    <input type="hidden" name="status" value="{{ $employee->user->status }}">
                    <label class="block text-xs font-medium text-gray-600">Role</label>
                    <select name="role" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                        @foreach(['admin','editor','viewer'] as $r)
                            <option value="{{ $r }}" @selected($employee->user->role === $r)>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">Save Role</button>
                </form>

                {{-- Login access --}}
                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-600">Login Access</label>
                    @if($employee->user->status === 'active')
                        <form action="{{ route('admin.users.deactivate', $employee->user) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">Disable Login</button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.activate', $employee->user) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition">Enable Login</button>
                        </form>
                    @endif
                </div>

                {{-- Reset password --}}
                <form action="{{ route('admin.users.reset-password', $employee->user) }}" method="POST" class="space-y-2">
                    @csrf @method('PATCH')
                    <label class="block text-xs font-medium text-gray-600">Reset Password</label>
                    <input type="password" name="password" required minlength="8" placeholder="New password"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                    <button type="submit" class="text-xs px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition">Reset Password</button>
                </form>
            </div>
            @endif
        @endif
    </div>

    {{-- Documents --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Documents</h2>
        </div>

        <form action="{{ route('admin.employees.documents.store', $employee) }}" method="POST" enctype="multipart/form-data"
              class="flex flex-wrap items-end gap-3 mb-4 pb-4 border-b border-gray-100">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                <select name="type" required class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['contract' => 'Employment Contract', 'identification' => 'Identification', 'certificate' => 'Certificate', 'performance_review' => 'Performance Review', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                <input type="text" name="title" required placeholder="e.g. 2026 Employment Contract"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">File</label>
                <input type="file" name="file" required class="text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Upload
            </button>
        </form>
        @error('file') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

        <div class="divide-y divide-gray-50">
            @forelse($employee->documents as $doc)
            <div class="flex items-center justify-between py-2.5">
                <div>
                    <div class="text-sm text-gray-800">{{ $doc->title }}</div>
                    <div class="text-xs text-gray-400">{{ ucwords(str_replace('_',' ',$doc->type)) }} · uploaded {{ $doc->created_at->format('M j, Y') }} by {{ $doc->uploadedBy?->name ?? '—' }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.employees.documents.download', [$employee, $doc]) }}" class="text-xs text-blue-600 hover:text-blue-800">Download</a>
                    <form action="{{ route('admin.employees.documents.destroy', [$employee, $doc]) }}" method="POST"
                          onsubmit="return confirm('Delete this document?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-center text-xs text-gray-400 py-6">No documents uploaded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
