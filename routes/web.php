<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;

// Home can redirect to properties list (optional)
Route::get('/', function () {
    return redirect()->route('properties.index');
});

// Public: everyone can view the list
Route::get('/properties', [PropertyController::class, 'index'])
    ->name('properties.index');

// Protected: only logged-in users can create properties
Route::middleware('auth')->group(function () {
    Route::get('/properties/create', [PropertyController::class, 'create'])
        ->name('properties.create');

    Route::post('/properties', [PropertyController::class, 'store'])
        ->name('properties.store');
});