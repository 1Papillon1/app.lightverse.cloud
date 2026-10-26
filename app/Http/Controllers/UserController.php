<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $username = $user->username;
        $roles = $user->roles()->pluck('name')->toArray();

        return view('user.index', compact('user', 'roles'));
    }

   

    public function update(Request $request)
    {
        $user = auth()->user();
     

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($request->only('first_name', 'last_name', 'address'));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    
}
