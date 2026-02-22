<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use Illuminate\Support\Collection;

class TraitScoringService
{
    /**
     * Calculate and persist trait scores for an assessment.
     *
     * Scoring rules (strict per PRD/ERD):
     *  - Likert scale: 1–5
     *  - Reverse score: 6 - value  (only when question.is_reverse = true)
     *  - Trait score  = average of adjusted scores for that trait
     *  - Notes do NOT affect scoring
     *
     * @return array<string, float>  Keyed by trait name
     */
    public function calculate(Assessment $assessment): array
    {
        // Eager-load answers with their questions to get is_reverse and trait
        $answers = $assessment->answers()->with('question')->get();

        // Group adjusted scores by trait
        $grouped = $answers->groupBy(fn (AssessmentAnswer $a) => $a->question->trait->value);

        $scores = [];

        foreach ($grouped as $trait => $traitAnswers) {
            $adjusted = $traitAnswers->map(function (AssessmentAnswer $answer) {
                $raw = $answer->likert_score;

                return $answer->question->is_reverse
                    ? (6 - $raw)
                    : $raw;
            });

            $scores[$trait] = round($adjusted->average(), 2);
        }

        // Persist computed scores back to the assessment
        $assessment->update([
            'openness_score'          => $scores['openness'] ?? null,
            'conscientiousness_score' => $scores['conscientiousness'] ?? null,
            'extraversion_score'      => $scores['extraversion'] ?? null,
            'agreeableness_score'     => $scores['agreeableness'] ?? null,
            'neuroticism_score'       => $scores['neuroticism'] ?? null,
        ]);

        return $scores;
    }

    /**
     * Determine textual level (high/low/moderate) for each trait score.
     * Used as input context for the AI prompt builder.
     *
     * @param  array<string, float>  $scores
     * @return array<string, string>
     */
    // public function levelMap(array $scores): array
    // {
    //     return array_map(function (float $score): string {
    //         if ($score >= 4.0) return 'high';
    //         if ($score <= 2.5) return 'low';
    //         return 'moderate';
    //     }, $scores);
    // }

    public function determineLevel(float $score): string
{
    if ($score >= 3.5) return 'tinggi';
    if ($score >= 2.6) return 'sedang';
    return 'rendah';
}

public function mapLevels(array $scores): array
{
    return collect($scores)
        ->map(fn ($score) => $this->determineLevel($score))
        ->toArray();
}
}
