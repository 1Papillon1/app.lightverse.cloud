<?php

namespace App\Policies;

use App\Models\File;
use App\Models\Role;
use App\Models\User;

class FilePolicy
{
    // Pravilo za preuzimanje datoteke
    public function download(User $user, File $file): bool
    {
        return $this->canManage($user, $file);
    }

    // Pravilo za brisanje datoteke
    public function delete(User $user, File $file): bool
    {
        return $this->canManage($user, $file);
    }

    // Vlasnik datoteke ili super admin
    private function canManage(User $user, File $file): bool
    {
        return $user->id === $file->user_id || $user->hasRole(Role::SUPER_ADMIN);
    }
}
