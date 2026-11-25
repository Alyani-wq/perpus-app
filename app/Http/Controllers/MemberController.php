<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    // Menampilkan daftar anggota dan melakukan pencarian
    public function index(Request $request)
    {
        $members = Member::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis_nim', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Members/Index', [
            'members' => $members,
            'search' => $request->search,
        ]);
    }

    // Menampilkan halaman tambah anggota
    public function create()
    {
        return inertia('Members/Create');
    }

    // Menyimpan anggota baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis_nim' => 'required|string|max:50|unique:members,nis_nim',
            'no_hp' => 'nullable|digits_between:8,15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        // Menyimpan foto ke storage/app/public/members
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')
                ->store('members', 'public');
        }

        Member::create($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    // Menampilkan detail anggota
    public function show(Member $member)
    {
        return inertia('Members/Show', [
            'member' => $member,
        ]);
    }

    // Menampilkan halaman edit anggota
    public function edit(Member $member)
    {
        return inertia('Members/Edit', [
            'member' => $member,
        ]);
    }

    // Memperbarui data anggota
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis_nim' => 'required|string|max:50|unique:members,nis_nim,' . $member->id,
            'no_hp' => 'nullable|digits_between:8,15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($member->foto) {
                Storage::disk('public')->delete($member->foto);
            }

            // Simpan foto baru
            $validated['foto'] = $request->file('foto')
                ->store('members', 'public');
        } else {
            // Pertahankan foto lama jika tidak ada foto baru
            unset($validated['foto']);
        }

        $member->update($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    // Menghapus anggota
    public function destroy(Member $member)
    {
        // Hapus foto anggota dari penyimpanan
        if ($member->foto) {
            Storage::disk('public')->delete($member->foto);
        }

        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
