@extends('layouts.dwello')

@section('title', 'Boost Up Ad')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-yellow-400">
            <div class="p-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 font-poppins mb-4">Boost Your Property</h1>
                <p class="text-lg text-gray-600 mb-8">
                    As a Gold Member, you can boost your listings to the top of search results!
                </p>

                @if(session('status'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                        role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800"
                        role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="mb-8 p-6 bg-blue-50 border-l-4 border-blue-400 rounded-r-xl text-left">
                    <h3 class="text-lg font-bold text-blue-800 mb-2">How to Boost Your Property</h3>
                    <ul class="list-decimal list-inside text-blue-700 space-y-1 text-sm">
                        <li><strong>Create a Listing:</strong> Ensure you have already posted your property in "Create Listing".</li>
                        <li><strong>Select Property:</strong> Choose the property from the dropdown below.</li>
                        <li><strong>One at a Time:</strong> You can only have <strong>one</strong> active boosted ad at a time.</li>
                        <li><strong>Auto-Image:</strong> We will automatically use the main photo from your property listing.</li>
                    </ul>
                </div>

                <div class="p-8 bg-yellow-50 rounded-xl border-2 border-dashed border-yellow-300">
                    <form action="{{ route('boost.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">Select
                                Property</label>
                            <select name="property_id" id="property_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                required>
                                <option value="">-- Choose a property to boost --</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->title }} ({{ $property->location }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Short Note</label>
                            <textarea name="note" id="note" rows="3"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                placeholder="e.g. Spacious room with balcony view! Available immediately." required
                                maxlength="255"></textarea>
                            <p class="mt-1 text-sm text-gray-500">Max 255 characters.</p>
                        </div>

                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                            Boost Now
                        </button>
                    </form>
                </div>
            </div>
            
            <hr class="border-gray-200">

            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 font-poppins mb-6 text-center">Your Boost History</h2>
                
                @if($boostedAds->isEmpty())
                    <p class="text-center text-gray-500">You haven't boosted any ads yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($boostedAds as $ad)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    @if($ad->property_image)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($ad->property_image) }}" alt="">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold text-xs">
                                                            No Img
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    @if($ad->property)
                                                        <div class="text-sm font-medium text-gray-900">{{ $ad->property->title }}</div>
                                                        <div class="text-sm text-gray-500">{{ $ad->property->location }}</div>
                                                    @else
                                                        <div class="text-sm font-medium text-gray-900 text-red-500">Property Removed</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 truncate max-w-xs" title="{{ $ad->note }}">{{ $ad->note }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $ad->created_at->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ route('boost.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this boosted ad?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
    </div>
@endsection