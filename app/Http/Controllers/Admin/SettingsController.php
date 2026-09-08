<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.general', [
            'brand' => Setting::get('brand', ['name' => config('app.name'), 'primary_color' => '#16a34a']),
            'socialLinks' => Setting::get('social_links', []),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'brand.name' => ['required', 'string', 'max:100'],
            'brand.primary_color' => ['required', 'string', 'max:7'],
            'social_links.telegram_channel' => ['nullable', 'url'],
            'social_links.whatsapp_channel' => ['nullable', 'url'],
            'social_links.telegram_support' => ['nullable', 'url'],
            'social_links.tiktok' => ['nullable', 'url'],
            'social_links.instagram' => ['nullable', 'url'],
        ]);

        Setting::set('brand', $data['brand']);
        Setting::set('social_links', $data['social_links']);

        return back()->with('success', 'Settings updated.');
    }
}
