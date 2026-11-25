<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, onBeforeUnmount } from 'vue';

const form = useForm({
    nama: '',
    nis_nim: '',
    no_hp: '',
    foto: null,
});

const pratinjau = ref(null);
const inputFoto = ref(null);

function pilihFoto(event) {
    const berkas = event.target.files?.[0] ?? null;

    if (pratinjau.value) {
        URL.revokeObjectURL(pratinjau.value);
    }

    form.foto = berkas;
    pratinjau.value = berkas ? URL.createObjectURL(berkas) : null;
}

function submit() {
    form.post(route('members.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            pratinjau.value && URL.revokeObjectURL(pratinjau.value);
            pratinjau.value = null;

            if (inputFoto.value) {
                inputFoto.value.value = '';
            }
        },
    });
}

onBeforeUnmount(() => {
    if (pratinjau.value) {
        URL.revokeObjectURL(pratinjau.value);
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-pink-50/50 px-4 py-8 sm:px-6">
            <div class="mx-auto max-w-3xl">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Tambah Anggota
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Silakan isi data anggota perpustakaan.
                    </p>
                </div>

                <div class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm sm:p-8">
                    <form
                        @submit.prevent="submit"
                        enctype="multipart/form-data"
                        class="space-y-5"
                    >
                        <!-- Nama -->
                        <div>
                            <label for="nama"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Nama Lengkap
                            </label>

                            <input
                                id="nama"
                                v-model="form.nama"
                                type="text"
                                required
                                maxlength="255"
                                placeholder="Masukkan nama lengkap"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                                :class="{ 'border-red-500': form.errors.nama }"
                            />

                            <p v-if="form.errors.nama"
                                class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama }}
                            </p>
                        </div>

                        <!-- NIS/NIM -->
                        <div>
                            <label for="nis_nim"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                NIS/NIM
                            </label>

                            <input
                                id="nis_nim"
                                v-model="form.nis_nim"
                                type="text"
                                required
                                maxlength="50"
                                placeholder="Masukkan NIS atau NIM"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                                :class="{ 'border-red-500': form.errors.nis_nim }"
                            />

                            <p v-if="form.errors.nis_nim"
                                class="mt-1 text-sm text-red-600">
                                {{ form.errors.nis_nim }}
                            </p>
                        </div>

                        <!-- Nomor HP -->
                        <div>
                            <label for="no_hp"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Nomor HP
                            </label>

                            <input
                                id="no_hp"
                                v-model="form.no_hp"
                                type="tel"
                                maxlength="20"
                                placeholder="Masukkan nomor HP"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                                :class="{ 'border-red-500': form.errors.no_hp }"
                            />

                            <p v-if="form.errors.no_hp"
                                class="mt-1 text-sm text-red-600">
                                {{ form.errors.no_hp }}
                            </p>
                        </div>

                        <!-- Foto -->
                        <div>
                            <label for="foto"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Foto Anggota
                            </label>

                            <input
                                id="foto"
                                ref="inputFoto"
                                type="file"
                                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                @change="pilihFoto"
                                class="block w-full cursor-pointer rounded-lg border border-gray-300 text-sm text-gray-600 file:mr-4 file:border-0 file:bg-pink-100 file:px-4 file:py-2.5 file:font-semibold file:text-pink-700 hover:file:bg-pink-200"
                                :class="{ 'border-red-500': form.errors.foto }"
                            />

                            <p class="mt-1 text-xs text-gray-500">
                                Format JPG atau PNG, maksimal 2 MB.
                            </p>

                            <p v-if="form.errors.foto"
                                class="mt-1 text-sm text-red-600">
                                {{ form.errors.foto }}
                            </p>

                            <div v-if="pratinjau" class="mt-4">
                                <p class="mb-2 text-sm font-medium text-gray-600">
                                    Pratinjau foto:
                                </p>

                                <img
                                    :src="pratinjau"
                                    alt="Pratinjau foto anggota"
                                    class="h-36 w-36 rounded-xl border border-pink-200 object-cover shadow-sm"
                                />
                            </div>
                        </div>

                        <!-- Pesan error umum -->
                        <div
                            v-if="form.hasErrors"
                            class="rounded-lg bg-red-50 p-3 text-sm text-red-600"
                        >
                            Data belum berhasil disimpan. Periksa kembali
                            kolom yang ditandai.
                        </div>

                        <!-- Tombol -->
                        <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-5">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-pink-500 px-6 py-2.5 font-semibold text-white shadow-sm transition hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>

                            <a
                                :href="route('members.index')"
                                class="rounded-lg border border-gray-300 px-6 py-2.5 font-semibold text-gray-600 transition hover:bg-gray-50"
                            >
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
