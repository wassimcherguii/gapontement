<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAdminPrivileges();
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasAdminPrivileges() && ! $target->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->hasAdminPrivileges();
    }

    public function update(User $user, User $target): bool
    {
        if (! $user->hasAdminPrivileges()) {
            return false;
        }

        if ($target->isSuperAdmin()) {
            return false;
        }

        if ($target->isAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return false;
        }

        return $this->update($user, $target);
    }
}
