<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        // Latest completed assessment with AI insight
        $latest = $user->assessments()
            ->with('aiInsight')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();

        // All assessment history (lightweight — no aiInsight loaded here)
        $history = $user->assessments()
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->get([
                'id', 'openness_score', 'conscientiousness_score',
                'extraversion_score', 'agreeableness_score', 'neuroticism_score',
                'completed_at', 'created_at',
            ]);

        return Inertia::render('Dashboard', [
            'latestAssessment' => $latest ? $latest->only([
                'id', 'openness_score', 'conscientiousness_score',
                'extraversion_score', 'agreeableness_score', 'neuroticism_score',
                'completed_at', 'created_at',
            ]) : null,
            'latestInsight'    => $latest?->aiInsight ? $latest->aiInsight->only([
                'core_strength', 'growth_suggestion',
            ]) : null,
            'history'          => $history,
        ]);
    }
}
