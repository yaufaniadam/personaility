<script setup>
import { ref, watch } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    provinces: Object,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    gender: 'male',
    str_number: '',
    sip_number: '',
    province_code: '',
    city_code: '',
    address: '',
    specialization: '',
    contact_phone: '',
    contact_whatsapp: '',
    bio: '',
});

const cities = ref({});
const loadingCities = ref(false);

watch(() => form.province_code, async (newProvince) => {
    form.city_code = '';
    cities.value = {};
    if (newProvince) {
        loadingCities.value = true;
        try {
            const response = await axios.get(`/api/cities/${newProvince}`);
            cities.value = response.data;
        } catch (error) {
            console.error('Failed to fetch cities', error);
        } finally {
            loadingCities.value = false;
        }
    }
});

const submit = () => {
    form.post(route('psychologist.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Daftar sebagai Psikolog" />

        <div class="mb-4">
            <h1 class="text-2xl font-bold text-[#0d1b1a]">Daftar sebagai Psikolog</h1>
            <p class="text-sm text-slate-500">Bergabunglah dengan direktori kami dan bantu lebih banyak orang.</p>
        </div>

        <form @submit.prevent="submit" class="w-full space-y-6">
            <!-- Account Info -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#4c9a93]">Informasi Akun</h2>
                
                <div>
                    <InputLabel for="name" value="Nama Lengkap" />
                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div>
                        <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
                    </div>
                </div>
            </div>

            <!-- Professional Info -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#4c9a93]">Informasi Profesional</h2>

                <div>
                    <InputLabel value="Jenis Kelamin" />
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.gender" value="male" class="text-[#40D5C8] focus:ring-[#40D5C8]" />
                            <span class="text-sm">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.gender" value="female" class="text-[#40D5C8] focus:ring-[#40D5C8]" />
                            <span class="text-sm">Perempuan</span>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.gender" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="str_number" value="No. STR" />
                        <TextInput id="str_number" type="text" class="mt-1 block w-full" v-model="form.str_number" required />
                        <InputError class="mt-2" :message="form.errors.str_number" />
                    </div>
                    <div>
                        <InputLabel for="sip_number" value="No. SIP (Opsional)" />
                        <TextInput id="sip_number" type="text" class="mt-1 block w-full" v-model="form.sip_number" />
                    </div>
                </div>

                <div>
                    <InputLabel for="specialization" value="Spesialisasi" />
                    <TextInput id="specialization" type="text" class="mt-1 block w-full" v-model="form.specialization" placeholder="Contoh: Psikolog Klinis Dewasa" required />
                    <InputError class="mt-2" :message="form.errors.specialization" />
                </div>
            </div>

            <!-- Location -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#4c9a93]">Lokasi & Kontak</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="province" value="Provinsi" />
                        <select id="province" v-model="form.province_code" class="mt-1 block w-full border-slate-200 focus:border-[#40D5C8] focus:ring-[#40D5C8] rounded-2xl shadow-sm text-sm" required>
                            <option value="">Pilih Provinsi</option>
                            <option v-for="(name, code) in provinces" :key="code" :value="code">{{ name }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.province_code" />
                    </div>
                    <div>
                        <InputLabel for="city" value="Kota/Kabupaten" />
                        <select id="city" v-model="form.city_code" class="mt-1 block w-full border-slate-200 focus:border-[#40D5C8] focus:ring-[#40D5C8] rounded-2xl shadow-sm text-sm" :disabled="!form.province_code || loadingCities" required>
                            <option value="">{{ loadingCities ? 'Memuat...' : 'Pilih Kota' }}</option>
                            <option v-for="(name, code) in cities" :key="code" :value="code">{{ name }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.city_code" />
                    </div>
                </div>

                <div>
                    <InputLabel for="address" value="Alamat Lengkap" />
                    <textarea id="address" v-model="form.address" rows="2" class="mt-1 block w-full border-slate-200 focus:border-[#40D5C8] focus:ring-[#40D5C8] rounded-2xl shadow-sm text-sm" placeholder="Contoh: Jl. Merdeka No. 123, Kel. Suka Maju, Kec. Suka Senang"></textarea>
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="contact_phone" value="No. Telepon" />
                        <TextInput id="contact_phone" type="tel" class="mt-1 block w-full" v-model="form.contact_phone" required />
                        <InputError class="mt-2" :message="form.errors.contact_phone" />
                    </div>
                    <div>
                        <InputLabel for="contact_whatsapp" value="WhatsApp" />
                        <TextInput id="contact_whatsapp" type="tel" class="mt-1 block w-full" v-model="form.contact_whatsapp" />
                    </div>
                </div>

                <div>
                    <InputLabel for="bio" value="Bio Singkat" />
                    <textarea id="bio" v-model="form.bio" rows="3" class="mt-1 block w-full border-slate-200 focus:border-[#40D5C8] focus:ring-[#40D5C8] rounded-2xl shadow-sm text-sm"></textarea>
                    <InputError class="mt-2" :message="form.errors.bio" />
                </div>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-4 rounded-2xl !bg-[#40D5C8] !text-[#0d1b1a] font-bold shadow-lg shadow-[#40D5C8]/20 hover:scale-[1.02] active:scale-95 transition-all outline-none"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Daftar Sekarang
                </PrimaryButton>
            </div>

            <div class="text-center">
                <Link :href="route('login')" class="text-sm text-slate-500 hover:text-[#4c9a93]">
                    Sudah punya akun? Masuk
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
