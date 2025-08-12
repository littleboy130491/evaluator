<?php

namespace App\Services;

use App\Models\History;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HistoryService
{
    /**
     * Record a history entry for a model.
     *
     * @param Model $model The model to record history for
     * @param string $action The action performed (created, updated, deleted, etc.)
     * @param array|null $oldValues The old values before the change
     * @param array|null $newValues The new values after the change
     * @return History
     */
    public function recordHistory(Model $model, string $action, ?array $oldValues = null, ?array $newValues = null): History
    {
        return History::create([
            'historiable_type' => get_class($model),
            'historiable_id' => $model->getKey(),
            'user_id' => Auth::id() ?? 1, // Default to ID 1 if not authenticated (for testing)
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Get history records with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHistory(array $filters = [])
    {
        $query = History::with(['user', 'historiable'])
            ->orderBy('created_at', 'desc');

        if (isset($filters['historiable_type'])) {
            $query->where('historiable_type', $filters['historiable_type']);
        }

        if (isset($filters['historiable_id'])) {
            $query->where('historiable_id', $filters['historiable_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date']);
        }

        return $query->get();
    }
}