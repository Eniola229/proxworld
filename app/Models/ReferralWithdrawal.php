<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralWithdrawal extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'currency', 'method',
        'bank_name', 'bank_code', 'account_number', 'account_name',
        'reference', 'status', 'failure_reason',
        'balance_before', 'balance_after', 'description',
        'processed_by', 'processed_at',
        'flutterwave_transfer_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:4',
            'balance_before' => 'decimal:4',
            'balance_after' => 'decimal:4',
            'processed_at' => 'datetime',
        ];
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

    /** The admin views use $withdrawal->withdrawal_method — keep that working without touching every blade file. */
    public function getWithdrawalMethodAttribute(): ?string
    {
        return $this->attributes['method'] ?? null;
    }

    /** Same idea for the "Admin Note" shown on failed withdrawals. */
    public function getAdminNoteAttribute(): ?string
    {
        return $this->attributes['failure_reason'] ?? null;
    }
}