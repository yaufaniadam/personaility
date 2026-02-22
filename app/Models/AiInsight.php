<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'character_type',
        'category_analysis',
        'core_strength',
        'blind_spot',
        'growth_suggestion',
        'stress_pattern',
        'raw_prompt',
        'raw_response',
        'model_version',
        'feedback_score',
        'feedback_comment',
    ];

    protected $casts = [
        'category_analysis' => 'array',
        'growth_suggestion' => 'array',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
