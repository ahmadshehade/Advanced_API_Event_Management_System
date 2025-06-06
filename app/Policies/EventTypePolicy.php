<?php

namespace App\Policies;

use App\Models\EventType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventTypePolicy
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole('adminRole')) {
            return true;
        }
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('userRole')&&$user->can('view all eventType');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EventType $eventType): bool
    {
        return $user->hasRole('userRole')&&$user->can('view eventType');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EventType $eventType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EventType $eventType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EventType $eventType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EventType $eventType): bool
    {
        return false;
    }
}
