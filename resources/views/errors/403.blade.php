@extends('layouts.dwello')

@section('title', 'Access Denied - Dwello')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-160px)] px-6 py-12">
    <div class="text-center max-w-md">
        <div class="mb-6 flex justify-center">
            <svg class="w-32 h-32 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-4 font-poppins">403</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Access Denied</h2>
        <p class="text-gray-600 mb-8">
            You don't have permission to access this page. Please contact support if you think this is a mistake.
        </p>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-orange-500 hover:bg-orange-600 transition-colors shadow-lg shadow-orange-500/30">
            Go back home
        </a>
    </div>
</div>
@endsection
