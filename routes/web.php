<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // Zaštita od brute-force napada: max 5 pokušaja prijave u minuti, nakon toga 429
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Registracija je onemogućena, ruta samo prikazuje poruku
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:'.Role::USER)->group(function () {
        Route::get('/profile', [UserController::class, 'index'])->name('profile');
        Route::patch('/profile', [UserController::class, 'update'])->name('profile.update');

        Route::get('/files', [FileController::class, 'index'])->name('files.index');
        Route::post('/files', [FileController::class, 'store'])->name('files.store');
    });

    // Izvan role:user jer ih koristi i super admin; pravo provjerava FilePolicy
    // (može i na razini rute: ->can('download', 'file') / ->can('delete', 'file'))
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download')->whereNumber('file');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy')->whereNumber('file');

    Route::middleware('role:'.Role::SUPER_ADMIN)->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/files', [AdminController::class, 'files'])->name('admin.files');
    });
});
