<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralWithdrawal extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'currency', 'bank_name', 'account_number', 'account_name',
        'reference', 'status', 'failure_reason', 'processed_by', 'processed_at',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'processed_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $w) => $w->uuid ??= (string) \Illuminate\Support\Str::uuid());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }
}
