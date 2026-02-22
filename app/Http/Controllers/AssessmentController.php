<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssessmentRequest;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\UserActivityLog;
use App\Services\AiInsightService;
use App\Services\TraitScoringService;
use App\Services\TraitPatternService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly TraitScoringService $scoringService,
        private readonly AiInsightService $aiService,
        private readonly TraitPatternService $patternService,
    ) {}


    /**
     * Show the consent page.
     */
    public function consent(): Response
    {
        return Inertia::render('Assessment/Consent');
    }

    /**
     * Show assessment questions page.
     */
    public function create(): Response
    {
        if (!session()->has('active_assessment_id')) {
            return redirect()->route('assessment.consent');
        }

        $questions = Question::active()
            ->ordered()
            ->get(['id', 'trait', 'question_text', 'options', 'is_reverse', 'allow_note', 'order_number']);

        return Inertia::render('Assessment/Create', [
            'questions' => $questions,
        ]);
    }

    /**
     * Start the assessment and capture guest details.
     */
    public function start(\Illuminate\Http\Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'guest_name'      => ['nullable', 'string', 'max:255'],
            'guest_gender'    => ['nullable', 'string', 'in:Laki-laki,Perempuan'],
            'guest_age_range' => ['nullable', 'string', 'in:< 18,18 - 24,25 - 34,35+'],
        ]);

        $assessment = Assessment::create([
            'user_id'         => $user ? $user->id : null,
            'guest_name'      => $validated['guest_name'] ?? null,
            'guest_gender'    => $validated['guest_gender'] ?? null,
            'guest_age_range' => $validated['guest_age_range'] ?? null,
            'version'         => config('app.question_version', '1.0'),
        ]);

        // Store active assessment ID in session for this flow
        session()->put('active_assessment_id', $assessment->id);

        return redirect()->route('assessment.create');
    }

    /**
     * Submit assessment: validate → save answers → score → generate AI insight.
     */
    public function store(StoreAssessmentRequest $request): RedirectResponse
    {
        $user = $request->user(); // Will be null for guests

        $assessmentId = session('active_assessment_id');
        
        if (!$assessmentId) {
            return redirect()->route('assessment.consent')->with('error', 'Sesi asesmen tidak ditemukan. Silakan mulai ulang.');
        }

        $assessment = Assessment::findOrFail($assessmentId);

        // 1. Bulk-insert answers
        $answerData = collect($request->validated('answers'))
            ->map(fn (array $a) => [
                'assessment_id' => $assessment->id,
                'question_id'   => $a['question_id'],
                'likert_score'  => $a['likert_score'],
                'note_text'     => $a['note_text'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ])
            ->all();

        $assessment->answers()->insert($answerData);

        // 2. Calculate and persist trait scores
        // Re-load with questions for scoring
        $assessment->load('answers.question');
        $scores = $this->scoringService->calculate($assessment);
        $levels = $this->scoringService->mapLevels($scores);

        // 3. Collect user notes for AI context (optional, from allow_note questions only)
        $notes = $assessment->answers
            ->filter(fn ($a) => $a->question->allow_note && ! empty($a->note_text))
            ->map(fn ($a) => $a->note_text)
            ->values()
            ->all();

        // 4. User notes collected, but AI generation is NOW MANUAL (opt-in).

        // 5. Mark assessment as completed
        $assessment->update(['completed_at' => now()]);

        // Clear active assessment session
        session()->forget('active_assessment_id');

        if ($user) {
            // 6. Log activity for authenticated user
            UserActivityLog::create([
                'user_id'       => $user->id,
                'activity_type' => 'assessment_completed',
                'metadata_json' => ['assessment_id' => $assessment->id],
            ]);

            return redirect()->route('assessment.result', $assessment->id);
        }

        // Guest user: save assessment ID in session and redirect to register
        session()->put('pending_assessment_id', $assessment->id);

        return redirect()->route('register')->with('status', 'Assessment selesai! Daftar atau masuk untuk melihat hasil dan Analisa AI Anda.');
    }

    /**
     * Show AI insight & results page.
     */
    public function result(Assessment $assessment): Response
    {
        $this->authorize('view', $assessment);

        $assessment->load('aiInsight');

        return Inertia::render('Assessment/Result', [
            'assessment' => $assessment->only(['id']),
            'insight'    => $assessment->aiInsight ? $assessment->aiInsight->only([
                'character_type', 'core_strength', 'blind_spot', 'growth_suggestion',
                'stress_pattern', 'category_analysis', 'feedback_score'
            ]) : null,
            'scores'     => $assessment->traitScores(),
        ]);
    }

    /**
     * Show assessment history for the authenticated user.
     */
    public function history(): Response
    {
        $assessments = auth()->user()
            ->assessments()
            ->orderByDesc('completed_at')
            ->get()
            ->map(function ($assessment) {
                return $assessment->only([
                    'id', 'completed_at', 'version',
                    'openness_score', 'conscientiousness_score',
                    'extraversion_score', 'agreeableness_score', 'neuroticism_score'
                ]);
            });

        return Inertia::render('Assessment/History', [
            'assessments' => $assessments,
        ]);
    }

    /**
     * Manually trigger AI insight generation for an assessment.
     */
    public function generateInsight(Assessment $assessment): RedirectResponse
    {
        $this->authorize('view', $assessment);

        $lock = \Illuminate\Support\Facades\Cache::lock("gen_insight_{$assessment->id}", 30);

        if (!$lock->get()) {
            return redirect()->back()->with('error', 'Analisa sedang diproses. Mohon tunggu.');
        }

        try {
            // Guard: return early if insight already exists (prevent redundant API calls)
            if ($assessment->aiInsight()->exists()) {
                return redirect()->route('assessment.result', $assessment->id)
                    ->with('info', 'Analisa AI sudah tersedia.');
            }

            $assessment->load('answers.question');
            
            $scores = $this->scoringService->calculate($assessment);
            $levels = $this->scoringService->mapLevels($scores);

            $patterns = $this->patternService->detect($levels);

            $notes = $assessment->answers
                ->filter(fn ($a) => $a->question->allow_note && ! empty($a->note_text))
                ->map(fn ($a) => $a->note_text)
                ->values()
                ->all();

            $this->aiService->generate($assessment, $scores, $levels, $notes, $patterns);
        } finally {
            $lock->release();
        }

        return redirect()->route('assessment.result', $assessment->id);
    }

    /**
     * Store feedback for AI insight.
     */
    public function storeFeedback(\Illuminate\Http\Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorize('view', $assessment);

        $validated = $request->validate([
            'feedback_score'   => ['required', 'integer', 'min:1', 'max:5'],
            'feedback_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $insight = $assessment->aiInsight;

        if ($insight) {
            $insight->update([
                'feedback_score'   => $validated['feedback_score'],
                'feedback_comment' => $validated['feedback_comment'],
            ]);
        }

        return redirect()->back();
    }
}
