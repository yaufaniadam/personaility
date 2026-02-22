<?php

namespace App\Http\Controllers\Psychologist;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    public function showRegistrationForm(): Response
    {
        return Inertia::render('Psychologist/Register', [
            'provinces' => \Laravolt\Indonesia\Models\Province::pluck('name', 'code'),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'gender' => 'required|string|in:male,female',
            'str_number' => 'required|string|max:255',
            'sip_number' => 'nullable|string|max:255',
            'province_code' => 'required|string',
            'city_code' => 'required|string',
            'address' => 'nullable|string',
            'specialization' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_whatsapp' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::Psychologist,
        ]);

        $provinceName = \Laravolt\Indonesia\Models\Province::where('code', $request->province_code)->first()?->name;
        $cityName = \Laravolt\Indonesia\Models\City::where('code', $request->city_code)->first()?->name;

        Psychologist::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'gender' => $request->gender,
            'str_number' => $request->str_number,
            'sip_number' => $request->sip_number,
            'province_code' => $request->province_code,
            'city_code' => $request->city_code,
            'address' => $request->address,
            'province' => $provinceName,
            'city' => $cityName,
            'specialization' => $request->specialization,
            'contact_phone' => $request->contact_phone,
            'contact_whatsapp' => $request->contact_whatsapp,
            'bio' => $request->bio,
            'active' => true,
            'verified_status' => false, // Needs admin verification
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('psychologist.dashboard');
    }
}
