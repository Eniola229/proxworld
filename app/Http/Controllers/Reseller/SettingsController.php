<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;

class SettingsController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary)
    {
    }

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
            'default_markup_percent' => ['required', 'numeric', 'min:0', 'max:200'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        // Logo is optional here — only touch it if a new file was actually uploaded.
        if ($request->hasFile('logo')) {
            if ($reseller->logo_public_id) {
                $this->cloudinary->delete($reseller->logo_public_id, 'image');
            }

            $upload = $this->cloudinary->uploadImage($request->file('logo'), 'resellers/logos');

            $data['logo_path'] = $upload['url'];
            $data['logo_public_id'] = $upload['public_id'];
        }

        $reseller->update($data);

        return back()->with('success', 'Panel settings updated.');
    }
}