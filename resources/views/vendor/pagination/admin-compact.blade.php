@if ($paginator->hasPages())
<nav class="flex items-center gap-1 text-sm" aria-label="Pagination">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="px-2 py-1.5 text-gray-300 cursor-default select-none">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="px-2 py-1.5 text-gray-600 hover:bg-gray-100 rounded transition">‹</a>
    @endif

    @php
        $current = $paginator->currentPage();
        $last    = $paginator->lastPage();
        $window  = range(max(2, $current - 2), min($last - 1, $current + 2));
    @endphp

    {{-- First page --}}
    <a href="{{ $paginator->url(1) }}"
       class="min-w-[2rem] text-center px-2 py-1.5 rounded transition text-sm
              {{ $current === 1 ? 'bg-blue-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">1</a>

    {{-- Left ellipsis --}}
    @if (count($window) && min($window) > 2)
        <span class="px-1 text-gray-400 select-none">…</span>
    @endif

    {{-- Sliding window --}}
    @foreach ($window as $page)
        <a href="{{ $paginator->url($page) }}"
           class="min-w-[2rem] text-center px-2 py-1.5 rounded transition text-sm
                  {{ $current === $page ? 'bg-blue-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">{{ $page }}</a>
    @endforeach

    {{-- Right ellipsis --}}
    @if (count($window) && max($window) < $last - 1)
        <span class="px-1 text-gray-400 select-none">…</span>
    @endif

    {{-- Last page --}}
    @if ($last > 1)
        <a href="{{ $paginator->url($last) }}"
           class="min-w-[2rem] text-center px-2 py-1.5 rounded transition text-sm
                  {{ $current === $last ? 'bg-blue-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">{{ $last }}</a>
    @endif

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="px-2 py-1.5 text-gray-600 hover:bg-gray-100 rounded transition">›</a>
    @else
        <span class="px-2 py-1.5 text-gray-300 cursor-default select-none">›</span>
    @endif

</nav>
@endif
