<script setup>
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-4 text-red-900">
        <p class="text-sm leading-relaxed mb-4">
            Aksi ini akan menghapus akun dan seluruh data assessment kamu secara permanen. Hal ini tidak bisa dibatalkan.
        </p>

        <button
            @click="confirmUserDeletion"
            class="bg-red-500 text-white font-bold py-3 px-6 rounded-xl hover:bg-red-600 transition-colors shadow-md shadow-red-500/20 active:scale-95 text-sm"
        >
            Hapus Akun Permanen
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-8">
                <h2 class="text-xl font-bold text-red-600 mb-2">
                    Apakah kamu yakin ingin menghapus akun?
                </h2>
                <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                    Setelah dihapus, seluruh data dan riwayat tidak akan bisa dipulihkan. Masukkan passwordmu untuk konfirmasi.
                </p>

                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="w-full rounded-xl border border-slate-200 bg-[#f6f8f8] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:bg-white transition-all"
                        placeholder="Masukkan passwordmu..."
                        @keyup.enter="deleteUser"
                    />
                    <p v-if="form.errors.password" class="mt-2 text-xs text-red-600">{{ form.errors.password }}</p>
                </div>

                <div class="mt-8 flex gap-3">
                    <button
                        @click="closeModal"
                        class="flex-1 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors text-sm"
                    >
                        Batal
                    </button>
                    <button
                        :disabled="form.processing"
                        @click="deleteUser"
                        class="flex-1 py-3 rounded-xl bg-red-500 text-white font-bold shadow-md hover:bg-red-600 transition-colors active:scale-95 text-sm disabled:opacity-50"
                    >
                        Ya, Hapus Akun
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
