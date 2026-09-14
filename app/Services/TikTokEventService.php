<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokEventService
{
    protected $pixelCode;
    protected $accessToken;
    protected $isEnabled;

    public function __construct()
    {
        $this->pixelCode   = config('services.tiktok.pixel_code');
        $this->accessToken = config('services.tiktok.access_token');
        $this->isEnabled   = config('services.tiktok.enabled', false);

        Log::info('TikTok Service initialized', [
            'pixel_exists'  => !empty($this->pixelCode),
            'token_exists'  => !empty($this->accessToken),
            'token_length'  => strlen($this->accessToken ?? ''),
            'enabled'       => $this->isEnabled,
        ]);
    }

    /**
     * Send purchase event to TikTok
     */
    public function sendPurchaseEvent(Order $order, ?User $user, Request $request)
    {
        if (!$this->isEnabled) {
            Log::warning('TikTok events disabled');
            return false;
        }

        if (empty($this->pixelCode)) {
            Log::error('TikTok pixel code is empty');
            return false;
        }

        if (empty($this->accessToken)) {
            Log::error('TikTok access token is empty - check your .env file and run php artisan config:clear');
            return false;
        }

        // ── Build context ──────────────────────────────────────────────────
        $context = [
            'user' => [
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'page' => [
                'url' => $request->fullUrl(),
            ],
        ];

        // TikTok cookie
        $tiktokCookie = $request->cookie('_ttp');
        if ($tiktokCookie) {
            $context['user']['ttp'] = $tiktokCookie;
        }

        // Hashed PII — SHA-256 required by TikTok
        if ($user && $user->email) {
            $context['user']['email'] = hash('sha256', strtolower(trim($user->email)));
        }

        if ($user && $user->phone) {
            $context['user']['phone_number'] = hash('sha256', preg_replace('/\D/', '', $user->phone));
        }

        // ── Build event payload ────────────────────────────────────────────
        $eventData = [
            'event'        => 'Purchase',
            'event_id'     => 'purchase_' . $order->id . '_' . time(),
            'event_source' => 'PIXEL_EVENTS',
            'timestamp'    => (string) now()->timestamp,   // Unix timestamp as string
            'context'      => $context,
            'properties'   => [
                'contents' => [
                    [
                        'content_id'   => (string) $order->external_service_id,
                        'content_name' => $order->service_name,
                        'content_type' => 'product',
                        'price'        => (float) $order->charge,
                        'quantity'     => (int) $order->quantity,
                    ],
                ],
                'content_type'     => 'product',
                'currency'         => $order->currency,
                'value'            => (float) $order->charge,
                'num_items'        => 1,
                'order_id'         => (string) $order->id,
                'description'      => "Order for {$order->service_name}",
                'content_category' => $this->getServiceCategory($order->product_type),
            ],
        ];

        Log::info('Sending to TikTok', [
            'pixel_code'   => substr($this->pixelCode, 0, 10) . '...',
            'token_prefix' => substr($this->accessToken, 0, 20) . '...',
            'event_id'     => $eventData['event_id'],
        ]);

        return $this->sendToTikTok($eventData);
    }

    /**
     * Send event to TikTok Events API
     */
    protected function sendToTikTok($eventData)
    {
        $url = "https://business-api.tiktok.com/open_api/v1.3/pixel/track/";

        // Flat payload — all event fields at top level, NOT nested inside data[]
        $payload = [
            'pixel_code'   => $this->pixelCode,
            'event'        => $eventData['event'],
            'event_id'     => $eventData['event_id'],
            'event_source' => $eventData['event_source'],
            'timestamp'    => $eventData['timestamp'],
            'context'      => $eventData['context'],
            'properties'   => $eventData['properties'],
        ];

        try {
            $response = Http::timeout(5)
                ->retry(2, 100)
                ->withHeaders([
                    'Access-Token' => $this->accessToken,
                ])
                ->post($url, $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['code']) && $result['code'] === 0) {
                Log::info('TikTok purchase event sent successfully', [
                    'order_id' => $eventData['properties']['order_id'] ?? 'unknown',
                    'event_id' => $eventData['event_id'],
                ]);
                return true;
            }

            Log::error('TikTok event API error', [
                'code'     => $result['code']    ?? $response->status(),
                'message'  => $result['message'] ?? 'Unknown error',
                'order_id' => $eventData['properties']['order_id'] ?? 'unknown',
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('TikTok event exception: ' . $e->getMessage(), [
                'order_id' => $eventData['properties']['order_id'] ?? 'unknown',
            ]);
            return false;
        }
    }

    /**
     * Map your product_type (residential/isp/datacenter/mobile) to a
     * TikTok-friendly content category — pulled straight from the Order
     * itself instead of guessing from the service name.
     */
    protected function getServiceCategory(?string $productType)
    {
        return match ($productType) {
            'residential' => 'residential_proxies',
            'isp'         => 'isp_proxies',
            'datacenter'  => 'datacenter_proxies',
            'mobile'      => 'mobile_proxies',
            default       => 'proxy_service',
        };
    }
}