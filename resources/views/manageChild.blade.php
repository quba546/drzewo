<ul>
    @foreach($childs as $child)
        <li>
            {{ $child->title }}
            <form action="{{ route('moveUp') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="parent_id" value="{{ $child->parent_id }}">
                <input type="hidden" name="id" value="{{ $child->id }}">
                <button type="submit" class="action-btn"><i class="fas fa-arrow-up"></i></button>
            </form>
            <form action="{{ route('destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $child->id }}">
                <button type="submit" class="action-btn"><i class="fas fa-times"></i></button>
            </form>

            @if(count($child->childs))
                @include('manageChild',['childs' => $child->childs])
            @endif
        </li>
    @endforeach
</ul>
