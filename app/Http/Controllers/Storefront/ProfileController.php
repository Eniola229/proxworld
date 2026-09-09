<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * NOTE: field list here is a best guess (name, phone, country) mirroring
 * User::$fillable. Please send App\Http\Controllers\ProfileController
 * (main site) so I can match validation rules and any password/avatar
 * handling exactly instead of guessing.
 */
class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $reseller = $request->attributes->get('storefront_reseller');

        return view('reseller.profile.index', ['reseller' => $reseller]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profile updated.');
    }
}