<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'date_desc');

        $query = Employee::with(['department', 'position'])
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('full_name', 'like', "%{$s}%")
                ->orWhere('employee_code', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")))
            ->when($request->department, fn ($q, $d) => $q->where('department_id', $d))
            ->when($request->position,   fn ($q, $p) => $q->where('position_id', $p))
            ->when($request->status,     fn ($q, $s) => $q->where('employment_status', $s))
            ->when($request->date_from,  fn ($q, $d) => $q->whereDate('date_of_joining', '>=', $d))
            ->when($request->date_to,    fn ($q, $d) => $q->whereDate('date_of_joining', '<=', $d));

        $query = match ($sort) {
            'name_asc'   => $query->orderBy('full_name'),
            'name_desc'  => $query->orderByDesc('full_name'),
            'date_asc'   => $query->orderBy('date_of_joining'),
            'code_asc'   => $query->orderBy('employee_code'),
            default      => $query->orderByDesc('date_of_joining'),
        };

        $perPage = in_array((int) $request->input('per_page'), config('pagination.per_page_options'))
            ? (int) $request->input('per_page')
            : config('pagination.default_per_page');

        $employees   = $query->paginate($perPage)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $positions   = Position::orderBy('title')->get();

        return view('admin.employees.index', compact('employees', 'departments', 'positions', 'sort'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $positions   = Position::orderBy('title')->get();
        $employees   = Employee::orderBy('full_name')->get(['id', 'full_name']);
        $users       = $this->availableUsers();

        return view('admin.employees.create', compact('departments', 'positions', 'employees', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $validated['employee_code'] = Employee::nextCode();

        $employee = Employee::create($validated);

        AuditLog::record('employee.created', $employee->id, ['employee_code' => $employee->employee_code, 'full_name' => $employee->full_name]);

        return redirect()->route('admin.employees.show', $employee)->with('message', "Employee \"{$employee->full_name}\" created.");
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'position', 'manager', 'user', 'documents.uploadedBy']);

        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::orderBy('name')->get();
        $positions   = Position::orderBy('title')->get();
        $employees   = Employee::where('id', '!=', $employee->id)->orderBy('full_name')->get(['id', 'full_name']);
        $users       = $this->availableUsers($employee->user_id);

        return view('admin.employees.edit', compact('employee', 'departments', 'positions', 'employees', 'users'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $this->validateData($request, $employee);

        if ($request->hasFile('photo')) {
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $employee->update($validated);

        AuditLog::record('employee.updated', $employee->id, ['full_name' => $employee->full_name]);

        return redirect()->route('admin.employees.show', $employee)->with('message', "Employee \"{$employee->full_name}\" updated.");
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->full_name;
        $employee->delete();

        AuditLog::record('employee.deleted', null, ['employee_code' => $employee->employee_code, 'full_name' => $name]);

        return redirect()->route('admin.employees.index')->with('message', "Employee \"{$name}\" deleted.");
    }

    public function activate(Employee $employee)
    {
        $employee->update(['employment_status' => 'active']);
        AuditLog::record('employee.activated', $employee->id, ['full_name' => $employee->full_name]);

        return back()->with('message', "\"{$employee->full_name}\" is now active.");
    }

    public function deactivate(Employee $employee)
    {
        $employee->update(['employment_status' => 'inactive']);
        AuditLog::record('employee.deactivated', $employee->id, ['full_name' => $employee->full_name]);

        return back()->with('message', "\"{$employee->full_name}\" is now inactive.");
    }

    private function availableUsers(?int $includeUserId = null)
    {
        return User::where(fn ($q) => $q
            ->doesntHave('employee')
            ->orWhere('id', $includeUserId))
            ->orderBy('name')
            ->get(['id', 'name', 'username']);
    }

    private function validateData(Request $request, ?Employee $employee = null): array
    {
        $rules = [
            'full_name'               => ['required', 'string', 'max:255'],
            'email'                   => ['nullable', 'email', 'max:255', 'unique:employees,email' . ($employee ? ",{$employee->id}" : '')],
            'phone'                   => ['nullable', 'string', 'max:50'],
            'date_of_birth'           => ['nullable', 'date'],
            'gender'                  => ['nullable', 'in:male,female,other'],
            'address'                 => ['nullable', 'string'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'nationality'             => ['nullable', 'string', 'max:100'],
            'marital_status'          => ['nullable', 'in:single,married,divorced,widowed'],
            'employment_status'       => ['required', 'in:active,inactive,resigned,terminated'],
            'department_id'           => ['nullable', 'integer', 'exists:departments,id'],
            'position_id'              => ['nullable', 'integer', 'exists:positions,id'],
            'manager_id'              => ['nullable', 'integer', 'exists:employees,id', 'different:' . ($employee->id ?? 'NULL')],
            'date_of_joining'         => ['nullable', 'date'],
            'employment_type'        => ['nullable', 'in:full_time,part_time,contract,internship'],
            'work_location'           => ['nullable', 'string', 'max:255'],
            'office_branch'           => ['nullable', 'string', 'max:255'],
            'user_id'                 => ['nullable', 'integer', 'exists:users,id'],
            'photo'                   => ['nullable', 'image', 'max:5120'],
        ];

        if (auth()->user()->isAdmin()) {
            $rules['salary'] = ['nullable', 'numeric', 'min:0', 'max:99999999999.99'];
        }

        $validated = $request->validate($rules);
        unset($validated['photo']);

        return $validated;
    }
}
