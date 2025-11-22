<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // Show the list of properties + simple search
    public function index(Request $request)
    {
        $query = Property::with('user');

        // basic search: by keyword (title/city/address)
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // filter by city (optional)
        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        // filter by min / max rent (optional)
        if ($minRent = $request->get('min_rent')) {
            $query->where('monthly_rent', '>=', $minRent);
        }

        if ($maxRent = $request->get('max_rent')) {
            $query->where('monthly_rent', '<=', $maxRent);
        }

        // order newest first, paginate
        $properties = $query->orderBy('created_at', 'desc')
                            ->paginate(10)
                            ->withQueryString();

        return view('properties.index', compact('properties'));
    }

    // Show the "create property" form (only for logged-in users)
    public function create()
    {
        return view('properties.create');
    }

    // Save a new property in the DB
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'city'          => 'required|string|max:255',
            'address'       => 'required|string|max:255',
            'monthly_rent'  => 'required|numeric|min:0',
            'bedrooms'      => 'required|integer|min:0',
            'bathrooms'     => 'required|integer|min:0',
            'property_type' => 'required|in:room,apartment,house',
            'available_from'=> 'nullable|date',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
        ]);

        $data['user_id'] = auth()->id();

        Property::create($data);

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property created successfully!');
    }
}