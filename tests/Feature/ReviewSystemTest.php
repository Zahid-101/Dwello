<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Property;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-1: Anti-Spam Verification
     * User A tries to review a property without messaging the landlord.
     */
    public function test_user_cannot_review_without_conversation()
    {
        $landlord = User::factory()->create(['role' => 'landlord']);
        $tenant = User::factory()->create(['role' => 'seeker']);
        $property = Property::factory()->create(['user_id' => $landlord->id]);

        $response = $this->actingAs($tenant)
            ->post(route('reviews.store', $property), [
                'rating' => 5,
                'comment' => 'Great place!',
            ]);

        $response->assertSessionHas('error', 'You need to message the landlord about this property before leaving a review.');
        $this->assertDatabaseMissing('reviews', ['property_id' => $property->id]);
    }

    /**
     * TC-2: Valid Submission
     * User A messages the landlord, then submits a review.
     */
    public function test_user_can_review_with_conversation()
    {
        $landlord = User::factory()->create(['role' => 'landlord']);
        $tenant = User::factory()->create(['role' => 'seeker']);
        $property = Property::factory()->create(['user_id' => $landlord->id]);

        // Create a conversation for this property
        Conversation::create([
            'type' => 'property',
            'property_id' => $property->id,
            'user_one_id' => $tenant->id,
            'user_two_id' => $landlord->id,
            'last_message_at' => now(),
        ]);

        $response = $this->actingAs($tenant)
            ->post(route('reviews.store', $property), [
                'rating' => 5,
                'comment' => 'Legit review.',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'property_id' => $property->id,
            'user_id' => $tenant->id,
            'status' => 'pending', // TC-4 logic (Default Visibility)
        ]);
    }

    /**
     * TC-3: Conflict of Interest
     * Landlord tries to review their own property.
     */
    public function test_landlord_cannot_review_own_property()
    {
        $landlord = User::factory()->create(['role' => 'landlord']);
        $property = Property::factory()->create(['user_id' => $landlord->id]);

        $response = $this->actingAs($landlord)
            ->post(route('reviews.store', $property), [
                'rating' => 5,
                'comment' => 'My house is best.',
            ]);

        $response->assertSessionHas('error', 'You cannot review your own property.');
    }

    /**
     * TC-5: Admin Approval
     * Admin approves review -> status 'approved'.
     */
    public function test_admin_can_approve_review()
    {
        $admin = User::factory()->create(['email' => 'admin@dwello.com']);
        
        $landlord = User::factory()->create(['role' => 'landlord']);
        $tenant = User::factory()->create(['role' => 'seeker']);
        $property = Property::factory()->create(['user_id' => $landlord->id]);

        $review = Review::create([
            'property_id' => $property->id,
            'user_id' => $tenant->id,
            'rating' => 4,
            'comment' => 'Pending check',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.reviews.approve', $review));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'status' => 'approved',
        ]);
    }
}
