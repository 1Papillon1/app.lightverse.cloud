<?php

namespace App\Policies;

use App\Models\User;
use App\Models\File;

class FilePolicy
{
    /**
     * Create a new policy instance.
     */
   
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
            return $user->id === $file->user_id || $user->hasRole('super_admin');
        }
    
}
