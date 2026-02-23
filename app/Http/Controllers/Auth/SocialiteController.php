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
    public function callback(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Google callback hit', [
            'session_id' => $request->session()->getId(),
            'has_pending' => $request->session()->has('pending_assessment_id'),
            'pending_id' => $request->session()->get('pending_assessment_id'),
        ]);

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
            
            \Illuminate\Support\Facades\Log::info('User authenticated via Google', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            $request->session()->regenerate();

            \Illuminate\Support\Facades\Log::info('Session regenerated', [
                'new_session_id' => $request->session()->getId(),
                'has_pending_after_regen' => $request->session()->has('pending_assessment_id'),
            ]);

            // Check for pending assessment from Guest
            if ($request->session()->has('pending_assessment_id')) {
                $assessmentId = $request->session()->get('pending_assessment_id');
                $assessment = \App\Models\Assessment::find($assessmentId);

                \Illuminate\Support\Facades\Log::info('Checking pending assessment', [
                    'assessment_id' => $assessmentId,
                    'found' => (bool)$assessment,
                    'user_id_in_assessment' => $assessment ? $assessment->user_id : null
                ]);

                // Case 1: Assessment not found or expired (older than 24h) and unowned -> Clean up
                if (!$assessment || (is_null($assessment->user_id) && $assessment->created_at->isBefore(now()->subDay()))) {
                    $request->session()->forget('pending_assessment_id');
                }
                // Case 2: Valid unowned assessment -> Claim it
                elseif (is_null($assessment->user_id)) {
                    $assessment->update(['user_id' => $user->id]);

                    \App\Models\UserActivityLog::create([
                        'user_id'       => $user->id,
                        'activity_type' => 'assessment_completed',
                        'metadata_json' => ['assessment_id' => $assessment->id, 'note' => 'claimed_after_google_login'],
                    ]);

                    $request->session()->forget('pending_assessment_id');
                    \Illuminate\Support\Facades\Log::info('Assessment claimed, redirecting to result', ['assessment_id' => $assessment->id]);
                    return redirect()->route('assessment.result', $assessment->id);
                }
                // Case 3: Already owned by THIS user -> Redirect
                elseif ($assessment->user_id === $user->id) {
                    $request->session()->forget('pending_assessment_id');
                    \Illuminate\Support\Facades\Log::info('Assessment already owned, redirecting to result', ['assessment_id' => $assessment->id]);
                    return redirect()->route('assessment.result', $assessment->id);
                }
                // Case 4: Owned by someone else -> Clean up
                else {
                    $request->session()->forget('pending_assessment_id');
                    \Illuminate\Support\Facades\Log::warning('Assessment owned by someone else', [
                        'assessment_id' => $assessment->id,
                        'owner_id' => $assessment->user_id,
                        'current_user_id' => $user->id
                    ]);
                }
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->withErrors(['oauth' => 'Authentication failed. Please try again or use your email. Error: ' . $e->getMessage()]);
        }
    }
}
