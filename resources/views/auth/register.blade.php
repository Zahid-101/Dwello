@extends('layouts.dwello')

@section('title', 'Create an account - Dwello')

@section('content')
    <div class="container mx-auto px-4 py-8 md:px-6"
        style="min-height: calc(100vh - 160px); display:flex; align-items:center; justify-content:center;">
        <div
            style="width:100%; max-width:480px; background:white; border-radius:20px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:24px;">
            <h2
                style="font-family:'Poppins',sans-serif; font-size:24px; font-weight:600; margin-bottom:8px; color:var(--gray-900);">
                Create your Dwello account
            </h2>
            <p style="color:var(--gray-600); margin-bottom:20px; font-size:14px;">
                Sign up to list your room and find compatible flatmates.
            </p>

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

            <form method="POST" action="{{ route('register') }}" style="display:flex; flex-direction:column; gap:14px;">
                @csrf

                <div>
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Name</label>
                    <input class="input @error('name') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                        type="text" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Email</label>
                    <input class="input @error('email') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                        type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Password</label>
                    <input class="input @error('password') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                        type="password" name="password" required autocomplete="new-password">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:4px;">Confirm
                        password</label>
                    <input class="input @error('password_confirmation') border-red-500 @enderror"
                        style="width:100%; border-radius:12px;" type="password" name="password_confirmation" required>
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Terms and Conditions --}}
                <div style="margin-top: 10px;">
                    <div
                        style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; height: 120px; overflow-y: auto; font-size: 11px; color: var(--gray-600); margin-bottom: 8px;">
                        <p style="margin-bottom: 8px;"><strong>User Responsibility:</strong> You are solely responsible for
                            any personal information, including contact details and profile pictures, that you choose to
                            share on this platform. Dwello is not liable for any consequences arising from the voluntary
                            disclosure of your data.</p>
                        <p style="margin-bottom: 8px;"><strong>Assumption of Risk:</strong> All interactions,
                            communications, room rentals, and roommate arrangements are conducted entirely at your own risk.
                            You agree to exercise caution and due diligence when interacting with other users.</p>
                        <p><strong>Platform Role:</strong> Dwello operates solely as a connecting platform and does not
                            verify, endorse, or guarantee the conduct, identity, or safety of any user. We assume no
                            responsibility for disputes, damages, or losses resulting from user interactions.</p>
                    </div>

                    <label
                        style="display: flex; gap: 8px; align-items: start; font-size: 13px; color: var(--gray-700); cursor: pointer;">
                        <input type="checkbox" name="terms" required {{ old('terms') ? 'checked' : '' }}
                            style="margin-top: 3px;">
                        <span>I have read and agree to the above terms. I understand that using Dwello is at my own risk and
                            that the company is not responsible for any issues arising from user interactions.</span>
                    </label>
                    @error('terms')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div
                    style="margin-top:8px; display:flex; justify-content:space-between; align-items:center; font-size:13px;">
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