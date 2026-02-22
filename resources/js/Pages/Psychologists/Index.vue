<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
  SparklesIcon,
  InformationCircleIcon,
  MagnifyingGlassIcon,
  UserIcon,
  UserCircleIcon,
  CheckBadgeIcon,
  MapPinIcon,
  ChevronRightIcon,
  ChatBubbleLeftIcon,
  ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

const props = defineProps({
  psychologists: Array,
  cities: Array,
  selectedCity: String,
});

const search = ref('');

const toTitleCase = (str) => {
  if (!str) return '';
  return str.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
};

const filtered = computed(() => {
  if (!search.value) return props.psychologists;
  const q = search.value.toLowerCase();
  return props.psychologists.filter(p =>
    p.name.toLowerCase().includes(q) ||
    p.specialization.toLowerCase().includes(q) ||
    p.city.toLowerCase().includes(q)
  );
});
</script>

<template>
  <Head title="Direktori Psikolog" />
  <div class="min-h-screen bg-[#f6f8f8] font-sans">

    <!-- Nav -->
    <Navbar />


    <main class="max-w-3xl mx-auto px-4 py-8">

      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-[#0d1b1a]">Direktori Psikolog</h1>
        <p class="text-slate-500 text-sm mt-1">Psikolog terverifikasi · Informasi saja · Tanpa booking via platform</p>
      </div>

      <!-- Disclaimer banner -->
      <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex gap-3">
        <InformationCircleIcon class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" />
        <p class="text-xs text-amber-700 leading-relaxed">
          Platform ini hanya menyediakan informasi. Seluruh layanan dan tanggung jawab berada pada psikolog terkait. Hubungi langsung untuk konsultasi.
        </p>
      </div>

      <!-- Psychologist Join link -->
      <div class="mb-6 px-1">
        <span class="text-xs text-slate-500">Anda seorang Psikolog? </span>
        <Link :href="route('psychologist.register')" class="text-xs font-bold text-[#4c9a93] hover:underline">
          Daftar untuk masuk ke listing ini →
        </Link>
      </div>

      <!-- Filters -->
      <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-[2]">
          <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 z-10" />
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama, spesialisasi..."
            class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#40D5C8]"
          />
        </div>
        <div class="flex-[3]">
          <Multiselect
            :model-value="selectedCity"
            :options="cities"
            :searchable="true"
            :close-on-select="true"
            :show-labels="false"
            placeholder="Pilih Kota"
            @update:model-value="$inertia.get(route('psychologists.index'), { city: $event || '' })"
            class="custom-multiselect"
          >
            <template #singleLabel="{ option }">
              <span class="text-sm">{{ toTitleCase(option) || 'Semua Kota' }}</span>
            </template>
            <template #option="{ option }">
              <span class="text-sm">{{ toTitleCase(option) }}</span>
            </template>
          </Multiselect>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="filtered.length === 0" class="bg-white rounded-3xl p-10 text-center shadow-sm">
        <UserIcon class="w-12 h-12 text-slate-300 mx-auto mb-3" />
        <p class="text-slate-500">Tidak ada psikolog ditemukan.</p>
      </div>

      <!-- Psychologist grid -->
      <div v-else class="grid gap-4">
        <Link
          v-for="p in filtered"
          :key="p.id"
          :href="route('psychologists.show', p.id)"
          class="bg-white rounded-3xl shadow-sm p-5 hover:shadow-md transition-shadow block"
        >
          <div class="flex items-start gap-4">
            <!-- Avatar placeholder -->
            <div
              class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center transition-colors overflow-hidden"
              :class="{
                'bg-blue-50 text-blue-600': p.gender === 'male' && !p.avatar_url,
                'bg-rose-50 text-rose-600': p.gender === 'female' && !p.avatar_url,
                'bg-[#4c9a93]/20 text-[#4c9a93]': !p.gender && !p.avatar_url
              }"
            >
              <template v-if="p.avatar_url">
                <img :src="p.avatar_url" class="w-full h-full object-cover" :alt="p.name" />
              </template>
              <template v-else>
                <UserIcon v-if="p.gender === 'male' || !p.gender" class="w-6 h-6" />
                <UserCircleIcon v-else class="w-6 h-6" />
              </template>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-bold text-[#0d1b1a]">{{ p.name }}</h3>
                <span v-if="p.verified_status" class="flex items-center gap-0.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                  <CheckBadgeIcon class="w-3 h-3" /> Terverifikasi
                </span>
              </div>
              <p class="text-xs text-[#4c9a93] font-medium mt-0.5">{{ p.specialization }}</p>
              
              <!-- Address & Location combined -->
              <div class="mt-2 flex items-start gap-2 text-[11px] text-slate-500">
                <MapPinIcon class="w-3.5 h-3.5 mt-0.5 text-[#4c9a93] shrink-0" />
                <div class="leading-relaxed">
                  <span v-if="p.address" class="text-slate-700 font-medium">{{ p.address }}, </span>
                  <span>{{ toTitleCase(p.city) }}, {{ toTitleCase(p.province) }}</span>
                </div>
              </div>
            </div>
            <ChevronRightIcon class="w-5 h-5 text-slate-300 flex-shrink-0" />
          </div>

          <!-- Contact quick actions -->
          <div class="flex gap-2 mt-4">
            <a
              v-if="p.contact_whatsapp"
              :href="`https://wa.me/${p.contact_whatsapp.replace(/[^0-9]/g,'')}`"
              target="_blank"
              @click.stop
              class="flex items-center gap-1.5 text-xs font-semibold text-white bg-green-500 px-3 py-1.5 rounded-xl hover:bg-green-600 transition-colors"
            >
              <ChatBubbleLeftIcon class="w-3.5 h-3.5" /> WhatsApp
            </a>
            <a
              v-if="p.website"
              :href="p.website"
              target="_blank"
              @click.stop
              class="flex items-center gap-1.5 text-xs font-semibold text-[#4c9a93] border border-[#4c9a93]/30 px-3 py-1.5 rounded-xl hover:bg-[#4c9a93]/5 transition-colors"
            >
              <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" /> Website
            </a>
          </div>
        </Link>
      </div>
    </main>

    <Footer />
  </div>
</template>


<style>
.custom-multiselect .multiselect__tags {
  border-radius: 1rem !important;
  border: 1px solid #e2e8f0 !important;
  padding: 8px 40px 0 8px !important;
  min-height: 48px !important;
}
.custom-multiselect .multiselect__select {
  height: 46px !important;
}
.custom-multiselect .multiselect__placeholder {
  margin-bottom: 0 !important;
  padding-top: 4px !important;
  font-size: 14px !important;
}
.custom-multiselect .multiselect__single {
  padding-top: 4px !important;
  font-size: 14px !important;
  background: transparent !important;
}
.custom-multiselect .multiselect__input {
  font-size: 14px !important;
  padding-top: 4px !important;
}
</style>
