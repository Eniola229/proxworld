<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Referral;
use App\Models\User;
use App\Types\AccountStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Auth for a reseller's white-label storefront (their end customers).
 *
 * Deliberately separate from App\Http\Controllers\Auth\LoginController /
 * RegisterController: those render the main-site branded views, these
 * render the reseller-branded `reseller.auth.*` views (panel_name, logo,
 * primary_color pulled from the $reseller shared by
 * App\Http\Middleware\ResolveResellerFromSubdomain). Both still
 * authenticate against the same `web` guard/users table — a storefront
 * customer is a regular User, not a separate account type.
 */
class StorefrontAuthController extends Controller
{
    public function showLogin()
    {
        return view('reseller.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        if ($user->status !== AccountStatus::ACTIVE) {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages(['email' => 'This account is suspended. Contact support for help.']);
        }

        return redirect()->intended(route('storefront.welcome'));
    }

    public function showRegister()
    {
        return view('reseller.auth.register', ['referralCode' => request('ref')]);
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'country' => $request->country,
                'status' => AccountStatus::ACTIVE,
                'terms_accepted_at' => now(),
            ]);

            if ($request->filled('referral_code')) {
                $referrer = User::where('referral_code', $request->referral_code)->first();

                if ($referrer && $referrer->id !== $newUser->id) {
                    $newUser->forceFill(['referred_by_id' => $referrer->id])->save();
                    Referral::create(['referrer_id' => $referrer->id, 'referred_user_id' => $newUser->id]);
                }
            }

            return $newUser;
        });

        event(new \Illuminate\Auth\Events\Registered($user));

        Auth::guard('web')->login($user);

        return redirect()->route('storefront.welcome');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.login');
    }
}
