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
 * App\Http\Middleware\ResolveResellerFromSubdomain).
 *
 * A storefront customer is a regular User row, tagged with the reseller_id
 * they registered under (see register()). login() enforces that a customer
 * can only log into the storefront they belong to — a customer who signed
 * up on Reseller A's panel can't authenticate on Reseller B's subdomain
 * with the same credentials, even though it's the same `web` guard/table.
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
            throw ValidationException::withMessages(['email' => 'This credential does not match our records.']);
        }

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        // The 'storefront' middleware (ResolveResellerFromSubdomain) already
        // ran and set this before we got here.
        $reseller = $request->attributes->get('storefront_reseller');

        // Is this account the OWNER of the panel they're logging into? Compare
        // ids directly against Reseller::owner_id rather than going through the
        // $user->reseller relation — same pattern as the other project, and it
        // avoids any lazy-load/identity mismatch between the two Reseller rows.
        $isOwner = $reseller && $user->id === $reseller->owner_id;

        // Is this account a registered customer of THIS panel?
        $isMember = $reseller && $user->reseller_id === $reseller->id;

        if (! $isOwner && ! $isMember) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages(['email' => 'Your account is not registered on this panel.']);
        }

        if ($user->status !== AccountStatus::ACTIVE) {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages(['email' => 'This account is suspended. Contact support for help.']);
        }

        if ($isOwner) {
            return redirect()->intended(route('storefront.dashboard'));
        }

        return redirect()->intended(route('storefront.dashboard'));
    }

    public function showRegister()
    {
        return view('reseller.auth.register', ['referralCode' => request('ref')]);
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $reseller = $request->attributes->get('storefront_reseller');

        $user = DB::transaction(function () use ($request, $reseller) {
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'country' => $request->country,
                'status' => AccountStatus::ACTIVE,
                'terms_accepted_at' => now(),
            ]);

            if ($reseller) {
                $newUser->forceFill(['reseller_id' => $reseller->id])->save();
            }

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

        return redirect()->route('storefront.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.login');
    }

}