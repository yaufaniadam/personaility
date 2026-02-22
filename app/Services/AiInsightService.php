<?php

namespace App\Services;

use App\Models\AiInsight;
use App\Models\Assessment;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiInsightService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.ai.key');
        $this->model   = config('services.ai.model', 'gpt-4o');
        $this->baseUrl = config('services.ai.base_url', 'https://api.openai.com/v1');
    }

    /**
     * Generate and persist AI insights for a completed assessment.
     * Called once after scoring — NOT on every page load.
     *
     * @param  Assessment  $assessment  Must have answers with questions loaded
     * @param  array<string, float>   $scores  e.g. ['openness' => 3.8, ...]
     * @param  array<string, string>  $levels  e.g. ['openness' => 'moderate', ...]
     * @param  array<string>          $notes   Selected user notes from allow_note questions
     * @param  array<string>          $patterns Detected specific trait combinations
     */
    public function generate(
        Assessment $assessment,
        array $scores,
        array $levels,
        array $notes = [],
        array $patterns = []
    ): ?AiInsight {
        // Skip entirely if no API key is configured
        if (empty($this->apiKey)) {
            Log::warning('AiInsightService: AI_API_KEY is not set. Skipping insight generation.');
            return null;
        }

        $prompt = $this->buildPrompt($scores, $levels, $notes, $patterns);

        $rawResponse = $this->callApi($prompt);

        // callApi returns null on failure — don't crash the assessment flow
        if ($rawResponse === null) {
            return null;
        }

        $parsed = $this->parseResponse($rawResponse);

        return AiInsight::create([
            'assessment_id'     => $assessment->id,
            'character_type'    => $parsed['character_type'],
            'category_analysis' => $parsed['category_analysis'],
            'core_strength'     => $parsed['core_strength'],
            'blind_spot'        => $parsed['blind_spot'],
            'growth_suggestion' => $parsed['growth_suggestion'],
            'stress_pattern'    => $parsed['stress_pattern'],
            'raw_prompt'        => $prompt,
            'raw_response'      => json_encode($rawResponse),
            'model_version'     => $this->model,
        ]);
    }

    // ---------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------

    private function buildPrompt(array $scores, array $levels, array $notes, array $patterns = []): string
    {
        $traitLines = collect($scores)
            ->map(fn ($score, $trait) => sprintf(
                '  - %s: %.2f (Status: %s)',
                ucfirst($trait),
                $score,
                ucfirst($levels[$trait] ?? 'sedang')
            ))
            ->implode("\n");

        $noteLines = ! empty($notes)
            ? "User reflections:\n" . implode("\n", array_map(fn ($n) => "  - {$n}", $notes))
            : 'No reflective notes provided.';

        $patternText = '';
        if (!empty($patterns)) {
            $patternText = "\nPOLA KEPRIBADIAN KHUSUS (DETECTED PATTERNS):\n" .
                collect($patterns)->map(fn ($p) => '- ' . $p)->implode("\n") .
                "\nBerikan penyesuaian analisis berdasarkan pola-pola ini.";
        }

        return <<<PROMPT
Identitas Anda: "Psikolog AI", seorang pemandu pengembangan diri yang hangat, empatik, dan sangat cerdas. Anda bukan mesin penilai, melainkan teman refleksi yang bijaksana.

Tugas Anda:
Analisis profil Big Five (OCEAN) berikut dan ceritakan kembali kepada pengguna siapa mereka dengan cara yang menginspirasi namun tetap jujur.

Data Input:
Skor OCEAN (1-5):
{$traitLines}

PANDUAN MEMBACA SKOR (SANGAT PENTING):
- Skor 1 - 2.5: RENDAH (Low). Skala terbalik dari sifat trait tersebut.
- Skor 2.6 - 3.4: SEDANG (Average/Neutral).
- Skor 3.5 - 5.0: TINGGI (High). Sangat kuat di sifat trait tersebut.

PERATURAN MUTLAK:
Anda TIDAK BOLEH memanipulasi kenyataan skor. Jika statusnya "Rendah", Anda wajib mengartikannya sebagai hal yang berlawanan/lemah dari sifat tersebut, BUKAN memujinya seolah-olah bernilai positif. Jangan menghibur pengguna jika skornya bertolak belakang dengan hal yang baik. Evaluasi fakta skor secara mentah dan objektif.

Contoh spesifik agar Anda TIDAK TERBALIK mengartikan skor:
* Neuroticism Tinggi (3.5 - 5) = Gampang cemas, panik, overthinking, reaktif secara emosional.
* Neuroticism Rendah (1 - 2.5) = Tenang, stabil, tahan banting, santai.
* Conscientiousness Tinggi (3.5 - 5) = Sangat disiplin, perfeksionis, teratur.
* Conscientiousness Rendah (1 - 2.5) = Kurang disiplin, suka menunda tugas, spontan, berantakan.
* (Berlaku logika yang sama untuk Openness, Extraversion, dan Agreeableness).

Konteks Jawaban Esai Pengguna (Refleksi Tambahan): 
{$noteLines}
PENTING: Anda WAJIB mengintegrasikan "Konteks Jawaban Esai" ini ke dalam analisis Anda. Hubungkan skor mereka dengan jawaban esai mereka secara langsung. Gunakan frasa seperti "Sesuai dengan jawabanmu bahwa..." atau "Mengingat kamu menyebutkan bahwa...".

{$patternText}

Output JSON Harus Berisi (Bahasa Indonesia Luwes):
1. "character_type": Berikan nama julukan kreatif untuk kepribadian mereka. Jika skor pengguna menunjukkan ketidakseimbangan yang ekstrem (misalnya Agreeableness sangat tinggi namun Conscientiousness sangat rendah), pilihlah dari daftar "Sisi Gelap" (The Shadow Archetypes) di bawah ini agar mereka mendapat perspektif kritis yang jujur. Jika skor mereka seimbang/positif, berikan julukan inspiratif (Maks 5 kata, misal: 'Sang Penjaga yang Bijaksana').
2. "category_analysis": Sebuah objek dengan 5 key: "openness", "conscientiousness", "extraversion", "agreeableness", dan "neuroticism". Masing-masing diisi dengan penjelasan 2-3 kalimat deskriptif dan luwes yang fokus pada bagaimana skor pada trait tersebut memengaruhi perilaku sehari-hari pengguna. WAJIB kaitkan dengan Konteks Jawaban Esai Pengguna jika ada yang relevan.
3. "core_strength": 2-3 kalimat hangat tentang kekuatan utama mereka. Jangan hanya sebutkan skor tinggi, tapi jelaskan bagaimana sifat itu membantu hidup mereka.
4. "blind_spot": 2-3 kalimat dengan gaya bahasa "Tough Love" (tegas dan jujur). Jangan ragu menunjukkan sisi negatif, kelemahan, atau risiko dari kombinasi skor mereka. Beritahu mereka apa yang orang lain mungkin rasakan saat berhadapan dengan kekurangan mereka, agar insight ini benar-benar menjadi pengingat (misal: sifat "terlalu baik" yang mengorbankan diri sendiri, atau kurang disiplin yang membebani orang lain). Awali dengan "Satu hal yang mungkin perlu kamu perhatikan adalah..."
5. "growth_suggestion": Sebuah *array* berisi 2-3 kalimat (string) saran praktis dan nyata yang bisa langsung dilakukan (actionable). JANGAN gunakan bullet points/strip (-) di dalam teksnya, cukup jadikan elemen array terpisah.
6. "stress_pattern": Deskripsi 2-3 kalimat jujur bagaimana mereka bereaksi saat tertekan berdasarkan kombinasi skor Neuroticism dan trait lainnya. Berikan *warning* yang jujur jika Neuroticism mereka tinggi (misal: cenderung reaktif/panik) agar mereka sadar kapan harus "tarik napas".

Daftar "Sisi Gelap" (The Shadow Archetypes) untuk Ketidakseimbangan Ekstrem:
- Kelompok "The Hidden Burden":
  * Si Penyelamat yang Lelah (High Agreeableness + Low Conscientiousness)
  * Si Peragu yang Kreatif (High Openness + High Neuroticism)
  * Si Penurut yang Rapuh (High Agreeableness + High Neuroticism)
- Kelompok "The Rigid & Cold":
  * Si Robot Perfeksionis (High Conscientiousness + Low Agreeableness)
  * Si Kuno yang Keras Kepala (Low Openness + High Conscientiousness)
  * Si Sinis yang Objektif (Low Agreeableness + Low Extraversion)
- Kelompok "The Chaotic & Loud":
  * Si Magnet Drama (High Extraversion + High Neuroticism)
  * Si Petualang Tanpa Arah (High Extraversion + Low Conscientiousness)
  * Si Pendominasi Percakapan (High Extraversion + Low Agreeableness)

Aturan Emas & Gaya Bahasa (SANGAT PENTING):
- Hindari bahasa seperti laporan akademik yang kaku. Gunakan kalimat reflektif dan natural.
- Buat pembaca merasa dipahami, bukan dihakimi. Gunakan pendekatan empatik.
- Hindari kata "kurang", "lemah", atau "gagal" secara langsung. Gunakan kalimat seperti "kadang", "cenderung", atau "mungkin".
- Gunakan teknik "Dual Framing": Ubah kelemahan menjadi dinamika. Contoh: "Kamu mudah merasa waswas... Itu bukan karena kamu lemah, tapi karena kamu peduli terlalu dalam."
- Gunakan teknik "Bukan karena... tapi karena...". Contoh: "Bukan karena kamu malas, tapi karena sistem kerja kamu belum cocok."
- Gunakan metafora ringan jika cocok.
- Gunakan sapaan "Kamu / Mu" agar terasa personal dan hangat.
- Tulis dalam Bahasa Indonesia yang luwes (seperti cara bicara mentor atau kakak kelas yang baik).
- JANGAN gunakan istilah medis, diagnosa klinis, atau menyarankan terapi/pengobatan.
- Output HANYA berupa JSON murni. Jangan tambahkan penjelasan apapun sebelum/sesudah JSON. Dilarang menggunakan backticks/markdown fences (```json).

Contoh Format Target JSON:
{
  "character_type": "...",
  "category_analysis": {
    "openness": "...",
    "conscientiousness": "...",
    "extraversion": "...",
    "agreeableness": "...",
    "neuroticism": "..."
  },
  "core_strength": "...",
  "blind_spot": "...",
  "growth_suggestion": ["saran 1...", "saran 2...", "saran 3..."],
  "stress_pattern": "..."
}
PROMPT;
    }

    private function callApi(string $prompt): ?array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->baseUrl($this->baseUrl)
                ->timeout(30)
                ->post('/chat/completions', [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'system', 'content' => 'Anda adalah agen psikologi analitik yang sangat kaku terhadap fakta angka skor.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.3,  // Lowered for strict fact adherence
                    'max_tokens'  => 2500,
                ]);

            if ($response->failed()) {
                Log::error('AiInsightService: API request failed.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('AiInsightService: Exception during API call.', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function parseResponse(array $rawResponse): array
    {
        $content = $rawResponse['choices'][0]['message']['content'] ?? '{}';

        // Strip markdown code fences in case the AI ignores negative prompts
        $content = preg_replace('/```(?:json)?\s*(.*?)\s*```/s', '$1', $content);
        $content = trim($content);

        $parsed = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($parsed)) {
            // Fallback: return empty strings rather than crashing the user flow
            Log::warning('AiInsightService: Failed to parse JSON response.', [
                'content' => $content,
            ]);

            return [
                'character_type'   => null,
                'category_analysis' => [],
                'core_strength'    => null,
                'blind_spot'       => null,
                'growth_suggestion' => null,
                'stress_pattern'   => null,
            ];
        }

        return [
            'character_type'   => $parsed['character_type'] ?? null,
            'category_analysis' => $parsed['category_analysis'] ?? [],
            'core_strength'    => $parsed['core_strength'] ?? null,
            'blind_spot'       => $parsed['blind_spot'] ?? null,
            'growth_suggestion' => $parsed['growth_suggestion'] ?? [],
            'stress_pattern'   => $parsed['stress_pattern'] ?? null,
        ];
    }
}
