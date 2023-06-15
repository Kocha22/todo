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
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/signin', [App\Http\Controllers\Auth\LoginController::class, 'index'])->name('signin');
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'index'])->name('register');
Route::post('/postregister', [App\Http\Controllers\Auth\RegisterController::class, 'postRegister'])->name('postregister');

Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index'])->name('posts');
Route::get('/newpost', [\App\Http\Controllers\PostController::class, 'create'])->name('newpost');
Route::get('/editpost/{id}', [\App\Http\Controllers\PostController::class, 'edit'])->name('editpost');
Route::get('/deletepost/{id}', [\App\Http\Controllers\PostController::class, 'delete'])->name('deletePost');
Route::get('/image/{id}', [\App\Http\Controllers\PostController::class, 'showImage'])->name('showImage');
Route::post('/storepost', [\App\Http\Controllers\PostController::class, 'store'])->name('storepost');
Route::get('/getPosts/{user_id}', [\App\Http\Controllers\PostController::class, 'getPosts'])->name('getPosts');
Route::get('/filterPosts/{id}', [\App\Http\Controllers\PostController::class, 'filterPosts'])->name('filterPosts');
Route::get('/search', [\App\Http\Controllers\PostController::class, 'search'])->name('search');

Route::group(['prefix' => '/auth', 'middleware' =>  ['auth']] ,function () {
    Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});
