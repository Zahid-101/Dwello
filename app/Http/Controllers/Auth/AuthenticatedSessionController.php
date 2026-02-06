<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Enforce Role Restriction
        $expectedRole = $request->input('expected_role');
        if ($expectedRole) {
            $user = Auth::user();
            $userRole = $user->role;

            // Map expected role (from URL/Form) to DB role
            // 'tenant' in URL maps to 'seeker' in DB
            $mappedExpected = match ($expectedRole) {
                'tenant' => 'seeker',
                'landlord' => 'landlord',
                default => null
            };

            // If a specific valid role was expected, checking it matches the user's actual role
            if ($mappedExpected && $userRole && $userRole !== $mappedExpected) {

                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $displayRole = $expectedRole === 'tenant' ? 'Tenant' : 'Landlord';
                $actualDisplayRole = $userRole === 'seeker' ? 'Tenant' : 'Landlord';

                $message = "You are attempting to log in as a $displayRole, but this account is registered as a $actualDisplayRole.";

                return redirect()->route('login', ['role' => $expectedRole])
                    ->withInput($request->only('email'))
                    ->with('show_role_mismatch_popup', true)
                    ->with('role_mismatch_message', $message)
                    ->with('actual_role', $userRole);
            }
        }

        if (Auth::user()->isAdmin()) {
            return redirect()->intended(route('admin.reviews.index', absolute: false));
        }

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
