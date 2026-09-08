<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('reseller.manage.settings', ['reseller' => $request->user()->reseller]);
    }

    public function update(Request $request)
    {
        $reseller = $request->user()->reseller;

        $data = $request->validate([
            'panel_name' => ['required', 'string', 'max:100'],
            'primary_color' => ['required', 'string', 'max:7'],
            'support_email' => ['nullable', 'email'],
            'support_telegram' => ['nullable', 'string', 'max:255'],
            'support_whatsapp' => ['nullable', 'string', 'max:255'],
        ]);

        $reseller->update($data);

        return back()->with('success', 'Panel settings updated.');
    }
}
