<script setup>
import { ref, computed } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  questions: Array,
});

// ── Trait metadata ────────────────────────────────────────────────────────────
const traitMeta = {
  openness: {
    label: 'Openness',
    tagline: 'Keterbukaan terhadap Pengalaman',
    emoji: '🔭',
    illustration: '🌌',
    desc: 'Bagian ini mengukur seberapa terbuka kamu terhadap ide-ide baru, kreativitas, dan pengalaman yang belum familiar. Tidak ada jawaban benar atau salah.',
    gradient: 'from-purple-50 to-purple-100',
    accent: 'bg-purple-500',
    ring: 'ring-purple-200',
    badge: 'bg-purple-100 text-purple-700',
    bar: 'bg-purple-400',
    textAccent: 'text-purple-600',
    optionActive: 'border-purple-300 bg-purple-50',
    optionHover: 'hover:border-purple-300',
    circleActive: 'border-purple-500 bg-purple-500 text-white',
  },
  conscientiousness: {
    label: 'Conscientiousness',
    tagline: 'Keteraturan & Tanggung Jawab',
    emoji: '📋',
    illustration: '⚙️',
    desc: 'Bagian ini mengukur seberapa teratur, disiplin, dan bertanggung jawab kamu dalam menjalani keseharianmu. Jawab sesuai kebiasaan nyatamu.',
    gradient: 'from-blue-50 to-blue-100',
    accent: 'bg-blue-500',
    ring: 'ring-blue-200',
    badge: 'bg-blue-100 text-blue-700',
    bar: 'bg-blue-400',
    textAccent: 'text-blue-600',
    optionActive: 'border-blue-300 bg-blue-50',
    optionHover: 'hover:border-blue-300',
    circleActive: 'border-blue-500 bg-blue-500 text-white',
  },
  extraversion: {
    label: 'Extraversion',
    tagline: 'Energi Sosial',
    emoji: '🌟',
    illustration: '🎉',
    desc: 'Bagian ini mengukur seberapa berenergi dan nyaman kamu saat berinteraksi dengan orang lain. Tidak ada yang lebih baik antara introvert atau ekstrovert.',
    gradient: 'from-amber-50 to-yellow-100',
    accent: 'bg-amber-400',
    ring: 'ring-amber-200',
    badge: 'bg-amber-100 text-amber-700',
    bar: 'bg-amber-400',
    textAccent: 'text-amber-600',
    optionActive: 'border-amber-300 bg-amber-50',
    optionHover: 'hover:border-amber-300',
    circleActive: 'border-amber-400 bg-amber-400 text-amber-900',
  },
  agreeableness: {
    label: 'Agreeableness',
    tagline: 'Empati & Kerja Sama',
    emoji: '🤝',
    illustration: '💚',
    desc: 'Bagian ini mengukur seberapa empatik, kooperatif, dan hangat kamu dalam berhubungan dengan orang lain.',
    gradient: 'from-[#40D5C8]/10 to-[#40D5C8]/20',
    accent: 'bg-[#40D5C8]',
    ring: 'ring-[#40D5C8]/30',
    badge: 'bg-[#40D5C8]/20 text-[#40D5C8]',
    bar: 'bg-[#40D5C8]',
    textAccent: 'text-[#40D5C8]',
    optionActive: 'border-[#40D5C8]/40 bg-[#40D5C8]/5',
    optionHover: 'hover:border-[#40D5C8]/40',
    circleActive: 'border-[#40D5C8] bg-[#40D5C8] text-white',
  },
  neuroticism: {
    label: 'Neuroticism',
    tagline: 'Stabilitas Emosi',
    emoji: '🌊',
    illustration: '🧘',
    desc: 'Bagian ini mengukur bagaimana kamu merespons tekanan, stres, dan emosi yang sulit. Skor tinggi bukan berarti buruk — hanya refleksi diri.',
    gradient: 'from-[#FF61C5]/10 to-[#FF61C5]/20',
    accent: 'bg-[#FF61C5]',
    ring: 'ring-[#FF61C5]/30',
    badge: 'bg-[#FF61C5]/20 text-[#FF61C5]',
    bar: 'bg-[#FF61C5]',
    textAccent: 'text-[#FF61C5]',
    optionActive: 'border-[#FF61C5]/40 bg-[#FF61C5]/5',
    optionHover: 'hover:border-[#FF61C5]/40',
    circleActive: 'border-[#FF61C5] bg-[#FF61C5] text-white',
  },
};

// ── Build screen list: [prolog, q, q, ..., prolog, q, q, ...] ─────────────────
const screens = computed(() => {
  const result = [];
  let lastTrait = null;
  props.questions.forEach((q, i) => {
    if (q.trait !== lastTrait) {
      result.push({ type: 'prolog', trait: q.trait });
      lastTrait = q.trait;
    }
    result.push({ type: 'question', questionIndex: i });
  });
  return result;
});

