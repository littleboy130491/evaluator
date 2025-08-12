<?php

namespace App\Traits;

use App\Models\History;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait HasHistory
{
    /**
     * Get all history records for this model.
     */
    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'historiable');
    }

    /**
     * Record a history entry for this model.
     *
     * @param string $action The action performed (created, updated, deleted, etc.)
     * @param array|null $oldValues The old values before the change
     * @param array|null $newValues The new values after the change
     * @return History
     */
    public function recordHistory(string $action, ?array $oldValues = null, ?array $newValues = null): History
    {
        return $this->histories()->create([
            'user_id' => Auth::id() ?? 1, // Default to ID 1 if not authenticated (for testing)
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Boot the trait.
     */
    protected static function bootHasHistory(): void
    {
        static::created(function ($model) {
            $model->recordHistory('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->recordHistory(
                'updated',
                array_intersect_key($model->getOriginal(), $model->getDirty()),
                $model->getDirty()
            );
        });

        static::deleted(function ($model) {
            $model->recordHistory('deleted', $model->getAttributes(), null);
        });
    }
}