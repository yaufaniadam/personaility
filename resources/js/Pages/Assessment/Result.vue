<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import {
  ChartBarIcon,
  SparklesIcon,
  ClockIcon,
  BoltIcon,
  EyeSlashIcon,
  ArrowTrendingUpIcon,
  ShareIcon,
  ArrowDownTrayIcon,
  ClipboardDocumentCheckIcon,
  UserGroupIcon,
  HeartIcon,
  TrophyIcon,
} from '@heroicons/vue/24/outline';
import html2canvas from 'html2canvas';

const props = defineProps({
  assessment: Object,
  insight: Object,
  scores: Object,
});

const traits = [
  { key: 'openness',          label: 'Openness (Keterbukaan)',          icon: SparklesIcon,               ic: 'text-purple-400', color: 'bg-purple-100 border-purple-200', bar: 'bg-purple-400' },
  { key: 'conscientiousness', label: 'Conscientiousness (Kehati-hatian)', icon: ClipboardDocumentCheckIcon, ic: 'text-blue-400',   color: 'bg-blue-100 border-blue-200',     bar: 'bg-blue-400' },
  { key: 'extraversion',      label: 'Extraversion (Ekstraversi)',      icon: UserGroupIcon,               ic: 'text-yellow-400', color: 'bg-yellow-100 border-yellow-200', bar: 'bg-yellow-400' },
  { key: 'agreeableness',     label: 'Agreeableness (Keramahan)',     icon: HeartIcon,                  ic: 'text-[#40D5C8]',  color: 'bg-[#40D5C8]/10 border-[#40D5C8]/20',   bar: 'bg-[#40D5C8]' },
  { key: 'neuroticism',       label: 'Neuroticism (Stabilitas Emosi)', icon: BoltIcon,                   ic: 'text-[#FF61C5]',  color: 'bg-[#FF61C5]/10 border-[#FF61C5]/20',       bar: 'bg-[#FF61C5]' },
];

function scorePercent(score) {
  return Math.round((score / 5) * 100);
}

const insightSections = [
  { key: 'core_strength',     icon: TrophyIcon,          label: 'Core Strength',    color: 'text-emerald-500' },
  { key: 'blind_spot',        icon: EyeSlashIcon,        label: 'Blind Spot',        color: 'text-amber-500'   },
  { key: 'stress_pattern',    icon: BoltIcon,            label: 'Stress Pattern',        color: 'text-red-400'     },
  { key: 'growth_suggestion', icon: ArrowTrendingUpIcon, label: 'Growth Suggestion', color: 'text-blue-500'   },
];

const form = useForm({});
const isGenerating = ref(false);

function generateInsight() {
  isGenerating.value = true;
  form.post(route('assessment.insight', props.assessment.id), {
    preserveScroll: true,
    onFinish: () => { isGenerating.value = false; },
  });
}

const feedbackForm = useForm({
  feedback_score: null,
  feedback_comment: '',
});

const feedbackSubmitted = ref({ value: false });
if (props.insight && props.insight.feedback_score !== null) {
    feedbackSubmitted.value = true;
} else {
    feedbackSubmitted.value = false;
}

function submitFeedback() {
  feedbackForm.post(route('assessment.insight.feedback', props.assessment.id), {
    preserveScroll: true,
    onSuccess: () => {
      feedbackSubmitted.value = true;
    }
  });
}

const shareResult = async () => {
  const shareData = {
    title: 'Hasil Assessment Kepribadianku',
    text: 'Cek hasil skor tes kepribadian Big Five OCEAN-ku di Personaility!',
    url: window.location.href,
  };

  try {
    if (navigator.share) {
      await navigator.share(shareData);
    } else {
      await navigator.clipboard.writeText(`${shareData.text}\n${shareData.url}`);
      alert('Link disalin ke clipboard!');
    }
  } catch (err) {
    console.error('Error sharing:', err);
  }
};

const shareCardRef = ref(null);
const isGeneratingImage = ref(false);

