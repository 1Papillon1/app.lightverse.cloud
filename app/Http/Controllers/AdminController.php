<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\User;

class AdminController extends Controller
{
    // Svi korisnici s ulogom user i njihov preostali prostor
    public function users()
    {
        $users = User::whereHas('roles', fn ($q) => $q->where('name', 'user'))
            ->withSum('files', 'size') // daje stupac files_sum_size (u KB), null ako nema datoteka
            ->orderBy('username')
            ->paginate(5);

        // Ako je zadnja stranica ostala prazna, vrati na zadnju postojeću
        if ($users->isEmpty() && $users->currentPage() > 1) {
            return redirect()->route('admin.users', ['page' => $users->lastPage()]);
        }

        return view('admin.users', compact('users'));
    }

    // Sve datoteke svih korisnika (uz podatak tko ih je uploadao)
    public function files()
    {
        $files = File::with('user')->latest()->paginate(5);

        // Npr. nakon brisanja jedine datoteke na zadnjoj stranici
        if ($files->isEmpty() && $files->currentPage() > 1) {
            return redirect()->route('admin.files', ['page' => $files->lastPage()]);
        }

        return view('admin.files', compact('files'));
    }
}