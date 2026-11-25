<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    loans: Object,
})
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Data Peminjaman</h1>

            <!-- Pesan sukses -->
            <div
                v-if="$page.props.flash?.success"
                class="my-3 rounded bg-green-100 p-3 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Tombol tambah peminjaman -->
            <div class="my-4">
                <Link
                    :href="route('loans.create')"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    + Pinjam Buku
                </Link>
            </div>

            <!-- Tabel peminjaman -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-3 py-2">Anggota</th>
                            <th class="border px-3 py-2">Kode Buku</th>
                            <th class="border px-3 py-2">Buku</th>
                            <th class="border px-3 py-2">Tanggal Pinjam</th>
                            <th class="border px-3 py-2">Jatuh Tempo</th>
                            <th class="border px-3 py-2">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="loan in loans.data"
                            :key="loan.id"
                        >
                            <td class="border px-3 py-2">
                                {{ loan.member?.nama ?? 'Anggota tidak ditemukan' }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ loan.book?.kode_buku ?? '-' }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ loan.book?.judul ?? 'Buku tidak ditemukan' }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ loan.tanggal_pinjam }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ loan.tanggal_kembali_rencana }}
                            </td>

                            <td class="border px-3 py-2">
                                <span
                                    :class="
                                        loan.status === 'dipinjam'
                                            ? 'rounded bg-yellow-100 px-2 py-1 text-yellow-700'
                                            : 'rounded bg-green-100 px-2 py-1 text-green-700'
                                    "
                                >
                                    {{ loan.status }}
                                </span>
                            </td>
                        </tr>

                        <!-- Jika belum ada data -->
                        <tr v-if="loans.data.length === 0">
                            <td
                                colspan="6"
                                class="border px-3 py-4 text-center text-gray-500"
                            >
                                Belum ada data peminjaman.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex flex-wrap gap-2">
                <template
                    v-for="link in loans.links"
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