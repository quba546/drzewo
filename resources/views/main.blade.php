@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center m-4">Mechanizm zarządzania strukturą drzewiastą</h1>
        <div class="mt-4 mb-4">
            <x-message></x-message>
        </div>
        <div class="row">
            @can('admin', App\Models\User::class)
                <div class="col-6">
            @else
                <div class="col-12">
            @endcan
                <div class="col-12">
                    <div class="card border border-primary">
                        <h5 class="card-header">Wyświetl gałąź</h5>
                        <div class="card-body pt-1 pb-1">
                            <form action="{{ route('index') }}" method="GET">
                                <div class="form-group">
                                    <label for="branch_id">Gałąź</label>
                                    <select name="show" id="branch_id" class="form-control form-control-sm">
                                        <option @if (!app('request')->input('show') || app('request')->input('show') === '0') selected @endif value="0">Root (ID: 0)</option>
                                        @foreach ($allCategories as $category)
                                            <option @if (app('request')->input('show') == $category->id) selected @endif value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <button class="btn btn-outline-primary btn-sm">Pokaż</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card border-primary mt-4">
                        <h5 class="card-header">Drzewo kategorii
                            @can('admin', App\Models\User::class)
                                <span class="pl-3 pr-3">|</span>
                                <span>
                                    Sortowanie alfabetyczne
                                    <a href="{{ route('index', ['show' => $branchId, 'sort' => 'asc']) }}" class="pl-2 pr-2 a-icon" title="rosnąco"><i class="fas fa-arrow-circle-up"></i></a>
                                    <a href="{{ route('index', ['show' => $branchId, 'sort' => 'desc']) }}" class="pl-2 a-icon" title="malejąco"><i class="fas fa-arrow-circle-down"></i></a>
                                </span>
                            @endcan
                        </h5>
                        <div class="card-body pt-3 pb-3">
                            <ul class="tree">
                                @foreach($categories as $category)
                                    <li>
                                        {{ $category->title . " (ID: $category->id)" }}
                                        @can('admin', App\Models\User::class)
                                            <div class="action-btn">
                                                <form action="{{ route('destroy') }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="id" value="{{ $category->id }}">
                                                    <button type="submit" title="Usuń" class="ml-1 mr-1"><i class="fas fa-times btn-destroy"></i></button>
                                                </form>
                                            </div>
                                        @endcan

                                        @if(count($category->childs))
                                            @include('manageChild',['childs' => $category->childs])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
            @can('admin', App\Models\User::class)
            <div class="col-6">
                <div class="col-12">
                    <div class="card border border-primary mb-3">
                        <h5 class="card-header">Panel administratora</h5>
                        <div class="card-body pt-3 pb-3">
                            <div class="card border border-info">
                                <div class="card-body pt-3 pb-3">
                                    <h5 class="card-title">Dodaj nową kategorię</h5>
                                    <form action="{{ route('store') }}" method="POST">
                                        @csrf
                                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                            <label for="title">Nazwa kategorii</label>
                                            <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control form-control-sm"
                                                   placeholder="Wpisz nazwę kategorii" maxlength="50" required>
                                            <span class="text-danger font-weight-bold">{{ $errors->first('title') }}</span>
                                        </div>

                                        <div class="form-group  {{ $errors->has('addParentId') ? 'has-error' : '' }}">
                                            <label for="parent_id">Rodzic</label>
                                            <select name="addParentId" id="parent_id" class="form-control form-control-sm">
                                                <option selected disabled>Wybierz rodzica...</option>
                                                <option value="0">Root (ID: 0)</option>
                                                @foreach ($allCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger font-weight-bold">{{ $errors->first('addParentId') }}</span>
                                        </div>

                                        <div class="form-group mb-1">
                                            <button class="btn btn-outline-primary btn-sm">Dodaj</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card border border-info mt-4">
                                <div class="card-body pt-3 pb-3">
                                    <h5 class="card-title">Przenieś kategorię</h5>
                                    <form action="{{ route('move') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group {{ $errors->has('moveId') ? 'has-error' : '' }}">
                                            <label for="category-to-move">Wybierz kategorię, którą chcesz przenieść</label>
                                            <select name="moveId" id="category-to-move" class="form-control form-control-sm">
                                                <option selected disabled>Wybierz kategorię...</option>
                                                @foreach ($allCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger font-weight-bold">{{ $errors->first('moveId') }}</span>
                                        </div>

                                        <div class="form-group  {{ $errors->has('parentId') ? 'has-error' : '' }}" id="moveCategory2">
                                            <label for="parent-to-moved">Wybierz rodzica, do którego chcesz przenieść wybraną kategorię</label>
                                            <select name="parentId" id="parent-to-moved" class="form-control form-control-sm">
                                                <option selected disabled>Wybierz rodzica...</option>
                                                <option value="0">Root (ID: 0)</option>
                                                @foreach ($allCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger font-weight-bold">{{ $errors->first('parentId') }}</span>
                                        </div>

                                        <div class="form-group mb-1">
                                            <button class="btn btn-outline-primary btn-sm">Przenieś</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card border border-info mt-4">
                                <div class="card-body pt-3 pb-3">
                                    <h5 class="card-title">Edytuj kategorię</h5>
                                    <form action="{{ route('update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group {{ $errors->has('editId') ? 'has-error' : '' }}">
                                            <label for="category-to-edit">Wybierz kategorię, którą chcesz edytować</label>
                                            <select name="editId" id="category-to-edit" class="form-control form-control-sm">
                                                <option selected disabled>Wybierz kategorię...</option>
                                                @foreach ($allCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger font-weight-bold">{{ $errors->first('editId') }}</span>
                                        </div>

                                        <div class="form-group {{ $errors->has('newTitle') ? 'has-error' : '' }}">
                                            <label for="title-edit">Nowy tytuł</label>
                                            <input type="text" name="newTitle" id="title-edit" value="{{ old('newTitle') }}" class="form-control form-control-sm"
                                                   placeholder="Wpisz nowy tytuł" maxlength="50" required>
                                            <span class="text-danger font-weight-bold">{{ $errors->first('newTitle') }}</span>
                                        </div>

                                        <div class="form-group mb-1">
                                            <button class="btn btn-outline-primary btn-sm">Zmień</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
@endsection
