<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/20/solid';
</script>

<template>
  <Head title="Kebijakan Privasi" />
  <GuestLayout>
    <div class="w-full mb-4">
      <Link :href="route('home')" class="inline-flex items-center text-sm font-medium text-[#4c9a93] hover:text-[#3a7570] transition-colors">
        <ArrowLeftIcon class="w-4 h-4 mr-1" />
        Kembali ke Beranda
      </Link>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm text-sm text-slate-700 space-y-6 w-full mb-8">
      <div>
        <h2 class="text-2xl font-bold text-[#0d1b1a] mb-1">Kebijakan Privasi</h2>
        <p class="text-xs text-slate-500">Terakhir diubah: 9 Januari 2025</p>
      </div>

      <div class="space-y-4">
        <p>
          Kebijakan Privasi ini menjelaskan bagaimana Personaility ("kami") mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan Layanan kami.
        </p>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Data yang Kami Kumpulkan</h3>
          <ul class="list-disc pl-5 mt-1 space-y-1">
            <li><strong>Data Pendaftaran:</strong> Jika Anda membuat akun, kami mengumpulkan data seperti nama, alamat email, dan kata sandi Anda.</li>
            <li><strong>Data Penggunaan:</strong> Informasi tentang interaksi perangkat Anda dengan Layanan kami.</li>
            <li><strong>Data Asesmen:</strong> Jawaban yang Anda berikan selama tes kepribadian untuk diproses oleh sistem sentral kami.</li>
          </ul>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Bagaimana Kami Menggunakan Data Anda</h3>
          <p class="mt-1">Kami menggunakan data Anda untuk:</p>
          <ul class="list-disc pl-5 mt-1 space-y-1">
            <li>Menyediakan, mengoperasikan, dan memelihara Layanan.</li>
            <li>Memproses respons tes Anda dan menghasilkan wawasan kepribadian (AI Insights).</li>
            <li>Menganalisis dan meningkatkan kinerja sistem kami.</li>
          </ul>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Berbagi Data Anda</h3>
          <p class="mt-1">
            Kami <strong>TIDAK MENJUAL</strong> data pribadi Anda kepada pihak ketiga. Kami mungkin membagikan sebagian data kepada penyedia layanan cloud / API pihak ketiga dalam rangka memproses hasil (misalnya penyedia AI/LLM), dan memastikannya selaras dengan pedoman privasi kerahasian data secara ketat.
          </p>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Kuki (Cookies)</h3>
          <p class="mt-1">
            Kami menggunakan kuki untuk mengingat sesi login Anda dan preferensi situs. Anda dapat mengatur browser Anda untuk menolak kuki sewaktu-waktu.
          </p>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Batasan Usia</h3>
          <p class="mt-1">
            Layanan kami tidak ditujukan untuk anak-anak di bawah 18 tahun. Kami tidak secara sadar mengumpulkan data dari anak-anak.
          </p>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Kontak</h3>
          <p class="mt-1">
            Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami via email pendukung layanan jika tersedia.
          </p>
        </div>
        
      </div>
    </div>
  </GuestLayout>
</template>
