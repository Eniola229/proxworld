<?php

namespace App\Http\Controllers;

use App\Services\DiditService;
use App\Services\KycService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Single webhook endpoint for both the main site and every reseller
 * storefront — vendor_data on the session is always our users.id, so
 * there's nothing reseller-specific to branch on here.
 *
 * Route (public, no auth — Didit calls this from their servers):
 *   Route::post('/webhooks/didit', [DiditWebhookController::class, 'handle'])->name('didit.webhook');
 */
class DiditWebhookController extends Controller
{
    public function handle(Request $request, DiditService $didit, KycService $kyc)
    {
        $matched = $didit->verifyWebhook($request);

        if (! $matched) {
            Log::warning('Didit webhook: signature check failed');
            return response()->json(['message' => 'invalid signature'], 401);
        }

        $payload = $request->json()->all();

        if (($payload['webhook_type'] ?? null) !== 'status.updated') {
            // We only care about session status changes for KYC.
            return response()->json(['ok' => true]);
        }

        $vendorData = $payload['vendor_data'] ?? null; // our users.id
        $sessionId = $payload['session_id'] ?? null;
        $status = $payload['status'] ?? null;

        if (! $vendorData || ! $sessionId || ! $status) {
            Log::warning('Didit webhook: missing fields', ['payload' => $payload]);
            return response()->json(['ok' => true]);
        }

        // X-Signature-Simple only authenticates the envelope (timestamp/session_id/status/webhook_type),
        // not `decision` — but we don't read `decision` here at all, only the status string, so it's safe
        // to act on regardless of which of the three signatures matched.
        $kyc->apply($vendorData, $sessionId, $status);

        return response()->json(['ok' => true]);
    }
}
