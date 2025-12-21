<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoommateProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FavoriteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('home');
})->name('home');

// Public browsing routes
Route::get('/properties', [PropertyController::class, 'index'])
    ->name('properties.index');

Route::get('/roommates', [RoommateProfileController::class, 'index'])
    ->name('roommates.index');

Route::get('/roommates/{roommateProfile}', [RoommateProfileController::class, 'show'])
    ->name('roommates.show');

// Generic "under development" page
Route::view('/under-development', 'under-development')
    ->name('under-development');

// Routes that require login
Route::middleware('auth')->group(function () {

    // Property creation
    Route::get('/properties/create', [PropertyController::class, 'create'])
        ->name('properties.create');

    Route::post('/properties', [PropertyController::class, 'store'])
        ->name('properties.store');

    // Roommate profile create/update
    Route::get('/roommate-profile/create', [RoommateProfileController::class, 'create'])
        ->name('roommate-profiles.create');

    Route::post('/roommate-profile', [RoommateProfileController::class, 'store'])

        ->name('roommate-profiles.store');

    Route::get('/roommates/{user}/compatibility', [RoommateProfileController::class, 'compatibility'])
        ->name('roommates.compatibility');

    // Dashboard just redirects to main app (properties)
    Route::get('/dashboard', function () {
        return redirect()->route('properties.index');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{roommateProfile}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Messaging Routes
    Route::get('/messages', [ConversationController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [ConversationController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{conversation}/poll', [MessageController::class, 'poll'])->name('messages.poll');

    Route::post('/properties/{property}/message', [ConversationController::class, 'startProperty'])->name('conversations.startProperty');
    Route::post('/roommates/{user}/message', [ConversationController::class, 'startRoommate'])->name('conversations.startRoommate');
});

// Breeze auth routes (login, register, logout, etc.)
require __DIR__ . '/auth.php';
