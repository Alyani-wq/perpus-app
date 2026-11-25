<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    members: Object,
    search: String,
});

// Mengisi kotak pencarian dengan kata kunci sebelumnya
const keyword = ref(props.search ?? '');

// Melakukan pencarian anggota
function cari() {
    router.get(
        '/members',
        { search: keyword.value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}

// Menghapus data anggota
function hapus(id) {
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        router.delete(route('members.destroy', id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Data Anggota</h1>

            <!-- Pesan berhasil -->
            <div
                v-if="$page.props.flash?.success"
                class="my-2 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Tombol tambah dan pencarian -->
            <div class="my-3 flex flex-wrap items-center gap-3">
                <Link
                    href="/members/create"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    + Tambah Anggota
                </Link>

                <input
                    v-model="keyword"
                    @keyup.enter="cari"
                    type="text"
                    placeholder="Cari nama, NIS/NIM, atau nomor HP..."
                    class="rounded border border-gray-300 px-3 py-2"
                />

                <button
                    type="button"
                    @click="cari"
                    class="rounded bg-gray-700 px-4 py-2 text-white"
                >
                    Cari
                </button>
            </div>

            <!-- Tabel data anggota -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
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
                            v-for="member in members.data"
                            :key="member.id"
                        >
                            <td class="border px-3 py-2">
                                <img
                                    v-if="member.foto"
                                    :src="`/storage/${member.foto}`"
                                    alt="Foto anggota"
                                    class="h-16 w-16 rounded object-cover"
                                />
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
                                        class="text-blue-600 hover:underline"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="hapus(member.id)"
                                        class="text-red-600 hover:underline"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="members.data.length === 0">
                            <td
                                colspan="5"
                                class="border px-3 py-4 text-center"
                            >
                                Data anggota tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex flex-wrap gap-2">
                <template
                    v-for="(link, index) in members.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded border px-3 py-2"
                        :class="{
                            'bg-blue-600 text-white': link.active,
                            'text-gray-700': !link.active,
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
