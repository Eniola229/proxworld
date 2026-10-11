<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $this->guard($request, $invitation, $token);

        return view('admin.auth.accept-invitation', ['invitation' => $invitation]);
    }

    public function store(Request $request, AdminInvitation $invitation, string $token)
    {
        $this->guard($request, $invitation, $token);

        $request->validate(['password' => ['required', 'confirmed', Password::defaults()]]);

        $invitation->admin->forceFill(['password' => Hash::make($request->password)])->save();
        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('admin.login')->with('status', 'Your password has been set. You can now log in.');
    }

    /** Same checks as before, but each failure has its own reason (logged; shown only in debug mode). */
    private function guard(Request $request, AdminInvitation $invitation, string $token): void
    {
        if (! $request->hasValidSignature()) {
            $this->deny($request, $invitation, 'signature invalid (host/scheme/query changed, or the link was altered)');
        }

        if (! hash_equals((string) $invitation->token, $token)) {
            $this->deny($request, $invitation, 'token does not match (invitation was probably resent; use the newest email)');
        }

        if ($invitation->isAccepted()) {
            $this->deny($request, $invitation, 'invitation already accepted');
        }

        if ($invitation->isExpired()) {
            $this->deny($request, $invitation, 'invitation expired (expires_at: '.$invitation->expires_at.', now: '.now().')');
        }
    }

    private function deny(Request $request, AdminInvitation $invitation, string $reason): never
    {
        Log::warning('Admin invitation denied: '.$reason, [
            'invitation' => $invitation->id,
            'url'        => $request->fullUrl(),
        ]);

        abort(403, config('app.debug')
            ? $reason
            : 'This invitation link is invalid, already used, or has expired.');
    }
}