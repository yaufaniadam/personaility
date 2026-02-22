<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { 
  UserCircleIcon,
  CameraIcon,
  CheckBadgeIcon,
  MapPinIcon
} from '@heroicons/vue/24/outline';
import axios from 'axios';

const props = defineProps({
    psychologist: Object,
    provinces: Object,
    cities: Object,
});

const page = usePage();

const form = useForm({
    _method: 'POST', // For file uploads via POST
    name: props.psychologist.name,
    gender: props.psychologist.gender,
    str_number: props.psychologist.str_number,
    sip_number: props.psychologist.sip_number,
    province_code: props.psychologist.province_code,
    city_code: props.psychologist.city_code,
    address: props.psychologist.address || '',
    specialization: props.psychologist.specialization,
    contact_phone: props.psychologist.contact_phone,
    contact_whatsapp: props.psychologist.contact_whatsapp,
    website: props.psychologist.website || '',
    bio: props.psychologist.bio || '',
    photo: null,
});

const cityOptions = ref(props.cities);
const loadingCities = ref(false);

watch(() => form.province_code, async (newProvince) => {
    if (newProvince && newProvince !== props.psychologist.province_code) {
        form.city_code = '';
        cityOptions.value = {};
        loadingCities.value = true;
        try {
            const response = await axios.get(`/api/cities/${newProvince}`);
            cityOptions.value = response.data;
        } catch (error) {
            console.error('Failed to fetch cities', error);
        } finally {
            loadingCities.value = false;
        }
    } else {
        cityOptions.value = props.cities;
    }
});

const photoPreview = ref(null);
const photoInput = ref(null);

const onPhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.photo = file;
        photoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('psychologist.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            form.photo = null;
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Psychologist Dashboard" />

        <div class="max-w-2xl mx-auto px-4 py-8 pb-32">
            <!-- Header Card -->
            <div class="bg-white rounded-3xl shadow-sm p-6 mb-6">
                <div class="flex items-center gap-4">
                    <div class="relative group">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-100 flex items-center justify-center border-2 border-white shadow-sm">
                            <img v-if="photoPreview || psychologist.avatar_url" 
                                 :src="photoPreview || psychologist.avatar_url" 
                                 class="w-full h-full object-cover" />
                            <UserCircleIcon v-else class="w-12 h-12 text-slate-300" />
                        </div>
                        <button @click="$refs.photoInput.click()" class="absolute -bottom-2 -right-2 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-[#4c9a93] hover:text-[#0d1b1a] transition-colors">
                            <CameraIcon class="w-5 h-5" />
                        </button>
                        <input type="file" ref="photoInput" class="hidden" @change="onPhotoChange" accept="image/*" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-bold text-[#0d1b1a]">{{ psychologist.name }}</h1>
                            <span v-if="psychologist.verified_status" class="text-emerald-600">
                                <CheckBadgeIcon class="w-5 h-5" />
                            </span>
                        </div>
                        <p class="text-sm text-[#4c9a93] font-medium">{{ psychologist.specialization }}</p>
                        <div class="flex items-center gap-1 mt-1 text-xs text-slate-500">
                            <MapPinIcon class="w-3 h-3" />
                            {{ psychologist.city }}, {{ psychologist.province }}
                        </div>
                    </div>
                </div>
                
                <div v-if="!psychologist.verified_status" class="mt-6 p-4 bg-amber-50 border border-amber-100 rounded-2xl flex gap-3">
                    <div class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 shrink-0"></div>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        Akun Anda sedang menunggu verifikasi admin. Selama masa tunggu, profil Anda mungkin belum muncul di direktori publik dengan label "Terverifikasi".
                    </p>
                </div>
            </div>

            <!-- Edit Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white rounded-3xl shadow-sm p-6 space-y-6">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-[#4c9a93]">Update Profil Publik</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="name" value="Nama Lengkap" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

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
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="str_number" value="No. STR" />
                            <TextInput id="str_number" type="text" class="mt-1 block w-full" v-model="form.str_number" required />
                            <InputError class="mt-2" :message="form.errors.str_number" />
                        </div>
                        <div>
                            <InputLabel for="sip_number" value="No. SIP" />
                            <TextInput id="sip_number" type="text" class="mt-1 block w-full" v-model="form.sip_number" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="specialization" value="Spesialisasi" />
                        <TextInput id="specialization" type="text" class="mt-1 block w-full" v-model="form.specialization" required />
                        <InputError class="mt-2" :message="form.errors.specialization" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="province" value="Provinsi" />
                            <select id="province" v-model="form.province_code" class="mt-1 block w-full border-slate-200 focus:border-[#40D5C8] focus:ring-[#40D5C8] rounded-2xl shadow-sm text-sm">
                                <option v-for="(name, code) in provinces" :key="code" :value="code">{{ name }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="city" value="Kota/Kabupaten" />
                            <select id="city" v-model="form.city_code" class="mt-1 block w-full border-slate-200 focus:border-[#40D5C8] focus:ring-[#40D5C8] rounded-2xl shadow-sm text-sm" :disabled="loadingCities">
                                <option v-for="(name, code) in cityOptions" :key="code" :value="code">{{ name }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <InputLabel for="address" value="Alamat Lengkap" />
                        <textarea id="address" v-model="form.address" rows="2" class="mt-1 block w-full border-slate-200 focus:border-[#40D5C8] focus:ring-[#40D5C8] rounded-2xl shadow-sm text-sm"></textarea>
                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <InputLabel for="contact_phone" value="No. Telepon" />
                            <TextInput id="contact_phone" type="tel" class="mt-1 block w-full" v-model="form.contact_phone" required />
                        </div>
                        <div>
                            <InputLabel for="contact_whatsapp" value="WhatsApp" />
                            <TextInput id="contact_whatsapp" type="tel" class="mt-1 block w-full" v-model="form.contact_whatsapp" />
                        </div>
                        <div>
                            <InputLabel for="website" value="Website" />
                            <TextInput id="website" type="text" class="mt-1 block w-full" v-model="form.website" placeholder="https://..." />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="bio" value="Bio Singkat" />
                        <textarea id="bio" v-model="form.bio" rows="4" class="mt-1 block w-full border-slate-200 focus:border-[#40D5C8] focus:ring-[#40D5C8] rounded-2xl shadow-sm text-sm"></textarea>
                    </div>

                    <div class="pt-4">
                        <PrimaryButton
                            class="w-full justify-center py-4 rounded-2xl !bg-[#40D5C8] !text-[#0d1b1a] font-bold shadow-lg shadow-[#40D5C8]/20 hover:scale-[1.01] active:scale-95 transition-all outline-none"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Perubahan</span>
                        </PrimaryButton>
                        <p v-if="form.recentlySuccessful" class="text-sm text-emerald-600 text-center mt-3 font-medium">Profil berhasil diperbarui!</p>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
