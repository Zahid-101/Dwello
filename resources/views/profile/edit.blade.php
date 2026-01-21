@extends('layouts.dwello')

@section('title', 'My Profile - Dwello')

@section('content')
    <div class="container mx-auto px-4 py-8 md:px-6">
        <div
            style="max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 24px;">
            <h2 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: 600; margin-bottom: 16px;">
                My Profile
            </h2>
            <p style="color: var(--gray-600); margin-bottom: 24px;">
                Update your account's profile information and email address.
            </p>

            @if (session('status') === 'profile-updated')
                <div
                    style="background: #D1FAE5; color: #065F46; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 14px;">
                    Profile updated successfully.
                </div>
            @endif

            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                {{-- Profile Photo --}}
                <div style="margin-bottom: 24px;">
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--gray-700);">Profile
                        Picture</h4>
                    <div class="flex items-center gap-4">
                        <div
                            class="relative w-20 h-20 rounded-full overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center">
                            <?php $profilePhotoUrl = $user->profile_photo_path ? Storage::url($user->profile_photo_path) : null; ?>
                            <img id="profile-preview" src="{{ $profilePhotoUrl }}" alt="Profile Preview"
                                class="{{ $profilePhotoUrl ? '' : 'hidden' }} w-full h-full object-cover">
                            <span id="profile-placeholder" class="{{ $profilePhotoUrl ? 'hidden' : '' }} text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                        </div>

                        <div class="flex-1">
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-orange-50 file:text-orange-700
                                  hover:file:bg-orange-100
                                  cursor-pointer
                                " onchange="previewImage(this)" />
                            <p class="text-xs text-gray-500 mt-2">Recommended: Square JPG, PNG. Max 1MB.</p>
                            @error('profile_photo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-bottom: 24px;">
                    {{-- Name --}}
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Name</label>
                        <input class="input @error('name') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                            id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                            autocomplete="name">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Phone
                            Number</label>
                        <input class="input @error('phone_number') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" id="phone_number" name="phone_number" type="text"
                            value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel" maxlength="10" pattern="\d*">
                        @error('phone_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Email</label>
                    <input class="input @error('email') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                        id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                        autocomplete="username">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div style="margin-top: 8px;">
                            <p class="text-sm text-gray-800">
                                {{ __('Your email address is unverified.') }}
                                <button form="send-verification"
                                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>
                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex justify-end" style="gap: 12px; margin-bottom: 32px;">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                </div>
            </form>

            <hr style="border: 0; border-top: 1px solid var(--gray-200); margin-bottom: 32px;">

            {{-- Update Password Section --}}
            <div>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--gray-900);">Update Password
                </h3>
                <p style="color: var(--gray-600); margin-bottom: 24px; font-size: 14px;">
                    Ensure your account is using a long, random password to stay secure.
                </p>

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-bottom: 24px;">
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Current
                                Password</label>
                            <input class="input @error('current_password', 'updatePassword') border-red-500 @enderror"
                                style="width:100%; border-radius:12px;" id="current_password" name="current_password"
                                type="password" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-bottom: 24px;">
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">New
                                Password</label>
                            <input class="input @error('password', 'updatePassword') border-red-500 @enderror"
                                style="width:100%; border-radius:12px;" id="password" name="password" type="password"
                                autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Confirm
                                Password</label>
                            <input class="input @error('password_confirmation', 'updatePassword') border-red-500 @enderror"
                                style="width:100%; border-radius:12px;" id="password_confirmation"
                                name="password_confirmation" type="password" autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end" style="gap: 12px; margin-bottom: 32px;">
                        <button class="btn btn-primary" type="submit">Update Password</button>
                        @if (session('status') === 'password-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-green-600 flex items-center">{{ __('Saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--gray-200); margin-bottom: 32px;">

            {{-- Delete Account Section --}}
            <div>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #DC2626;">Delete Account</h3>
                <p style="color: var(--gray-600); margin-bottom: 24px; font-size: 14px;">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>
                <div class="flex justify-end">
                    <button class="btn btn-outline border-red-500 text-red-500 hover:bg-red-50" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
                        Delete Account
                    </button>
                </div>

                <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                        @csrf
                        @method('delete')

                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Are you sure you want to delete your account?') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mt-6">
                            <label for="password" class="sr-only">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" class="mt-1 block w-3/4 input"
                                placeholder="{{ __('Password') }}" />
                            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" class="btn btn-outline mr-3" x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary bg-red-600 hover:bg-red-700 border-red-600">
                                {{ __('Delete Account') }}
                            </button>
                        </div>
                    </form>
                </x-modal>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('profile-preview');
            const placeholder = document.getElementById('profile-placeholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection