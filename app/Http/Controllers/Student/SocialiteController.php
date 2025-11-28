<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
            $path = null;

            if ($socialUser->getAvatar()) {
                $response = Http::get($socialUser->getAvatar());

                if ($response->successful()) {
                    $extension = pathinfo($socialUser->getAvatar(), PATHINFO_EXTENSION);
                    $filename = 'avatar_' . $socialUser->getId() . '_' . time() . '.' . ($extension ?: 'jpg');
                    Storage::disk('public')->put('avatars/' . $filename, $response->body());
                    $path = 'avatars/' . $filename;
                }
            }

            $user = User::create([
                'name' => $socialUser->getName() ?: $socialUser->getNickname(),
                'email_verified_at' => now(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $path,
                'role' => 'student',
            ]);
        }

        Auth::login($user);

        return redirect()->intended(route('student.home', absolute: false));
    }
}
