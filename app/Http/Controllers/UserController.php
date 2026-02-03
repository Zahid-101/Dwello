<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the specified user (public profile).
     */
    public function show(User $user)
    {
        // Ideally only show landlords or seekers, but let's be open for now.
        // Maybe check if they are a landlord if we only want landlord profiles:
        // if (!$user->isLandlord()) abort(404);

        $user->load(['properties.photos', 'roommateProfile']);

        return view('users.show', compact('user'));
    }
}