const currentScreen = ref(0);
const currentScreenData = computed(() => screens.value[currentScreen.value]);

// Track how many questions answered for progress bar
const questionsAnswered = computed(() =>
  form.answers.filter(a => a.likert_score !== null).length
);
const totalQuestions = computed(() => props.questions.length);
const progress = computed(() => Math.round((questionsAnswered.value / totalQuestions.value) * 100));

// Current question when on a question screen
const currentQuestion = computed(() => {
  if (currentScreenData.value?.type !== 'question') return null;
  return props.questions[currentScreenData.value.questionIndex];
});
const currentAnswer = computed(() => {
  if (currentScreenData.value?.type !== 'question') return null;
  return form.answers[currentScreenData.value.questionIndex];
});
const currentTrait = computed(() => currentScreenData.value?.trait ?? currentQuestion.value?.trait);
const currentMeta = computed(() => traitMeta[currentTrait.value] ?? traitMeta.openness);

const form = useForm({
  consent: true,
  answers: props.questions.map(q => ({
    question_id: q.id,
    likert_score: null,
    note_text: '',
  })),
});

const likertLabels = [
  'Sangat Tidak Setuju',
  'Tidak Setuju',
  'Netral',
  'Setuju',
  'Sangat Setuju',
];

function selectScore(score) {
  form.answers[currentScreenData.value.questionIndex].likert_score = score;
}

function advance() {
  if (currentScreen.value < screens.value.length - 1) {
    currentScreen.value++;
  }
}

function goBack() {
  if (currentScreen.value > 0) {
    currentScreen.value--;
  }
}

function canAdvanceQuestion() {
  return currentAnswer.value?.likert_score !== null;
}

function submit() {
  form.post(route('assessment.store'), {
    onError: () => window.scrollTo(0, 0),
  });
}

const isLastScreen = computed(() => currentScreen.value === screens.value.length - 1);

// Which question number (1-based) is this in the overall list?
const questionNumber = computed(() => {
  if (currentScreenData.value?.type !== 'question') return null;
  return currentScreenData.value.questionIndex + 1;
});
</script>

