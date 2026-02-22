<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => null, // Password nullable for Google users
                'email_verified_at' => now(), // Assume Google emails are verified
            ]);

            Auth::login($user);

            // Check for pending assessment from Guest
            if (session()->has('pending_assessment_id')) {
                $assessmentId = session()->pull('pending_assessment_id');
                
                $assessment = \App\Models\Assessment::find($assessmentId);
                // Claim assessment: must be unowned AND created within the last 1 hour
                if ($assessment && is_null($assessment->user_id) && $assessment->created_at->isAfter(now()->subHour())) {
                    $assessment->update(['user_id' => $user->id]);

                    \App\Models\UserActivityLog::create([
                        'user_id'       => $user->id,
                        'activity_type' => 'assessment_completed',
                        'metadata_json' => ['assessment_id' => $assessment->id, 'note' => 'claimed_after_google_login'],
                    ]);

                    return redirect()->intended(route('assessment.result', $assessment->id, absolute: false));
                }
            }

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            // Log the exception if needed
            return redirect()->route('login')->withErrors(['oauth' => 'Authentication field. Please try again or use your email.']);
        }
    }
}
