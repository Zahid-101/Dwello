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
     * Show property details.
     */
    public function show(Property $property)
    {
        // Eager load everything needed for the view including reviews and their authors
        $property->load(['photos', 'user', 'approvedReviews.user']);
        return view('properties.show', compact('property'));
    }

    /**
     * Store property (you already had this – keep your existing version if different).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string|max:2000',
            'city'          => ['required', 'string', 'max:50', Rule::in(config('cities'))],
            'address'       => 'required|string|max:255',
            'monthly_rent'  => 'required|numeric|min:0|max:10000000', // Cap at 10 million for safety
            'bedrooms'      => 'required|integer|min:1|max:20',
            'bathrooms'     => 'required|integer|min:1|max:20',
            'property_type' => 'required|in:room,apartment,house',
            'available_from'=> 'nullable|date|after_or_equal:today',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'photos'        => 'nullable|array',
            'photos.*'      => 'image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB max per image
        ]);

        $validated['user_id'] = auth()->id();

        $property = Property::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store("properties/{$property->id}", 'public');
                $property->photos()->create(['path' => $path]);
            }
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
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string|max:2000',
            'city'          => ['required', 'string', 'max:50', Rule::in(config('cities'))],
            'address'       => 'required|string|max:255',
            'monthly_rent'  => 'required|numeric|min:0|max:10000000',
            'bedrooms'      => 'required|integer|min:1|max:20',
            'bathrooms'     => 'required|integer|min:1|max:20',
            'property_type' => 'required|in:room,apartment,house',
            'available_from'=> 'nullable|date|after_or_equal:today',
            // 'latitude' & 'longitude' usually shouldn't change easily or require map re-picker, ignoring for MVU or keeping if needed. 
            // Let's allow them if invalid/missing, or just keep basic fields for now to avoid complexity with map logic in edit.
            // Actually, let's include them as nullable just in case.
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'photos'        => 'nullable|array',
            'photos.*'      => 'image|mimes:jpeg,png,jpg,webp|max:2048',
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

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}
