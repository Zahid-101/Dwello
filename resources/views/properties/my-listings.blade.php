@extends('layouts.dwello')
@section('title', 'My Listings - Dwello')
@section('content')

    <div class="container" style="padding: 40px 24px; min-height: calc(100vh - 300px);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px;">
            <h1 style="font-size: 32px; font-weight: 700; color: var(--gray-900); font-family: 'Poppins', sans-serif;">
                My Listings
            </h1>
            <a href="{{ route('properties.create') }}" class="btn btn-primary">
                + Create New Listing
            </a>
        </div>

        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 32px;">
            @forelse($properties as $property)
                <div
                    style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column;">
                    <div style="height: 200px; position: relative;">
                        @if($property->photos->count() > 0)
                            <img src="{{ Storage::url($property->photos->first()->path) }}" alt="{{ $property->title }}"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&h=400&fit=crop"
                                alt="{{ $property->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @endif
                        <div
                            style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); color: white; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 500;">
                            {{ ucfirst($property->property_type) }}
                        </div>
                    </div>

                    <div style="padding: 24px; flex-grow: 1; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <h3 style="font-weight: 600; font-size: 18px; color: var(--gray-900); line-height: 1.4;">
                                {{ $property->title }}
                            </h3>
                        </div>

                        <p
                            style="color: var(--gray-600); font-size: 14px; margin-bottom: 16px; display: flex; align-items: center; gap: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            {{ $property->city }}
                        </p>

                        <div style="margin-top: auto;">
                            <span style="font-size: 20px; font-weight: 700; color: var(--dwello-primary);">
                                LKR {{ number_format($property->monthly_rent / 1000, 1) }}k
                            </span>
                            <span style="font-size: 13px; color: var(--gray-500);">/mo</span>
                        </div>
                    </div>

                    <div
                        style="padding: 16px 24px; background: var(--gray-50); border-top: 1px solid var(--gray-100); display: flex; gap: 12px;">
                        <a href="{{ route('properties.edit', $property) }}" class="btn btn-outline"
                            style="flex: 1; font-size: 14px; text-align:center;">
                            Edit
                        </a>
                        <a href="{{ route('properties.show', $property) }}" class="btn btn-outline"
                            style="flex: 1; font-size: 14px; text-align:center;">
                            View
                        </a>
                        <form method="POST" action="{{ route('properties.destroy', $property) }}"
                            onsubmit="return confirm('Are you sure you want to delete this listing?');" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn"
                                style="width:100%; font-size: 14px; background-color: #fee2e2; color: #ef4444; border: 1px solid #fecaca; font-weight: 600;">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 64px 0;">
                    <div
                        style="width: 80px; height: 80px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                        <svg style="width: 40px; height: 40px; color: var(--gray-400);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">
                        No listings yet
                    </h3>
                    <p style="color: var(--gray-500); max-width: 400px; margin: 0 auto 24px; line-height: 1.6;">
                        You haven't posted any properties yet. Create your first listing to start finding tenants.
                    </p>
                    <a href="{{ route('properties.create') }}" class="btn btn-primary" style="padding: 12px 24px;">
                        Create Your First Listing
                    </a>
                </div>
            @endforelse
        </div>
    </div>

@endsection