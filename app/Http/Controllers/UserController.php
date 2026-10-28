<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roles = $user->roles()->pluck('name')->toArray();

        return view('user.index', compact('user', 'roles'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $user->update($request->validated());

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
