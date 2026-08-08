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
        $clientId = config('services.google.client_id');

        if (empty($clientId) || $clientId === 'your-google-client-id') {
            return redirect()->route('login')->with('error', 'Google Sign-In is not configured. Please set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in environment settings.');
        }

        $baseUrl = config('app.url') ?: (request()->secure() ? 'https://' : 'http://') . request()->getHost();
        $redirectUrl = env('GOOGLE_REDIRECT_URI', rtrim($baseUrl, '/') . '/auth/google/callback');

        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    /**
     * Handle incoming OAuth callback from Google
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $baseUrl = config('app.url') ?: (request()->secure() ? 'https://' : 'http://') . request()->getHost();
            $redirectUrl = env('GOOGLE_REDIRECT_URI', rtrim($baseUrl, '/') . '/auth/google/callback');

            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->user();

            $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: $googleUser->getEmail());
            $email = $googleUser->getEmail();
            $googleId = $googleUser->getId();

            return $this->loginOrCreateUser($name, $email, $googleId);
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }

    protected function loginOrCreateUser(string $name, string $email, ?string $googleId = null): RedirectResponse
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            if (!$user->google_id && $googleId) {
                $user->update(['google_id' => $googleId]);
            }
        } else {
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

        return redirect()->route('dashboard')->with('success', "Welcome, {$user->name}! Successfully authenticated with Google.");
    }
}
