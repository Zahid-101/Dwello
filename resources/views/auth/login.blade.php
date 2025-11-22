@extends('layouts.dwello')

@section('title', 'Log in - Dwello')

@section('content')
<div class="container" style="min-height: calc(100vh - 160px); display:flex; align-items:center; justify-content:center; padding: 40px 24px;">
    <div style="width:100%; max-width:420px; background:white; border-radius:20px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:24px;">
        <h2 style="font-family:'Poppins',sans-serif; font-size:24px; font-weight:600; margin-bottom:8px; color:var(--gray-900);">
            Welcome back 👋
        </h2>
        <p style="color:var(--gray-600); margin-bottom:20px; font-size:14px;">
            Log in to manage your listings and connect with flatmates.
        </p>

        {{-- Session status (e.g. password reset message) --}}
        @if (session('status'))
            <div style="background:#ECFEFF; color:#0E7490; padding:10px 14px; border-radius:12px; font-size:13px; margin-bottom:16px;">
                {{ session('status') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div style="background:#FEE2E2; color:#991B1B; padding:10px 14px; border-radius:12px; font-size:13px; margin-bottom:16px;">
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
                <input
                    class="input"
                    style="width:100%; border-radius:12px;"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Password</label>
                <input
                    class="input"
                    style="width:100%; border-radius:12px;"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px; margin-top:4px;">
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
@endsection
