<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasUuids;

    protected $fillable = ['currency', 'name', 'symbol', 'is_default', 'is_active'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean', 'is_active' => 'boolean'];
    }
}
