<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Drzewo</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body>
    <div class="container">
        <x-message></x-message>
        <div>
            <div>Manage Category TreeView</div>
            <div>
                <div class="row">
                    <div class="col-6">
                        <div class="col-12">
                            <form action="{{ route('index') }}" method="GET">
                                <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                    <label for="branch_id">Branch</label>
                                    <select name="show" id="branch_id" class="form-control">
                                        <option @if (!app('request')->input('show') || app('request')->input('show') === '0') selected @endif value="0">Root (ID: 0)</option>
                                        @foreach ($allCategories as $category)
                                            <option @if (app('request')->input('show') == $category->id) selected @endif value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-success">Show</button>
                                </div>
                            </form>
                        </div>
                        <h3>Category List</h3>
                        <ul>
                            <li>
                                {{ $showCategory }}
                                <ul>
                                    @foreach($categories as $category)
                                        <li>
                                            {{ $category->title . " (ID: $category->id)" }}
                                            <form action="{{ route('destroy') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $category->id }}">
                                                <button type="submit" class="action-btn"><i class="fas fa-times"></i></button>
                                            </form>
                                            @if(count($category->childs))
                                                @include('manageChild',['childs' => $category->childs])
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <h3>Add New Category</h3>
                                <form action="{{ route('store') }}" method="POST">
                                    @csrf
                                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                        <label for="title">Title</label>
                                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control"
                                               placeholder="Enter Title">
                                        <span class="text-danger">{{ $errors->first('title') }}</span>
                                    </div>

                                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                        <label for="parent_id">Parent</label>
                                        <select name="parent_id" id="parent_id" class="form-control">
                                            <option selected value="0">Wybierz rodzica...</option>
                                            @foreach ($allCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-success">Add New</button>
                                    </div>

                                </form>
                            </div>
                            <div class="col-12">
                                <h3>Move Category</h3>
                                <form action="{{ route('move') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                        <label for="category-to-move"></label>
                                        <select name="category_id" id="category-to-move" class="form-control">
                                            <option selected value="0">Select category to move...</option>
                                            @foreach ($allCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                    </div>

                                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                        <label for="parent-to-moved"></label>
                                        <select name="parent_id" id="parent-to-moved" class="form-control">
                                            <option selected value="0">Select parent...</option>
                                            @foreach ($allCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-success">Move</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12">
                                <h3>Edit Category</h3>
                                <form action="{{ route('update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                        <label for="category-to-edit"></label>
                                        <select name="id" id="category-to-edit" class="form-control">
                                            <option selected value="0">Select category to edit...</option>
                                            @foreach ($allCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->title . " (ID: $category->id)" }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                    </div>

                                    <div class="form-group {{ $errors->has('newTitle') ? 'has-error' : '' }}">
                                        <label for="title-edit">New Title</label>
                                        <input type="text" name="newTitle" id="title-edit" value="{{ old('newTitle') }}" class="form-control"
                                               placeholder="Edit title">
                                        <span class="text-danger">{{ $errors->first('newTitle') }}</span>
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-success">Edit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
