<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $user = auth()->user();
        $plan = $request->input('plan', 'premium'); // Default to premium if not specified

        // Prevent double upgrade if already on the same plan (optional, simplified for now)
        // if ($user->is_premium && $user->subscription_plan === $plan) { ... }

        Stripe::setApiKey(config('services.stripe.secret'));

        $price = 50000; // Default Premium: 500.00 LKR
        $name = 'Dwello Premium Membership';

        if ($plan === 'silver') {
            $price = 150000; // 1500.00 LKR
            $name = 'Dwello Silver Membership';
        } elseif ($plan === 'gold') {
            $price = 300000; // 3000.00 LKR
            $name = 'Dwello Gold Membership';
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'customer_email' => $user->email, // Prefill email for better UX
                'billing_address_collection' => 'auto', // Do not force address collection
                'locale' => 'auto',
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'lkr',
                            'product_data' => [
                                'name' => $name,
                            ],
                            'unit_amount' => $price,
                        ],
                        'quantity' => 1,
                    ]
                ],
                // Append session_id to success URL to retrieve it later
                'success_url' => config('services.stripe.client_url') . '/payment-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('services.stripe.client_url') . '/payment-cancel',
                'metadata' => [
                    'user_id' => $user->id,
                    'plan' => $plan,
                ],
            ]);

            return response()->json([
                'url' => $session->url
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        $user = auth()->user();
        $sessionId = $request->get('session_id');

        if ($sessionId) {
            Stripe::setApiKey(config('services.stripe.secret'));
            try {
                $session = Session::retrieve($sessionId);
                $plan = $session->metadata->plan ?? 'premium';

                if ($user) {
                    $user->is_premium = true;
                    $user->premium_subscription_date = now();
                    $user->subscription_plan = $plan;

                    // Set listing limits
                    if ($plan === 'silver') {
                        $user->listing_limit = 12;
                    } elseif ($plan === 'gold') {
                        $user->listing_limit = 999999; // Unlimited
                    } else {
                        $user->listing_limit = null; // Tenant premium has no listing limit concept usually, or N/A
                    }


                    $user->save();

                    // Verify Roommate Profile if exists
                    if ($user->roommateProfile) {
                        $user->roommateProfile->update(['is_verified' => true]);
                    }
                }
            } catch (\Exception $e) {
                // Log error if session retrieval fails
                \Log::error('Stripe Session Error: ' . $e->getMessage());
            }
        } elseif ($user && !$user->is_premium) {
            // Fallback for legacy flows or direct access (should be discouraged)
            // Keeping it minimally safe or just doing nothing.
            // For now, let's rely on session verification.
        }

        return view('payment-success');
    }
}
