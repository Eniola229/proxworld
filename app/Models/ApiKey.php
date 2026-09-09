<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'name', 'abilities', 'status',
        'key_hash', 'key_encrypted', 'key_preview',
    ];

    protected $hidden = ['key_encrypted', 'key_hash'];

    protected function casts(): array
    {
        return [
            'key_encrypted' => 'encrypted',
            'abilities' => 'array',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generatePlainKey(): string
    {
        return 'pxw_'.Str::random(40);
    }

    public static function hashKey(string $plainKey): string
    {
        return hash('sha256', $plainKey);
    }
}