<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\TikTokEventService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTikTokPurchaseEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // TikTokEventService already retries the HTTP call itself

    public function __construct(
        public string $orderId,
        public ?string $userId,
        public string $requestIp,
        public ?string $userAgent,
        public string $fullUrl,
        public ?string $ttpCookie,
    ) {
    }

    public function handle(TikTokEventService $tikTok): void
    {
        $order = Order::find($this->orderId);

        if (! $order) {
            return;
        }

        $user = $this->userId ? \App\Models\User::find($this->userId) : null;

        // Rebuild a minimal fake request carrying only what sendPurchaseEvent()
        // actually reads (ip, user agent, url, _ttp cookie) — the real Request
        // object can't be queued/serialized.
        $fakeRequest = \Illuminate\Http\Request::create($this->fullUrl);
        $fakeRequest->server->set('REMOTE_ADDR', $this->requestIp);
        $fakeRequest->headers->set('User-Agent', $this->userAgent);
        if ($this->ttpCookie) {
            $fakeRequest->cookies->set('_ttp', $this->ttpCookie);
        }

        $tikTok->sendPurchaseEvent($order, $user, $fakeRequest);
    }
}