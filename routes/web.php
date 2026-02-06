<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoommateProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\BoostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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

    // Role Selection
    Route::get('/select-role', [\App\Http\Controllers\RoleSelectionController::class, 'show'])->name('role.select');
    Route::post('/select-role', [\App\Http\Controllers\RoleSelectionController::class, 'store'])->name('role.store');

    // Property creation & management (Landlords only)
    Route::middleware(['role:landlord'])->group(function () {
        Route::get('/my-listings', [PropertyController::class, 'myListings'])
            ->name('properties.my-listings');

        Route::get('/properties/create', [PropertyController::class, 'create'])
            ->name('properties.create');
        Route::post('/properties', [PropertyController::class, 'store'])
            ->name('properties.store');

        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])
            ->name('properties.edit');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])
            ->name('properties.update');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])
            ->name('properties.destroy');
    });

    // Roommate profile create/update (Seekers only)
    Route::middleware(['role:seeker'])->group(function () {
        Route::get('/roommate-profile/create', [RoommateProfileController::class, 'create'])
            ->name('roommate-profiles.create');
        Route::post('/roommate-profile', [RoommateProfileController::class, 'store'])
            ->name('roommate-profiles.store');
    });

    Route::get('/roommates/{user}/compatibility', [RoommateProfileController::class, 'compatibility'])
        ->name('roommates.compatibility');

    Route::post('/roommates/{user}/reject', [RoommateProfileController::class, 'reject'])
        ->name('roommates.reject');

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

    // Notifications
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'readAll'])->name('notifications.readAll');

    // Messaging Routes
    // Scheduled Messages (Must be before wildcard /messages/{conversation})
    Route::post('/messages/schedule', [\App\Http\Controllers\ScheduledMessageController::class, 'store'])->name('messages.schedule');

    Route::get('/messages', [ConversationController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [ConversationController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{conversation}/poll', [MessageController::class, 'poll'])->name('messages.poll');

    Route::post('/properties/{property}/message', [ConversationController::class, 'startProperty'])->name('conversations.startProperty');
    Route::post('/roommates/{user}/message', [ConversationController::class, 'startRoommate'])->name('conversations.startRoommate');



    // Message Request Actions
    Route::post('/conversations/{conversation}/accept', [ConversationController::class, 'accept'])->name('conversations.accept');
    Route::post('/conversations/{conversation}/reject', [ConversationController::class, 'reject'])->name('conversations.reject');
    Route::post('/conversations/{conversation}/unblock', [ConversationController::class, 'unblock'])->name('conversations.unblock');

    // Reviews
    Route::post('/properties/{property}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // Admin Reviews
    Route::get('/admin/reviews', [\App\Http\Controllers\AdminReviewController::class, 'index'])->name('admin.reviews.index');
    Route::post('/admin/reviews/{review}/approve', [\App\Http\Controllers\AdminReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::post('/admin/reviews/{review}/reject', [\App\Http\Controllers\AdminReviewController::class, 'reject'])->name('admin.reviews.reject');

    Route::post('/properties/{property}/share', [PropertyController::class, 'share'])->name('properties.share');
    Route::get('/users/search', [ProfileController::class, 'search'])->name('users.search');

    Route::get('/boost-ads', [BoostController::class, 'index'])->name('boost.index');
    Route::post('/boost-ads', [BoostController::class, 'store'])->name('boost.store');
    Route::delete('/boost-ads/{id}', [BoostController::class, 'destroy'])->name('boost.destroy');
});



// Temporary Debug Route for Production
Route::get('/debug-boost', function () {
    $ads = \App\Models\BoostedAd::with(['user', 'property'])->get();
    return [
        'total_ads_in_db' => $ads->count(),
        'current_user' => auth()->check() ? [
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'my_budget_max' => auth()->user()->roommateProfile?->budget_max ?? 'No Profile/Budget',
        ] : 'Guest (Not Logged In)',
        'ad_details' => $ads->map(function ($ad) {
            return [
                'ad_id' => $ad->id,
                'is_active' => (bool) $ad->is_active,
                'has_property_linked' => $ad->property ? 'YES' : 'NULL (BROKEN)',
                'property_rent' => $ad->property?->monthly_rent ?? 'N/A',
                'will_show_for_current_user' => auth()->check() && auth()->user()->roommateProfile && auth()->user()->roommateProfile->budget_max
                    ? ($ad->property->monthly_rent <= auth()->user()->roommateProfile->budget_max ? 'YES' : 'NO (Too Expensive)')
                    : 'YES (Guest/No Filter)',
            ];
        })
    ];
});

// Wildcards (must be last)
Route::get('/properties/{property}', [PropertyController::class, 'show'])
    ->name('properties.show');

Route::get('/users/{user}', [UserController::class, 'show'])
    ->name('users.show');


// Breeze auth routes (login, register, logout, etc.)
require __DIR__ . '/auth.php';

Route::get('/debug-notify', function () {
    $user = auth()->user();
    if (!$user)
        return 'Not logged in';

    // 1. Check Config
    $queueConn = config('queue.default');

    // 2. Create Dummy Profile for notification
    $dummyProfile = new \App\Models\RoommateProfile([
        'user_id' => 99999,
        'preferred_city' => 'Test City',
        'budget_max' => 50000,
    ]);
    // We need to associate a mock user for the name
    $dummyUser = new \App\Models\User(['name' => 'Test User']);
    $dummyProfile->setRelation('user', $dummyUser);

    // 3. Send Notification
    try {
        $user->notify(new \App\Notifications\NewRoommateMatch($dummyProfile));
        $status = "Notification Sent to {$user->name}!";
    } catch (\Exception $e) {
        $status = "Error sending: " . $e->getMessage();
    }

    return [
        'queue_connection' => $queueConn,
        'status' => $status,
        'db_notification_count_before' => $user->notifications()->count(),
        'unread_count' => $user->unreadNotifications()->count(),
    ];
});

Route::view('/payment', 'payment');

Route::view('/success', 'payment-success');
Route::view('/cancel', 'payment-cancel');

Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession'])->name('stripe.checkout');

Route::view('/payment', 'payment')->name('payment');
Route::get('/payment-success', [StripeController::class, 'success'])->name('payment.success');
Route::view('/payment-cancel', 'payment-cancel')->name('payment.cancel');


