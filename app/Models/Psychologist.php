<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psychologist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'photo_path',
        'str_number',
        'sip_number',
        'city',
        'province',
        'city_code',
        'address',
        'province_code',
        'specialization',
        'contact_phone',
        'contact_whatsapp',
        'website',
        'bio',
        'verified_status',
        'active',
    ];

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->photo_path ? '/storage/' . $this->photo_path : null;
    }

    public function indoProvince()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\Province::class, 'province_code', 'code');
    }

    public function indoCity()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\City::class, 'city_code', 'code');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
