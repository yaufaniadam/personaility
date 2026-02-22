<script setup>
import { Link } from '@inertiajs/vue3';
import { SparklesIcon } from '@heroicons/vue/24/outline';
</script>

<template>
  <div class="min-h-screen bg-[#f6f8f8] flex flex-col font-sans">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>

    <!-- Logo header -->
    <header class="flex flex-col items-center pt-14 pb-8 px-6">
      <Link :href="route('home')" class="flex flex-col items-center group">
        <img src="/logo.png" alt="Personaility Logo" class="h-12 w-auto object-contain shrink-0 mb-5 drop-shadow-sm group-hover:scale-105 transition-transform duration-300" />
      </Link>
      <p class="text-slate-500 text-sm text-center mt-1 max-w-xs leading-relaxed">
        Kenali Kedalaman Dirimu, Wujudkan Potensi Terbaikmu.
      </p>
    </header>

    <!-- Content slot -->
    <main class="flex-1 flex flex-col items-center px-6 w-full max-w-md mx-auto">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="py-8 px-6 text-center">
      <p class="text-slate-400 text-xs">
        Dengan melanjutkan, Anda menyetujui
        <Link :href="route('terms')" class="text-[#4c9a93] font-semibold hover:underline">Ketentuan</Link> &
        <Link :href="route('privacy')" class="text-[#4c9a93] font-semibold hover:underline">Privasi</Link> kami.
      </p>
      <p class="text-slate-300 text-[10px] mt-2">Platform ini bukan layanan kesehatan mental. Tidak ada diagnosis.</p>
    </footer>
  </div>
</template>
