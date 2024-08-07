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
use App\Http\Controllers\TextContentController;
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

Route::get('/kvkk', [KvkkController::class, 'showkvkk']);

Route::get('text-contents/{type}', [TextContentController::class, 'show']);

Route::middleware('checkAuth')->group(function () {
    Route::get('users/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::post('users/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('users/profile/delete', [UserProfileController::class, 'delete'])->name('profile.delete');

    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('comments/approve/{id}', [CommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/update/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::post('comments/delete/{id}', [CommentController::class, 'delete'])->name('comments.delete');

    Route::post('logout', [UserController::class, 'logout'])->name('logout');
});
