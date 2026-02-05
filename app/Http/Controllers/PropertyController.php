<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    /**
     * Show property list with filters.
     */
    public function index(Request $request)
    {
        $query = Property::query()->orderByDesc('created_at');

        // dump('Checking smart filters...'); // Debug

        // Default Recommendations based on Roommate Profile
        if (
            !$request->filled('q') &&
            !$request->filled('city') &&
            !$request->filled('min_rent') &&
            !$request->filled('max_rent') &&
            !$request->filled('type') &&
            !$request->has('filter') &&
            auth()->check() &&
            auth()->user()->roommateProfile
        ) {
            $profile = auth()->user()->roommateProfile;

            if ($profile->preferred_city) {
                $query->where('city', 'like', '%' . $profile->preferred_city . '%');
            }

            if ($profile->budget_max) {
                $query->where('monthly_rent', '<=', $profile->budget_max);
            }

            // Optional: Min budget might filter too much if they set it high, but let's include it for accuracy
            if ($profile->budget_min) {
                $query->where('monthly_rent', '>=', $profile->budget_min);
            }
        }

        // Keyword search: title, city, address
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        // City filter
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        // Rent range
        if ($request->filled('min_rent')) {
            $query->where('monthly_rent', '>=', (float) $request->input('min_rent'));
        }

        if ($request->filled('max_rent')) {
            $query->where('monthly_rent', '<=', (float) $request->input('max_rent'));
        }

        // Property type (room / apartment / house)
        if ($request->filled('type')) {
            $query->where('property_type', $request->input('type'));
        }

        // Paginate results and keep query string (?q=.. etc)
        // Eager load photos and user to prevent N+1
        $properties = $query->with(['photos', 'user'])->paginate(9)->withQueryString();

        return view('properties.index', compact('properties'));
    }

    /**
     * Show create form (already done earlier).
     */
    public function create()
    {
        return view('properties.create');
    }

    /**
     * Show create form (already done earlier).
     */
    public function myListings()
    {
        $properties = auth()->user()->properties()->with('photos')->latest()->get();
        return view('properties.my-listings', compact('properties'));
    }

    /**
     * Show property details.
     */
    public function show(Property $property)
    {
        // Eager load everything needed for the view including reviews and their authors
        $property->load(['photos', 'user', 'approvedReviews.user']);

        $userReview = auth()->check()
            ? $property->reviews()->where('user_id', auth()->id())->first()
            : null;

        return view('properties.show', compact('property', 'userReview'));
    }

    /**
     * Store property (you already had this – keep your existing version if different).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'city' => ['required', 'string', 'max:50', Rule::in(config('cities'))],
            'address' => 'required|string|max:255',
            'monthly_rent' => 'required|numeric|min:0|max:10000000', // Cap at 10 million for safety
            'bedrooms' => 'required|integer|min:1|max:20',
            'bathrooms' => 'required|integer|min:1|max:20',
            'property_type' => 'required|in:room,apartment,house',
            'available_from' => 'nullable|date|after_or_equal:today',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB max per image
        ]);

        $validated['user_id'] = auth()->id();

        $property = Property::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store("properties/{$property->id}", 'public');
                $property->photos()->create(['path' => $path]);
            }
        }

        // Notify Matching Users (Roommates looking for this city/budget)
        try {
            $matchingUsers = \App\Models\User::whereHas('roommateProfile', function ($q) use ($property) {
                // Determine City Match (If preferred_city is set, match it. If NULL, allow all.)
                $q->where(function ($sub) use ($property) {
                    $sub->whereNull('preferred_city')
                        ->orWhere('preferred_city', 'like', '%' . $property->city . '%');
                });

                // Budget Match
                if ($property->monthly_rent) {
                    $q->where('budget_max', '>=', $property->monthly_rent);
                }
            })->where('id', '!=', auth()->id())->get();

            if ($matchingUsers->count() > 0) {
                \Illuminate\Support\Facades\Notification::send($matchingUsers, new \App\Notifications\NewPropertyMatch($property));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification Error: ' . $e->getMessage());
        }

        return redirect()->route('properties.index')
            ->with('success', 'Property created successfully.');
    }


    /**
     * Show edit form.
     */
    public function edit(Property $property)
    {
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }
        return view('properties.edit', compact('property'));
    }

    /**
     * Update property.
     */
    public function update(Request $request, Property $property)
    {
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'city' => ['required', 'string', 'max:50', Rule::in(config('cities'))],
            'address' => 'required|string|max:255',
            'monthly_rent' => 'required|numeric|min:0|max:10000000',
            'bedrooms' => 'required|integer|min:1|max:20',
            'bathrooms' => 'required|integer|min:1|max:20',
            'property_type' => 'required|in:room,apartment,house',
            'available_from' => 'nullable|date|after_or_equal:today',
            // 'latitude' & 'longitude' usually shouldn't change easily or require map re-picker, ignoring for MVU or keeping if needed. 
            // Let's allow them if invalid/missing, or just keep basic fields for now to avoid complexity with map logic in edit.
            // Actually, let's include them as nullable just in case.
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $property->update($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store("properties/{$property->id}", 'public');
                $property->photos()->create(['path' => $path]);
            }
        }

        return redirect()->route('properties.show', $property)
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Delete property.
     */
    public function destroy(Property $property)
    {
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        // Ideally delete photos from storage too, but basic DB delete handles model
        $property->delete();

        return back()->with('success', 'Property deleted successfully.');
    }
    /**
     * Share property with another user.
     */
    public function share(Request $request, Property $property)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
        ]);

        $recipientId = $validated['recipient_id'];
        $authUserId = auth()->id();

        if ($recipientId == $authUserId) {
            return response()->json(['success' => false, 'message' => 'You cannot share with yourself.']);
        }

        // Find or create conversation
        // Logic similar to ConversationController::startProperty but simplified for generic sharing
        // We check for existing conversation regardless of type, or create a 'property' type if none exists.
        // Actually, let's keep it simple: Find ANY conversation between these two. If none, create new one.

        $conversation = \App\Models\Conversation::where(function ($q) use ($authUserId, $recipientId) {
            $q->where('user_one_id', $authUserId)->where('user_two_id', $recipientId);
        })->orWhere(function ($q) use ($authUserId, $recipientId) {
            $q->where('user_one_id', $recipientId)->where('user_two_id', $authUserId);
        })->first();

        if (!$conversation) {
            $conversation = \App\Models\Conversation::create([
                'type' => 'property', // Defaulting to property type context, though it could be just a chat
                'property_id' => $property->id,
                'user_one_id' => $authUserId,
                'user_two_id' => $recipientId,
                'last_message_at' => now(),
                'status' => 'pending', // New convos are pending
                'started_by' => $authUserId,
            ]);
        } else {
            // Update last message time
            $conversation->touch('last_message_at');
        }

        // Check if blocked
        if ($conversation->status === 'rejected') {
            return response()->json(['success' => false, 'message' => 'Cannot send message to this user.']);
        }

        // Send the message
        $body = "Shared a property: {$property->title}";

        $conversation->messages()->create([
            'sender_id' => $authUserId,
            'body' => $body,
            'property_id' => $property->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Property shared successfully!']);
    }
}
