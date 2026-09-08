<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerServiceOverride extends Model
{
    protected $fillable = ['reseller_id', 'provider_id', 'external_service_id', 'markup_percent', 'is_hidden'];

    protected function casts(): array
    {
        return ['markup_percent' => 'decimal:2', 'is_hidden' => 'boolean'];
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
