<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\User;

class AdminController extends Controller
{
    
    public function users() 
    {
        $users = User::with('roles')->get();
        $totalFileSize = File::sum('size');
      
        

        return view('admin.users', compact('users'));
    }

    public function files() 
    {
        $files = File::all();

        return view('admin.files', compact('files'));
    }

    
}
