<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import axios from 'axios'

const props = defineProps({
    books: Object,
    search: String,
})

const keyword = ref(props.search ?? '')
const hasilCari = ref(null)
const sedangMencari = ref(false)
const pesanError = ref('')

let timer = null
let permintaan = null

const daftarBuku = computed(() => {
    return hasilCari.value ?? props.books.data
})

// Mengirim permintaan pencarian ke server
async function ambilHasil() {
    if (permintaan) {
        permintaan.abort()
    }

    const controller = new AbortController()
    permintaan = controller

    sedangMencari.value = true
    pesanError.value = ''

    try {
        const respons = await axios.get(route('books.search'), {
            params: {
                q: keyword.value.trim(),
            },
            signal: controller.signal,
        })

        if (permintaan === controller) {
            hasilCari.value = respons.data
        }
    } catch (error) {
        if (
            error.code === 'ERR_CANCELED' ||
            axios.isCancel(error)
        ) {
            return
        }

        if (permintaan === controller) {
            pesanError.value =
                'Pencarian gagal. Muat ulang halaman, lalu coba lagi.'
        }
    } finally {
        if (permintaan === controller) {
            sedangMencari.value = false
            permintaan = null
        }
    }
}

// Debounce 800 milidetik
watch(keyword, (nilai) => {
    clearTimeout(timer)

    if (permintaan) {
        permintaan.abort()
        permintaan = null
    }

    sedangMencari.value = false
    pesanError.value = ''

    if (nilai.trim() === '') {
        hasilCari.value = null
        return
    }

    // Permintaan baru dikirim setelah pengguna berhenti mengetik 800 ms.
    timer = setTimeout(() => {
        ambilHasil()
    }, 800)
})

function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id), {
            onSuccess: () => {
                if (keyword.value.trim() !== '') {
                    ambilHasil()
                }
            },
        })
    }
}

onBeforeUnmount(() => {
    clearTimeout(timer)

    if (permintaan) {
        permintaan.abort()
    }
})
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Data Buku</h1>

            <div
                v-if="$page.props.flash?.success"
                class="my-3 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <div class="my-4 flex flex-wrap items-center gap-3">
                <Link
                    :href="route('books.create')"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    + Tambah Buku
                </Link>

                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Ketik judul, pengarang, kode buku, atau penerbit..."
                    class="min-w-64 rounded border px-3 py-2"
                >

                <span v-if="sedangMencari" class="text-gray-500">
                    Mencari...
                </span>
            </div>

            <p v-if="pesanError" class="mb-3 text-red-600">
                {{ pesanError }}
            </p>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-3 py-2">Kode</th>
                            <th class="border px-3 py-2">Judul</th>
                            <th class="border px-3 py-2">Pengarang</th>
                            <th class="border px-3 py-2">Penerbit</th>
                            <th class="border px-3 py-2">Stok</th>
                            <th class="border px-3 py-2">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="book in daftarBuku"
                            :key="book.id"
                        >
                            <td class="border px-3 py-2">
                                {{ book.kode_buku }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ book.judul }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ book.pengarang }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ book.penerbit || '-' }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ book.stok }}
                            </td>

                            <td class="border px-3 py-2">
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('books.edit', book.id)"
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

                        <tr v-if="daftarBuku.length === 0">
                            <td
                                colspan="6"
                                class="border px-3 py-4 text-center text-gray-500"
                            >
                                Tidak ada buku yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination ditampilkan saat menggunakan daftar bawaan -->
            <div
                v-if="hasilCari === null"
                class="mt-4 flex flex-wrap gap-2"
            >
                <template
                    v-for="link in books.links"
                    :key="link.label"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded border px-3 py-2"
                        :class="{
                            'bg-blue-600 text-white': link.active
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
