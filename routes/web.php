<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoommateProfileController;

Route::get('/', function () {
    // Home goes to properties list
    return redirect()->route('properties.index');
});

// PUBLIC ROUTES
Route::get('/properties', [PropertyController::class, 'index'])
    ->name('properties.index');

Route::get('/roommates', [RoommateProfileController::class, 'index'])
    ->name('roommates.index');

// AUTH ONLY ROUTES
Route::middleware('auth')->group(function () {

    // Properties - create/store
    Route::get('/properties/create', [PropertyController::class, 'create'])
        ->name('properties.create');

    Route::post('/properties', [PropertyController::class, 'store'])
        ->name('properties.store');

    // Roommate profile - create/update
    Route::get('/roommate-profile/create', [RoommateProfileController::class, 'create'])
        ->name('roommate-profiles.create');

    Route::post('/roommate-profile', [RoommateProfileController::class, 'store'])
        ->name('roommate-profiles.store');

    // If something in Breeze still links to /dashboard, redirect it nicely
    Route::get('/dashboard', function () {
        return redirect()->route('properties.index');
    })->name('dashboard');
});

// Breeze auth routes (login, register, logout, etc.)
require __DIR__.'/auth.php';