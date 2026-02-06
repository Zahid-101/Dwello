@extends('layouts.dwello')

@section('title', 'Log in - Dwello')

@section('content')
    <div class="container mx-auto px-4 py-8 md:px-6"
        style="min-height: calc(100vh - 160px); display:flex; align-items:center; justify-content:center;">
        <div
            style="width:100%; max-width:420px; background:white; border-radius:20px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:24px;">
            <h2
                style="font-family:'Poppins',sans-serif; font-size:24px; font-weight:600; margin-bottom:8px; color:var(--gray-900);">
                @if(request('role'))
                    Welcome back, {{ ucfirst(request('role')) }} 👋
                @else
                    Welcome back 👋
                @endif
            </h2>
            <p style="color:var(--gray-600); margin-bottom:20px; font-size:14px;">
                Log in to manage your listings and connect with flatmates.
            </p>

            {{-- Session status (e.g. password reset message) --}}
            @if (session('status'))
                <div
                    style="background:#ECFEFF; color:#0E7490; padding:10px 14px; border-radius:12px; font-size:13px; margin-bottom:16px;">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div
                    style="background:#FEE2E2; color:#991B1B; padding:10px 14px; border-radius:12px; font-size:13px; margin-bottom:16px;">
                    <ul style="padding-left:18px; margin:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:14px;">
                @csrf

                <div>
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Email</label>
                    <input class="input @error('email') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                        type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Password</label>
                    <input class="input @error('password') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                        type="password" name="password" required autocomplete="current-password">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if(request('role'))
                    <input type="hidden" name="expected_role" value="{{ request('role') }}">
                @endif

                <div
                    style="display:flex; justify-content:space-between; align-items:center; font-size:13px; margin-top:4px;">
                    <label style="display:flex; align-items:center; gap:6px; color:var(--gray-600);">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>

                    {{-- We can leave forgot-password route existing, or remove link if you don't use it --}}
                    <a href="{{ route('password.request') }}" style="color:var(--dwello-primary); text-decoration:none;">
                        Forgot password?
                    </a>
                </div>

                <div style="margin-top:12px;">
                    <button type="submit" class="btn btn-primary" style="width:100%; border-radius:14px;">
                        Log in
                    </button>
                </div>

                <div style="margin-top:12px; text-align:center; font-size:13px; color:var(--gray-600);">
                    Don’t have an account?
                    <a href="{{ route('register') }}" style="color:var(--dwello-primary); text-decoration:none;">
                        Sign up
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Role Mismatch Popup Modal --}}
    @if(session('show_role_mismatch_popup'))
        <div id="role-error-modal"
            style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
            <div
                style="background: white; width: 90%; max-width: 480px; padding: 32px; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); position: relative; text-align: center;">

                <button onclick="document.getElementById('role-error-modal').remove()"
                    style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray-400);">
                    &times;
                </button>

                <div
                    style="width: 64px; height: 64px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 16px auto;">
                    ⚠️
                </div>

                <h2
                    style="font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 700; color: var(--gray-900); margin-bottom: 12px;">
                    Account Role Mismatch
                </h2>

                <p style="color: var(--gray-600); font-size: 15px; margin-bottom: 24px; line-height: 1.6;">
                    {{ session('role_mismatch_message') ?? 'You are trying to log in with an account type that does not match this portal.' }}
                </p>

                <div
                    style="background: var(--gray-50); padding: 16px; border-radius: 12px; font-size: 14px; text-align: left; margin-bottom: 24px; border: 1px solid var(--gray-200);">
                    <p style="margin-bottom: 8px;"><strong>Correct Action:</strong></p>
                    <ul style="padding-left: 20px; color: var(--gray-700);">
                        @if(session('actual_role') === 'landlord')
                            <li>Please use the <a href="{{ route('login', ['role' => 'landlord']) }}"
                                    style="color: var(--dwello-primary); text-decoration: underline; font-weight: 600;">Login as
                                    Landlord</a> link.</li>
                        @elseif(session('actual_role') === 'seeker')
                            <li>Please use the <a href="{{ route('login', ['role' => 'tenant']) }}"
                                    style="color: var(--dwello-primary); text-decoration: underline; font-weight: 600;">Login as
                                    Tenant</a> link.</li>
                        @else
                            <li>Please use the standard login.</li>
                        @endif
                    </ul>
                </div>

                <button onclick="document.getElementById('role-error-modal').remove()" class="btn btn-primary"
                    style="width: 100%;">
                    Understood
                </button>
            </div>
        </div>
    @endif
@endsection