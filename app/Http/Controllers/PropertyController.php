<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

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
        $properties = $query->paginate(9)->withQueryString();

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
     * Store property (you already had this – keep your existing version if different).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'city'          => 'required|string|max:255',
            'address'       => 'required|string|max:255',
            'monthly_rent'  => 'required|numeric|min:0',
            'bedrooms'      => 'required|integer|min:1',
            'bathrooms'     => 'required|integer|min:1',
            'property_type' => 'required|in:room,apartment,house',
            'available_from'=> 'nullable|date',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
        ]);

        $validated['user_id'] = auth()->id();

        Property::create($validated);

        return redirect()->route('properties.index')
            ->with('success', 'Property created successfully.');
    }
}
