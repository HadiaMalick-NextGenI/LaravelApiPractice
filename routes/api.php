<?php

use App\Http\Controllers\Api\JsonPlaceholderController;
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

Route::get('/status', function () {
    return response()->json(['status' => 'API is running']);
});

Route::middleware('auth:sanctum')->group(function (){
    Route::get('/json/posts', [JsonPlaceholderController::class, 'fetchPosts']);
    Route::get('/json/posts/{post}', [JsonPlaceholderController::class, 'getPost']);
    Route::post('/json/posts', [JsonPlaceholderController::class, 'createPost']);
    Route::put('/json/posts/{post}', [JsonPlaceholderController::class, 'updatePost']);
    Route::delete('json/posts/{post}', [JsonPlaceholderController::class, 'deletePost']);
});

// Route::fallback(function () {
//     return redirect('api/v2');
// });