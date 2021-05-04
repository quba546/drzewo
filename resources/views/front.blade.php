<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Drzewo</title>

        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <h5 class="card-header">Panel administratora</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Dodaj kategorię</h5>
                                        <form action="">
                                            <div class="form-group">
                                                <label for="category-name">Nazwa</label>
                                                <input type="text" name="category-name" class="form-control" id="category-name">
                                            </div>
                                            <div class="form-group">
                                                <label for="select-parent">Rodzic</label>
                                                <select id="select-parent" name="category-parent" class="form-control">
                                                    <option selected>Wybierz...</option>
                                                    <option>1</option>
                                                    <option>2</option>
                                                    <option>3</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Dodaj</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <h5 class="card-header">Struktura</h5>
                    <div class="card-body">
                        <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                            Root
                        </a>
                        <div class="collapse" id="collapseExample">
                            @foreach($tree as $node)
                                <li class="list-group-item">{!! str_repeat('&nbsp&nbsp&nbsp', $node->depth) !!} {{ $node->name }}</li>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
