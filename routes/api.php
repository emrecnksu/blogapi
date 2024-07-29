<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KvkkController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\Auth\UserProfileController;

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

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/show/{id}', [CategoryController::class, 'show']);

Route::get('/posts/related/{id}', [PostController::class, 'relatedPosts']);
Route::get('posts', [PostController::class, 'index']);

Route::get('posts/show/{id}', [PostController::class, 'show']);

Route::get('comments', [CommentController::class, 'index']);

Route::get('categories/{id}/posts', [CategoryController::class, 'posts']);

Route::get('/kvkk', [KvkkController::class, 'showkvkk']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('users/approve/{id}', [UserProfileController::class, 'approveUser']);
    Route::post('users/deactivate/{id}', [UserProfileController::class, 'deactivate']);

    Route::get('users/profile', [UserProfileController::class, 'show']);
    Route::post('users/profile/update', [UserProfileController::class, 'update']);
    Route::post('users/profile/delete', [UserProfileController::class, 'delete']);

    Route::post('logout', [UserController::class, 'logout']);

    Route::post('comments', [CommentController::class, 'store']);
    Route::put('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'delete']);
});
