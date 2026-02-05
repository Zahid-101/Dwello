<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoostController extends Controller
{
    public function index()
    {
        // Ensure user is a landlord (middleware covers auth, but let's be safe)
        if (!auth()->user()->isLandlord()) {
            return redirect()->route('home')->with('error', 'Only landlords can access this page.');
        }

        if (!auth()->user()->is_premium) {
            return redirect()->route('payment')->with('error', 'You need a Gold subscription to access this feature.');
        }

        $properties = auth()->user()->properties()->get();
        $boostedAds = \App\Models\BoostedAd::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('boost.index', compact('properties', 'boostedAds'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        if (!auth()->user()->isLandlord() || !auth()->user()->is_premium) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Check if user already has an active boosted ad
        if (\App\Models\BoostedAd::where('user_id', auth()->id())->where('is_active', true)->exists()) {
            return redirect()->back()->with('error', 'You can only have one boosted ad at a time. Please delete your existing ad before boosting a new one.');
        }

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'note' => 'required|string|max:255',
        ]);

        // Verify property belongs to user
        $property = auth()->user()->properties()->find($request->property_id);
        if (!$property) {
            return redirect()->back()->with('error', 'Invalid property selected.');
        }

        $path = null;
        $firstPhoto = $property->photos()->first();
        if ($firstPhoto) {
            $path = $firstPhoto->path;
        }

        // If no photo, we just proceed with null path. 
        // The view will handle the default placeholder.

        \App\Models\BoostedAd::create([
            'user_id' => auth()->id(),
            'property_id' => $request->property_id,
            'property_image' => $path,
            'note' => $request->note,
            'is_active' => true,
        ]);

        return redirect()->back()->with('status', 'Your ad has been boosted successfully!');
    }

    public function destroy($id)
    {
        $ad = \App\Models\BoostedAd::findOrFail($id);

        if ($ad->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Optional: Delete image from storage
        // \Illuminate\Support\Facades\Storage::disk('public')->delete($ad->property_image);

        $ad->delete();

        return redirect()->back()->with('status', 'Boosted ad removed successfully.');
    }
}
