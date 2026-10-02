<?php

use App\Http\Controllers\Security\ModuleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('security')->name('security.')->group(function () {
    // Users Management
    Route::post('users/{user}/resend-invitation', [UserController::class, 'resendInvitation'])
        ->name('users.resend-invitation');
    Route::resource('users', UserController::class);

    // Roles Management
    Route::resource('roles', RoleController::class);

    // Permissions Management
    Route::resource('permissions', PermissionController::class)->except(['show']);

    // Modules Management
    Route::resource('modules', ModuleController::class)->except(['show']);
});
