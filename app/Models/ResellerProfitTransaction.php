<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerProfitTransaction extends Model
{
    use HasUuids;

    protected $fillable = ['reseller_id', 'order_id', 'type', 'amount', 'balance_before', 'balance_after', 'description'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'balance_before' => 'decimal:4', 'balance_after' => 'decimal:4'];
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }
}
