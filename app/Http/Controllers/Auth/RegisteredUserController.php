<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => 'nullable|string|max:20',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        // Generate a simple default name from email
        $defaultName = explode('@', $request->email)[0];

        $user = User::create([
            'name' => ucfirst($defaultName),
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Check for pending assessment from Guest
        if (session()->has('pending_assessment_id')) {
            $assessmentId = session()->pull('pending_assessment_id');
            
            $assessment = \App\Models\Assessment::find($assessmentId);
            if ($assessment && is_null($assessment->user_id)) {
                $assessment->update(['user_id' => $user->id]);

                \App\Models\UserActivityLog::create([
                    'user_id'       => $user->id,
                    'activity_type' => 'assessment_completed',
                    'metadata_json' => ['assessment_id' => $assessment->id, 'note' => 'claimed_after_registration'],
                ]);

                return redirect(route('assessment.result', $assessment->id, absolute: false));
            }
        }

        return redirect(route('dashboard', absolute: false));
    }
}
