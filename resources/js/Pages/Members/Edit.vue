<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    member: Object,
});

const form = useForm({
    _method: 'put',
    nama: props.member.nama ?? '',
    nis_nim: props.member.nis_nim ?? '',
    no_hp: props.member.no_hp ?? '',
    foto: null,
});

const pratinjau = ref(null);

function pilihFoto(event) {
    const berkas = event.target.files[0];

    form.foto = berkas ?? null;

    if (pratinjau.value) {
        URL.revokeObjectURL(pratinjau.value);
    }

    pratinjau.value = berkas
        ? URL.createObjectURL(berkas)
        : null;
}

function submit() {
    form.post(route('members.update', props.member.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="mx-auto max-w-3xl p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    Edit Anggota
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi anggota perpustakaan.
                </p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Nama -->
                    <div>
                        <label class="mb-1 block font-medium text-gray-700">
                            Nama Lengkap
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >

                        <p v-if="form.errors.nama" class="mt-1 text-sm text-red-600">
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <!-- NIS/NIM -->
                    <div>
                        <label class="mb-1 block font-medium text-gray-700">
                            NIS/NIM
                        </label>

                        <input
                            v-model="form.nis_nim"
                            type="text"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >

                        <p v-if="form.errors.nis_nim" class="mt-1 text-sm text-red-600">
                            {{ form.errors.nis_nim }}
                        </p>
                    </div>

                    <!-- Nomor HP -->
                    <div>
                        <label class="mb-1 block font-medium text-gray-700">
                            Nomor HP
                        </label>

                        <input
                            v-model="form.no_hp"
                            type="text"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        <p v-if="form.errors.no_hp" class="mt-1 text-sm text-red-600">
                            {{ form.errors.no_hp }}
                        </p>
                    </div>

                    <!-- Foto saat ini -->
                    <div>
                        <label class="mb-2 block font-medium text-gray-700">
                            Foto Saat Ini
                        </label>

                        <img
                            v-if="member.foto"
                            :src="`/storage/${member.foto}`"
                            alt="Foto anggota saat ini"
                            class="h-32 w-32 rounded-lg border object-cover"
                        >

                        <p v-else class="text-sm text-gray-500">
                            Belum ada foto.
                        </p>
                    </div>

                    <!-- Foto baru -->
                    <div>
                        <label class="mb-2 block font-medium text-gray-700">
                            Ganti Foto
                        </label>

                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/jpg"
                            @change="pilihFoto"
                            class="block w-full text-sm text-gray-600"
                        >

                        <p class="mt-1 text-xs text-gray-500">
                            Format JPG, JPEG, atau PNG. Maksimal 2 MB.
                            Kosongkan jika tidak ingin mengganti foto.
                        </p>

                        <p v-if="form.errors.foto" class="mt-1 text-sm text-red-600">
                            {{ form.errors.foto }}
                        </p>

                        <div v-if="pratinjau" class="mt-3">
                            <p class="mb-2 text-sm font-medium text-gray-700">
                                Pratinjau Foto Baru
                            </p>

                            <img
                                :src="pratinjau"
                                alt="Pratinjau foto baru"
                                class="h-32 w-32 rounded-lg border object-cover"
                            >
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="flex gap-3 border-t pt-5">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-indigo-600 px-5 py-2.5 font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>

                        <Link
                            :href="route('members.index')"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
