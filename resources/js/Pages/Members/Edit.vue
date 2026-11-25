<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, watch, onBeforeUnmount } from 'vue'
import axios from 'axios'

const props = defineProps({
    member: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    _method: 'put',
    nama: props.member.nama ?? '',
    nis_nim: props.member.nis_nim ?? '',
    no_hp: props.member.no_hp ?? '',
    foto: null,
})

const pratinjau = ref(null)
const statusNis = ref('')

let timerNis = null
let permintaanNis = null
let alamatPratinjau = null

// Memilih foto baru dan membuat pratinjau
function pilihFoto(event) {
    const berkas = event.target.files[0]

    if (alamatPratinjau) {
        URL.revokeObjectURL(alamatPratinjau)
        alamatPratinjau = null
    }

    form.foto = berkas ?? null

    if (berkas) {
        alamatPratinjau = URL.createObjectURL(berkas)
        pratinjau.value = alamatPratinjau
    } else {
        pratinjau.value = null
    }
}

// Memeriksa NIS/NIM melalui server
async function periksaNis() {
    if (permintaanNis) {
        permintaanNis.abort()
    }

    const controller = new AbortController()
    permintaanNis = controller
    const nisYangDiperiksa = form.nis_nim.trim()

    statusNis.value = 'memeriksa'

    try {
        const respons = await axios.get(route('members.cek-nis'), {
            params: {
                nis_nim: nisYangDiperiksa,
                abaikan: props.member.id,
            },
            signal: controller.signal,
        })

        // Abaikan respons dari permintaan yang sudah tidak berlaku
        if (permintaanNis !== controller) return
        if (form.nis_nim.trim() !== nisYangDiperiksa) return

        statusNis.value = respons.data.tersedia
            ? 'tersedia'
            : 'terpakai'
    } catch (error) {
        if (
            error.code === 'ERR_CANCELED' ||
            axios.isCancel(error)
        ) {
            return
        }

        if (permintaanNis === controller) {
            statusNis.value = 'gagal'
        }
    } finally {
        if (permintaanNis === controller) {
            permintaanNis = null
        }
    }
}

// Validasi realtime saat NIS/NIM berubah
watch(
    () => form.nis_nim,
    (nilai) => {
        clearTimeout(timerNis)

        if (permintaanNis) {
            permintaanNis.abort()
            permintaanNis = null
        }

        if (!nilai || nilai.trim() === '') {
            statusNis.value = ''
            return
        }

        // Jika nilainya sama dengan NIS/NIM awal, tidak perlu diperiksa ulang
        if (nilai.trim() === String(props.member.nis_nim).trim()) {
            statusNis.value = ''
            return
        }

        statusNis.value = ''

        timerNis = setTimeout(() => {
            periksaNis()
        }, 500)
    }
)

// Membersihkan timer dan pratinjau ketika meninggalkan halaman
onBeforeUnmount(() => {
    clearTimeout(timerNis)

    if (permintaanNis) {
        permintaanNis.abort()
    }

    if (alamatPratinjau) {
        URL.revokeObjectURL(alamatPratinjau)
    }
})

// Menyimpan perubahan data anggota
function submit() {
    form.post(route('members.update', props.member.id), {
        forceFormData: true,
        onSuccess: () => {
            if (alamatPratinjau) {
                URL.revokeObjectURL(alamatPratinjau)
                alamatPratinjau = null
            }

            pratinjau.value = null
        },
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Edit Anggota</h1>
            <p class="mt-1 text-gray-600">
                Perbarui informasi anggota di bawah ini.
            </p>

            <form
                @submit.prevent="submit"
                class="mt-5 max-w-xl space-y-4"
            >
                <!-- Nama -->
                <div>
                    <label class="mb-1 block font-medium">
                        Nama
                    </label>

                    <input
                        v-model="form.nama"
                        type="text"
                        placeholder="Masukkan nama anggota"
                        class="w-full rounded border px-3 py-2"
                        required
                    >

                    <div
                        v-if="form.errors.nama"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.nama }}
                    </div>
                </div>

                <!-- NIS/NIM -->
                <div>
                    <label class="mb-1 block font-medium">
                        NIS/NIM
                    </label>

                    <input
                        v-model="form.nis_nim"
                        type="text"
                        placeholder="Masukkan NIS/NIM"
                        class="w-full rounded border px-3 py-2"
                        required
                    >

                    <div
                        v-if="statusNis === 'memeriksa'"
                        class="mt-1 text-sm text-gray-500"
                    >
                        Memeriksa NIS/NIM...
                    </div>

                    <div
                        v-else-if="statusNis === 'tersedia'"
                        class="mt-1 text-sm text-green-700"
                    >
                        NIS/NIM ini tersedia.
                    </div>

                    <div
                        v-else-if="statusNis === 'terpakai'"
                        class="mt-1 text-sm text-red-600"
                    >
                        NIS/NIM sudah digunakan anggota lain.
                    </div>

                    <div
                        v-else-if="statusNis === 'gagal'"
                        class="mt-1 text-sm text-yellow-700"
                    >
                        Pemeriksaan otomatis gagal. Server akan
                        memeriksa kembali saat data disimpan.
                    </div>

                    <div
                        v-if="form.errors.nis_nim"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.nis_nim }}
                    </div>
                </div>

                <!-- Nomor HP -->
                <div>
                    <label class="mb-1 block font-medium">
                        No. HP
                    </label>

                    <input
                        v-model="form.no_hp"
                        type="tel"
                        placeholder="Masukkan nomor HP"
                        class="w-full rounded border px-3 py-2"
                    >

                    <div
                        v-if="form.errors.no_hp"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.no_hp }}
                    </div>
                </div>

                <!-- Foto lama -->
                <div>
                    <label class="mb-2 block font-medium">
                        Foto Saat Ini
                    </label>

                    <img
                        v-if="member.foto"
                        :src="`/storage/${member.foto}`"
                        alt="Foto anggota saat ini"
                        class="h-32 w-32 rounded border object-cover"
                    >

                    <p v-else class="text-sm text-gray-500">
                        Anggota belum memiliki foto.
                    </p>
                </div>

                <!-- Foto baru -->
                <div>
                    <label class="mb-1 block font-medium">
                        Ganti Foto
                    </label>

                    <p class="mb-2 text-sm text-gray-500">
                        Kosongkan jika tidak ingin mengganti foto.
                        Format JPG/PNG, maksimal 1 MB.
                    </p>

                    <input
                        type="file"
                        accept="image/jpeg,image/png"
                        class="block w-full rounded border p-2"
                        @change="pilihFoto"
                    >

                    <div
                        v-if="form.errors.foto"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.foto }}
                    </div>

                    <div v-if="pratinjau" class="mt-3">
                        <p class="mb-2 text-sm text-gray-600">
                            Pratinjau foto baru:
                        </p>

                        <img
                            :src="pratinjau"
                            alt="Pratinjau foto baru"
                            class="h-32 w-32 rounded border object-cover"
                        >
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="
                            form.processing ||
                            statusNis === 'terpakai' ||
                            statusNis === 'memeriksa'
                        "
                        class="rounded bg-blue-600 px-5 py-2 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Perubahan'
                        }}
                    </button>

                    <a
                        :href="route('members.index')"
                        class="rounded border px-5 py-2 text-gray-700 hover:bg-gray-100"
                    >
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
