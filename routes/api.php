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
Route::get('categories/{slug}/posts', [CategoryController::class, 'posts']);
Route::get('categories/show/{slug}', [CategoryController::class, 'show']);

Route::get('posts', [PostController::class, 'index']);
Route::get('posts/related/{slug}', [PostController::class, 'relatedPosts']);
Route::get('post/{slug}', [PostController::class, 'show']);

Route::get('comments', [CommentController::class, 'index']);
Route::post('comments', [CommentController::class, 'store'])->middleware('checkAuth');

Route::get('/kvkk', [KvkkController::class, 'showkvkk']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('users/profile', [UserProfileController::class, 'show']);
    Route::post('users/profile/update', [UserProfileController::class, 'update']);
    Route::post('users/profile/delete', [UserProfileController::class, 'delete']);

    Route::post('logout', [UserController::class, 'logout']);

    Route::get('comments/approve/{id}', [CommentController::class, 'approve']);
    Route::post('comments/update/{id}', [CommentController::class, 'update']);
    Route::delete('comments/delete/{id}', [CommentController::class, 'delete']);
});
