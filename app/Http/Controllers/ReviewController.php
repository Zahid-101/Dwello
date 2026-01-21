<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Property $property)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $user = auth()->user();

        // 1. Prevent owner from reviewing their own property
        if ($property->user_id === $user->id) {
            return back()->with('error', 'You cannot review your own property.');
        }

        // 2. Verify conversation existence (Optional check removed to allow direct reviews)
        // Check removed as per user request to allow tenants to comment directly

        // 3. Create or Update Review (Upsert logic)
        // If a review exists, we update it and reset status to pending
        $review = Review::where('property_id', $property->id)
            ->where('user_id', $user->id)
            ->first();

        if ($review) {
            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'pending', // Re-approval required
            ]);
            $message = 'Your review has been updated and is pending approval.';
        } else {
            Review::create([
                'property_id' => $property->id,
                'user_id' => $user->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'pending',
            ]);
            $message = 'Your review has been submitted for approval.';
        }

        return back()->with('success', $message);
    }
}
