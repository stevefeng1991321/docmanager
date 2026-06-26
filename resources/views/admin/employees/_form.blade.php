@php
    $e = $employee ?? null;
    $val = fn($field, $default = null) => old($field, $e?->$field ?? $default);
@endphp

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h2 class="text-sm font-semibold text-gray-700">Photo</h2>
    <div class="flex items-center gap-4">
        @php $photoUrl = $e?->photoUrl(); @endphp
        <div id="photo-preview-wrap">
            @if($photoUrl)
                <img id="photo-preview" src="{{ $photoUrl }}" alt=""
                     class="w-16 h-16 rounded-full object-cover border border-gray-200">
            @else
                <div id="photo-initials" class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase text-xl">
                    {{ $e ? substr($e->full_name, 0, 1) : '?' }}
                </div>
                <img id="photo-preview" src="" alt=""
                     class="w-16 h-16 rounded-full object-cover border border-gray-200 hidden">
            @endif
        </div>
        <div>
            <input type="file" name="photo" id="photo-input" accept="image/*"
                   class="block text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-gray-400">JPG, PNG, WEBP — max 5 MB</p>
        </div>
    </div>
    <script>
        document.getElementById('photo-input').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const preview  = document.getElementById('photo-preview');
            const initials = document.getElementById('photo-initials');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            if (initials) initials.classList.add('hidden');
        });
    </script>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h2 class="text-sm font-semibold text-gray-700">Personal Information</h2>

    <div class="grid grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="full_name" value="{{ $val('full_name') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('full_name') border-red-400 @enderror">
            @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ $val('email') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('email') border-red-400 @enderror">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ $val('phone') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ $val('date_of_birth') ? \Illuminate\Support\Carbon::parse($val('date_of_birth'))->format('Y-m-d') : '' }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— Not specified —</option>
                @foreach(['male','female','other'] as $g)
                    <option value="{{ $g }}" @selected($val('gender') === $g)>{{ ucfirst($g) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
            <select name="marital_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— Not specified —</option>
                @foreach(['single','married','divorced','widowed'] as $m)
                    <option value="{{ $m }}" @selected($val('marital_status') === $m)>{{ ucfirst($m) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
            <input type="text" name="nationality" value="{{ $val('nationality') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
            <input type="text" name="emergency_contact_name" value="{{ $val('emergency_contact_name') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone</label>
            <input type="text" name="emergency_contact_phone" value="{{ $val('emergency_contact_phone') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
        <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y">{{ $val('address') }}</textarea>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h2 class="text-sm font-semibold text-gray-700">Employment Details</h2>

    <div class="grid grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— None —</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected((string) $val('department_id') === (string) $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
            <select name="position_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— None —</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->id }}" @selected((string) $val('position_id') === (string) $pos->id)>{{ $pos->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
            <select name="manager_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— None —</option>
                @foreach($employees as $mgr)
                    <option value="{{ $mgr->id }}" @selected((string) $val('manager_id') === (string) $mgr->id)>{{ $mgr->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
            <select name="employment_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach(['active','inactive','resigned','terminated'] as $s)
                    <option value="{{ $s }}" @selected($val('employment_status', 'active') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Joining</label>
            <input type="date" name="date_of_joining" value="{{ $val('date_of_joining') ? \Illuminate\Support\Carbon::parse($val('date_of_joining'))->format('Y-m-d') : '' }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
            <select name="employment_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— Not specified —</option>
                @foreach(['full_time','part_time','contract','internship'] as $t)
                    <option value="{{ $t }}" @selected($val('employment_type') === $t)>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Work Location</label>
            <input type="text" name="work_location" value="{{ $val('work_location') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Office Branch</label>
            <input type="text" name="office_branch" value="{{ $val('office_branch') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        @if(auth()->user()->isAdmin())
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Salary <span class="text-gray-400">(admin only)</span></label>
            <input type="number" step="0.01" name="salary" value="{{ $val('salary') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        @endif
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h2 class="text-sm font-semibold text-gray-700">System Account <span class="text-gray-400 font-normal">(optional)</span></h2>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Linked User Account</label>
        <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— No login access —</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) $val('user_id') === (string) $user->id)>{{ $user->name }} ({{ '@' . $user->username }})</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-400 mt-1">Links this employee to an existing DocManager login. Role and password are still managed under Users.</p>
    </div>
</div>
