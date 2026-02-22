<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    // ERD defines only created_at (no updated_at)
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'activity_type',
        'metadata_json',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata_json' => 'array',
            'created_at'    => 'datetime',
        ];
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ---------------------------------------------------------------
    // Factory helper – set created_at on creation
    // ---------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            if (! $log->created_at) {
                $log->created_at = now();
            }
        });
    }
}
