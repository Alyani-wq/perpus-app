<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'
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
let timerKode = null
let permintaanKode = null

async function periksaKode() {
    if (permintaanKode) {
        permintaanKode.abort()
    }

    const controller = new AbortController()
    permintaanKode = controller
    const kodeYangDiperiksa = form.kode_buku.trim()

    statusKode.value = 'memeriksa'

    try {
        const respons = await axios.get(route('books.cek-kode'), {
            params: {
                kode_buku: kodeYangDiperiksa,
            },
            signal: controller.signal,
        })

        if (permintaanKode !== controller) return
        if (form.kode_buku.trim() !== kodeYangDiperiksa) return

        statusKode.value = respons.data.tersedia
            ? 'tersedia'
            : 'terpakai'
    } catch (error) {
        if (
            error.code === 'ERR_CANCELED' ||
            axios.isCancel(error)
        ) {
            return
        }

        if (permintaanKode === controller) {
            statusKode.value = 'gagal'
        }
    } finally {
        if (permintaanKode === controller) {
            permintaanKode = null
        }
    }
}

// Memeriksa kode 500 ms setelah pengguna berhenti mengetik
watch(
    () => form.kode_buku,
    (nilai) => {
        clearTimeout(timerKode)

        if (permintaanKode) {
            permintaanKode.abort()
            permintaanKode = null
        }

        if (!nilai || nilai.trim() === '') {
            statusKode.value = ''
            return
        }

        statusKode.value = ''

        timerKode = setTimeout(() => {
            periksaKode()
        }, 500)
    }
)

onBeforeUnmount(() => {
    clearTimeout(timerKode)

    if (permintaanKode) {
        permintaanKode.abort()
    }
})

function submit() {
    // Validasi unique di BookController@store tetap wajib.
    form.post(route('books.store'))
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Tambah Buku</h1>

            <form
                @submit.prevent="submit"
                class="mt-5 max-w-xl space-y-4"
            >
                <div>
                    <label class="mb-1 block font-medium">
                        Kode Buku
                    </label>

                    <input
                        v-model="form.kode_buku"
                        type="text"
                        required
                        placeholder="Masukkan kode buku"
                        class="w-full rounded border px-3 py-2"
                    >

                    <p
                        v-if="statusKode === 'memeriksa'"
                        class="mt-1 text-sm text-gray-500"
                    >
                        Memeriksa kode buku...
                    </p>

                    <p
                        v-else-if="statusKode === 'tersedia'"
                        class="mt-1 text-sm text-green-700"
                    >
                        Kode buku tersedia.
                    </p>

                    <p
                        v-else-if="statusKode === 'terpakai'"
                        class="mt-1 text-sm text-red-600"
                    >
                        Kode buku sudah terdaftar. Gunakan kode lain.
                    </p>

                    <p
                        v-else-if="statusKode === 'gagal'"
                        class="mt-1 text-sm text-yellow-700"
                    >
                        Pemeriksaan gagal. Server tetap memvalidasi
                        kode saat data disimpan.
                    </p>

                    <p
                        v-if="form.errors.kode_buku"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.kode_buku }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Judul</label>
                    <input
                        v-model="form.judul"
                        type="text"
                        required
                        class="w-full rounded border px-3 py-2"
                    >
                    <p v-if="form.errors.judul" class="text-sm text-red-600">
                        {{ form.errors.judul }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Pengarang</label>
                    <input
                        v-model="form.pengarang"
                        type="text"
                        required
                        class="w-full rounded border px-3 py-2"
                    >
                    <p v-if="form.errors.pengarang" class="text-sm text-red-600">
                        {{ form.errors.pengarang }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Penerbit</label>
                    <input
                        v-model="form.penerbit"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                    >
                    <p v-if="form.errors.penerbit" class="text-sm text-red-600">
                        {{ form.errors.penerbit }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Tahun Terbit</label>
                    <input
                        v-model="form.tahun"
                        type="number"
                        min="1000"
                        max="9999"
                        class="w-full rounded border px-3 py-2"
                    >
                    <p v-if="form.errors.tahun" class="text-sm text-red-600">
                        {{ form.errors.tahun }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Stok</label>
                    <input
                        v-model.number="form.stok"
                        type="number"
                        min="0"
                        required
                        class="w-full rounded border px-3 py-2"
                    >
                    <p v-if="form.errors.stok" class="text-sm text-red-600">
                        {{ form.errors.stok }}
                    </p>
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="
                            form.processing ||
                            statusKode === 'terpakai' ||
                            statusKode === 'memeriksa'
                        "
                        class="rounded bg-blue-600 px-5 py-2 text-white disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>

                    <Link
                        :href="route('books.index')"
                        class="rounded border px-5 py-2"
                    >
                        Kembali
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