<template>
  <Head title="Assessment" />
  <div class="font-sans antialiased text-slate-900 bg-[#f6f8f8]">
    <!-- Minimal Header -->
    <nav class="sticky top-0 z-50 flex items-center justify-center bg-[#f6f8f8]/80 backdrop-blur-md px-6 py-4 border-b border-[#4c9a93]/10">
      <Link :href="route('home')" class="flex items-center gap-2">
        <img src="/logo.png" alt="Personaility Logo" class="h-8 w-auto object-contain shrink-0" />
      </Link>
    </nav>

    <div class="min-h-[90vh] py-6 px-4 transition-colors duration-300">
      <div class="max-w-lg mx-auto">

        <!-- Progress bar (always visible) -->
        <div class="mb-6">
          <div class="flex items-center justify-between text-xs mb-2">
            <span class="text-slate-400">{{ questionsAnswered }} / {{ totalQuestions }} pertanyaan</span>
            <span class="font-bold" :class="currentMeta.textAccent">{{ progress }}%</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-2">
            <div
              class="h-2 rounded-full transition-all duration-700"
              :class="currentMeta.accent"
              :style="{ width: progress + '%' }"
            ></div>
          </div>
        </div>

        <!-- ─────────────────────────────────────────────────── -->
        <!-- PROLOG SCREEN                                        -->
        <!-- ─────────────────────────────────────────────────── -->
        <transition name="slide-up" mode="out-in">
          <div v-if="currentScreenData?.type === 'prolog'" :key="'prolog-' + currentScreenData.trait">
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

              <!-- Illustration area -->
              <div
                class="flex flex-col items-center justify-center py-14 px-6 bg-gradient-to-br"
                :class="currentMeta.gradient"
              >
                <!-- Cartoon-style illustration bubble -->
                <div
                  class="w-32 h-32 rounded-full flex items-center justify-center shadow-lg ring-4 mb-6 bg-white"
                  :class="currentMeta.ring"
                >
                  <span class="text-6xl select-none">{{ currentMeta.illustration }}</span>
                </div>

                <!-- Trait badge -->
                <span
                  class="text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full"
                  :class="currentMeta.badge"
                >
                  {{ currentMeta.label }}
                </span>

                <h2 class="text-2xl font-extrabold text-[#0d1b1a] mt-3 text-center">
                  {{ currentMeta.tagline }}
                </h2>

                <span class="text-3xl mt-1 select-none">{{ currentMeta.emoji }}</span>
              </div>

              <!-- Description & action -->
              <div class="px-6 py-8 text-center space-y-6">
                <p class="text-sm text-slate-600 leading-relaxed">
                  {{ currentMeta.desc }}
                </p>

                <button
                  @click="advance"
                  class="w-full py-4 rounded-2xl font-bold text-white shadow-lg active:scale-95 transition-transform"
                  :class="currentMeta.accent"
                >
                  Mulai Pertanyaan →
                </button>

                <p class="text-xs text-slate-400">Jawab dengan jujur · Tidak ada benar atau salah</p>
              </div>
            </div>
          </div>

          <!-- ─────────────────────────────────────────────────── -->
          <!-- QUESTION SCREEN                                      -->
          <!-- ─────────────────────────────────────────────────── -->
          <div v-else-if="currentScreenData?.type === 'question'" :key="'q-' + currentScreenData.questionIndex">
            <div class="bg-white rounded-3xl shadow-sm p-6">

              <!-- Trait badge + question number -->
              <div class="flex items-center justify-between mb-5">
                <span
                  class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-full"
                  :class="currentMeta.badge"
                >
                  {{ currentMeta.emoji }} {{ currentMeta.label }}
                </span>
                <span class="text-xs text-slate-400">No. {{ questionNumber }}</span>
              </div>

              <!-- Question text -->
              <h2 class="text-xl font-bold text-[#0d1b1a] leading-snug mb-8">
                {{ currentQuestion.question_text }}
              </h2>

              <!-- Likert scale -->
              <div class="space-y-3">
                <button
                  v-for="score in 5"
                  :key="score"
                  @click="selectScore(score)"
                  class="w-full flex items-center gap-4 p-4 rounded-2xl border-2 transition-all"
                  :class="currentAnswer.likert_score === score
                    ? currentMeta.optionActive
                    : ['border-slate-100 bg-[#f6f8f8]', currentMeta.optionHover]"
                >
                  <div
                    class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-sm flex-shrink-0 transition-all"
                    :class="currentAnswer.likert_score === score
                      ? currentMeta.circleActive
                      : 'border-slate-300 text-slate-400'"
                  >
                    {{ score }}
                  </div>
                  <span
                    class="text-sm font-medium"
                    :class="currentAnswer.likert_score === score ? 'text-[#0d1b1a]' : 'text-slate-500'"
                  >
                    {{ currentQuestion.options ? currentQuestion.options[score - 1] : likertLabels[score - 1] }}
                  </span>
                </button>
              </div>

              <!-- Context note (Always available) -->
              <div class="mt-6">
                <label class="block text-xs flex items-center gap-1 font-semibold text-slate-500 mb-2">
                  <span class="text-slate-400">💬</span> Ceritakan konteks Anda (Opsional)
                </label>
                <textarea
                  v-model="form.answers[currentScreenData.questionIndex].note_text"
                  rows="3"
                  placeholder="Ceritakan lebih lanjut jika ada..."
                  class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#40D5C8] resize-none pb-8 relative"
                ></textarea>
                <p class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                  <span class="text-slate-300">✨</span> AI akan menganalisis ceritamu untuk insight lebih personal.
                </p>
              </div>
            </div>

            <!-- Navigation -->
            <div class="flex gap-3 mt-4">
              <button
                @click="goBack"
                class="flex-1 py-4 rounded-2xl border-2 border-slate-200 font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
              >
                ← Kembali
              </button>

              <!-- Next question -->
              <button
                v-if="!isLastScreen"
                @click="advance"
                :disabled="!canAdvanceQuestion()"
                class="flex-1 py-4 rounded-2xl font-bold text-white transition-all"
                :class="canAdvanceQuestion()
                  ? [currentMeta.accent, 'shadow-lg active:scale-95']
                  : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
              >
                Lanjut →
              </button>

              <!-- Submit on last question screen -->
              <button
                v-else
                @click="submit"
                :disabled="!canAdvanceQuestion() || form.processing"
                class="flex-1 py-4 rounded-2xl font-bold text-white transition-all"
                :class="canAdvanceQuestion() && !form.processing
                  ? 'bg-[#4c9a93] shadow-lg active:scale-95'
                  : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
              >
                {{ form.processing ? 'Memproses...' : 'Selesai →' }}
              </button>
            </div>

            <!-- Validation error -->
            <p v-if="form.errors.answers" class="text-red-500 text-xs text-center mt-3">
              {{ form.errors.answers }}
            </p>
          </div>
        </transition>

      </div>
    </div>
  </div>
</template>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}
.slide-up-enter-from {
  opacity: 0;
  transform: translateY(16px);
}
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
