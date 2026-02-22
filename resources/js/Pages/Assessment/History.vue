<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ClockIcon, SparklesIcon, ClipboardDocumentCheckIcon, UserGroupIcon, HeartIcon, BoltIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps({ assessments: Array });

const traits = [
  { key: 'openness_score',          label: 'O', name: 'Openness (Keterbukaan)', icon: SparklesIcon, color: '#c084fc' }, // purple
  { key: 'conscientiousness_score', label: 'C', name: 'Conscientiousness (Kehati-hatian)', icon: ClipboardDocumentCheckIcon, color: '#60a5fa' }, // blue
  { key: 'extraversion_score',      label: 'E', name: 'Extraversion (Ekstraversi)', icon: UserGroupIcon, color: '#fbbf24' }, // amber
  { key: 'agreeableness_score',     label: 'A', name: 'Agreeableness (Keramahan)', icon: HeartIcon, color: '#40D5C8' }, // signature cyan
  { key: 'neuroticism_score',       label: 'N', name: 'Neuroticism (Stabilitas Emosi)', icon: BoltIcon, color: '#FF61C5' }, // pink
];

function formatDate(d) {
  return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
}

const chartData = computed(() => {
  // We want chronological order (oldest -> newest) for the chart X-axis
  const ascAssessments = [...props.assessments].reverse();
  
  return {
    labels: ascAssessments.map(a => formatDate(a.completed_at)),
    datasets: traits.map(t => ({
      label: t.name,
      backgroundColor: t.color,
      borderColor: t.color,
      data: ascAssessments.map(a => a[t.key]),
      tension: 0.3,
      pointRadius: 4,
      pointHoverRadius: 6,
    }))
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      min: 1,
      max: 5,
      ticks: { stepSize: 1 }
    }
  },
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        usePointStyle: true,
        boxWidth: 8,
        padding: 20,
        font: { size: 10 }
      }
    }
  }
};
</script>

<template>
  <Head title="Riwayat Assessment" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-[#f6f8f8] py-8 px-4">
      <div class="max-w-lg mx-auto">

        <h1 class="text-2xl font-extrabold text-[#0d1b1a] mb-6">Riwayat Assessment</h1>

        <div v-if="assessments.length === 0" class="bg-white rounded-3xl shadow-sm p-10 text-center">
          <ClockIcon class="w-12 h-12 text-slate-300 mx-auto mb-3" />
          <p class="text-slate-500">Belum ada assessment yang selesai.</p>
          <Link :href="route('assessment.consent')" class="inline-block mt-4 bg-[#40D5C8] text-[#0d1b1a] font-bold px-6 py-3 rounded-xl">
            Mulai Sekarang
          </Link>
        </div>

        <div v-else class="space-y-6">
          
          <!-- TREND CHART -->
          <div v-if="assessments.length > 1" class="bg-white rounded-3xl shadow-sm p-5 md:p-8">
            <h2 class="font-bold text-[#0d1b1a] mb-6">Grafik Perkembangan Sifat</h2>
            <div class="h-64 sm:h-80 relative w-full">
              <Line :data="chartData" :options="chartOptions" />
            </div>
          </div>

          <!-- LIST OF PAST ASSESSMENTS -->
          <div class="space-y-4">
            <h2 class="font-bold text-[#0d1b1a] mb-2 px-2">Daftar Assessment</h2>
            <Link
              v-for="assessment in assessments"
              :key="assessment.id"
              :href="route('assessment.result', assessment.id)"
              class="block bg-white rounded-3xl shadow-sm p-5 hover:shadow-md transition-shadow border border-transparent hover:border-[#40D5C8]/30"
            >
              <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold text-[#4c9a93]">
                  {{ formatDate(assessment.completed_at) }}
                </span>
                <span class="text-xs px-2 py-1 bg-slate-100 rounded-lg text-slate-500 font-medium">v{{ assessment.version }}</span>
              </div>

              <!-- Mini score bars -->
              <div class="grid grid-cols-5 gap-2">
                <div v-for="t in traits" :key="t.key" class="text-center" :title="t.name">
                  <component :is="t.icon" class="w-4 h-4 mx-auto mb-1" :style="{ color: t.color }" />
                  <div class="w-full bg-slate-100 rounded-full h-12 flex items-end overflow-hidden">
                    <div
                      class="w-full rounded-full transition-all"
                      :style="{ height: (assessment[t.key] ? ((assessment[t.key] - 1) / 4 * 100) : 0) + '%', backgroundColor: t.color }"
                    ></div>
                  </div>
                  <div class="text-xs font-bold text-slate-600 mt-1">{{ t.label }}</div>
                </div>
              </div>
            </Link>
          </div>
        </div>

        <div class="mt-6">
          <Link :href="route('dashboard')" class="text-sm text-[#4c9a93] hover:underline">&larr; Kembali ke Dashboard</Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
