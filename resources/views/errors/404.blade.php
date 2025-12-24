@extends('layouts.dwello')

@section('title', 'Page Not Found - Dwello')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-160px)] px-6 py-12">
    <div class="text-center max-w-md">
        <div class="mb-6 flex justify-center">
            <svg class="w-32 h-32 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-4 font-poppins">404</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Page Not Found</h2>
        <p class="text-gray-600 mb-8">
            Oops! The page you are looking for doesn't exist or has been moved.
        </p>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-orange-500 hover:bg-orange-600 transition-colors shadow-lg shadow-orange-500/30">
            Go back home
        </a>
    </div>
</div>
@endsection
