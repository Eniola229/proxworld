<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Platform-level profit ledger. Populated only by App\Services\ProfitService. */
class ProfitTransaction extends Model
{
    use HasUuids;

    protected $fillable = ['order_id', 'provider_id', 'reseller_id', 'channel', 'amount', 'currency'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:4'];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }
}
