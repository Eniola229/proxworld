<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class ActivityLog extends SpatieActivity
{
    protected $table = 'activity_log';

    public function getTypeAttribute()
    {
        return $this->properties['type'] ?? $this->event;
    }

    public function getReferenceAttribute()
    {
        return $this->properties['reference'] ?? null;
    }

    public function getAmountAttribute()
    {
        return $this->properties['amount'] ?? 0;
    }

    public function getStatusAttribute()
    {
        return $this->properties['status'] ?? null;
    }

    public function getErrorMessageAttribute()
    {
        return $this->properties['error_message'] ?? null;
    }

    public function getRequestDataAttribute()
    {
        return $this->properties['request_data'] ?? null;
    }

    public function getResponseDataAttribute()
    {
        return $this->properties['response_data'] ?? null;
    }
}