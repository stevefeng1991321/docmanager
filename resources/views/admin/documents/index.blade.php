@extends('layouts.admin')
@section('title', __('admin.documents.heading'))

@section('content')

<div x-data="bulkDocs()" x-init="init()">

{{-- Top bar: filters + upload --}}
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.documents.search_placeholder') }}"
               class="w-52 border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('common.all_status') }}</option>
            @foreach(['draft','pending_review','published','rejected','archived'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('admin.documents.filter_category') }}</option>
            @foreach($categories as $parent)
                <option value="{{ $parent->id }}" @selected(request('category') == $parent->id)>{{ $parent->name }}</option>
                @foreach($parent->children as $child)
                    <option value="{{ $child->id }}" @selected(request('category') == $child->id)>&nbsp;&nbsp;└ {{ $child->name }}</option>
                @endforeach
            @endforeach
        </select>
        <select name="sort" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="date_desc" @selected($sort === 'date_desc')>Newest First</option>
            <option value="date_asc"  @selected($sort === 'date_asc') >Oldest First</option>
            <option value="name_asc"  @selected($sort === 'name_asc') >Title A–Z</option>
            <option value="name_desc" @selected($sort === 'name_desc')>Title Z–A</option>
            <option value="size_desc" @selected($sort === 'size_desc')>Largest File</option>
            <option value="downloads" @selected($sort === 'downloads')>Most Downloaded</option>
        </select>
        <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('common.filter') }}</button>
        @if(request()->hasAny(['search','status','category','sort']))
            <a href="{{ route('admin.documents.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">{{ __('common.clear') }}</a>
        @endif
    </form>
    <div class="flex gap-2">
        <a href="{{ route('admin.documents.trash') }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition">
            Trash
        </a>
        <a href="{{ route('admin.documents.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            + Upload
        </a>
    </div>
</div>

{{-- Bulk action bar (appears when rows are selected) --}}
<div x-show="selected.length > 0" x-cloak
     class="mb-4 flex flex-wrap items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
    <span class="text-sm font-medium text-blue-700" x-text="selected.length + ' selected'"></span>

    {{-- Bulk Approve --}}
    <form method="POST" action="{{ route('admin.documents.bulk-approve') }}" @submit="attachIds($event)">
        @csrf
        <button type="submit"
                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition">
            Approve
        </button>
    </form>

    {{-- Bulk Trash --}}
    <form method="POST" action="{{ route('admin.documents.bulk-trash') }}" @submit="confirmAndAttach($event, 'Move selected to Trash?')">
        @csrf
        <button type="submit"
                class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition">
            Move to Trash
        </button>
    </form>

    {{-- Bulk Reject --}}
    <button type="button" @click="rejectModal = true"
            class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium rounded-lg transition">
        Reject
    </button>

    {{-- Bulk Assign Category --}}
    <button type="button" @click="categoryModal = true"
            class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition">
        Assign Category
    </button>

    <button type="button" @click="selected = []"
            class="ml-auto text-xs text-blue-500 hover:text-blue-700">Clear selection</button>
</div>

