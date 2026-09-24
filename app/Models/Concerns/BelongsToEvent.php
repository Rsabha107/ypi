<?php

namespace App\Models\Concerns;

use App\Models\Ypi\Event;
use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes a lookup model to the currently selected event.
 * Rows with a NULL event_id are shared across every event.
 */
trait BelongsToEvent
{
    protected static function bootBelongsToEvent(): void
    {
        static::addGlobalScope('event', function (Builder $builder) {
            $eventId = current_event_id();

            if (! $eventId) {
                return;
            }

            $table = $builder->getModel()->getTable();

            $builder->where(function (Builder $query) use ($table, $eventId) {
                $query->whereNull("{$table}.event_id")
                    ->orWhere("{$table}.event_id", $eventId);
            });
        });

        static::creating(function ($model) {
            // Only default when event_id was never touched, so an explicit null stays global.
            if (! array_key_exists('event_id', $model->getAttributes())) {
                $model->event_id = current_event_id();
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /** Rows shared by every event. */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull($query->getModel()->getTable() . '.event_id');
    }

    /** Bypass the event scope (e.g. cross-event admin screens). */
    public static function allEvents(): Builder
    {
        return static::withoutGlobalScope('event');
    }

    public function getIsGlobalAttribute(): bool
    {
        return is_null($this->event_id);
    }
}
