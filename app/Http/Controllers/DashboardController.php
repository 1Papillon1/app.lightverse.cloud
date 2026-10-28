<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Super admin vidi ukupne brojke, user svoje datoteke i prostor
        if ($user->hasRole(Role::SUPER_ADMIN)) {
            $filesCount = File::count();
            $usersCount = User::whereHas('roles', fn ($q) => $q->where('name', Role::USER))->count();

            return view('dashboard', compact('filesCount', 'usersCount'));
        }

        $filesCount = $user->files()->count();
        $used = (int) $user->getStorageUsed(); // KB

        return view('dashboard', compact('filesCount', 'used'));
    }
}
