<?php

namespace App\Models;

use App\Types\NewsletterStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    use HasUuids;

    protected $fillable = [
        'subject', 'slug', 'excerpt', 'body', 'featured_image_url', 'featured_video_url',
        'audience', 'status', 'show_on_blog', 'scheduled_at', 'sent_at', 'created_by',
        'recipients_total', 'recipients_sent', 'recipients_failed',
    ];

    protected function casts(): array
    {
        return [
            'show_on_blog' => 'boolean',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Newsletter $n) {
            $n->slug ??= \Illuminate\Support\Str::slug($n->subject).'-'.\Illuminate\Support\Str::random(6);
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }

    public function scopePublishedOnBlog($query)
    {
        return $query->where('show_on_blog', true)
            ->where('status', NewsletterStatus::SENT)
            ->whereNotNull('sent_at');
    }
}
