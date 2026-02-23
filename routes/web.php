<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PsychologistController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ---------------------------------------------------------------
// Public routes
// ---------------------------------------------------------------

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::get('/terms-conditions', function () {
    return Inertia::render('Terms');
})->name('terms');

Route::get('/privacy-policy', function () {
    return Inertia::render('Privacy');
})->name('privacy');

Route::get('/psychologists', [PsychologistController::class, 'index'])->name('psychologists.index');
Route::get('/psychologists/{psychologist}', [PsychologistController::class, 'show'])->name('psychologists.show');

Route::get('/api/cities/{province}', function ($province) {
    return \Laravolt\Indonesia\Models\City::where('province_code', $province)->pluck('name', 'code');
});

// ---------------------------------------------------------------
// OAuth Routes
// ---------------------------------------------------------------
// The callback URL must be added to the Google Cloud Console "Authorized redirect URIs".
// Format: {APP_URL}/auth/google/callback
// Example: https://personaility.me/auth/google/callback
Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'callback'])->name('auth.google.callback');

// ---------------------------------------------------------------
// Assessment flow - Public
// ---------------------------------------------------------------
Route::get('/assessment/consent', [AssessmentController::class, 'consent'])->name('assessment.consent');
Route::post('/assessment/start', [AssessmentController::class, 'start'])
    ->name('assessment.start')
    ->middleware('throttle:60,1'); // Max 60 starts per minute per IP
Route::get('/assessment/create', [AssessmentController::class, 'create'])->name('assessment.create');
Route::post('/assessment', [AssessmentController::class, 'store'])
    ->name('assessment.store')
    ->middleware('throttle:60,1'); // Layer 1: Max 60 submissions per 1 minute per IP

// ---------------------------------------------------------------
// Authenticated routes
// ---------------------------------------------------------------

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Assessment flow - Authenticated Only
    Route::get('/assessment/{assessment}/result', [AssessmentController::class, 'result'])->name('assessment.result');
    Route::post('/assessment/{assessment}/insight', [AssessmentController::class, 'generateInsight'])
        ->name('assessment.insight')
        ->middleware('throttle:ai_insight');
    Route::post('/assessment/{assessment}/insight/feedback', [AssessmentController::class, 'storeFeedback'])->name('assessment.insight.feedback');
    Route::get('/assessment/history', [AssessmentController::class, 'history'])->name('assessment.history');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Psychologist Dashboard
    Route::middleware(['role:psychologist'])->prefix('psychologist')->name('psychologist.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Psychologist\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/profile', [\App\Http\Controllers\Psychologist\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
});

// ---------------------------------------------------------------
// Psychologist Onboarding
// ---------------------------------------------------------------
Route::get('/psychologist/register', [\App\Http\Controllers\Psychologist\OnboardingController::class, 'showRegistrationForm'])->name('psychologist.register');
Route::post('/psychologist/register', [\App\Http\Controllers\Psychologist\OnboardingController::class, 'register'])->name('psychologist.register.submit');

require __DIR__.'/auth.php';

