<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'question_id',
        'likert_score',
        'note_text',
    ];

    protected function casts(): array
    {
        return [
            'likert_score' => 'integer',
        ];
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
