<?php

namespace App\Models;

use App\Enums\PersonalityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'trait',
        'question_text',
        'options',
        'is_reverse',
        'allow_note',
        'order_number',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'trait'        => PersonalityTrait::class,
            'options'      => 'array',
            'is_reverse'   => 'boolean',
            'allow_note'   => 'boolean',
            'active'       => 'boolean',
            'order_number' => 'integer',
        ];
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_number');
    }
}
