<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        // Simple admin check
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $stats = [
            'pending' => Review::pending()->count(),
            'approved' => Review::approved()->count(),
            'total' => Review::count(),
        ];

        $reviews = Review::with(['property', 'user'])
            ->pending()
            ->latest()
            ->get();

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function approve(Review $review)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $review->update(['status' => 'approved']);

        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $review->update(['status' => 'rejected']);

        return back()->with('success', 'Review rejected.');
    }
}
