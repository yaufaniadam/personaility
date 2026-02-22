<?php

namespace App\Services;

class TraitPatternService
{
    // public function detect(array $levels): array
    // {
    //     $patterns = [];

    //     // Creative without structure
    //     if ($levels['openness'] === 'tinggi' && $levels['conscientiousness'] === 'rendah') {
    //         $patterns[] = 'creative_without_structure';
    //     }

    //     // Emotional procrastination
    //     if ($levels['neuroticism'] === 'tinggi' && $levels['conscientiousness'] === 'rendah') {
    //         $patterns[] = 'emotional_procrastination';
    //     }

    //     // Social catalyst
    //     if ($levels['extraversion'] === 'tinggi' && $levels['agreeableness'] === 'tinggi') {
    //         $patterns[] = 'social_catalyst';
    //     }

    //     // Calm strategist
    //     if ($levels['neuroticism'] === 'rendah' && $levels['conscientiousness'] === 'tinggi') {
    //         $patterns[] = 'calm_strategist';
    //     }

    //     // Internal tension
    //     if ($levels['neuroticism'] === 'tinggi' && $levels['agreeableness'] === 'rendah') {
    //         $patterns[] = 'internal_tension';
    //     }

    //     return $patterns;
    // }

    public function detect(array $levels): array
{
    $patterns = [];

    $highCount = collect($levels)->where('tinggi')->count();
    $lowCount  = collect($levels)->where('rendah')->count();

    // Activation
    if ($highCount >= 3) {
        $patterns[] = 'high_activation';
    }

    if ($lowCount >= 3) {
        $patterns[] = 'low_activation';
    }

    // Emotional Load
    if ($levels['neuroticism'] === 'tinggi') {
        $patterns[] = 'emotional_pressure';
    }

    // Structure
    if ($levels['conscientiousness'] === 'rendah') {
        $patterns[] = 'low_structure';
    }

    // Social Orientation
    if ($levels['extraversion'] === 'rendah' && $levels['agreeableness'] === 'rendah') {
        $patterns[] = 'social_withdrawal';
    }

    return $patterns;
}
}