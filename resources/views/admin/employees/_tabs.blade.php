@php
    $tabs = [
        'employees'   => ['label' => 'All Employees', 'route' => 'admin.employees.index'],
        'departments' => ['label' => 'Departments',   'route' => 'admin.departments.index'],
        'positions'   => ['label' => 'Positions',     'route' => 'admin.positions.index'],
        'reports'     => ['label' => 'Reports',       'route' => 'admin.employee-reports.index'],
    ];
@endphp
<div class="border-b border-gray-200 flex gap-1 -mt-1">
    @foreach($tabs as $key => $tab)
        <a href="{{ route($tab['route']) }}"
           class="px-3.5 py-2 text-sm font-medium border-b-2 transition
                  {{ $active === $key ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
