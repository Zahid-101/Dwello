<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Review;
use App\Models\Conversation;

echo "--- Review System Verification ---\n";

// 1. Identify Test Users
$landlord = User::where('role', 'landlord')->first();
$seeker = User::where('role', 'seeker')->first();
$adminEmail = config('app.admin_email', 'admin@dwello.com');

echo "Landlord: {$landlord->name} (ID: {$landlord->id})\n";
echo "Seeker: {$seeker->name} (ID: {$seeker->id})\n";

// 2. Identify a Property
$property = Property::where('user_id', $landlord->id)->first();
if (!$property) {
    echo "No property found for landlord. Creating dummy property...\n";
    // Simplified creation for test
    $property = Property::create([
        'user_id' => $landlord->id,
        'title' => 'Test Property',
        'description' => 'Test Desc',
        'monthly_rent' => 50000,
        'bedrooms' => 1,
        'bathrooms' => 1,
        'property_type' => 'apartment',
        'address' => '123 Test St', 
        'city' => 'Colombo'
    ]);
}
echo "Property: {$property->title} (ID: {$property->id})\n";

// 3. Ensure Conversation Exists (for permission to review)
$convo = Conversation::firstOrCreate([
    'type' => 'property',
    'property_id' => $property->id,
    'user_one_id' => min($landlord->id, $seeker->id),
    'user_two_id' => max($landlord->id, $seeker->id),
]);
echo "Conversation ensured between Seeker and Landlord.\n";

// 4. Create a Review (Pending)
echo "Creating Pending Review from Seeker...\n";
foreach(Review::where('property_id', $property->id)->get() as $r) $r->delete(); // Clean up

$review = Review::create([
    'property_id' => $property->id,
    'user_id' => $seeker->id,
    'rating' => 4,
    'comment' => 'Great place, test review.',
    'status' => 'pending'
]);

echo "Review Created (ID: {$review->id}). Status: {$review->status}\n";

// 5. Verify Visibility
$approvedCount = $property->approvedReviews()->count();
echo "Property Approved Reviews Count: {$approvedCount} (Expected: 0)\n";

// 6. Approve Review (Simulate Admin)
echo "Approving Review...\n";
$review->update(['status' => 'approved']);
$property->refresh();

$approvedCount = $property->approvedReviews()->count();
echo "Property Approved Reviews Count: {$approvedCount} (Expected: 1)\n";
echo "Average Rating: {$property->average_rating}\n";

// 7. Check Admin Access Logic
echo "Admin Logic Check: Does Seeker have admin access? " . ($seeker->isAdmin() ? 'YES' : 'NO') . "\n";
// Temporarily mock admin
$seeker->email = $adminEmail; 
echo "Admin Logic Check (Email Match): " . ($seeker->isAdmin() ? 'YES' : 'NO') . "\n";

echo "--- Verification Complete ---\n";
