<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterSend extends Model
{
    use HasUuids;

    protected $fillable = ['newsletter_id', 'user_id', 'status', 'error', 'sent_at'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
