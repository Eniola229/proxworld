<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'slug', 'driver', 'auth_type', 'api_url', 'api_key', 'api_secret',
        'priority', 'is_active', 'notes', 'config',
    ];

    protected $hidden = ['api_key', 'api_secret'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'api_key' => 'encrypted',
            'api_secret' => 'encrypted',
            'config' => 'array',
            'cached_balance' => 'decimal:4',
            'balance_checked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Provider $p) {
            $p->slug ??= \Illuminate\Support\Str::slug($p->name);
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ProviderServiceCache::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('priority');
    }
}