<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use Illuminate\Http\Request;

class SocialiteController extends Controller
{
    //
=======
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return response()->json([
            'success' => true,
            'socialite_url' => Socialite::driver($provider)->redirect()->getTargetUrl(),
        ]);
    }

    public function callback($provider)
    {
        /**
         * @var \Laravel\Socialite\Two\AbstractProvider $providerDriver
         */
        $providerDriver = Socialite::driver($provider);

        $socialUser = request()->expectsJson()
            ? $providerDriver->stateless()->user()
            : $providerDriver->user();

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

        if (request()->expectsJson()) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token,
            ]);
        }

        Auth::login($user);
        return redirect('/dashboard');
    }
>>>>>>> upstream-main
}
