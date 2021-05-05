<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Laravel Category Treeview Example</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body>
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @elseif ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="panel panel-primary">
            <div class="panel-heading">Manage Category TreeView</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-6">
                        <h3>Category List</h3>
                        <ul id="tree">
                            @foreach($categories as $category)
                                <li>
                                    {{ $category->title }}
                                    @if(count($category->childs))
                                        @include('manageChild',['childs' => $category->childs])
                                    @endif
                                </li>
                            @endforeach
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
                                            @foreach ($allCategories as $id => $title)
                                                <option value="{{ $id }}">{{ $title }}</option>
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
                                            @foreach ($allCategories as $id => $title)
                                                <option value="{{ $id }}">{{ $title }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                    </div>

                                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                                        <label for="parent-to-moved"></label>
                                        <select name="parent_id" id="parent-to-moved" class="form-control">
                                            <option selected value="0">Select parent...</option>
                                            @foreach ($allCategories as $id => $title)
                                                <option value="{{ $id }}">{{ $title }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-success">Move</button>
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
