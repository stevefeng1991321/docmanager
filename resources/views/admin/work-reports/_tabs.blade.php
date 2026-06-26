@php
    $tabs = [
        'reports'   => ['label' => 'Reports',   'route' => 'admin.work-reports.index'],
        'analytics' => ['label' => 'Analytics',  'route' => 'admin.work-report-analytics.index'],
        'projects'  => ['label' => 'Projects',   'route' => 'admin.projects.index'],
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
