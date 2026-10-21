<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

// Membuat form tambah buku
const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
});

// Mengirim data ke BookController@store
function submit() {
    form.post(route('books.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Tambah Buku</h1>

            <form
                @submit.prevent="submit"
                class="max-w-lg space-y-4"
            >
                <!-- Kode Buku -->
                <div>
                    <label class="block mb-1">Kode Buku</label>
                    <input
                        v-model="form.kode_buku"
                        type="text"
                        class="w-full border rounded p-2"
                        placeholder="Masukkan kode buku"
                    />
                    <div v-if="form.errors.kode_buku" class="text-red-500 text-sm">
                        {{ form.errors.kode_buku }}
                    </div>
                </div>

                <!-- Judul -->
                <div>
                    <label class="block mb-1">Judul Buku</label>
                    <input
                        v-model="form.judul"
                        type="text"
                        class="w-full border rounded p-2"
                        placeholder="Masukkan judul buku"
                    />
                    <div v-if="form.errors.judul" class="text-red-500 text-sm">
                        {{ form.errors.judul }}
                    </div>
                </div>

                <!-- Pengarang -->
                <div>
                    <label class="block mb-1">Pengarang</label>
                    <input
                        v-model="form.pengarang"
                        type="text"
                        class="w-full border rounded p-2"
                        placeholder="Masukkan nama pengarang"
                    />
                    <div v-if="form.errors.pengarang" class="text-red-500 text-sm">
                        {{ form.errors.pengarang }}
                    </div>
                </div>

                <!-- Penerbit -->
                <div>
                    <label class="block mb-1">Penerbit</label>
                    <input
                        v-model="form.penerbit"
                        type="text"
                        class="w-full border rounded p-2"
                        placeholder="Masukkan nama penerbit"
                    />
                    <div v-if="form.errors.penerbit" class="text-red-500 text-sm">
                        {{ form.errors.penerbit }}
                    </div>
                </div>

                <!-- Tahun -->
                <div>
                    <label class="block mb-1">Tahun Terbit</label>
                    <input
                        v-model="form.tahun"
                        type="number"
                        class="w-full border rounded p-2"
                        placeholder="Contoh: 2025"
                    />
                    <div v-if="form.errors.tahun" class="text-red-500 text-sm">
                        {{ form.errors.tahun }}
                    </div>
                </div>

                <!-- Stok -->
                <div>
                    <label class="block mb-1">Stok Buku</label>
                    <input
                        v-model="form.stok"
                        type="number"
                        min="0"
                        class="w-full border rounded p-2"
                    />
                    <div v-if="form.errors.stok" class="text-red-500 text-sm">
                        {{ form.errors.stok }}
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-600 text-white px-4 py-2 rounded"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Buku' }}
                    </button>

                    <Link
                        href="/books"
                        class="bg-gray-500 text-white px-4 py-2 rounded"
                    >
                        Kembali
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
