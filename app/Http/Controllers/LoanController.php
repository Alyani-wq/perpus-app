<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    // Menampilkan data peminjaman
    public function index()
    {
        $loans = Loan::with(['member', 'book'])
            ->orderByDesc('id')
            ->paginate(10);

        return inertia('Loans/Index', [
            'loans' => $loans,
        ]);
    }

    // Menampilkan form peminjaman
    public function create()
    {
        $members = Member::orderBy('nama')
            ->get(['id', 'nama', 'nis_nim']);

        $books = Book::where('stok', '>', 0)
            ->orderBy('judul')
            ->get(['id', 'judul', 'kode_buku', 'stok']);

        return inertia('Loans/Create', [
            'members' => $members,
            'books' => $books,
        ]);
    }

    // Menyimpan peminjaman
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
        ], [
            'member_id.required' => 'Anggota wajib dipilih.',
            'member_id.exists' => 'Anggota yang dipilih tidak ditemukan.',
            'book_id.required' => 'Buku wajib dipilih.',
            'book_id.exists' => 'Buku yang dipilih tidak ditemukan.',
        ]);

        // Memeriksa jumlah buku yang masih dipinjam anggota
        $jumlahPinjamanAktif = Loan::where(
            'member_id',
            $validated['member_id']
        )
            ->where('status', 'dipinjam')
            ->count();

        // Maksimal 3 buku berstatus dipinjam untuk satu anggota
        if ($jumlahPinjamanAktif >= 3) {
            return back()
                ->withErrors([
                    'member_id' => 'Anggota sudah meminjam maksimal 3 buku. Kembalikan salah satu buku terlebih dahulu.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($validated) {
            $book = Book::where('id', $validated['book_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Memastikan stok buku tersedia
            if ($book->stok < 1) {
                throw ValidationException::withMessages([
                    'book_id' => 'Stok buku ini sudah habis. Pilih buku lain.',
                ]);
            }

            // Tanggal jatuh tempo adalah 14 hari setelah tanggal pinjam
            Loan::create([
                'member_id' => $validated['member_id'],
                'book_id' => $validated['book_id'],
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(14)->toDateString(),
                'status' => 'dipinjam',
            ]);

            // Mengurangi stok buku setelah peminjaman disimpan
            $book->decrement('stok');
        });

        return redirect()
            ->route('loans.index')
            ->with(
                'success',
                'Peminjaman berhasil disimpan. Jatuh tempo 14 hari setelah tanggal pinjam.'
            );
    }
}
