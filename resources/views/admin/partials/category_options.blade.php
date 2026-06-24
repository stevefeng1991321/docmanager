<option value="">— None —</option>
@foreach($categories as $parent)
    <option value="{{ $parent->id }}"
            @if(isset($selected) && $selected == $parent->id) selected @endif>
        {{ $parent->name }}
    </option>
    @foreach($parent->children as $child)
        <option value="{{ $child->id }}"
                @if(isset($selected) && $selected == $child->id) selected @endif>
            &nbsp;&nbsp;└ {{ $child->name }}
        </option>
    @endforeach
@endforeach
