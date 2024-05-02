<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Post routes
    Route::post('/store-post', [PostController::class, 'storePosts'])->name('store-post');
    Route::get('/get-posts', [PostController::class, 'getPosts'])->name('get-posts');
    Route::delete('/delete-post/{id}', [PostController::class, 'destroy'])->name('delete-post');
});

require __DIR__.'/auth.php';
