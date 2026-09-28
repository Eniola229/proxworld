<?php

namespace App\Http\Controllers;

use App\Services\DiditService;
use App\Services\KycService;
use App\Types\KycStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Main-site KYC flow: show status / start Didit session / handle the
 * user's return from Didit. The reseller-domain equivalent is
 * Storefront\KycController — same logic, different redirects/views.
 */
class KycController extends Controller
{
    public function show(Request $request)
    {
        return view('kyc.pending', ['user' => $request->user()]);
    }

    public function start(Request $request, DiditService $didit)
    {

        $user = $request->user();

        try {
            $session = $didit->createSession(
                vendorData: $user->id,
                callbackUrl: rtrim(config('app.url'), '/') . route('kyc.callback', [], false),
            );
        } catch (\Throwable $e) {
            Log::error('KYC start failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Could not start verification right now. Please try again shortly.');
        }

        $user->forceFill([
            'kyc_status'          => KycStatus::PENDING,
            'kyc_session_id'      => $session['session_id'],
            'kyc_last_attempt_at' => now(),
        ])->save();

        return redirect()->away($session['url']);
    }

    /**
     * Didit redirects the user back here after they finish the hosted flow.
     * Didit appends ?verificationSessionId=...&status=...; the real,
     * trustworthy status update still comes from the webhook — this is just
     * a friendly landing page for the user, plus a one-shot status refresh
     * so the page reflects reality even if the webhook is still in flight.
     */
    public function callback(Request $request, DiditService $didit, KycService $kyc)
    {
        $user = $request->user();
        $sessionId = $request->query('verificationSessionId', $user->kyc_session_id);

        if ($sessionId) {
            $status = $didit->fetchStatus($sessionId);
            if ($status) {
                $kyc->apply($user->id, $sessionId, $status);
            }
        }

        return redirect()->route('kyc.show');
    }
}
