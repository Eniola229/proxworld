<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TrackingLink extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function getGeneratedUrlAttribute(): string
    {
        $params = array_filter([
            'utm_source'   => $this->source,
            'utm_medium'   => $this->medium,
            'utm_campaign' => $this->campaign,
            'utm_content'  => $this->content,
            'utm_term'     => $this->term,
        ], fn ($v) => $v !== null && $v !== '');

        $path = '/' . ltrim($this->landing_path ?: '/', '/');

        return rtrim(config('app.url'), '/') . $path . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}