<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

defineProps({
    members: Array,
    books: Array,
})

const form = useForm({
    member_id: '',
    book_id: '',
})

function submit() {
    form.post(route('loans.store'))
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Pinjam Buku</h1>

            <form
                @submit.prevent="submit"
                class="mt-4 max-w-xl space-y-4"
            >
                <!-- Pilihan anggota -->
                <div>
                    <label class="mb-1 block">Anggota</label>

                    <select
                        v-model="form.member_id"
                        class="w-full rounded border px-3 py-2"
                    >
                        <option value="" disabled>
                            -- Pilih anggota --
                        </option>

                        <option
                            v-for="member in members"
                            :key="member.id"
                            :value="member.id"
                        >
                            {{ member.nama }} ({{ member.nis_nim }})
                        </option>
                    </select>

                    <p
                        v-if="form.errors.member_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.member_id }}
                    </p>

                    <p
                        v-if="members.length === 0"
                        class="mt-1 text-sm text-yellow-700"
                    >
                        Belum ada data anggota. Tambahkan anggota terlebih dahulu.
                    </p>
                </div>

                <!-- Pilihan buku -->
                <div>
                    <label class="mb-1 block">Buku</label>

                    <select
                        v-model="form.book_id"
                        class="w-full rounded border px-3 py-2"
                    >
                        <option value="" disabled>
                            -- Pilih buku --
                        </option>

                        <option
                            v-for="book in books"
                            :key="book.id"
                            :value="book.id"
                        >
                            {{ book.judul }} (Stok: {{ book.stok }})
                        </option>
                    </select>

                    <p
                        v-if="form.errors.book_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.book_id }}
                    </p>

                    <p
                        v-if="books.length === 0"
                        class="mt-1 text-sm text-yellow-700"
                    >
                        Tidak ada buku dengan stok tersedia.
                    </p>
                </div>

                <!-- Informasi tanggal -->
                <div class="rounded bg-gray-100 p-3 text-sm text-gray-600">
                    Tanggal pinjam diisi otomatis dengan tanggal hari ini.
                    Jatuh tempo adalah 14 hari setelah tanggal pinjam.
                    Maksimal setiap anggota meminjam 3 buku yang masih berstatus dipinjam.
                </div>

                <!-- Tombol -->
                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="
                            form.processing ||
                            members.length === 0 ||
                            books.length === 0
                        "
                        class="rounded bg-blue-600 px-4 py-2 text-white disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Peminjaman'
                        }}
                    </button>

                    <Link
                        :href="route('loans.index')"
                        class="rounded bg-gray-500 px-4 py-2 text-white"
                    >
                        Kembali
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>