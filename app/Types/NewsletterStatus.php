<?php

namespace App\Types;

class NewsletterStatus
{
    const DRAFT = 'draft';
    const SCHEDULED = 'scheduled';
    const SENDING = 'sending';
    const SENT = 'sent';
    const FAILED = 'failed';

    public static function all(): array
    {
        return [self::DRAFT, self::SCHEDULED, self::SENDING, self::SENT, self::FAILED];
    }
}
