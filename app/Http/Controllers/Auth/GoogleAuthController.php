<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
            $avatarPath = $this->storeGoogleAvatar($googleUser->getAvatar(), $user->profile_picture);
            if ($avatarPath) {
                $user->profile_picture = $avatarPath;
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

    public function loginWithGoogleToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'credential' => ['required', 'string'],
        ]);

        $googleClientId = config('services.google.client_id');
        if (empty($googleClientId)) {
            return response()->json([
                'message' => 'Google login is not configured yet. Please set GOOGLE_CLIENT_ID in your environment.',
            ], 500);
        }

        try {
            $tokenInfoResponse = Http::timeout(10)
                ->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $validated['credential'],
                ]);

            if (!$tokenInfoResponse->ok()) {
                return response()->json([
                    'message' => 'Google credential is invalid or expired. Please try again.',
                ], 422);
            }

            $tokenInfo = $tokenInfoResponse->json();
            $audience = $tokenInfo['aud'] ?? null;
            $email = isset($tokenInfo['email']) ? strtolower(trim((string) $tokenInfo['email'])) : null;
            $googleId = $tokenInfo['sub'] ?? null;
            $emailVerified = $tokenInfo['email_verified'] ?? false;

            if ($audience !== $googleClientId) {
                return response()->json([
                    'message' => 'Google credential audience mismatch. Please use the configured Google app.',
                ], 422);
            }

            if ($emailVerified !== true && $emailVerified !== 'true') {
                return response()->json([
                    'message' => 'Google email is not verified. Please verify your Google account first.',
                ], 422);
            }

            if (empty($email) || empty($googleId)) {
                return response()->json([
                    'message' => 'Google did not return a valid account email.',
                ], 422);
            }

            $linkedUser = User::where('google_id', $googleId)->first();
            if ($linkedUser) {
                Auth::login($linkedUser, true);

                return response()->json([
                    'message' => 'Login successful.',
                    'redirect_url' => $this->redirectPathByRole($linkedUser),
                    'user' => [
                        'id' => $linkedUser->id,
                        'email' => $linkedUser->email,
                        'role_id' => $linkedUser->role_id,
                    ],
                ]);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'No account exists with this Google email. Please register first.',
                    'code' => 'NO_ACCOUNT',
                    'email' => $email,
                ], 422);
            }

            if (!empty($user->google_id) && $user->google_id !== $googleId) {
                Log::warning('Google ID mismatch during token login', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);

                return response()->json([
                    'message' => 'This account is already linked to a different Google account.',
                ], 409);
            }

            $user->google_id = $googleId;

            $avatarPath = $this->storeGoogleAvatar($tokenInfo['picture'] ?? null, $user->profile_picture);
            if ($avatarPath) {
                $user->profile_picture = $avatarPath;
            }

            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }

            $user->save();

            Auth::login($user, true);

            return response()->json([
                'message' => 'Login successful.',
                'redirect_url' => $this->redirectPathByRole($user),
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Google token login failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Google login failed. Please try again.',
            ], 500);
        }
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        return redirect()->to($this->redirectPathByRole($user));
    }

    /**
     * Download the Google avatar into local storage and return the stored path.
     * Keeps an existing locally-stored picture untouched; replaces stale URLs
     * stored by earlier versions of this controller.
     */
    private function storeGoogleAvatar(?string $avatarUrl, ?string $existing = null): ?string
    {
        if (empty($avatarUrl)) {
            return null;
        }

        // Keep an existing locally-stored picture.
        if (!empty($existing) && !str_starts_with($existing, 'http')) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get($avatarUrl);
            if (!$response->ok()) {
                return null;
            }

            $contentType = strtolower((string) $response->header('Content-Type', ''));
            $ext = 'jpg';
            if (str_contains($contentType, 'png')) {
                $ext = 'png';
            } elseif (str_contains($contentType, 'webp')) {
                $ext = 'webp';
            } elseif (str_contains($contentType, 'gif')) {
                $ext = 'gif';
            }

            $filename = 'profile_pictures/google_' . md5($avatarUrl . uniqid('', true)) . '.' . $ext;
            \Storage::disk('public')->put($filename, $response->body());

            // Remove the old local file it replaced.
            if (!empty($existing) && !str_starts_with($existing, 'http') && \Storage::disk('public')->exists($existing)) {
                \Storage::disk('public')->delete($existing);
            }

            return $filename;
        } catch (Throwable $e) {
            Log::warning('Failed to download Google profile picture', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function redirectPathByRole(User $user): string
    {
        return match ((int) $user->role_id) {
            2 => '/student/classes',
            3 => '/home',
            default => '/home',
        };
    }

    private function socialiteFactory(): object
    {
        return app('Laravel\\Socialite\\Contracts\\Factory');
    }
}
