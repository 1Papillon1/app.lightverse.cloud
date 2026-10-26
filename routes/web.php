<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminController;



Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware(['auth'])->group(function () {

    Route::get('/', fn () => view('dashboard'))->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
   
    Route::get('/profile', [UserController::class, 'index'])->name('index');
    Route::post('/profile', [UserController::class, 'update'])->name('profile.update');

    Route::get('/files', [FileController::class, 'index'])->name('files');
    Route::get('/files/{file}/download', [FileController::class, 'download']);
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files');
    Route::post('/files', [FileController::class, 'store'])->name('files');



    Route::middleware('role:super_admin')->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/files', [AdminController::class, 'files'])->name('admin.files');
    });
});