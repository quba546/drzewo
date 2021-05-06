@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center m-4">Mechanizm zarządzania strukturą drzewiastą</h1>
        <div class="m-4">
            <x-message></x-message>
        </div>
        <div class="row">
            <div class="col-6">
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
                        <h5 class="card-header">Drzewo kategorii</h5>
                        <div class="card-body pt-3 pb-3">
                            <ul class="tree">
                                @foreach($categories as $category)
                                    <li>
                                        {{ $category->title . " (ID: $category->id)" }}
                                        <div class="action-btn">
                                            <form action="{{ route('destroy') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $category->id }}">
                                                <button type="submit" title="Usuń" class="ml-1 mr-1"><i class="fas fa-times btn-destroy"></i></button>
                                            </form>
                                        </div>

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
            <div class="col-6">
                <div class="col-12">
                    <div class="card border border-primary">
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
                                            <span class="text-danger">{{ $errors->first('title') }}</span>
                                        </div>

                                        <div class="form-group">
                                            <label for="parent_id">Rodzic</label>
                                            <select name="parent_id" id="parent_id" class="form-control form-control-sm">
                                                <option selected value="0">Wybierz rodzica...</option>
                                                @foreach ($allCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                                @endforeach
                                            </select>
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
                                        <div class="form-group">
                                            <label for="category-to-move">Wybierz kategorię, którą chcesz przenieść</label>
                                            <select name="category_id" id="category-to-move" class="form-control form-control-sm">
                                                <option selected value="0">Wybierz kategorię...</option>
                                                @foreach ($allCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="parent-to-moved">Wybierz rodzica, do którego chcesz przeniść wybraną kategorię</label>
                                            <select name="parent_id" id="parent-to-moved" class="form-control form-control-sm">
                                                <option selected value="0">Wybierz rodzica...</option>
                                                @foreach ($allCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                                @endforeach
                                            </select>
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
                                        <div class="form-group">
                                            <label for="category-to-edit">Wybierz kategorię, którą chcesz edytować</label>
                                            <select name="id" id="category-to-edit" class="form-control form-control-sm">
                                                <option selected value="0">Wybierz kategorię...</option>
                                                @foreach ($allCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group {{ $errors->has('newTitle') ? 'has-error' : '' }}">
                                            <label for="title-edit">Nowy tytuł</label>
                                            <input type="text" name="newTitle" id="title-edit" value="{{ old('newTitle') }}" class="form-control form-control-sm"
                                                   placeholder="Wpisz nowy tytuł" maxlength="50" required>
                                            <span class="text-danger">{{ $errors->first('newTitle') }}</span>
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
        </div>
    </div>
@endsection
