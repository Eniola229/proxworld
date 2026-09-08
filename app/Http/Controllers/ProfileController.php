<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return view('profile.index', ['user' => $request->user()]);
    }

    public function edit(Request $request)
    {
        return view('profile.index', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'size:2'],
            'preferred_currency' => ['required', 'string', 'size:3'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->forceFill(['password' => Hash::make($request->password)])->save();

        return back()->with('success', 'Password updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password:web']]);

        $user = $request->user();
        Auth::guard('web')->logout();

        $user->update(['status' => \App\Types\AccountStatus::SUSPENDED]); // soft — preserves financial/order history for records
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('success', 'Your account has been deactivated.');
    }
}
