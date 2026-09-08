<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = ['referrer_id', 'referred_user_id', 'has_deposited', 'has_ordered', 'bonus_paid', 'bonus_amount', 'bonus_paid_at'];

    protected function casts(): array
    {
        return [
            'has_deposited' => 'boolean',
            'has_ordered' => 'boolean',
            'bonus_paid' => 'boolean',
            'bonus_amount' => 'decimal:4',
            'bonus_paid_at' => 'datetime',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
