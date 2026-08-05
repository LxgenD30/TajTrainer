<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        if (
            empty(config('services.google.client_id')) ||
            empty(config('services.google.client_secret')) ||
            empty(config('services.google.redirect'))
        ) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Google login is not configured yet. Please set GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, and GOOGLE_REDIRECT_URI in your environment.']);
        }

        return $this->socialiteFactory()->driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = $this->socialiteFactory()->driver('google')->user();
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();

            if (!$googleId || !$email) {
                return redirect()->route('login')
                    ->withErrors(['google' => 'Google did not return a valid account email. Please try again or use email/password login.']);
            }

            $linkedUser = User::where('google_id', $googleId)->first();
            if ($linkedUser) {
                Auth::login($linkedUser, true);
                return $this->redirectByRole($linkedUser);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return redirect()->route('register')
                    ->withErrors(['email' => 'No account exists with this Google email. Please register first, then use Google login.'])
                    ->withInput(['email' => $email]);
            }

            if (!empty($user->google_id) && $user->google_id !== $googleId) {
                Log::warning('Google ID mismatch during login', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);

                return redirect()->route('login')
                    ->withErrors(['google' => 'This account is already linked to a different Google account.']);
            }

            $user->google_id = $googleId;
            if (!$user->profile_picture && $googleUser->getAvatar()) {
                $user->profile_picture = $googleUser->getAvatar();
            }
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();

            Auth::login($user, true);

            return $this->redirectByRole($user);
        } catch (Throwable $e) {
            Log::error('Google login failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->withErrors(['google' => 'Google login failed. Please try again.']);
        }
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        return match ((int) $user->role_id) {
            2 => redirect()->to('/student/classes'),
            3 => redirect()->to('/home'),
            default => redirect()->to('/home'),
        };
    }

    private function socialiteFactory(): object
    {
        return app('Laravel\\Socialite\\Contracts\\Factory');
    }
}
