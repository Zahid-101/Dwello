<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    /**
     * Show the properties list with search & filters.
     */
    public function index(Request $request)
    {
        $query = Property::query()->orderByDesc('created_at');

        // Text search by city or address
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('city', 'like', '%' . $q . '%')
                    ->orWhere('address', 'like', '%' . $q . '%')
                    ->orWhere('title', 'like', '%' . $q . '%');
            });
        }

        // Filter: city
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        // Filter: min rent
        if ($request->filled('min_rent')) {
            $query->where('monthly_rent', '>=', (float) $request->input('min_rent'));
        }

        // Filter: max rent
        if ($request->filled('max_rent')) {
            $query->where('monthly_rent', '<=', (float) $request->input('max_rent'));
        }

        // Filter: property type
        if ($request->filled('property_type')) {
            $query->where('property_type', $request->input('property_type'));
        }

        // Paginate + keep query string in links
        $properties = $query->paginate(9)->withQueryString();

        return view('properties.index', compact('properties'));
    }

    /**
     * Show the create property form (auth only).
     */
    public function create()
    {
        return view('properties.create');
    }

    /**
     * Store a new property (basic validation).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'city'          => 'required|string|max:100',
            'address'       => 'required|string|max:255',
            'monthly_rent'  => 'required|numeric|min:0',
            'bedrooms'      => 'nullable|integer|min:0',
            'bathrooms'     => 'nullable|integer|min:0',
            'property_type' => 'required|string|max:50',
            'available_from'=> 'nullable|date',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
        ]);

        $validated['user_id'] = auth()->id();

        Property::create($validated);

        return redirect()
            ->route('properties.index')
            ->with('status', 'Property listed successfully!');
    }
}
