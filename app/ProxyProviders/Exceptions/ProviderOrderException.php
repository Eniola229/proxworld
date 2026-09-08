<?php

namespace App\ProxyProviders\Exceptions;

use RuntimeException;

class ProviderOrderException extends RuntimeException
{
    public function __construct(string $message, public readonly ?array $rawResponse = null)
    {
        parent::__construct($message);
    }
}
