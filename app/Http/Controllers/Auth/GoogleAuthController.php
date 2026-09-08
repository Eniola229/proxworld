<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Types\AccountStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Link the Google account to an existing email/password account
            // the first time they use "Sign in with Google".
            if (! $user->google_id) {
                $user->forceFill([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ])->save();
            }
        } else {
            $referralCode = session('referral_code'); // captured on landing if ?ref=CODE was present

            $user = DB::transaction(function () use ($googleUser, $referralCode) {
                $newUser = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'ProxWorld User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null,
                    'email_verified_at' => now(), // Google already verified this email
                    'status' => AccountStatus::ACTIVE,
                ]);

                if ($referralCode) {
                    $referrer = User::where('referral_code', $referralCode)->first();

                    if ($referrer && $referrer->id !== $newUser->id) {
                        $newUser->forceFill(['referred_by_id' => $referrer->id])->save();
                        Referral::create(['referrer_id' => $referrer->id, 'referred_user_id' => $newUser->id]);
                    }
                }

                return $newUser;
            });
        }

        if (! $user->isActive()) {
            return redirect()->route('login')->withErrors(['email' => 'This account is not active. Contact support for help.']);
        }

        Auth::guard('web')->login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
