<?php

namespace App\Models;

use App\Types\AccountStatus;
use App\Types\NewsletterAudience;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'country', 'preferred_currency',
        'google_id', 'avatar', 'referral_code', 'referred_by_id',
    ];

    /**
     * balance / profit_balance / referral_balance are DELIBERATELY absent
     * from $fillable AND explicitly guarded — see App\Services\WalletService,
     * App\Services\ResellerProfitService is separate (that one lives on
     * Reseller, not User), and App\Services\ReferralService. No controller
     * or mass-assignment path may ever touch these three columns directly.
     */
    protected $guarded = [
        'id', 'balance', 'profit_balance', 'referral_balance', 'status', 'is_reseller', 'reseller_status',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'reseller_approved_at' => 'datetime',
            'balance' => 'decimal:4',
            'profit_balance' => 'decimal:4',
            'referral_balance' => 'decimal:4',
            'reseller_discount_percent' => 'decimal:2',
            'is_reseller' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->uuid ??= (string) \Illuminate\Support\Str::uuid();
            $user->referral_code ??= strtoupper(\Illuminate\Support\Str::random(8));
        });
    }

    // --- Relationships ---
    public function wallet(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function reseller(): HasOne
    {
        return $this->hasOne(Reseller::class, 'owner_id');
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referralWithdrawals(): HasMany
    {
        return $this->hasMany(ReferralWithdrawal::class);
    }

    // --- Newsletter audience scopes — one method per App\Types\NewsletterAudience value.
    // Add a new segment by adding one constant + one scope here; nothing else changes.
    public function scopeAudience(Builder $query, string $audience): Builder
    {
        return match ($audience) {
            NewsletterAudience::NEW_USERS => $query->where('created_at', '>=', now()->subDays(7)),
            NewsletterAudience::NEVER_ORDERED => $query->doesntHave('orders'),
            NewsletterAudience::RESELLERS => $query->where('is_reseller', true),
            NewsletterAudience::INACTIVE_30_DAYS => $query->where('updated_at', '<=', now()->subDays(30)),
            default => $query, // NewsletterAudience::ALL
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', AccountStatus::ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::ACTIVE;
    }
}
