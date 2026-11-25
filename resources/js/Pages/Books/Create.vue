<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, watch, onBeforeUnmount } from 'vue'
import axios from 'axios'

const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
})

const statusKode = ref('')
const sedangCek = ref(false)

let timer = null
let permintaan = null

// Mengecek kode buku saat pengguna mengetik
watch(() => form.kode_buku, (nilai) => {
    clearTimeout(timer)

    if (permintaan) {
        permintaan.abort()
        permintaan = null
    }

    statusKode.value = ''
    sedangCek.value = false

    const kode = nilai.trim()

    if (!kode) {
        return
    }

    sedangCek.value = true

    timer = setTimeout(async () => {
        const controller = new AbortController()
        permintaan = controller

        try {
            const respons = await axios.get(
                route('books.cek-kode'),
                {
                    params: { kode_buku: kode },
                    signal: controller.signal,
                }
            )

            if (permintaan === controller) {
                statusKode.value = respons.data.tersedia
                    ? 'tersedia'
                    : 'dipakai'
            }
        } catch (error) {
            if (
                error.code !== 'ERR_CANCELED' &&
                !axios.isCancel(error) &&
                permintaan === controller
            ) {
                statusKode.value = 'gagal'
            }
        } finally {
            if (permintaan === controller) {
                sedangCek.value = false
                permintaan = null
            }
        }
    }, 500)
})

// Menyimpan buku baru
function submit() {
    form.post(route('books.store'))
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
            <h1 class="text-xl font-bold">Tambah Buku</h1>

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

                    <p
                        v-if="sedangCek"
                        class="text-gray-500"
                    >
                        Memeriksa kode buku...
                    </p>

                    <p
                        v-else-if="statusKode === 'tersedia'"
                        class="text-green-600"
                    >
                        Kode buku tersedia.
                    </p>

                    <p
                        v-else-if="statusKode === 'dipakai'"
                        class="text-red-600"
                    >
                        Kode buku sudah digunakan.
                    </p>

                    <p
                        v-else-if="statusKode === 'gagal'"
                        class="text-red-600"
                    >
                        Gagal memeriksa kode buku. Coba lagi.
                    </p>

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
                    <div v-if="form.errors.judul" class="text-red-600">
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
                    <div v-if="form.errors.pengarang" class="text-red-600">
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
                    <div v-if="form.errors.penerbit" class="text-red-600">
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
                    <div v-if="form.errors.tahun" class="text-red-600">
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
                    <div v-if="form.errors.stok" class="text-red-600">
                        {{ form.errors.stok }}
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
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
