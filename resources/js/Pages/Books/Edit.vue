
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    book: Object,
});

const form = useForm({
    kode_buku: props.book.kode_buku ?? '',
    judul: props.book.judul ?? '',
    pengarang: props.book.pengarang ?? '',
    penerbit: props.book.penerbit ?? '',
    tahun: props.book.tahun ?? '',
    stok: props.book.stok ?? 0,
});

function submit() {
    form.put(route('books.update', props.book.id));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-purple-50 px-6 py-10">
            <div class="mx-auto max-w-2xl rounded-xl bg-white p-8 shadow-md">
                <h1 class="mb-6 text-2xl font-bold text-purple-800">
                    Edit Data Buku
                </h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="mb-1 block font-medium">Kode Buku</label>
                        <input
                            v-model="form.kode_buku"
                            type="text"
                            class="w-full rounded-lg border border-purple-200 px-3 py-2"
                        />
                        <div v-if="form.errors.kode_buku" class="mt-1 text-sm text-red-600">
                            {{ form.errors.kode_buku }}
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block font-medium">Judul Buku</label>
                        <input
                            v-model="form.judul"
                            type="text"
                            class="w-full rounded-lg border border-purple-200 px-3 py-2"
                        />
                        <div v-if="form.errors.judul" class="mt-1 text-sm text-red-600">
                            {{ form.errors.judul }}
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block font-medium">Pengarang</label>
                        <input
                            v-model="form.pengarang"
                            type="text"
                            class="w-full rounded-lg border border-purple-200 px-3 py-2"
                        />
                        <div v-if="form.errors.pengarang" class="mt-1 text-sm text-red-600">
                            {{ form.errors.pengarang }}
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block font-medium">Penerbit</label>
                        <input
                            v-model="form.penerbit"
                            type="text"
                            class="w-full rounded-lg border border-purple-200 px-3 py-2"
                        />
                        <div v-if="form.errors.penerbit" class="mt-1 text-sm text-red-600">
                            {{ form.errors.penerbit }}
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block font-medium">Tahun Terbit</label>
                        <input
                            v-model="form.tahun"
                            type="number"
                            class="w-full rounded-lg border border-purple-200 px-3 py-2"
                        />
                        <div v-if="form.errors.tahun" class="mt-1 text-sm text-red-600">
                            {{ form.errors.tahun }}
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block font-medium">Stok</label>
                        <input
                            v-model="form.stok"
                            type="number"
                            min="0"
                            class="w-full rounded-lg border border-purple-200 px-3 py-2"
                        />
                        <div v-if="form.errors.stok" class="mt-1 text-sm text-red-600">
                            {{ form.errors.stok }}
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-purple-600 px-5 py-2 text-white hover:bg-purple-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>

                        <Link
                            :href="route('books.index')"
                            class="rounded-lg bg-gray-200 px-5 py-2 text-gray-700 hover:bg-gray-300"
                        >
                            Kembali
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
