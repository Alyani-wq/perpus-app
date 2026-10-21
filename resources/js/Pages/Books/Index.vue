
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    books: Object,
    search: String,
});

const keyword = ref(props.search ?? '');

function cari() {
    router.get('/books', {
        search: keyword.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">Data Buku</h1>

            <div
                v-if="$page.props.flash?.success"
                class="mb-4 rounded bg-green-100 p-3 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <form @submit.prevent="cari" class="mb-4 flex flex-wrap gap-2">
                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Cari judul atau pengarang buku..."
                    class="rounded border border-gray-300 px-3 py-2"
                />

                <button
                    type="submit"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    Cari
                </button>

                <Link
                    href="/books"
                    class="rounded bg-gray-500 px-4 py-2 text-white"
                >
                    Reset
                </Link>

                <Link
                    href="/books/create"
                    class="rounded bg-green-600 px-4 py-2 text-white"
                >
                    + Tambah Buku
                </Link>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-3">Kode</th>
                            <th class="border p-3">Judul</th>
                            <th class="border p-3">Pengarang</th>
                            <th class="border p-3">Stok</th>
                            <th class="border p-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="book in (props.books?.data ?? [])"
                            :key="book.id"
                        >
                            <td class="border p-3">{{ book.kode_buku }}</td>
                            <td class="border p-3">{{ book.judul }}</td>
                            <td class="border p-3">{{ book.pengarang }}</td>
                            <td class="border p-3">{{ book.stok }}</td>
                            <td class="border p-3">
                                <div class="flex gap-2">
                                    <Link
                                        :href="`/books/${book.id}/edit`"
                                        class="rounded bg-yellow-500 px-3 py-1 text-white"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="hapus(book.id)"
                                        class="rounded bg-red-600 px-3 py-1 text-white"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="(props.books?.data ?? []).length === 0">
                            <td colspan="5" class="border p-4 text-center">
                                Belum ada data buku.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="props.books?.links"
                class="mt-4 flex flex-wrap gap-2"
            >
                <template v-for="(link, index) in props.books.links" :key="index">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded border px-3 py-2"
                        :class="{
                            'bg-blue-600 text-white': link.active,
                            'text-gray-400': !link.active
                        }"
                    />
                    <span
                        v-else
                        v-html="link.label"
                        class="rounded border px-3 py-2 text-gray-400"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
