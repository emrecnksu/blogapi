<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('comments/approve/{id}', [CommentController::class, 'approve'])->name('comments.approve');
});