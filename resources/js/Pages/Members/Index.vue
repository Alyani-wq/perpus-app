<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import axios from 'axios'

const props = defineProps({
    members: Object,
    search: String,
})

const keyword = ref(props.search ?? '')
const hasilCari = ref(null)
const sedangMencari = ref(false)
const pesanError = ref('')

let timer = null
let permintaan = null

const daftarAnggota = computed(() => {
    return hasilCari.value ?? props.members.data ?? []
})

async function ambilHasil() {
    if (permintaan) {
        permintaan.abort()
    }

    const controller = new AbortController()
    permintaan = controller

    sedangMencari.value = true
    pesanError.value = ''

    try {
        const respons = await axios.get(route('members.search'), {
            params: {
                q: keyword.value.trim(),
            },
            signal: controller.signal,
        })

        // Abaikan hasil permintaan lama.
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

    timer = setTimeout(() => {
        ambilHasil()
    }, 300)
})

function hapus(id) {
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        router.delete(route('members.destroy', id), {
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
            <h1 class="text-xl font-bold">
                Data Anggota
            </h1>

            <div
                v-if="$page.props.flash?.success"
                class="my-2 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <div class="my-3 flex items-center gap-3">
                <Link
                    :href="route('members.create')"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    + Tambah Anggota
                </Link>

                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Ketik nama, NIS/NIM, atau nomor HP..."
                    class="rounded border px-3 py-2"
                >

                <span
                    v-if="sedangMencari"
                    class="text-gray-500"
                >
                    Mencari...
                </span>
            </div>

            <div
                v-if="pesanError"
                class="my-2 text-red-600"
            >
                {{ pesanError }}
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-3 py-2">Foto</th>
                            <th class="border px-3 py-2">NIS/NIM</th>
                            <th class="border px-3 py-2">Nama</th>
                            <th class="border px-3 py-2">No. HP</th>
                            <th class="border px-3 py-2">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="member in daftarAnggota"
                            :key="member.id"
                        >
                            <td class="border px-3 py-2">
                                <img
                                    v-if="member.foto"
                                    :src="`/storage/${member.foto}`"
                                    alt="Foto anggota"
                                    class="h-16 w-16 rounded object-cover"
                                >

                                <span v-else>-</span>
                            </td>

                            <td class="border px-3 py-2">
                                {{ member.nis_nim }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ member.nama }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ member.no_hp || '-' }}
                            </td>

                            <td class="border px-3 py-2">
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('members.edit', member.id)"
                                        class="rounded bg-yellow-500 px-3 py-1 text-white"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="hapus(member.id)"
                                        class="rounded bg-red-600 px-3 py-1 text-white"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="daftarAnggota.length === 0">
                            <td
                                colspan="5"
                                class="border px-3 py-4 text-center text-gray-500"
                            >
                                {{
                                    keyword.trim() !== ''
                                        ? 'Tidak ada anggota yang ditemukan.'
                                        : 'Belum ada data anggota.'
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="hasilCari === null"
                class="mt-4 flex flex-wrap gap-2"
            >
                <template
                    v-for="link in members.links"
                    :key="link.label"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded border px-3 py-2"
                        :class="{
                            'bg-blue-600 text-white': link.active,
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
