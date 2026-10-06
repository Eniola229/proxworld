<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderExtension extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'cost_price_snapshot' => 'decimal:4',
        'platform_price_snapshot' => 'decimal:4',
        'profit' => 'decimal:4',
        'markup_percentage' => 'decimal:4',
        'charge' => 'decimal:4',
        'exchange_rate_snapshot' => 'decimal:8',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function provider(): BelongsTo { return $this->belongsTo(Provider::class); }

    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', 'completed');
    }
}