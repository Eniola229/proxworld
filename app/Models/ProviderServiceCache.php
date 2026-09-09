<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderServiceCache extends Model
{
    use HasUuids;

    protected $table = 'provider_services_cache';

    protected $fillable = [
        'provider_id', 'external_service_id', 'name', 'type', 'unit',
        'raw_rate', 'raw_currency', 'raw_payload', 'is_active', 'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_rate' => 'decimal:6',
            'raw_payload' => 'array',
            'is_active' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
