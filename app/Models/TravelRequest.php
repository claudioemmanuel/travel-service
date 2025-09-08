<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TravelRequest extends Model
{
    use HasFactory;

    const STATUS_REQUESTED = 'requested';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_id',
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'user_id',
        'approved_at',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];
    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class);
    }

    public function canBeCancelled(): bool
    {
        if ($this->status !== self::STATUS_APPROVED) {
            return false;
        }

        $departureDate = Carbon::parse($this->departure_date);
        $now = Carbon::now();

        return $now->diffInHours($departureDate) >= 24;
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByDestination(Builder $query, string $destination): Builder
    {
        return $query->where('destination', 'like', '%' . $destination . '%');
    }

    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('departure_date', [$startDate, $endDate]);
    }
}
