<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/20/solid';
</script>

<template>
  <Head title="Syarat dan Ketentuan" />
  <GuestLayout>
    <div class="w-full mb-4">
      <Link :href="route('home')" class="inline-flex items-center text-sm font-medium text-[#4c9a93] hover:text-[#3a7570] transition-colors">
        <ArrowLeftIcon class="w-4 h-4 mr-1" />
        Kembali ke Beranda
      </Link>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm text-sm text-slate-700 space-y-6 w-full mb-8">
      <div>
        <h2 class="text-2xl font-bold text-[#0d1b1a] mb-1">Syarat dan Ketentuan</h2>
        <p class="text-xs text-slate-500">Terakhir diubah: 9 Januari 2025</p>
      </div>

      <div class="space-y-4">
        <p>
          Terima kasih telah menggunakan Personaility. Syarat Penggunaan ini, beserta Kebijakan Privasi kami (secara kolektif disebut "Perjanjian"), mengatur akses dan penggunaan Anda atas layanan Personaility ("Layanan"). Dengan menggunakan Layanan, Anda menyetujui Perjanjian ini. Jika Anda tidak setuju, Anda tidak berhak menggunakan Layanan kami.
        </p>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Definisi</h3>
          <p class="mt-1">
            "Layanan" mencakup situs web, asesmen, konten, dan hasil analisis AI dari Personaility. "Pengguna" berarti setiap individu yang mengakses atau menggunakan Layanan.
          </p>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Kelayakan</h3>
          <p class="mt-1">
            Anda harus berusia sekurang-kurangnya 18 tahun atau memiliki persetujuan dari orang tua/wali sah untuk menggunakan Layanan.
          </p>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Akun dan Keamanan</h3>
          <p class="mt-1">
            Saat membuat akun, Anda harus memberikan informasi yang akurat. Anda bertanggung jawab penuh untuk menjaga kerahasiaan kata sandi Anda dan aktivitas apa pun yang terjadi pada akun Anda.
          </p>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Batasan Penggunaan</h3>
          <p class="mt-1">Layanan ini disediakan untuk penggunaan pribadi dan non-komersial Anda. Anda setuju untuk TIDAK:</p>
          <ul class="list-disc pl-5 mt-1 space-y-1">
            <li>Menyalin, memodifikasi, atau mendistribusikan konten Layanan.</li>
            <li>Menggunakan Layanan untuk melecehkan atau mengancam orang lain.</li>
            <li>Memberikan informasi palsu saat registrasi.</li>
            <li>Mencoba meretas atau mengganggu keamanan Layanan.</li>
          </ul>
        </div>

        <div>
           <h3 class="font-bold text-[#0d1b1a]">Penafian Layanan AI (Disclaimer)</h3>
           <p class="mt-1">
             Layanan kami menggunakan Kecerdasan Buatan (AI) untuk menganalisis sifat kepribadian berdasarkan input Anda. Analisis ini ditujukan semata-mata untuk tujuan hiburan, pengembangan diri, dan edukasi. HASIL ANALISIS BUKANLAH DIAGNOSIS KLINIS, SARAN MEDIS, ATAU PSIKOLOGIS PROFESIONAL. Kami tidak menjamin keakuratan absolut dari profil yang dihasilkan.
           </p>
        </div>
        
        <div>
          <h3 class="font-bold text-[#0d1b1a]">Pengakhiran</h3>
          <p class="mt-1">
            Kami berhak menangguhkan atau mencabut akses Anda ke Layanan kapan saja jika Anda melanggar ketentuan Perjanjian ini.
          </p>
        </div>

        <div>
          <h3 class="font-bold text-[#0d1b1a]">Modifikasi</h3>
          <p class="mt-1">
            Kami dapat memperbarui Syarat dan Ketentuan ini dari waktu ke waktu. Penggunaan Layanan yang berkelanjutan setelah modifikasi merupakan bentuk persetujuan Anda terhadap perubahan tersebut.
          </p>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>
