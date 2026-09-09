<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasUuids;

    protected $fillable = ['from_currency', 'to_currency', 'rate', 'source', 'fetched_at'];

    protected function casts(): array
    {
        return ['rate' => 'decimal:8', 'fetched_at' => 'datetime'];
    }

    public function isStale(): bool
    {
        return ! $this->fetched_at || $this->fetched_at->lt(now()->subHours(12));
    }
}
