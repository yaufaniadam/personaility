<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import {
  ClipboardDocumentListIcon,
  AcademicCapIcon,
  ClockIcon,
  ShieldCheckIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const page = usePage();
const isAuthenticated = computed(() => page.props.auth.user !== null);

const form = useForm({
    guest_name: '',
    guest_age_range: '',
    guest_gender: '',
});

const submit = () => {
    form.post(route('assessment.start'));
};
</script>

<template>
  <Head title="Persetujuan Assessment" />
  <div class="font-sans antialiased text-slate-900 bg-[#f6f8f8]">
    <!-- Minimal Header -->
    <nav class="sticky top-0 z-50 flex items-center justify-center bg-[#f6f8f8]/80 backdrop-blur-md px-6 py-4 border-b border-[#4c9a93]/10">
      <Link :href="route('home')" class="flex items-center gap-2">
        <img src="/logo.png" alt="Personaility Logo" class="h-8 w-auto object-contain shrink-0" />
      </Link>
    </nav>

    <div class="min-h-screen py-10 px-4">
      <div class="max-w-lg mx-auto">

        <!-- Header -->
        <div class="text-center mb-10">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#40D5C8]/20 mb-4">
            <ClipboardDocumentListIcon class="w-8 h-8 text-[#4c9a93]" />
          </div>
          <h1 class="text-2xl font-extrabold text-[#0d1b1a]">Sebelum Memulai</h1>
          <p class="text-slate-500 mt-2 text-sm leading-relaxed">Bacalah informasi berikut sebelum mengikuti assessment kepribadian.</p>
        </div>

        <!-- Consent Card -->
        <div class="bg-white rounded-3xl shadow-sm p-6 space-y-6 mb-6">

          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-[#40D5C8]/20 flex items-center justify-center">
              <AcademicCapIcon class="w-5 h-5 text-[#4c9a93]" />
            </div>
            <div>
              <h3 class="font-bold text-sm text-[#0d1b1a]">Model Big Five (OCEAN)</h3>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">Assessment ini menggunakan model kepribadian ilmiah Big Five yang telah teruji secara akademis.</p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-[#40D5C8]/20 flex items-center justify-center">
              <ClockIcon class="w-5 h-5 text-[#4c9a93]" />
            </div>
            <div>
              <h3 class="font-bold text-sm text-[#0d1b1a]">Durasi ~5 Menit</h3>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">Terdiri dari 30 pertanyaan. Jawab dengan jujur sesuai kondisi saat ini.</p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-[#40D5C8]/20 flex items-center justify-center">
              <ShieldCheckIcon class="w-5 h-5 text-[#4c9a93]" />
            </div>
            <div>
              <h3 class="font-bold text-sm text-[#0d1b1a]">Data Terlindungi</h3>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">Jawaban dan hasilmu hanya dapat dilihat oleh kamu sendiri.</p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
              <ExclamationTriangleIcon class="w-5 h-5 text-amber-500" />
            </div>
            <div>
              <h3 class="font-bold text-sm text-[#0d1b1a]">Bukan Alat Diagnostik</h3>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Platform ini <strong>bukan penyedia layanan kesehatan mental</strong>. Hasil tidak mendiagnosis kondisi psikologis apapun dan tidak menggantikan konsultasi profesional.
              </p>
            </div>
          </div>
        </div>

        <!-- Agreement & Start Form -->
        <form @submit.prevent="submit" class="space-y-4">
          <div v-if="!isAuthenticated" class="bg-white rounded-3xl auto-cols-auto p-6 space-y-4 shadow-sm mb-6">
            <div>
              <label for="guest_name" class="block text-sm font-medium text-slate-700">Nama (Boleh nama panggilan atau inisial)</label>
              <input type="text" id="guest_name" v-model="form.guest_name" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-[#40D5C8] focus:ring-[#40D5C8] sm:text-sm" placeholder="Masukkan nama Anda" required>
               <p class="text-[11px] text-slate-500 mt-1">Hanya digunakan untuk menyapa Anda di lembar hasil akhir.</p>
            </div>

            <!-- Gender Radio -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Kelamin</label>
              <div class="grid grid-cols-2 gap-3">
                <label 
                  class="cursor-pointer rounded-xl border p-3 text-center transition-all bg-slate-50 border-slate-200"
                  :class="{ 'ring-2 ring-[#4c9a93] bg-[#40D5C8]/10 border-[#40D5C8] font-bold text-[#0d1b1a]': form.guest_gender === 'Laki-laki', 'text-slate-600': form.guest_gender !== 'Laki-laki' }"
                >
                  <input type="radio" v-model="form.guest_gender" value="Laki-laki" class="hidden" required>
                  <span class="text-sm">Laki-laki</span>
                </label>
                <label 
                  class="cursor-pointer rounded-xl border p-3 text-center transition-all bg-slate-50 border-slate-200"
                  :class="{ 'ring-2 ring-[#4c9a93] bg-[#40D5C8]/10 border-[#40D5C8] font-bold text-[#0d1b1a]': form.guest_gender === 'Perempuan', 'text-slate-600': form.guest_gender !== 'Perempuan' }"
                >
                  <input type="radio" v-model="form.guest_gender" value="Perempuan" class="hidden" required>
                  <span class="text-sm">Perempuan</span>
                </label>
              </div>
            </div>

            <!-- Age Range Radio -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Rentang Usia</label>
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <label v-for="age in ['< 18', '18 - 24', '25 - 34', '35+']" :key="age"
                  class="cursor-pointer rounded-xl border p-3 text-center transition-all bg-slate-50 border-slate-200"
                  :class="{ 'ring-2 ring-[#4c9a93] bg-[#40D5C8]/10 border-[#40D5C8] font-bold text-[#0d1b1a]': form.guest_age_range === age, 'text-slate-600': form.guest_age_range !== age }"
                >
                  <input type="radio" v-model="form.guest_age_range" :value="age" class="hidden" required>
                  <span class="text-xs sm:text-sm whitespace-nowrap">{{ age }}</span>
                </label>
              </div>
            </div>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="bg-[#40D5C8] text-[#0d1b1a] font-bold py-4 rounded-2xl shadow-lg shadow-[#40D5C8]/20 active:scale-95 transition-transform text-center block w-full"
            :class="{ 'opacity-70 cursor-not-allowed': form.processing }"
          >
           {{ form.processing ? 'Memproses...' : 'Saya Setuju & Mulai Assessment' }}
          </button>
          
          <Link
            :href="route('home')"
            class="text-center block w-full py-3 text-sm text-slate-500 hover:text-slate-700 transition-colors"
          >
            Kembali ke Beranda
          </Link>
        </form>

      </div>
    </div>
  </div>
</template>
