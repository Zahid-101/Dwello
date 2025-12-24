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

        // 2. Verify conversation existence (Optional but requested)
        // Check if there is a conversation of type 'property' involving this property and this user
        $hasConversation = Conversation::where('property_id', $property->id)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
            })
            ->exists();

        // If strict verification is needed, uncomment:
        // if (!$hasConversation) {
        //    return back()->with('error', 'You must contact the landlord before reviewing.');
        // }
        
        // Since the prompt requested "Optional but recommended" and "Users can review only if...", 
        // I will enforce it but allow a bypass if needed, or stick to the UI hiding the form.
        // For security, enforcing it is better.
        if (!$hasConversation) {
             return back()->with('error', 'You need to message the landlord about this property before leaving a review.');
        }

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
