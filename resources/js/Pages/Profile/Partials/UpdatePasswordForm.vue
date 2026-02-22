<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <form @submit.prevent="updatePassword" class="space-y-4">
            <div>
                <label for="current_password" class="block text-xs font-bold text-slate-700 mb-1">Password Saat Ini</label>
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="w-full rounded-xl border border-slate-200 bg-[#f6f8f8] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition-all"
                    autocomplete="current-password"
                />
                <p v-if="form.errors.current_password" class="mt-2 text-xs text-red-600">{{ form.errors.current_password }}</p>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password Baru</label>
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="w-full rounded-xl border border-slate-200 bg-[#f6f8f8] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition-all"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password" class="mt-2 text-xs text-red-600">{{ form.errors.password }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="w-full rounded-xl border border-slate-200 bg-[#f6f8f8] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition-all"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password_confirmation" class="mt-2 text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-amber-400 text-amber-950 px-6 py-3 rounded-xl font-bold hover:scale-[1.02] transition-transform shadow-md shadow-amber-400/20 text-sm disabled:opacity-50"
                >
                    Ubah Password
                </button>
                <Transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0" leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm font-semibold text-amber-600">Berhasil diubah!</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
