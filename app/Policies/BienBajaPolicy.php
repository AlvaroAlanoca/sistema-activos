<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BienBaja;
use Illuminate\Auth\Access\HandlesAuthorization;

class BienBajaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bien::baja');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BienBaja $bienBaja): bool
    {
        return $user->can('view_bien::baja');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_bien::baja');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BienBaja $bienBaja): bool
    {
        return $user->can('update_bien::baja');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BienBaja $bienBaja): bool
    {
        return $user->can('delete_bien::baja');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_bien::baja');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, BienBaja $bienBaja): bool
    {
        return $user->can('force_delete_bien::baja');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_bien::baja');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, BienBaja $bienBaja): bool
    {
        return $user->can('restore_bien::baja');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_bien::baja');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, BienBaja $bienBaja): bool
    {
        return $user->can('replicate_bien::baja');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_bien::baja');
    }
}
