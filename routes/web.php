<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CategoryController::class, 'index'])
    ->name('index');

Route::post('/store', [CategoryController::class, 'store'])
    ->name('store');

Route::put('/move-up', [CategoryController::class, 'moveUp'])
    ->name('moveUp');

Route::put('/move', [CategoryController::class, 'move'])
    ->name('move');

Route::delete('/destroy', [CategoryController::class, 'destroy'])
    ->name('destroy');

Route::put('/update', [CategoryController::class, 'update'])
    ->name('update');

Auth::routes(['reset' => false]);
