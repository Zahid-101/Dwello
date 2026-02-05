@extends('layouts.dwello')

@section('title', 'Payment Successful')

@section('content')
    <div class="container mx-auto px-4 py-24 text-center">
        <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-8">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4 font-poppins">Payment Successful!</h1>
        <p class="text-xl text-gray-600 mb-8 max-w-lg mx-auto">
            Welcome to Dwello Premium! Your account has been upgraded. You now have access to unlimited messaging and
            verified badges.
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary px-8 py-3 text-lg">
            Start Browsing
        </a>
    </div>
@endsection