<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('comments/approve/{id}', [CommentController::class, 'approve'])->name('comments.approve');