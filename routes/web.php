<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
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

Route::middleware(['web'])->group(function(){
    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::get('/posts',[PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('/posts/edit/{post}', [PostController::class, 'edit'])->name('posts.edit');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    
    Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); 
});