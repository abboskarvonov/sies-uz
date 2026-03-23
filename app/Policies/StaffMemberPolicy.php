<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StaffMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffMemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_staff::member');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StaffMember $staffMember): bool
    {
        if ($user->can('view_staff::member')) {
            return true;
        }

        return $this->hasAccessViaPage($user, $staffMember);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->can('create_staff::member')) {
            return true;
        }

        if ($user->can('ViewAllPages')) {
            return true;
        }

        // Biriktirilgan sahifasi bor userlar staff qo'sha oladi
        return $user->assignedPages()
            ->whereIn('pages.page_type', ['department', 'faculty', 'center', 'section'])
            ->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StaffMember $staffMember): bool
    {
        if ($user->can('update_staff::member')) {
            return true;
        }

        return $this->hasAccessViaPage($user, $staffMember);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StaffMember $staffMember): bool
    {
        if ($user->can('delete_staff::member')) {
            return true;
        }

        return $this->hasAccessViaPage($user, $staffMember);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        if ($user->can('delete_any_staff::member')) {
            return true;
        }

        if ($user->can('ViewAllPages')) {
            return true;
        }

        return $user->assignedPages()
            ->whereIn('pages.page_type', ['department', 'faculty', 'center', 'section'])
            ->exists();
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, StaffMember $staffMember): bool
    {
        return $user->can('force_delete_staff::member');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_staff::member');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, StaffMember $staffMember): bool
    {
        return $user->can('restore_staff::member');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_staff::member');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, StaffMember $staffMember): bool
    {
        return $user->can('replicate_staff::member');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_staff::member');
    }

    /**
     * User biriktirilgan pagega tegishli staffni boshqara oladimi
     */
    private function hasAccessViaPage(User $user, StaffMember $staffMember): bool
    {
        if ($user->can('ViewAllPages')) {
            return true;
        }

        if (!$staffMember->page_id) {
            return false;
        }

        return $user->assignedPages()->where('pages.id', $staffMember->page_id)->exists();
    }
}
