<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    use HasUuids;

    protected $fillable = ['ticket_id', 'sender_type', 'sender_admin_id', 'message', 'is_read'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function senderAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'sender_admin_id');
    }
}
