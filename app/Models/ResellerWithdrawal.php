<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerWithdrawal extends Model
{
    use HasUuids;

    protected $fillable = [
        'reseller_id', 'amount', 'currency',
        'bank_name', 'bank_code', 'account_number', 'account_name',
        'reference', 'status', 'failure_reason',
        'processed_by', 'processed_at',
        'flutterwave_transfer_id',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'processed_at' => 'datetime'];
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }
}