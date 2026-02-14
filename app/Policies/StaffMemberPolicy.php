<?php

namespace App\Policies;

use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StaffMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StaffMember $staffMember): bool
    {
        return $user->hasRole('super-admin') || $staffMember->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StaffMember $staffMember): bool
    {
        return $user->hasRole('super-admin') || $staffMember->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StaffMember $staffMember): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StaffMember $staffMember): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StaffMember $staffMember): bool
    {
        return false;
    }
}