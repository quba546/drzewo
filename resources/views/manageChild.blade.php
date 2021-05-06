<ul>
    @foreach($childs as $child)
        <li>
            {{ $child->title . " (ID: $child->id)" }}
            @can('admin', App\Models\User::class)
                <div class="action-btn">
                    <form action="{{ route('moveUp') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="parent_id" value="{{ $child->parent_id }}">
                        <input type="hidden" name="id" value="{{ $child->id }}">
                        <button type="submit" title="Przenieś poziom do góry" class="ml-1 mr-1"><i class="fas fa-arrow-up btn-move-up"></i></button>
                    </form>
                </div>

                <div class="action-btn">
                    <form action="{{ route('destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" value="{{ $child->id }}">
                        <button type="submit" title="Usuń" class="ml-1 mr-1"><i class="fas fa-times btn-destroy"></i></button>
                    </form>
                </div>
            @endcan

            @if(count($child->childs))
                @include('manageChild',['childs' => $child->childs])
            @endif
        </li>
    @endforeach
</ul>
