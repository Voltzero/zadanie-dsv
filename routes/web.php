<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/authors', [AuthorController::class, 'getAuthors']);
Route::post('/items', [ItemController::class, 'storeItem']);
Route::get('/items', [ItemController::class, 'getItems']);
Route::get('/items/{item}/description', [ItemController::class, 'getItemDescription']);
