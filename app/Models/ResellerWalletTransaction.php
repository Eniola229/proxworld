<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerWalletTransaction extends Model
{
    use HasUuids;

    protected $table = 'reseller_wallet_transactions';

    protected $fillable = [
        'reseller_id', 'reference', 'type', 'amount', 'balance_before', 'balance_after',
        'currency', 'payment_method', 'status', 'description', 'order_id',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'balance_before' => 'decimal:4', 'balance_after' => 'decimal:4'];
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }
}
