<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class PricingRule extends Model
{
    use HasUuids;

    protected $fillable = [
        'scope_type', 'product_type', 'protocol', 'provider_id',
        'markup_percentage', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'markup_percentage' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('pricing_rules.active'));
        static::deleted(fn () => Cache::forget('pricing_rules.active'));
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}