<script setup>
// Mengambil layout dashboard yang sudah dibuat
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

// Menerima data buku dari BookController
defineProps({
    books: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Data Buku</h1>

            <!-- Link menuju form tambah buku -->
            <Link
                href="/books/create"
                class="bg-blue-500 text-white px-4 py-2 rounded inline-block mb-4"
            >
                + Tambah Buku
            </Link>

            <!-- Tabel data buku -->
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2">Kode</th>
                        <th class="border border-gray-300 p-2">Judul</th>
                        <th class="border border-gray-300 p-2">Pengarang</th>
                        <th class="border border-gray-300 p-2">Tahun</th>
                        <th class="border border-gray-300 p-2">Stok</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="book in books.data"
                        :key="book.id"
                    >
                        <td class="border border-gray-300 p-2">
                            {{ book.kode_buku }}
                        </td>
                        <td class="border border-gray-300 p-2">
                            {{ book.judul }}
                        </td>
                        <td class="border border-gray-300 p-2">
                            {{ book.pengarang }}
                        </td>
                        <td class="border border-gray-300 p-2">
                            {{ book.tahun ?? '-' }}
                        </td>
                        <td class="border border-gray-300 p-2">
                            {{ book.stok }}
                        </td>
                    </tr>

                    <tr v-if="books.data.length === 0">
                        <td
                            colspan="5"
                            class="border border-gray-300 p-4 text-center"
                        >
                            Belum ada data buku.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