{{-- Documents table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" @change="toggleAll($event)"
                               :checked="selected.length === rowIds.length && rowIds.length > 0"
                               class="rounded border-gray-300 text-blue-600">
                    </th>
                    <th class="px-4 py-3 text-left">{{ __('admin.documents.col_title') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('common.type') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('admin.documents.col_category') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('admin.documents.col_status') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('admin.documents.col_uploader') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('common.date') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('admin.documents.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($documents as $doc)
                    @php
                        $badge = ['draft'=>'bg-gray-100 text-gray-600','pending_review'=>'bg-yellow-100 text-yellow-700','published'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-600','archived'=>'bg-slate-100 text-slate-600'];
                    @endphp
                    <tr class="hover:bg-gray-50" :class="selected.includes({{ $doc->id }}) ? 'bg-blue-50' : ''">
                        <td class="px-4 py-3">
                            <input type="checkbox" :value="{{ $doc->id }}"
                                   @change="toggle({{ $doc->id }})"
                                   :checked="selected.includes({{ $doc->id }})"
                                   class="rounded border-gray-300 text-blue-600">
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 truncate max-w-xs">{{ $doc->title }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $doc->original_filename }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs uppercase">{{ pathinfo($doc->original_filename, PATHINFO_EXTENSION) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $doc->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge[$doc->status] ?? '' }}">
                                    {{ ucfirst(str_replace('_',' ',$doc->status)) }}
                                </span>
                                @if($doc->isStale($staleMonths))
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                        Review Due
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $doc->uploader?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 items-center">
                                <a href="{{ route('admin.documents.edit', $doc) }}" class="text-blue-600 hover:underline text-xs">{{ __('admin.documents.edit_action') }}</a>
                                @if($doc->status === 'pending_review')
                                    <form action="{{ route('admin.documents.approve', $doc) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-green-600 hover:underline text-xs">{{ __('common.approve') }}</button>
                                    </form>
                                @endif
                                @if($doc->status === 'archived')
                                    <form action="{{ route('admin.documents.unarchive', $doc) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-slate-600 hover:underline text-xs">{{ __('common.restore') }}</button>
                                    </form>
                                @elseif($doc->status !== 'draft')
                                    <form action="{{ route('admin.documents.archive', $doc) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-slate-500 hover:underline text-xs">{{ __('admin.documents.archive') }}</button>
                                    </form>
                                @endif
                                @if(!$doc->locked_by)
                                    <form action="{{ route('admin.documents.lock', $doc) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-yellow-600 hover:underline text-xs">Lock</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.documents.unlock', $doc) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-gray-500 hover:underline text-xs">Unlock</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST"
                                      onsubmit="return confirm('Move to Trash?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline text-xs">{{ __('admin.documents.delete_action') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400">{{ __('admin.documents.no_documents') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            <span class="text-xs text-gray-500">
                Showing {{ $documents->firstItem() ?? 0 }}–{{ $documents->lastItem() ?? 0 }} of {{ $documents->total() }}
            </span>
            @if($documents->total() > 10)
            <form method="GET" class="flex items-center gap-2">
                @foreach(request()->except(['per_page', 'page']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <label class="text-xs text-gray-500 whitespace-nowrap">{{ __('common.per_page') }}</label>
                <select name="per_page" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                    @foreach(config('pagination.per_page_options') as $n)
                        <option value="{{ $n }}" @selected((int) request('per_page', config('pagination.default_per_page')) === $n)>{{ $n }}</option>
                    @endforeach
                </select>
            </form>
            @endif
        </div>
        {{ $documents->links('vendor.pagination.admin-compact') }}
    </div>
</div>

{{-- Reject modal --}}
<div x-show="rejectModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" @click.stop>
        <h3 class="font-semibold text-gray-800">{{ __('admin.documents.bulk_delete') }}</h3>
        <form method="POST" action="{{ route('admin.documents.bulk-reject') }}" @submit="attachIds($event)">
            @csrf
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Reason <span class="text-gray-400 text-xs">(optional)</span></label>
                <textarea name="reason" rows="3" placeholder="Reason for rejection…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition">
                    Reject <span x-text="selected.length"></span> Document(s)
                </button>
                <button type="button" @click="rejectModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                    {{ __('common.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Assign Category modal --}}
<div x-show="categoryModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4" @click.stop>
        <h3 class="font-semibold text-gray-800">{{ __('admin.documents.filter_category') }}</h3>
        <form method="POST" action="{{ route('admin.documents.bulk-assign-category') }}" @submit="attachIds($event)">
            @csrf
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">— select —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Assign to <span x-text="selected.length"></span> Document(s)
                </button>
                <button type="button" @click="categoryModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                    {{ __('common.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>

</div>{{-- end x-data --}}

@push('scripts')
<script>
function bulkDocs() {
    return {
        selected: [],
        rowIds: @json($documents->pluck('id')),
        rejectModal: false,
        categoryModal: false,

        init() {},

        toggle(id) {
            const idx = this.selected.indexOf(id);
            if (idx === -1) this.selected.push(id);
            else this.selected.splice(idx, 1);
        },

        toggleAll(e) {
            this.selected = e.target.checked ? [...this.rowIds] : [];
        },

        attachIds(e) {
            const form = e.target;
            this.selected.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                form.appendChild(inp);
            });
        },

        confirmAndAttach(e, msg) {
            if (!confirm(msg)) { e.preventDefault(); return; }
            this.attachIds(e);
        },
    }
}
</script>
@endpush

@endsection
