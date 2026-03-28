<?php

namespace App\Filament\Concerns;

/**
 * Resource uchun Spatie permission asosida authorization.
 * Har bir resourceda $permPrefix ni belgilang: protected static string $permPrefix = 'Menu';
 */
trait HasSpatiePermissions
{
    public static function canViewAny(): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can('ViewAny:' . static::$permPrefix);
    }

    public static function canCreate(): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can('Create:' . static::$permPrefix);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can('Update:' . static::$permPrefix);
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can('ViewAny:' . static::$permPrefix);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can('Delete:' . static::$permPrefix);
    }

    public static function canDeleteAny(): bool
    {
        $user = authUser();
        if (! $user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->can('DeleteAny:' . static::$permPrefix);
    }
}
