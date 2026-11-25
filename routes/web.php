<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LoanController; 
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Halaman utama
Route::get('/', [HomeController::class, 'index']);

// Halaman Tentang
Route::get('/tentang', [HomeController::class, 'tentang']);

// Halaman Kontak
Route::get('/kontak', [HomeController::class, 'kontak']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']) 
->middleware(['auth', 'verified']) 
->name('dashboard'); 

// Profil pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// CRUD Buku dan Anggota: hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {

    // Route pencarian buku (JSON)
    // Harus ditulis sebelum Route::resource
    Route::get('/books/search', [BookController::class, 'search'])
        ->name('books.search');

    // Route validasi kode buku realtime
    Route::get('/books/cek-kode', [BookController::class, 'cekKode'])
        ->name('books.cek-kode');

    // Route pencarian anggota (JSON)
    Route::get('/members/search', [MemberController::class, 'search'])
        ->name('members.search');

    // Route validasi NIS/NIM realtime
    Route::get('/members/cek-nis', [MemberController::class, 'cekNis'])
        ->name('members.cek-nis');

    // CRUD Buku
    Route::resource('books', BookController::class);

    // CRUD Anggota
    Route::resource('members', MemberController::class);
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store']); 
    Route::patch('loans/{loan}/kembalikan', [LoanController::class, 'kembalikan']) ->name('loans.kembalikan');
});

// Autentikasi
require __DIR__.'/auth.php';
