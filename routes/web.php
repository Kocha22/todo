<?php

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

Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index'])->name('posts');
Route::post('/storepost', [\App\Http\Controllers\PostController::class, 'store'])->name('storepost');
Route::get('/getPosts', [\App\Http\Controllers\PostController::class, 'getPosts'])->name('getPosts');

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'index'])->name('login');
