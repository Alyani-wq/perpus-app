<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Menampilkan daftar buku dan pencarian.
     */
    public function index(Request $request)
    {
        $books = Book::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('pengarang', 'like', "%{$search}%")
                    ->orWhere('kode_buku', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%");
            });
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Books/Index', [
            'books' => $books,
            'search' => $request->search,
        ]);
    }

    /**
     * Endpoint JSON untuk live search buku.
     */
    public function search(Request $request)
    {
        $kataKunci = $request->query('q', '');

        $books = Book::where(function ($query) use ($kataKunci) {
            $query->where('judul', 'like', "%{$kataKunci}%")
                ->orWhere('pengarang', 'like', "%{$kataKunci}%")
                ->orWhere('kode_buku', 'like', "%{$kataKunci}%")
                ->orWhere('penerbit', 'like', "%{$kataKunci}%");
        })
            ->latest()
            ->limit(10)
            ->get([
                'id',
                'kode_buku',
                'judul',
                'pengarang',
                'penerbit',
                'stok',
            ]);

        return response()->json($books);
    }

    /**
     * Endpoint JSON untuk memeriksa ketersediaan kode buku.
     */
    public function cekKode(Request $request)
    {
        $kodeBuku = trim($request->query('kode_buku', ''));

        // Kode kosong belum dianggap duplikat.
        if ($kodeBuku === '') {
            return response()->json([
                'tersedia' => true,
            ]);
        }

        $sudahDipakai = Book::where('kode_buku', $kodeBuku)->exists();

        return response()->json([
            'tersedia' => ! $sudahDipakai,
        ]);
    }

    /**
     * Menampilkan form tambah buku.
     */
    public function create()
    {
        return inertia('Books/Create');
    }

    /**
     * Menyimpan buku baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Validasi unique tetap dipertahankan.
            'kode_buku' => 'required|unique:books,kode_buku',
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'nullable|max:255',
            'tahun' => 'nullable|digits:4',
            'stok' => 'required|integer|min:0',
        ]);

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(string $id)
    {
        $book = Book::findOrFail($id);

        return inertia('Books/Show', [
            'book' => $book,
        ]);
    }

    /**
     * Menampilkan form edit buku.
     */
    public function edit(string $id)
    {
        $book = Book::findOrFail($id);

        return inertia('Books/Edit', [
            'book' => $book,
        ]);
    }

    /**
     * Memperbarui data buku.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'kode_buku' => 'required|unique:books,kode_buku,' . $book->id,
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'nullable|max:255',
            'tahun' => 'nullable|digits:4',
            'stok' => 'required|integer|min:0',
        ]);

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Menghapus data buku.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
