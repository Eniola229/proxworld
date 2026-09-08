<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

/**
 * Sends via Brevo's transactional email HTTP API (not SMTP). All app mail
 * is queued (ShouldQueue on every Mailable/Notification), so a slow Brevo
 * response never blocks a request.
 *
 * Registered via Mail::extend('brevo', ...) in AppServiceProvider::boot(),
 * selected by setting MAIL_MAILER=brevo in .env.
 */
class BrevoApiTransport extends AbstractTransport
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            return;
        }

        $payload = [
            'sender' => $this->addressPayload($email->getFrom()[0] ?? null, config('services.brevo.sender_name'), config('services.brevo.sender_email')),
            'to' => array_map(fn ($addr) => $this->addressPayload($addr), $email->getTo()),
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?: nl2br((string) $email->getTextBody()),
        ];

        if ($textBody = $email->getTextBody()) {
            $payload['textContent'] = $textBody;
        }

        if ($replyTo = $email->getReplyTo()) {
            $payload['replyTo'] = $this->addressPayload($replyTo[0]);
        }

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if (! $response->successful()) {
            Log::error('Brevo email send failed: '.$response->body());
            throw new \RuntimeException('Failed to send email via Brevo: '.$response->body());
        }
    }

    protected function addressPayload($address, ?string $fallbackName = null, ?string $fallbackEmail = null): array
    {
        if (! $address) {
            return ['email' => $fallbackEmail, 'name' => $fallbackName];
        }

        return array_filter([
            'email' => $address->getAddress(),
            'name' => $address->getName() ?: $fallbackName,
        ]);
    }

    public function __toString(): string
    {
        return 'brevo+api';
    }
}
