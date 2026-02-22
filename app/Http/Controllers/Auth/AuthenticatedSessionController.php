<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Check for pending assessment from Guest
        if (session()->has('pending_assessment_id')) {
            $assessmentId = session()->pull('pending_assessment_id');
            
            $assessment = \App\Models\Assessment::find($assessmentId);
            if ($assessment && is_null($assessment->user_id)) {
                $assessment->update(['user_id' => $user->id]);

                \App\Models\UserActivityLog::create([
                    'user_id'       => $user->id,
                    'activity_type' => 'assessment_completed',
                    'metadata_json' => ['assessment_id' => $assessment->id, 'note' => 'claimed_after_login'],
                ]);

                return redirect()->intended(route('assessment.result', $assessment->id, absolute: false));
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
