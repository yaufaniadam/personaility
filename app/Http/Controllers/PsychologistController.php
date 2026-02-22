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
            'id', 'name', 'city', 'province', 'specialization',
            'contact_phone', 'contact_whatsapp', 'website', 'verified_status',
        ]);

        // Distinct cities for the filter dropdown
        $cities = Psychologist::active()->verified()
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return Inertia::render('Psychologists/Index', [
            'psychologists' => $psychologists,
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
            'psychologist' => $psychologist->only([
                'id', 'name', 'city', 'province', 'specialization', 'verified_status',
                'bio', 'str_number', 'sip_number', 'contact_whatsapp', 'contact_phone', 'website'
            ]),
        ]);
    }
}