const downloadImage = async () => {
  if (!shareCardRef.value) return;
  isGeneratingImage.value = true;
  
  const shareTextContent = 'Ternyata ini sisi lain diriku! 🕵️‍♂️✨ Penasaran sama kepribadian Big Five kamu juga? Yuk, cek sekarang di www.personaility.me dan temukan potensi tersembunyimu! 🚀';
  const shareUrl = 'https://www.personaility.me';
  const combinedClipboardText = `${shareTextContent}\n${shareUrl}`;

  try {
    const canvas = await html2canvas(shareCardRef.value, {
      scale: 2, // Retina quality
      backgroundColor: '#ffffff',
      useCORS: true,
      logging: false,
      windowWidth: 1080,
    });
    
    // Convert canvas to Blob
    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
    const file = new File([blob], `personaility-profil-${props.assessment.id}.png`, { type: 'image/png' });

    let sharedNatively = false;

    // Try native Web Share API
    if (navigator.share) {
      try {
        await navigator.share({
          files: [file],
          title: 'Hasil Assessment Kepribadianku',
          text: shareTextContent,
          url: shareUrl,
        });
        sharedNatively = true;
      } catch (shareErr) {
        if (shareErr.name === 'AbortError') {
          // User intentionally cancelled the share sheet, do nothing
          sharedNatively = true;
        } else {
          console.warn('Native share with file failed, falling back to download:', shareErr);
        }
      }
    }

    if (!sharedNatively) {
      // Fallback: Download file directly
      const image = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = image;
      link.download = `personaility-profil-${props.assessment.id}.png`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(image);
      
      // Also copy the text to clipboard since we couldn't share it natively
      try {
        await navigator.clipboard.writeText(combinedClipboardText);
        alert('Gambar berhasil diunduh!\nTeks ajakan telah disalin ke clipboard.');
      } catch (e) {
        alert('Gambar berhasil diunduh!');
      }
    }
  } catch (err) {
    console.error('Error generating image:', err);
    alert('Maaf, fitur berbagi gagal memproses gambar.');
  } finally {
    isGeneratingImage.value = false;
  }
};
</script>

