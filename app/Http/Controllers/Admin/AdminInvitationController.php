<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Handles the "set your password" link an invited admin receives by email.
 * The admin row is created with password=null; nothing else can log in as
 * them until this flow completes.
 */
class AdminInvitationController extends Controller
{
    public function accept(Request $request, AdminInvitation $invitation, string $token)
    {
        abort_unless($request->hasValidSignature(), 403, 'This invitation link is invalid or has expired.');
        abort_if($invitation->token !== $token || $invitation->isAccepted() || $invitation->isExpired(), 403, 'This invitation link is invalid, already used, or has expired.');

        return view('admin.auth.accept-invitation', ['invitation' => $invitation]);
    }

    public function store(Request $request, AdminInvitation $invitation, string $token)
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_if($invitation->token !== $token || $invitation->isAccepted() || $invitation->isExpired(), 403);

        $request->validate(['password' => ['required', 'confirmed', Password::defaults()]]);

        $invitation->admin->forceFill(['password' => Hash::make($request->password)])->save();
        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('admin.login')->with('status', 'Your password has been set. You can now log in.');
    }
}
