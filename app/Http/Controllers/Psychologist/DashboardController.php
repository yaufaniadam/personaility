<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $psychologist = $request->user()->psychologist;
        
        return Inertia::render('Psychologist/Dashboard', [
            'psychologist' => $psychologist->append('avatar_url'),
            'provinces' => \Laravolt\Indonesia\Models\Province::pluck('name', 'code'),
            'cities' => \Laravolt\Indonesia\Models\City::where('province_code', $psychologist->province_code)->pluck('name', 'code'),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $psychologist = $request->user()->psychologist;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'str_number' => 'required|string|max:255',
            'sip_number' => 'nullable|string|max:255',
            'province_code' => 'required|string',
            'city_code' => 'required|string',
            'address' => 'nullable|string',
            'specialization' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_whatsapp' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('psychologists/photos', 'public');
            $validated['photo_path'] = $path;
        }

        $provinceName = \Laravolt\Indonesia\Models\Province::where('code', $request->province_code)->first()?->name;
        $cityName = \Laravolt\Indonesia\Models\City::where('code', $request->city_code)->first()?->name;

        $validated['province'] = $provinceName;
        $validated['city'] = $cityName;

        $psychologist->update($validated);

        return back()->with('message', 'Profil berhasil diperbarui.');
    }
}
