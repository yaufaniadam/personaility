<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_gender',
        'guest_age_range',
        'openness_score',
        'conscientiousness_score',
        'extraversion_score',
        'agreeableness_score',
        'neuroticism_score',
        'openness_percentile',
        'conscientiousness_percentile',
        'extraversion_percentile',
        'agreeableness_percentile',
        'neuroticism_percentile',
        'version',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'openness_score'               => 'decimal:2',
            'conscientiousness_score'      => 'decimal:2',
            'extraversion_score'           => 'decimal:2',
            'agreeableness_score'          => 'decimal:2',
            'neuroticism_score'            => 'decimal:2',
            'openness_percentile'          => 'integer',
            'conscientiousness_percentile' => 'integer',
            'extraversion_percentile'      => 'integer',
            'agreeableness_percentile'     => 'integer',
            'neuroticism_percentile'       => 'integer',
            'completed_at'                 => 'datetime',
        ];
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function aiInsight(): HasOne
    {
        return $this->hasOne(AiInsight::class);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Returns all trait scores as a keyed array.
     */
    public function traitScores(): array
    {
        return [
            'openness'          => $this->openness_score,
            'conscientiousness' => $this->conscientiousness_score,
            'extraversion'      => $this->extraversion_score,
            'agreeableness'     => $this->agreeableness_score,
            'neuroticism'       => $this->neuroticism_score,
        ];
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
