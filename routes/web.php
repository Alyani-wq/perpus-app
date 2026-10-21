<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Halaman utama
Route::get('/', [HomeController::class, 'index']);

// Halaman Tentang
Route::get('/tentang', [HomeController::class, 'tentang']);

// Halaman Kontak
Route::get('/kontak', [HomeController::class, 'kontak']);

// Dashboard
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profil pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// CRUD Buku: hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {
    Route::resource('books', BookController::class);
});

// Autentikasi
require __DIR__.'/auth.php';