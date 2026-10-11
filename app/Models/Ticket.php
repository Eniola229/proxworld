<?php

namespace App\Models;

use App\Types\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'subject', 'status', 'assigned_admin_id', 'closed_at',
        'rating', 'rating_comment', 'rated_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
            'rated_at'  => 'datetime',
            'rating'    => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    public function isClosed(): bool
    {
        return $this->status === TicketStatus::CLOSED;
    }

    public function isRated(): bool
    {
        return $this->rating !== null;
    }

    public function canBeRated(): bool
    {
        return $this->isClosed() && ! $this->isRated();
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            TicketStatus::OPEN => 'bg-green-100 text-green-700',
            TicketStatus::IN_PROGRESS => 'bg-yellow-100 text-yellow-700',
            TicketStatus::CLOSED => 'bg-gray-100 text-gray-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(TicketMessage::class)->latestOfMany('created_at');
    }

    /** Tickets where the given admin has sent at least one reply. */
    public function scopeRepliedBy(Builder $query, $adminId): Builder
    {
        return $query->whereExists(function ($sub) use ($adminId) {
            $sub->select(DB::raw(1))
                ->from('ticket_messages as rm')
                ->whereColumn('rm.ticket_id', 'tickets.id')
                ->where('rm.sender_admin_id', $adminId);
        });
    }

    /**
     * Admins who replied on each of the given tickets, most recent replier first.
     * Returns a collection keyed by ticket id; each value is a list of
     * rows (ticket_id, admin_id, name, last_reply_at). One query for the whole page.
     */
    public static function repliersFor(array $ticketIds): Collection
    {
        if (empty($ticketIds)) {
            return collect();
        }

        $admins = (new Admin)->getTable();

        return DB::table('ticket_messages as m')
            ->join($admins.' as a', 'a.id', '=', 'm.sender_admin_id')
            ->whereIn('m.ticket_id', $ticketIds)
            ->whereNotNull('m.sender_admin_id')
            ->groupBy('m.ticket_id', 'a.id', 'a.name')
            ->selectRaw('m.ticket_id, a.id as admin_id, a.name, MAX(m.created_at) as last_reply_at')
            ->orderByDesc('last_reply_at')
            ->get()
            ->groupBy('ticket_id');
    }

    /**
     * Every admin who replied on a rated ticket shares that ticket's rating.
     * Returns rows (id, name, avg_rating, ratings_count), best first.
     */
    public static function adminRatingLeaderboard(): Collection
    {
        $admins = (new Admin)->getTable();

        return DB::table('tickets as t')
            ->join(
                DB::raw('(SELECT DISTINCT ticket_id, sender_admin_id FROM ticket_messages WHERE sender_admin_id IS NOT NULL) as r'),
                'r.ticket_id', '=', 't.id'
            )
            ->join($admins.' as a', 'a.id', '=', 'r.sender_admin_id')
            ->whereNotNull('t.rating')
            ->groupBy('a.id', 'a.name')
            ->selectRaw('a.id, a.name, ROUND(AVG(t.rating), 2) as avg_rating, COUNT(*) as ratings_count')
            ->orderByDesc('avg_rating')
            ->orderByDesc('ratings_count')
            ->get();
    }
}