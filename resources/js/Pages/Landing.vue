<script setup>
import { Link } from '@inertiajs/vue3';
import {
  SparklesIcon,
  UserCircleIcon,
  IdentificationIcon,
  BoltIcon,
  EyeSlashIcon,
  ArrowTrendingUpIcon,
  ClipboardDocumentCheckIcon,
  UserGroupIcon,
  HeartIcon,
  TrophyIcon,
  ChatBubbleLeftIcon,
  ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline';
import Navbar from '@/Components/Navbar.vue';

const traits = [
  { icon: SparklesIcon, name: 'Openness', desc: 'Kreativitas, imajinasi, dan keterbukaan terhadap pengalaman baru', color: 'bg-purple-100 text-purple-600' },
  { icon: ClipboardDocumentCheckIcon, name: 'Conscientiousness', desc: 'Keteraturan, disiplin, dan tanggung jawab', color: 'bg-blue-100 text-blue-600' },
  { icon: UserGroupIcon, name: 'Extraversion', desc: 'Sosiabilitas, energi, dan kenyamanan di tengah orang banyak', color: 'bg-yellow-100 text-yellow-600' },
  { icon: HeartIcon, name: 'Agreeableness', desc: 'Empati, kerja sama, dan kepercayaan terhadap orang lain', color: 'bg-[#40D5C8]/10 text-[#40D5C8]' },
  { icon: BoltIcon, name: 'Neuroticism', desc: 'Stabilitas emosi dan pola respons terhadap stres', color: 'bg-[#FF61C5]/10 text-[#FF61C5]' },
];

const steps = [
  { title: 'Ambil Assessment', desc: 'Jawab 30 pertanyaan singkat.' },
  { title: 'Hitung Skor Trait', desc: 'Algoritma menghitung profil kepribadianmu secara akurat.' },
  { title: 'Dapatkan AI Insight', desc: 'AI memberikan insight personal berdasarkan profilmu.' },
  { title: 'Lacak Perkembanganmu', desc: 'Bandingkan hasil antar waktu di dashboard.' },
];

const insights = [
  { icon: TrophyIcon,          title: 'Core Strength',    desc: 'Kekuatan utama yang kamu miliki' },
  { icon: EyeSlashIcon,        title: 'Blind Spot',       desc: 'Area yang mungkin kamu lewatkan' },
  { icon: ArrowTrendingUpIcon, title: 'Growth Suggestion', desc: 'Langkah konkret untuk berkembang' },
  { icon: BoltIcon,            title: 'Stress Pattern',   desc: 'Pola reaksimu di bawah tekanan' },
];
</script>

<template>
  <div class="min-h-screen bg-[#f6f8f8] font-sans text-[#0d1b1a]">
    <!-- Google Fonts Inter & Lora only -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet"/>

    <!-- Navigation -->
    <Navbar />


    <main class="max-w-md mx-auto">

      <!-- Hero Section -->
      <section class="relative px-6 pt-12 pb-20 overflow-hidden">
        <div class="absolute top-0 right-0 -z-10 w-64 h-64 bg-[#40D5C8]/20 rounded-full blur-3xl opacity-50 translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 -z-10 w-48 h-48 bg-[#4c9a93]/10 rounded-full blur-3xl opacity-50 -translate-x-1/2 translate-y-1/2"></div>

        <div class="flex flex-col gap-6 text-center">
          <div class="mx-auto w-fit px-3 py-1 rounded-full bg-[#40D5C8]/10 border border-[#40D5C8]/20">
            <span class="text-xs font-semibold text-[#4c9a93] uppercase tracking-wider">Mulai Perjalananmu</span>
          </div>

          <h1 class="text-4xl font-extrabold leading-[1.1] tracking-tight">
            Kenali Dirimu Lebih Dalam dengan <span class="text-[#4c9a93]">Personaility</span>
          </h1>

          <p class="text-slate-600 text-base leading-relaxed">
            Assessment Big Five dengan dukungan analisis berbasis AI — temukan kekuatan inti, blind spot, dan pola stresmu.
          </p>

          <div class="flex flex-col gap-3 pt-4">
            <Link :href="route('assessment.consent')" class="bg-[#40D5C8] text-[#0d1b1a] font-bold py-4 rounded-2xl shadow-lg shadow-[#40D5C8]/20 hover:scale-[1.02] active:scale-95 transition-all text-center block">
              Mulai Assessment Gratis
            </Link>
            <p class="text-xs text-slate-500">Selesai dalam 5 menit · Rahasiamu terjamin.</p>
          </div>
        </div>

        <!-- Hero visual -->
        <div class="mt-12 flex items-center justify-center relative">
          <img src="/ocean.png" alt="Big Five OCEAN Model Illustration" class="w-full max-w-[320px] drop-shadow-xl z-10 object-contain" />
        </div>
      </section>

      <!-- Trait Preview -->
      <section class="px-6 py-12 bg-white rounded-t-[3rem] shadow-2xl">
        <h2 class="text-2xl font-bold mb-2">5 Dimensi Kepribadian</h2>
        <p class="text-slate-500 text-sm mb-8">Diukur menggunakan model ilmiah Big Five (OCEAN)</p>

        <div class="space-y-4">
          <div v-for="(trait, i) in traits" :key="i" class="flex items-center gap-4 p-4 rounded-2xl bg-[#f6f8f8] border border-[#4c9a93]/5">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-lg" :class="trait.color">
              <component :is="trait.icon" class="w-6 h-6" />
            </div>
            <div>
              <h3 class="font-bold text-sm">{{ trait.name }}</h3>
              <p class="text-xs text-slate-500 mt-0.5 leading-snug">{{ trait.desc }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- How It Works -->
      <section class="px-6 py-16 bg-[#f6f8f8]">
        <div class="text-center mb-12">
          <h2 class="text-2xl font-bold">Cara Kerja</h2>
          <div class="w-12 h-1 bg-[#40D5C8] mx-auto mt-2 rounded-full"></div>
        </div>

        <div class="relative space-y-12 before:content-[''] before:absolute before:left-[23px] before:top-4 before:bottom-4 before:w-[2px] before:bg-[#40D5C8]/30">
          <div v-for="(step, i) in steps" :key="i" class="relative flex gap-6">
            <div class="z-10 flex-shrink-0 w-12 h-12 rounded-full bg-[#40D5C8] text-[#0d1b1a] flex items-center justify-center font-bold text-lg ring-4 ring-[#f6f8f8]">
              {{ i + 1 }}
            </div>
            <div>
              <h4 class="font-bold text-lg">{{ step.title }}</h4>
              <p class="text-slate-600 mt-1 text-sm">{{ step.desc }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- AI Output Preview -->
      <section class="px-8 py-16 bg-[#40D5C8]/5">
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold">Apa yang Kamu Dapatkan</h2>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div v-for="(item, i) in insights" :key="i" class="bg-white rounded-2xl p-4 shadow-sm">
            <component :is="item.icon" class="w-6 h-6 text-[#4c9a93] mb-2" />
            <h4 class="font-bold text-sm">{{ item.title }}</h4>
            <p class="text-xs text-slate-500 mt-1">{{ item.desc }}</p>
          </div>
        </div>
      </section>

      <!-- CTA -->
      <section class="px-6 py-20 bg-white rounded-b-[3rem] mb-12">
        <div class="text-center space-y-6">
          <h2 class="text-3xl font-extrabold tracking-tight">Siap Mengenal Dirimu?</h2>
          <p class="text-slate-600">Assessment gratis · AI insight · Tanpa diagnosis klinis</p>
          <div class="flex flex-col gap-4">
            <Link :href="route('assessment.consent')" class="bg-[#40D5C8] text-[#0d1b1a] font-bold py-4 rounded-2xl shadow-lg shadow-[#40D5C8]/20 hover:scale-[1.02] active:scale-95 transition-all text-center block">
              Mulai Sekarang — Gratis
            </Link>
            <div class="mt-4">
              <span class="text-slate-500 text-sm">Sudah punya akun? </span>
              <Link :href="route('login')" class="text-sm font-semibold text-[#4c9a93] hover:underline">
                Masuk
              </Link>
            </div>
          </div>
          <p class="text-xs text-slate-400">
            Platform ini bukan penyedia layanan kesehatan mental. AI tidak mendiagnosis kondisi psikologis.
          </p>
        </div>
      </section>

      <!-- Psychologist CTA -->
      <section class="px-6 pb-24">
        <div class="bg-[#4c9a93]/10 rounded-2xl p-6 text-center">
          <IdentificationIcon class="w-8 h-8 text-[#4c9a93] mx-auto mb-2" />
          <h3 class="font-bold text-lg mb-1">Temukan Psikolog Terverifikasi</h3>
          <p class="text-sm text-slate-600 mb-4">Direktori psikolog lokal dengan STR & SIP terverifikasi</p>
          <Link :href="route('psychologists.index')" class="inline-block text-sm font-semibold text-[#4c9a93] underline hover:no-underline">
            Lihat Direktori →
          </Link>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="px-6 py-12 text-center space-y-6 border-t border-slate-100">
      <div class="flex flex-col items-center gap-2">
        <img src="/logo.png" alt="Personaility Logo" class="h-12 w-auto object-contain shrink-0 mb-2" />
        <p class="text-sm text-slate-500 max-w-[200px]">Berbasis Big Five OCEAN &middot; Bukan alat diagnostik klinis</p>
      </div>
      <div class="flex justify-center gap-6 text-sm font-medium text-[#4c9a93]">
        <Link :href="route('privacy')" class="hover:underline">Privasi</Link>
        <Link :href="route('terms')" class="hover:underline">Ketentuan</Link>
        <Link :href="route('psychologists.index')" class="hover:underline">Direktori Psikolog</Link>
      </div>
      <p class="text-xs text-slate-400">© 2025 Personaility. All rights reserved.</p>
    </footer>
  </div>
</template>
