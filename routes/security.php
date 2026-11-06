<?php

use App\Http\Controllers\Security\ModuleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('security')->name('security.')->group(function () {
    Route::post('users/{user}/resend-invitation', [UserController::class, 'resendInvitation'])
        ->name('users.resend-invitation');
    Route::resource('users', UserController::class);

    Route::resource('roles', RoleController::class);

    Route::resource('permissions', PermissionController::class)->only(['index']);

    Route::resource('modules', ModuleController::class)->only(['index']);
});
