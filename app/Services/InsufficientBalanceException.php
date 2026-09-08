<?php

namespace App\Services;

use App\Models\User;
use RuntimeException;

class InsufficientBalanceException extends RuntimeException
{
    public function __construct(public readonly User $user, public readonly float $requested, public readonly float $available)
    {
        parent::__construct("Insufficient balance: requested {$requested}, available {$available}.");
    }
}
