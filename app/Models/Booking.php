<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property BookingStatus $status
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'service_id', 'start_time', 'end_time', 'status', 'notes'])]
class Booking extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'status' => BookingStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    /**
     * reservas que ocupan la agenda
     *
     * @param  Builder<Booking>  $query
     */
    #[Scope]
    protected function blocking(Builder $query): void
    {
        $query->whereIn(
            'status',
            array_map(
                static fn (BookingStatus $status): string => $status->value,
                BookingStatus::blocking(),
            ),
        );
    }

    /**
     * reservas que se superponen con el rango dado
     *
     * @param  Builder<Booking>  $query
     */
    #[Scope]
    protected function overlapping(Builder $query, CarbonInterface $start, CarbonInterface $end): void
    {
        $query->where('start_time', '<', $end)->where('end_time', '>', $start);
    }
}
