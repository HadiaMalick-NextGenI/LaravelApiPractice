<?php

use App\Http\Controllers\Api\V2\AdminController;
use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\PostController;
use App\Http\Controllers\Api\V2\SubscriberController;
use App\Http\Controllers\Api\V2\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('signup', [AuthController::class, 'signup'])->name('signup');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function (){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('posts',PostController::class);
    Route::post('profile', [UserController::class, 'insertProfileData']);
    Route::post('/subscribe', [SubscriberController::class, 'subscribe']);
    Route::post('/unsubscribe', [SubscriberController::class, 'unsubscribe']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'index']);
    Route::get('/users/{id}', [AdminController::class, 'show']);
    Route::post('/users/{id}/assign-role', [AdminController::class, 'assignRole']);
    Route::delete('/users/{id}', [AdminController::class, 'destroy']);
});