<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostCategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\VerificationController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider.
|
*/

// Rute untuk registrasi
Route::post('/register', [AuthController::class, 'register']);

// Rute untuk login
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

// Rute yang membutuhkan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // Logout endpoint
    Route::post('/logout', [AuthController::class, 'logout']);

    // Mendapatkan informasi pengguna
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route User
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Route Kategory
    Route::get('/categories', [PostCategoryController::class, 'index']);
    Route::post('/categories', [PostCategoryController::class, 'store']);
    Route::get('/categories/{id}', [PostCategoryController::class, 'show']);
    Route::put('/categories/{id}', [PostCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [PostCategoryController::class, 'destroy']);

    // Route Post
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/not-bookmarked', [PostController::class, 'notBookmarked']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::delete('/posts/{id}/image', [PostController::class, 'deleteImage']);


    // Route Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
    Route::get('/bookmarks/{id}', [BookmarkController::class, 'show']);
    Route::put('/bookmarks/{id}', [BookmarkController::class, 'update']);
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy']);
});