<template>
  <Head title="Hasil Assessment" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-[#f6f8f8] py-8 px-4">
      <div class="max-w-lg mx-auto space-y-6">

        <!-- Hero result card -->
        <div class="bg-gradient-to-br from-[#40D5C8]/20 to-[#4c9a93]/10 rounded-3xl p-6 text-center">
          <div class="w-16 h-16 mx-auto rounded-full bg-[#40D5C8]/30 flex items-center justify-center mb-4">
            <ChartBarIcon class="w-8 h-8 text-[#4c9a93]" />
          </div>
          <h1 class="text-2xl font-extrabold text-[#0d1b1a]">Profilmu Sudah Siap!</h1>
          <p class="text-slate-600 mt-2 text-sm">Berikut hasil analisis kepribadian Big Five kamu.</p>
          <p class="text-xs text-slate-400 mt-1">
            Hasil ini bukan diagnosis klinis. Konsultasikan dengan profesional untuk kebutuhan lebih lanjut.
          </p>
        </div>

        <!-- Trait Scores -->
        <div class="bg-white rounded-3xl shadow-sm p-6">
          <h2 class="font-bold text-lg text-[#0d1b1a] mb-4">Skor Trait OCEAN</h2>
          <div class="space-y-4">
            <div v-for="t in traits" :key="t.key">
              <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                  <component :is="t.icon" class="w-4 h-4 flex-shrink-0" :class="t.ic" />
                  <span class="text-sm font-semibold text-[#0d1b1a]">{{ t.label }}</span>
                </div>
                <span class="text-sm font-bold text-[#4c9a93]">{{ scores[t.key] ?? '—' }} / 5</span>
              </div>
              <div class="w-full bg-slate-100 rounded-full h-2.5">
                <div
                  class="h-2.5 rounded-full transition-all duration-700"
                  :class="t.bar"
                  :style="{ width: (scores[t.key] ? scorePercent(scores[t.key]) : 0) + '%' }"
                ></div>
              </div>

              <!-- Category Analysis Insight -->
              <transition
                enter-active-class="transition ease-out duration-300 transform"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
              >
                <div v-if="insight && insight.category_analysis && insight.category_analysis[t.key]" class="mt-3 bg-slate-50/80 border border-slate-100 rounded-2xl p-4 text-sm text-slate-600 leading-relaxed relative">
                  <!-- Small arrow pointing up -->
                  <div class="absolute -top-2 left-6 w-4 h-4 bg-slate-50/80 border-t border-l border-slate-100 transform rotate-45"></div>
                  <div class="relative z-10 font-medium text-slate-700">
                    {{ insight.category_analysis[t.key] }}
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </div>

        <!-- AI Insight sections -->
        <div v-if="insight" class="space-y-4">
          <!-- Character Type Banner -->
          <div v-if="insight.character_type" class="bg-gradient-to-r from-[#40D5C8]/20 to-[#4c9a93]/10 rounded-3xl p-6 text-center shadow-sm border border-[#40D5C8]/30">
            <p class="text-sm font-bold text-[#4c9a93] mb-1 uppercase tracking-wider">Tipe Karaktermu</p>
            <h2 class="text-2xl font-black text-[#0d1b1a]">{{ insight.character_type }}</h2>
          </div>

          <div
            v-for="section in insightSections"
            :key="section.key"
            class="bg-white rounded-3xl shadow-sm p-6"
          >
            <div class="flex items-center gap-3 mb-4">
              <component :is="section.icon" class="w-5 h-5" :class="section.color" />
              <h3 class="font-bold text-[#0d1b1a]">{{ section.label }}</h3>
            </div>

            <!-- Rendering array for Growth Suggestions -->
            <div v-if="section.key === 'growth_suggestion' && Array.isArray(insight[section.key]) && insight[section.key].length > 0" class="space-y-3">
              <div 
                v-for="(item, index) in insight[section.key]" 
                :key="index"
                class="flex items-start gap-4 p-4 rounded-2xl border border-[#40D5C8]/20 bg-[#40D5C8]/5"
              >
                <!-- Checkmark Icon -->
                <div class="flex-shrink-0 mt-0.5">
                  <div class="w-6 h-6 rounded-full bg-white border border-[#4c9a93] flex items-center justify-center text-[#4c9a93]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <p class="text-sm text-[#0d1b1a] leading-relaxed">
                  {{ item }}
                </p>
              </div>
            </div>

            <!-- Rendering standard string for other sections -->
            <p v-else class="text-sm text-slate-600 leading-relaxed">
              {{ insight[section.key] ?? 'Tidak tersedia.' }}
            </p>
          </div>

          <!-- Feedback Section -->
          <div class="bg-white rounded-3xl shadow-sm p-6 border border-[#40D5C8]/20 mt-4">
            <h3 class="font-bold text-[#0d1b1a] mb-4 text-center">Seberapa akurat hasil analisis ini?</h3>
            
            <div v-if="feedbackSubmitted" class="text-center py-4 text-[#4c9a93] font-medium bg-[#40D5C8]/10 rounded-2xl">
              🎉 Terima kasih atas feedback Anda!
            </div>
            
            <form v-else @submit.prevent="submitFeedback" class="space-y-4">
              <div class="flex justify-center gap-3">
                <button 
                  v-for="score in 5" 
                  :key="score"
                  type="button"
                  @click="feedbackForm.feedback_score = score"
                  class="w-12 h-12 rounded-full font-bold text-lg transition-all flex items-center justify-center"
                  :class="feedbackForm.feedback_score === score ? 'bg-[#4c9a93] text-white shadow-md scale-110' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                >
                  {{ score }}
                </button>
              </div>
              <div v-if="feedbackForm.errors.feedback_score" class="text-red-500 text-sm text-center">
                {{ feedbackForm.errors.feedback_score }}
              </div>
              
              <div>
                <textarea 
                  v-model="feedbackForm.feedback_comment"
                  placeholder="Ada komentar atau saran tambahan? (Opsional)"
                  rows="3"
                  class="w-full rounded-2xl border-slate-200 focus:border-[#4c9a93] focus:ring focus:ring-[#4c9a93]/20 text-sm resize-none p-4"
                ></textarea>
                <div v-if="feedbackForm.errors.feedback_comment" class="text-red-500 text-sm mt-1">
                  {{ feedbackForm.errors.feedback_comment }}
                </div>
              </div>
              
              <button 
                type="submit" 
                :disabled="feedbackForm.processing || !feedbackForm.feedback_score"
                class="w-full bg-[#102220] hover:bg-[#1a3835] text-white font-bold py-3 px-6 rounded-2xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
              >
                <template v-if="feedbackForm.processing">
                  <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>Mengirim...</span>
                </template>
                <span v-else>Kirim Feedback</span>
              </button>
            </form>
          </div>
        </div>

        <!-- AI generating... fallback / Manual Trigger -->
        <div v-else class="bg-gradient-to-br from-[#40D5C8]/10 to-[#4c9a93]/5 rounded-3xl shadow-sm p-8 text-center border border-[#40D5C8]/20">
          <div class="w-16 h-16 mx-auto rounded-full bg-white flex items-center justify-center mb-4 shadow-sm">
            <SparklesIcon class="w-8 h-8 text-[#4c9a93]" />
          </div>
          <h2 class="text-xl font-bold text-[#0d1b1a] mb-2">Buka Potensimu</h2>
          <p class="text-sm text-slate-500 mb-6 px-4">
            Dapatkan analisis mendalam dari AI mengenai kekuatan utama, potensi titik buta, dan saran pengembangan diri yang dipersonalisasi khusus untukmu.
          </p>
          
          <form @submit.prevent="generateInsight">
            <button 
              type="submit" 
              :disabled="isGenerating"
              class="w-full bg-[#102220] hover:bg-[#1a3835] text-white font-bold py-4 px-6 rounded-2xl shadow-lg active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-wait"
            >
              <template v-if="!isGenerating">
                <SparklesIcon class="w-5 h-5" />
                <span>Dapatkan Analisa AI</span>
              </template>
              <template v-else>
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>AI Sedang Membaca...</span>
              </template>
            </button>
          </form>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pb-8 mt-4">
          <Link :href="route('assessment.consent')" class="flex-1 bg-[#40D5C8] text-[#0d1b1a] font-bold py-4 px-2 rounded-2xl shadow-lg shadow-[#40D5C8]/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center text-sm">
            Ulangi Assessment
          </Link>

          <!-- Download as Image if Insight rendered, else normal share -->
          <button 
            v-if="insight"
            @click="downloadImage"
            :disabled="isGeneratingImage"
            class="flex-1 bg-[#0d1b1a] text-white font-bold py-4 px-2 rounded-2xl shadow-lg shadow-[#0d1b1a]/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 text-sm disabled:opacity-50 disabled:hover:scale-100"
          >
            <template v-if="!isGeneratingImage">
              <ShareIcon class="w-5 h-5" />
              <span>Bagikan</span>
            </template>
            <template v-else>
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Memproses...</span>
            </template>
          </button>
          
          <button 
            v-else
            @click="shareResult"
            class="flex-1 bg-[#0d1b1a] text-white font-bold py-4 px-2 rounded-2xl shadow-lg shadow-[#0d1b1a]/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 text-sm"
          >
            <ShareIcon class="w-5 h-5" />
            <span>Bagikan</span>
          </button>
        </div>

      </div>
    </div>

    <!-- HIDDEN SHARE CARD (For html2canvas rendering) -->
    <div style="position: fixed; left: -9999px; top: 0;">
      <!-- Portrait 9:16 Canvas (1080x1920 logical proportions, scaled down for fast render) -->
      <div 
        ref="shareCardRef" 
        class="bg-[#f0fffe] flex flex-col justify-between" 
        style="width: 540px; height: 960px; padding: 40px; box-sizing: border-box; overflow: hidden; position: relative;"
      >
        <!-- Background Accents -->
        <div style="position: absolute; top: -100px; right: -100px; width: 300px; height: 300px; background: rgba(19, 236, 218, 0.2); border-radius: 50%; filter: blur(40px);"></div>
        <div style="position: absolute; bottom: -50px; left: -50px; width: 400px; height: 400px; background: rgba(76, 154, 147, 0.1); border-radius: 50%; filter: blur(50px);"></div>

        <div style="position: relative; z-index: 10;">
          <!-- Header Logo -->
          <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 40px;">
            <img src="/logo.png" style="height: 48px; width: auto; object-fit: contain;" alt="Personaility Logo" />
          </div>

          <!-- Character Wrapper -->
          <div v-if="insight && insight.character_type" style="margin-bottom: 40px;">
            <p style="font-size: 14px; font-weight: 700; color: #4c9a93; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">Tipe Kepribadian Saya</p>
            <h2 style="font-size: 42px; font-weight: 900; color: #0d1b1a; line-height: 1.1; margin: 0;">{{ insight.character_type }}</h2>
          </div>
          <div v-else style="margin-bottom: 40px;">
            <h2 style="font-size: 42px; font-weight: 900; color: #0d1b1a; line-height: 1.1; margin: 0;">Analisis Big Five OCEAN</h2>
          </div>

          <!-- Core Strength Quote -->
          <div v-if="insight && insight.core_strength" style="background: white; border-radius: 24px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 40px; border-left: 6px solid #40D5C8;">
            <p style="font-size: 16px; color: #334155; line-height: 1.6; font-style: italic; margin: 0;">"{{ insight.core_strength }}"</p>
          </div>

          <!-- Simplified Traits -->
          <div style="background: white; border-radius: 28px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
            <div v-for="t in traits" :key="t.key" style="margin-bottom: 20px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 15px; font-weight: 800; color: #0d1b1a;">{{ t.label }}</span>
                <span style="font-size: 15px; font-weight: 900; color: #4c9a93;">{{ scores[t.key] ? Number(scores[t.key]).toFixed(1) : '—' }}</span>
              </div>
              <div style="width: 100%; background: #f1f5f9; border-radius: 999px; height: 12px; overflow: hidden;">
                <div 
                  :class="t.bar"
                  :style="{ width: (scores[t.key] ? scorePercent(scores[t.key]) : 0) + '%', height: '100%', borderRadius: '999px' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; position: relative; z-index: 10;">
          <p style="font-size: 14px; font-weight: 700; color: #4c9a93; margin: 0;">Ikuti tes gratismu sekarang di</p>
          <p style="font-size: 16px; font-weight: 900; color: #0d1b1a; margin-top: 4px;">www.personaility.me</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
