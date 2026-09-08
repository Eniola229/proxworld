<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Referral;
use App\Models\User;
use App\Types\AccountStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register', ['referralCode' => request('ref')]);
    }

    public function store(RegisterRequest $request): RedirectResponse
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

        return redirect()->route('dashboard');
    }
}
