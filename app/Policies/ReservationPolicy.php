<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReservationPolicy
{

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {

        if ($user->hasRole('adminRole')) {
            return true;
        }


        return $user->can('view reservations');
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole('adminRole')) {
            return true;
        }
        return $reservation->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('adminRole') ||
            ($user->hasRole('userRole') && $user->can('create reservation'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reservation $reservation): bool
    {
        return( $user->hasRole('userRole') &&
            ($user->can('edit reservation') && $user->id === $reservation->user_id)
            ||$user->hasRole('adminRole') &&$user->id===$reservation->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole('adminRole')) {
            return true;
        }
        return $user->can('cancel reservation') && $user->hasRole('userRole') && $user->id === $reservation->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return false;
    }
}
