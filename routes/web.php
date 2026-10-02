<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome/pages/Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard/pages/Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/security.php';
