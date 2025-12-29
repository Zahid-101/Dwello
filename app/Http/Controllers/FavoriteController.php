<?php

namespace App\Http\Controllers;

use App\Models\RoommateProfile;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // List favorites
    public function index()
    {
        $favorites = auth()->user()->favorites()->with('user')->paginate(9);

        return view('roommates.favorites', compact('favorites'));
    }

    // Toggle favorite (AJAX or Redirect)
    public function toggle(RoommateProfile $roommateProfile)
    {
        $user = auth()->user();
        
        $toggled = $user->favorites()->toggle($roommateProfile->id);

        $isSaved = count($toggled['attached']) > 0;

        if (request()->wantsJson()) {
            return response()->json(['saved' => $isSaved]);
        }

        return back()->with('success', $isSaved ? 'Added to favorites' : 'Removed from favorites');
    }
}
