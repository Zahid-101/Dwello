@extends('layouts.dwello')

@section('title', 'Payment Cancelled')

@section('content')
    <div class="container mx-auto px-4 py-24 text-center">
        <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-8">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4 font-poppins">Payment Cancelled</h1>
        <p class="text-xl text-gray-600 mb-8 max-w-lg mx-auto">
            Your payment was not processed. No charges were made.
        </p>
        <a href="{{ url('/payment') }}" class="btn btn-outline px-8 py-3 text-lg">
            Try Again
        </a>
    </div>
@endsection