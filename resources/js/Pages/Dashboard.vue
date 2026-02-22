<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
  SparklesIcon,
  ArrowTopRightOnSquareIcon,
  IdentificationIcon,
  FireIcon,
  ClipboardDocumentListIcon,
  ChevronRightIcon,
  ClipboardDocumentCheckIcon,
  UserGroupIcon,
  HeartIcon,
  BoltIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  latestAssessment: Object,
  latestInsight: Object,
  history: Array,
});

const page = usePage();
const user = page.props.auth.user;

const traits = [
  { key: 'openness_score',          label: 'Openness (Keterbukaan)',          icon: SparklesIcon,               bar: 'bg-purple-400', light: 'bg-purple-50',       ic: 'text-purple-400' },
  { key: 'conscientiousness_score', label: 'Conscientiousness (Kehati-hatian)', icon: ClipboardDocumentCheckIcon, bar: 'bg-blue-400',   light: 'bg-blue-50',         ic: 'text-blue-400'   },
  { key: 'extraversion_score',      label: 'Extraversion (Ekstraversi)',      icon: UserGroupIcon,               bar: 'bg-amber-400',  light: 'bg-amber-50',        ic: 'text-amber-400'  },
  { key: 'agreeableness_score',     label: 'Agreeableness (Keramahan)',     icon: HeartIcon,                  bar: 'bg-[#40D5C8]',  light: 'bg-[#40D5C8]/10',   ic: 'text-[#40D5C8]'  },
  { key: 'neuroticism_score',       label: 'Neuroticism (Stabilitas Emosi)', icon: BoltIcon,                   bar: 'bg-[#FF61C5]',  light: 'bg-[#FF61C5]/10',   ic: 'text-[#FF61C5]'  },
];

function scorePercent(score) {
  return Math.round(((score - 1) / 4) * 100);
}
function formatDate(d) {
  return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' });
}

const greeting = () => {
  const h = new Date().getHours();
  if (h < 11) return 'Selamat pagi';
  if (h < 15) return 'Selamat siang';
  if (h < 18) return 'Selamat sore';
  return 'Selamat malam';
};


