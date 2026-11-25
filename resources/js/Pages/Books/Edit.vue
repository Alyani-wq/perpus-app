<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    book: Object,
})

// Mengisi form dengan data buku yang akan diedit
const form = useForm({
    kode_buku: props.book.kode_buku ?? '',
    judul: props.book.judul ?? '',
    pengarang: props.book.pengarang ?? '',
    penerbit: props.book.penerbit ?? '',
    tahun: props.book.tahun ?? '',
    stok: props.book.stok ?? 0,
})

// Mengirim perubahan data buku
function submit() {
    form.put(route('books.update', props.book.id))
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Edit Buku</h1>

            <form
                @submit.prevent="submit"
                class="mt-3 space-y-3"
            >
                <div>
                    <label class="block">Kode Buku</label>
                    <input
                        v-model="form.kode_buku"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div
                        v-if="form.errors.kode_buku"
                        class="text-red-600"
                    >
                        {{ form.errors.kode_buku }}
                    </div>
                </div>

                <div>
                    <label class="block">Judul</label>
                    <input
                        v-model="form.judul"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div
                        v-if="form.errors.judul"
                        class="text-red-600"
                    >
                        {{ form.errors.judul }}
                    </div>
                </div>

                <div>
                    <label class="block">Pengarang</label>
                    <input
                        v-model="form.pengarang"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div
                        v-if="form.errors.pengarang"
                        class="text-red-600"
                    >
                        {{ form.errors.pengarang }}
                    </div>
                </div>

                <div>
                    <label class="block">Penerbit</label>
                    <input
                        v-model="form.penerbit"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div
                        v-if="form.errors.penerbit"
                        class="text-red-600"
                    >
                        {{ form.errors.penerbit }}
                    </div>
                </div>

                <div>
                    <label class="block">Tahun</label>
                    <input
                        v-model="form.tahun"
                        type="number"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div
                        v-if="form.errors.tahun"
                        class="text-red-600"
                    >
                        {{ form.errors.tahun }}
                    </div>
                </div>

                <div>
                    <label class="block">Stok</label>
                    <input
                        v-model="form.stok"
                        type="number"
                        min="0"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div
                        v-if="form.errors.stok"
                        class="text-red-600"
                    >
                        {{ form.errors.stok }}
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>

                    <Link
                        :href="route('books.index')"
                        class="rounded bg-gray-500 px-4 py-2 text-white"
                    >
                        Kembali
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
