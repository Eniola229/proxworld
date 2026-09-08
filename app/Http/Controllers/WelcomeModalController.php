<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeModalController extends Controller
{
    /** AJAX-dismissed — no page reload. Marks it seen so it never shows again. */
    public function dismiss(Request $request)
    {
        $request->user()->forceFill(['welcome_modal_seen_at' => now()])->save();

        return response()->json(['success' => true]);
    }
}
