@extends('layouts.dwello')

@section('title', 'Dwello Premium')

@section('content')

    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1
                            class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl font-poppins">
                            <span class="block xl:inline">Upgrade to</span>
                            <span class="block text-orange-600 xl:inline">Dwello Premium</span>
                        </h1>
                        <p
                            class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Get unlimited access to verified roommates, priority listings, and direct messaging to find your
                            perfect match faster.
                        </p>
                    </div>
                </main>
            </div>
        </div>
    </div>

    @if(auth()->check() && auth()->user()->is_premium)
        <!-- ALREADY PREMIUM: Subscription Details -->
        @php
            $plan = auth()->user()->subscription_plan;
            $color = 'green';
            $borderColor = 'border-green-100';
            $badgeBg = 'bg-green-100';
            $badgeText = 'text-green-800';
            $iconBg = 'bg-green-50';
            $iconText = 'text-green-600';

            if ($plan === 'gold') {
                $color = 'yellow';
                $borderColor = 'border-yellow-400 border-2'; // Thicker border for Gold
                $badgeBg = 'bg-yellow-100';
                $badgeText = 'text-yellow-800';
                $iconBg = 'bg-yellow-50';
                $iconText = 'text-yellow-600';
            } elseif ($plan === 'silver') {
                $color = 'gray';
                $borderColor = 'border-gray-300';
                $badgeBg = 'bg-gray-100';
                $badgeText = 'text-gray-800';
                $iconBg = 'bg-gray-50';
                $iconText = 'text-gray-600';
            }
        @endphp

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border {{ $borderColor }}">
                <div class="p-8 sm:p-12">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeBg }} {{ $badgeText }} mb-4">
                                ● Active
                            </span>
                            <h2 class="text-3xl font-bold text-gray-900 font-poppins">My Subscription</h2>
                        </div>
                        <div class="h-16 w-16 {{ $iconBg }} rounded-full flex items-center justify-center">
                            @if($plan === 'gold')
                                <svg class="w-8 h-8 {{ $iconText }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            @else
                                <svg class="w-8 h-8 {{ $iconText }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8 mb-8 border-t border-gray-100 pt-8">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Plan</p>
                            <p class="font-semibold text-lg text-gray-900 capitalize">
                                {{ ucfirst($plan) }} Membership
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Upgraded On</p>
                            <p class="font-semibold text-lg text-gray-900">
                                {{ auth()->user()->premium_subscription_date ? \Carbon\Carbon::parse(auth()->user()->premium_subscription_date)->format('F j, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Next Billing Date</p>
                            <p class="font-semibold text-lg text-gray-900">
                                {{ auth()->user()->premium_subscription_date ? \Carbon\Carbon::parse(auth()->user()->premium_subscription_date)->addMonth()->format('F j, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Limit</p>
                            <p class="text-gray-900 font-semibold">
                                @if(auth()->user()->listing_limit > 1000)
                                    Unlimited Listings
                                @elseif(auth()->user()->listing_limit)
                                    {{ auth()->user()->listing_limit }} Listings
                                @else
                                    Standard
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($plan === 'silver')
                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <div
                                class="flex items-center justify-between bg-gradient-to-r from-yellow-50 to-white p-4 rounded-xl border border-yellow-200">
                                <div>
                                    <h3 class="font-bold text-gray-900">Unlock Gold Power ⚡</h3>
                                    <p class="text-sm text-gray-600">Get unlimited listings and priority support.</p>
                                </div>
                                <button data-plan="gold"
                                    class="checkout-btn bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-yellow-600 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    Upgrade to Gold
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- NOT PREMIUM: Show Pricing Cards -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            @if(auth()->user()->isLandlord())
                <!-- LANDLORD PLANS -->
                <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">

                    <!-- Silver Plan -->
                    <div
                        class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200 hover:border-gray-300 transform transition-all hover:-translate-y-1 duration-300 relative">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 font-poppins mb-2">Silver Class</h3>
                            <p class="text-gray-500 mb-6">Perfect for managing a few properties.</p>
                            <div class="flex items-baseline mb-6">
                                <span class="text-4xl font-extrabold text-gray-900">LKR 1,500</span>
                                <span class="text-gray-500 ml-2">/month</span>
                            </div>
                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                        </path>
                                    </svg>
                                    12 Property Listings
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                        </path>
                                    </svg>
                                    Unlimited Messages
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                        </path>
                                    </svg>
                                    Verified Badge
                                </li>
                            </ul>
                            <button data-plan="silver"
                                class="checkout-btn w-full block bg-gray-800 text-white font-bold py-3 px-6 rounded-xl hover:bg-gray-900 transition-colors text-center">
                                Choose Silver
                            </button>
                        </div>
                    </div>

                    <!-- Gold Plan -->
                    <div
                        class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-yellow-400 transform transition-all hover:-translate-y-1 duration-300 relative">
                        <div class="absolute top-0 right-0 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-bl-lg">
                            POPULAR</div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 font-poppins mb-2">Gold Class</h3>
                            <p class="text-gray-500 mb-6">Unlimited power for professional landlords.</p>
                            <div class="flex items-baseline mb-6">
                                <span class="text-4xl font-extrabold text-yellow-500">LKR 3,000</span>
                                <span class="text-gray-500 ml-2">/month</span>
                            </div>
                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                        </path>
                                    </svg>
                                    Unlimited Property Listings
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                        </path>
                                    </svg>
                                    Unlimited Messages
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                        </path>
                                    </svg>
                                    Gold Verified Badge
                                </li>
                                <li class="flex items-start text-gray-600">
                                <svg class="w-5 h-5 text-yellow-500 mr-3 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <div>
                                    <span class="block">Boost Up Ads (Top Placement)</span>
                                    <p class="text-xs text-yellow-600 mt-1 leading-snug">
                                        Your ads will be shown on the homepage to users who match your property's location and
                                        budget.
                                    </p>
                                </div>
                                </li>
                            </ul>
                            <button data-plan="gold"
                                class="checkout-btn w-full block bg-yellow-500 text-white font-bold py-3 px-6 rounded-xl hover:bg-yellow-600 transition-colors text-center shadow-lg shadow-yellow-200">
                                Choose Gold
                            </button>
                        </div>
                    </div>

                </div>

            @else
                <!-- TENANT / SEEKER PLAN -->
                <div class="max-w-3xl mx-auto">
                    <div
                        class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 transform transition-all hover:scale-105 duration-300">
                        <div class="p-8 sm:p-12 text-center bg-gradient-to-br from-orange-50 to-white">
                            <h2 class="text-3xl font-bold text-gray-900 font-poppins mb-4">Premium Membership</h2>
                            <p class="text-gray-600 mb-8">Unlock all features and find your perfect match faster.</p>

                            <div class="flex items-baseline justify-center mb-8">
                                <span class="text-5xl font-extrabold text-orange-600">LKR 500</span>
                                <span class="text-xl text-gray-500 ml-2">/month</span>
                            </div>

                            <ul class="space-y-4 mb-10 text-left max-w-md mx-auto">
                                <li class="flex items-center">
                                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-gray-600">Unlimited Messaging</span>
                                </li>
                                <li class="flex items-center">
                                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-gray-600">Verified Badge</span>
                                </li>
                            </ul>

                            <button data-plan="premium"
                                class="checkout-btn w-full bg-orange-600 text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:bg-orange-700 transition-all transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Upgrade Now
                            </button>
                            <p class="mt-4 text-xs text-gray-400">Secure payment via Stripe. Cancel anytime.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    @endif

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.querySelectorAll('.checkout-btn').forEach(button => {
            button.addEventListener('click', async function () {
                const btn = this;
                const originalText = btn.innerHTML;
                const plan = btn.getAttribute('data-plan');

                try {
                    // Show loading state
                    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
                    btn.disabled = true;

                    const response = await fetch("{{ route('stripe.checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ plan: plan })
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        alert('Request Failed: ' + response.status + ' ' + response.statusText + '\n' + errorText.substring(0, 100));
                        throw new Error('Network response was not ok');
                    }

                    const session = await response.json();

                    if (session.error) {
                        alert(session.error);
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    } else if (session.url) {
                        window.location.href = session.url;
                    } else {
                        alert('Error: No checkout URL received');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Something went wrong. Please try again.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            });
        });
    </script>
@endsection