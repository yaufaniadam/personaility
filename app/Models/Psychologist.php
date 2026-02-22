<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psychologist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'str_number',
        'sip_number',
        'city',
        'province',
        'specialization',
        'contact_phone',
        'contact_whatsapp',
        'website',
        'bio',
        'verified_status',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'verified_status' => 'boolean',
            'active'          => 'boolean',
        ];
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('verified_status', true);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }
}
