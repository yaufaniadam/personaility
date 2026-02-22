<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { UserCircleIcon, KeyIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Profil Pengguna" />

    <AuthenticatedLayout>
        <div class="max-w-md mx-auto px-4 py-6 space-y-6">
            <!-- Header section -->
            <div>
                <h1 class="text-2xl font-extrabold text-[#0d1b1a]">Profil Saya</h1>
                <p class="text-slate-500 text-sm mt-1">Kelola data diri & keamanan akunmu.</p>
            </div>

            <!-- Profile Info Form -->
            <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <UserCircleIcon class="w-6 h-6 text-[#4c9a93]" />
                    <h2 class="font-bold text-[#0d1b1a]">Informasi Akun</h2>
                </div>
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <!-- Password Form -->
            <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <KeyIcon class="w-6 h-6 text-amber-500" />
                    <h2 class="font-bold text-[#0d1b1a]">Ubah Password</h2>
                </div>
                <UpdatePasswordForm />
            </div>

            <!-- Delete Auth Form -->
            <div class="bg-red-50 rounded-3xl shadow-sm p-6 border border-red-100">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-red-200">
                    <ExclamationTriangleIcon class="w-6 h-6 text-red-500" />
                    <h2 class="font-bold text-red-900">Hapus Akun</h2>
                </div>
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
