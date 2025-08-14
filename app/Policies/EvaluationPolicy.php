<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Evaluation;
use App\Models\Outlet;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_evaluation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Evaluation $evaluation): bool
    {
        // Admin and super_admin can view all evaluations
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $user->can('view_evaluation');
        }

        // Check if user can view evaluations based on outlet group area
        if ($evaluation->outlet && $evaluation->outlet->groupArea) {
            return $user->can('view_evaluation') && $user->canEvaluateInGroupArea($evaluation->outlet->groupArea);
        }

        // Default permission check for evaluations without outlet group area
        return $user->can('view_evaluation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_evaluation');
    }

    /**
     * Determine whether the user can create evaluations for a specific outlet.
     */
    public function createForOutlet(User $user, Outlet $outlet): bool
    {
        // Admin and super_admin can create evaluations for all outlets
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $user->can('create_evaluation');
        }

        // Check if user can create evaluations based on outlet group area
        if ($outlet->groupArea) {
            return $user->can('create_evaluation') && $user->canEvaluateInGroupArea($outlet->groupArea);
        }

        // Default permission check for outlets without group area
        return $user->can('create_evaluation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Evaluation $evaluation): bool
    {
        return $user->can('update_evaluation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Evaluation $evaluation): bool
    {
        return $user->can('delete_evaluation');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_evaluation');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Evaluation $evaluation): bool
    {
        return $user->can('force_delete_evaluation');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_evaluation');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Evaluation $evaluation): bool
    {
        return $user->can('restore_evaluation');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_evaluation');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Evaluation $evaluation): bool
    {
        return $user->can('replicate_evaluation');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_evaluation');
    }
}