const getTrend = (traitKey) => {
  if (!props.history || props.history.length < 2) return null;
  const current = props.history[0][traitKey];
  const previous = props.history[1][traitKey];
  if (current === undefined || previous === undefined) return null;
  
  const diff = (current - previous).toFixed(1);
  if (diff > 0) return { text: `+${diff}`, class: 'text-emerald-500 bg-emerald-50' };
  if (diff < 0) return { text: `${diff}`, class: 'text-red-500 bg-red-50' };
  return { text: '=', class: 'text-slate-400 bg-slate-50' };
};
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <div class="max-w-lg mx-auto px-4 py-6 space-y-5">

      <!-- Greeting -->
      <div>
        <p class="text-slate-500 text-sm">{{ greeting() }},</p>
        <h1 class="text-2xl font-extrabold text-[#0d1b1a]">{{ user.name.split(' ')[0] }} 👋</h1>
      </div>

      <!-- ─── No assessment yet ─── -->
      <div v-if="!latestAssessment" class="bg-gradient-to-br from-[#40D5C8]/20 to-[#4c9a93]/10 rounded-3xl p-8 text-center">
        <SparklesIcon class="w-12 h-12 text-[#4c9a93] mx-auto mb-3" />
        <h2 class="font-bold text-lg text-[#0d1b1a] mb-2">Mulai Perjalananmu</h2>
        <p class="text-slate-500 text-sm mb-6 leading-relaxed">
          Kamu belum pernah mengikuti assessment. Temukan profil kepribadianmu sekarang — gratis.
        </p>
        <Link :href="route('assessment.consent')" class="inline-block bg-[#40D5C8] text-[#0d1b1a] font-bold px-8 py-3 rounded-2xl shadow-lg shadow-[#40D5C8]/30 hover:scale-105 transition-transform">
          Mulai Assessment →
        </Link>
      </div>

      <!-- ─── Latest result ─── -->
      <template v-else>

        <!-- Trait score card -->
        <div class="bg-white rounded-3xl shadow-sm p-5">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="font-bold text-[#0d1b1a]">Profil Terakhir</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ formatDate(latestAssessment.completed_at) }}</p>
            </div>
            <Link
              :href="route('assessment.result', latestAssessment.id)"
              class="flex items-center gap-1 text-xs font-semibold text-[#4c9a93] hover:underline"
            >
              Detail <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
            </Link>
          </div>

          <!-- Score mini bars grid -->
          <div class="grid grid-cols-5 gap-2">
            <div v-for="t in traits" :key="t.key" class="flex flex-col items-center gap-1.5" :title="t.label">
              <component :is="t.icon" class="w-5 h-5" :class="t.ic" />
              <div class="w-full h-20 rounded-xl overflow-hidden flex flex-col-reverse" :class="t.light">
                <div
                  class="w-full rounded-t-xl transition-all duration-700"
                  :class="t.bar"
                  :style="{ height: (latestAssessment[t.key] ? scorePercent(latestAssessment[t.key]) : 0) + '%' }"
                ></div>
              </div>
              <div class="flex flex-col items-center gap-0.5">
                <span class="text-xs font-black text-slate-700">{{ latestAssessment[t.key] ? Number(latestAssessment[t.key]).toFixed(1) : '—' }}</span>
                <span v-if="getTrend(t.key)" :class="['text-[10px] font-bold px-1.5 py-0.5 rounded border', getTrend(t.key).class, getTrend(t.key).class.includes('emerald') ? 'border-emerald-100' : (getTrend(t.key).class.includes('red') ? 'border-red-100' : 'border-slate-100')]">
                  {{ getTrend(t.key).text }}
                </span>
                <span v-else class="text-[10px] text-transparent select-none">-</span>
              </div>
            </div>
          </div>
        </div>

        <!-- AI Insight card -->
        <div v-if="latestInsight" class="bg-gradient-to-br from-[#e8faf8] to-[#f0fffe] rounded-3xl p-5 border border-[#40D5C8]/20">
          <div class="flex items-center gap-2 mb-4">
            <SparklesIcon class="w-5 h-5 text-[#4c9a93]" />
            <h2 class="font-bold text-[#0d1b1a]">AI Insight</h2>
          </div>

          <div class="space-y-4">
            <div class="bg-white/80 rounded-2xl p-4">
              <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">💪 Core Strength</p>
              <p class="text-sm text-slate-700 leading-relaxed">{{ latestInsight.core_strength }}</p>
            </div>
            <div class="bg-white/80 rounded-2xl p-4">
              <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider mb-1">📈 Growth Suggestion</p>
              <p class="text-sm text-slate-700 leading-relaxed">{{ latestInsight.growth_suggestion }}</p>
            </div>
          </div>

          <Link
            :href="route('assessment.result', latestAssessment.id)"
            class="mt-4 flex items-center justify-center gap-2 text-xs font-semibold text-[#4c9a93] hover:text-[#0d1b1a] transition-colors"
          >
            Lihat insight lengkap <ChevronRightIcon class="w-3.5 h-3.5" />
          </Link>
        </div>

        <!-- No AI insight yet -->
        <div v-else class="bg-white rounded-3xl p-5 shadow-sm text-center">
          <SparklesIcon class="w-8 h-8 text-slate-300 mx-auto mb-2" />
          <p class="text-sm text-slate-400">AI insight belum tersedia untuk assessment ini.</p>
        </div>

        <!-- History mini -->
        <div class="bg-white rounded-3xl shadow-sm p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-[#0d1b1a]">Riwayat</h2>
            <Link :href="route('assessment.history')" class="text-xs font-semibold text-[#4c9a93] hover:underline">
              Semua →
            </Link>
          </div>

          <div v-if="history.length <= 1" class="text-center py-3">
            <p class="text-slate-400 text-sm">Ikuti lebih banyak assessment untuk melihat tren.</p>
          </div>

          <div v-else class="space-y-3">
            <Link
              v-for="a in history.slice(0, 4)"
              :key="a.id"
              :href="route('assessment.result', a.id)"
              class="flex items-center gap-3 py-2.5 px-3 rounded-2xl hover:bg-[#f6f8f8] transition-colors"
            >
              <ClipboardDocumentListIcon class="w-5 h-5 text-slate-400 flex-shrink-0" />
              <span class="text-xs text-slate-500 flex-1">{{ formatDate(a.completed_at) }}</span>
              <div class="flex gap-1.5">
                <span v-for="t in traits" :key="t.key" class="text-[10px] font-bold text-slate-500">
                  {{ a[t.key] ? Number(a[t.key]).toFixed(1) : '—' }}
                </span>
              </div>
              <ChevronRightIcon class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" />
            </Link>
          </div>
        </div>

        <!-- Re-assess CTA -->
        <div>
          <Link
            :href="route('assessment.consent')"
            class="w-full block py-4 rounded-2xl bg-[#40D5C8] text-[#0d1b1a] font-bold text-center shadow-lg shadow-[#40D5C8]/20 hover:scale-[1.02] transition-transform text-sm"
          >
            Ulangi Assessment
          </Link>
        </div>

      </template>

      <!-- Psychologist banner -->
      <div class="bg-[#4c9a93]/10 rounded-2xl p-4 flex items-center gap-3">
        <IdentificationIcon class="w-8 h-8 text-[#4c9a93] flex-shrink-0" />
        <div class="flex-1 min-w-0">
          <p class="font-bold text-sm text-[#0d1b1a]">Butuh Bantuan Profesional?</p>
          <p class="text-xs text-slate-500">Psikolog terverifikasi · STR & SIP resmi</p>
        </div>
        <Link :href="route('psychologists.index')" class="flex-shrink-0">
          <ChevronRightIcon class="w-5 h-5 text-[#4c9a93]" />
        </Link>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
