@extends('layouts.dwello')

@section('title', 'Create an account - Dwello')

@section('content')
<div class="container" style="min-height: calc(100vh - 160px); display:flex; align-items:center; justify-content:center; padding: 40px 24px;">
    <div style="width:100%; max-width:480px; background:white; border-radius:20px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:24px;">
        <h2 style="font-family:'Poppins',sans-serif; font-size:24px; font-weight:600; margin-bottom:8px; color:var(--gray-900);">
            Create your Dwello account
        </h2>
        <p style="color:var(--gray-600); margin-bottom:20px; font-size:14px;">
            Sign up to list your room and find compatible flatmates.
        </p>

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

        <form method="POST" action="{{ route('register') }}" style="display:flex; flex-direction:column; gap:14px;">
            @csrf

            <div>
                <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Name</label>
                <input
                    class="input"
                    style="width:100%; border-radius:12px;"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                >
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Email</label>
                <input
                    class="input"
                    style="width:100%; border-radius:12px;"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
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
                    autocomplete="new-password"
                >
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Confirm password</label>
                <input
                    class="input"
                    style="width:100%; border-radius:12px;"
                    type="password"
                    name="password_confirmation"
                    required
                >
            </div>

            <div style="margin-top:8px; display:flex; justify-content:space-between; align-items:center; font-size:13px;">
                <span style="color:var(--gray-600);">
                    Already have an account?
                    <a href="{{ route('login') }}" style="color:var(--dwello-primary); text-decoration:none;">
                        Log in
                    </a>
                </span>
            </div>

            <div style="margin-top:12px;">
                <button type="submit" class="btn btn-primary" style="width:100%; border-radius:14px;">
                    Create account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
