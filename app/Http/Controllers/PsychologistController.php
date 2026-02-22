<?php

namespace App\Http\Controllers;

use App\Models\Psychologist;
use Inertia\Inertia;
use Inertia\Response;

class PsychologistController extends Controller
{
    /**
     * Public directory listing – filter by city.
     * Informational only. No booking. No payment.
     */
    public function index(): Response
    {
        $city = request('city');

        $query = Psychologist::active()
            ->verified()
            ->orderBy('name');

        if ($city) {
            $query->byCity($city);
        }

        $psychologists = $query->get([
            'id', 'name', 'gender', 'photo_path', 'city', 'address', 'province', 'specialization',
            'contact_phone', 'contact_whatsapp', 'website', 'verified_status',
        ]);

        // Distinct cities for the filter dropdown
        $cities = Psychologist::active()
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return Inertia::render('Psychologists/Index', [
            'psychologists' => $psychologists->each->append('avatar_url'),
            'cities'        => $cities,
            'selectedCity'  => $city,
        ]);
    }

    /**
     * Public detail page.
     * Informational only. No booking. No payment.
     */
    public function show(Psychologist $psychologist): Response
    {
        abort_unless($psychologist->active && $psychologist->verified_status, 404);

        return Inertia::render('Psychologists/Show', [
            'psychologist' => $psychologist->append('avatar_url')->only([
                'id', 'name', 'gender', 'city', 'address', 'province', 'specialization', 'verified_status', 'avatar_url', 'photo_path',
                'bio', 'str_number', 'sip_number', 'contact_whatsapp', 'contact_phone', 'website'
            ]),
        ]);
    }
}
