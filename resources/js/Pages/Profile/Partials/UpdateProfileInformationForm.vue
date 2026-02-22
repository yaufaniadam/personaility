<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({ mustVerifyEmail: Boolean, status: String });

const user = usePage().props.auth.user;
const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-4">
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                <input
                    id="name"
                    type="text"
                    class="w-full rounded-xl border border-slate-200 bg-[#f6f8f8] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#40D5C8] focus:bg-white transition-all"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <p v-if="form.errors.name" class="mt-2 text-xs text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                <input
                    id="email"
                    type="email"
                    class="w-full rounded-xl border border-slate-200 bg-[#f6f8f8] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#40D5C8] focus:bg-white transition-all"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <p v-if="form.errors.email" class="mt-2 text-xs text-red-600">{{ form.errors.email }}</p>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-amber-600">
                    Email kamu belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline font-semibold hover:text-amber-800 focus:outline-none"
                    >
                        Kirim ulang email
                    </Link>
                </p>
                <div v-show="status === 'verification-link-sent'" class="mt-2 text-xs font-medium text-emerald-600">
                    Link verifikasi baru telah dikirimkan ke emailmu.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-[#40D5C8] text-[#0d1b1a] px-6 py-3 rounded-xl font-bold shadow-md shadow-[#40D5C8]/20 hover:scale-[1.02] transition-transform text-sm disabled:opacity-50"
                >
                    Simpan Profil
                </button>
                <Transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0" leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm font-semibold text-[#4c9a93]">Berhasil disimpan!</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
