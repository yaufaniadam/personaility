<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
  ArrowLeftIcon,
  UserIcon,
  UserCircleIcon,
  CheckBadgeIcon,
  MapPinIcon,
  ChatBubbleLeftIcon,
  PhoneIcon,
  ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';

defineProps({ psychologist: Object });
</script>

<template>
  <Head :title="psychologist.name" />
  <div class="min-h-screen bg-[#f6f8f8] font-sans">

    <!-- Nav -->
    <Navbar />

    <div class="max-w-lg mx-auto px-4 pt-6">
      <Link :href="route('psychologists.index')" class="flex items-center gap-2 text-[#4c9a93] hover:text-[#0d1b1a] transition-colors font-semibold text-sm">
        <ArrowLeftIcon class="w-4 h-4" /> Kembali ke Direktori
      </Link>
    </div>


    <main class="max-w-lg mx-auto px-4 py-8 space-y-5">

      <!-- Profile card -->
      <div class="bg-white rounded-3xl shadow-sm p-6">
        <div class="flex items-start gap-4 mb-5">
          <div
            class="flex-shrink-0 w-16 h-16 rounded-2xl flex items-center justify-center transition-colors overflow-hidden"
            :class="{
              'bg-blue-50 text-blue-600': psychologist.gender === 'male' && !psychologist.avatar_url,
              'bg-rose-50 text-rose-600': psychologist.gender === 'female' && !psychologist.avatar_url,
              'bg-[#4c9a93]/20 text-[#4c9a93]': !psychologist.gender && !psychologist.avatar_url
            }"
          >
            <template v-if="psychologist.avatar_url">
              <img :src="psychologist.avatar_url" class="w-full h-full object-cover" :alt="psychologist.name" />
            </template>
            <template v-else>
              <UserIcon v-if="psychologist.gender === 'male' || !psychologist.gender" class="w-8 h-8" />
              <UserCircleIcon v-else class="w-8 h-8" />
            </template>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h1 class="text-xl font-extrabold text-[#0d1b1a]">{{ psychologist.name }}</h1>
              <span v-if="psychologist.verified_status" class="flex items-center gap-0.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                <CheckBadgeIcon class="w-3 h-3" /> Terverifikasi
              </span>
            </div>
            <p class="text-sm text-[#4c9a93] font-medium mt-0.5">{{ psychologist.specialization }}</p>
            <div class="flex items-center gap-1 mt-1">
              <MapPinIcon class="w-3 h-3 text-slate-400" />
              <span class="text-xs text-slate-500">{{ psychologist.city }}, {{ psychologist.province }}</span>
            </div>
          </div>
        </div>

        <!-- Bio -->
        <div v-if="psychologist.bio" class="mb-5">
          <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Tentang</h2>
          <p class="text-sm text-slate-700 leading-relaxed">{{ psychologist.bio }}</p>
        </div>

        <!-- STR/SIP -->
        <div class="grid grid-cols-2 gap-3 mb-5">
          <div class="bg-[#f6f8f8] rounded-2xl p-3">
            <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-1">Nomor STR</p>
            <p class="text-sm font-bold text-[#0d1b1a]">{{ psychologist.str_number }}</p>
          </div>
          <div v-if="psychologist.sip_number" class="bg-[#f6f8f8] rounded-2xl p-3">
            <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-1">Nomor SIP</p>
            <p class="text-sm font-bold text-[#0d1b1a]">{{ psychologist.sip_number }}</p>
          </div>
        </div>

        <!-- Disclaimer -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-700 leading-relaxed">
          Platform ini hanya menyediakan informasi. Seluruh layanan dan tanggung jawab berada pada psikolog terkait.
        </div>
      </div>

      <!-- Contact card -->
      <div class="bg-white rounded-3xl shadow-sm p-6">
        <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-4">Hubungi Langsung</h2>

        <div class="space-y-3">
          <a
            v-if="psychologist.contact_whatsapp"
            :href="`https://wa.me/${psychologist.contact_whatsapp.replace(/[^0-9]/g,'')}`"
            target="_blank"
            class="flex items-center gap-3 p-4 rounded-2xl bg-green-50 border border-green-200 hover:bg-green-100 transition-colors"
          >
            <ChatBubbleLeftIcon class="w-6 h-6 text-green-600" />
            <div>
              <p class="font-bold text-sm text-green-700">WhatsApp</p>
              <p class="text-xs text-green-600">{{ psychologist.contact_whatsapp }}</p>
            </div>
          </a>

          <a
            v-if="psychologist.contact_phone"
            :href="`tel:${psychologist.contact_phone}`"
            class="flex items-center gap-3 p-4 rounded-2xl bg-[#4c9a93]/10 border border-[#4c9a93]/20 hover:bg-[#4c9a93]/20 transition-colors"
          >
            <PhoneIcon class="w-6 h-6 text-[#4c9a93]" />
            <div>
              <p class="font-bold text-sm text-[#0d1b1a]">Telepon</p>
              <p class="text-xs text-slate-500">{{ psychologist.contact_phone }}</p>
            </div>
          </a>

          <a
            v-if="psychologist.website"
            :href="psychologist.website"
            target="_blank"
            class="flex items-center gap-3 p-4 rounded-2xl bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors"
          >
            <ArrowTopRightOnSquareIcon class="w-6 h-6 text-blue-600" />
            <div>
              <p class="font-bold text-sm text-blue-700">Website</p>
              <p class="text-xs text-blue-500 truncate">{{ psychologist.website }}</p>
            </div>
          </a>

          <div v-if="!psychologist.contact_whatsapp && !psychologist.contact_phone && !psychologist.website" class="text-sm text-slate-500 text-center py-4">
            Informasi kontak tidak tersedia.
          </div>
        </div>

        <p class="text-xs text-slate-400 text-center mt-5">
          Tidak ada proses booking via platform ini. Hubungi psikolog langsung.
        </p>
      </div>

    </main>

    <Footer />
  </div>
</template>

