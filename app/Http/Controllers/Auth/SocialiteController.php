<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect user to Google OAuth consent screen
     */
    public function redirectToGoogle(): RedirectResponse
    {
        // If Google credentials are not configured locally, gracefully fall back to dev simulation
        if (empty(config('services.google.client_id')) || config('services.google.client_id') === 'your-google-client-id') {
            return $this->handleMockGoogleLogin();
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle incoming OAuth callback from Google
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            return $this->loginOrCreateUser($googleUser->getName(), $googleUser->getEmail(), $googleUser->getId());
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed or was cancelled. Please try logging in again.');
        }
    }

    /**
     * Dev simulation mode when OAuth keys are not yet added to .env
     */
    public function handleMockGoogleLogin(): RedirectResponse
    {
        $mockEmail = 'candidate.google@example.com';
        $mockName = 'Google Candidate User';

        return $this->loginOrCreateUser($mockName, $mockEmail, 'google-mock-12345');
    }

    protected function loginOrCreateUser(string $name, string $email, ?string $googleId = null): RedirectResponse
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'password' => Hash::make(Str::random(24)),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ]);

            CandidateProfile::create([
                'user_id' => $user->id,
                'education_level' => null,
                'years_experience' => 0,
                'field_experience_months' => 0,
                'skills' => [],
                'reliability_score' => 80.0,
                'summary' => null,
                'languages' => ['English'],
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Welcome, {$user->name}! Successfully authenticated via Google.");
    }
}
