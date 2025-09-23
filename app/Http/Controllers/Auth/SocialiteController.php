<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            if ($socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            if (!$user) {
                $avatarPath = $this->storeAvatar($socialUser->getAvatar(), $socialUser->getId());

                $user = User::create([
                    'name' => $socialUser->getName() ?: $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $avatarPath,
                ]);
            } else {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $user->avatar ?: $this->storeAvatar($socialUser->getAvatar(), $socialUser->getId()),
                ]);
            }
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function storeAvatar($avatarUrl, $userId)
    {
        if ($avatarUrl) {
            $response = Http::get($avatarUrl);

            if ($response->successful()) {
                $extension = pathinfo($avatarUrl, PATHINFO_EXTENSION);
                $filename = 'avatar_' . $userId . '_' . time() . '.' . ($extension ?: 'jpg');
                Storage::disk('public')->put('avatars/' . $filename, $response->body());
                return 'avatars/' . $filename;
            }
        }
        return null;
    }
}
