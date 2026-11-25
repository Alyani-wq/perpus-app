<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda.
     */
    public function index()
    {
        $judul = 'Sistem Informasi Peminjaman Buku Perpustakaan Mini';

        return view('home', [
            'judul' => $judul,
        ]);
    }

    /**
     * Menampilkan halaman tentang.
     */
    public function tentang()
    {
        return view('tentang');
    }

    /**
     * Menampilkan halaman kontak.
     */
    public function kontak()
    {
        return view('kontak');
    }
}
