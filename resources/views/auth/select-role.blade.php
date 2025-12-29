@extends('layouts.dwello')

@section('title', 'Select Your Role')

@section('content')
<div class="container" style="padding-top: 64px; padding-bottom: 64px;">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 32px; font-weight: bold; margin-bottom: 16px;">Welcome to Dwello!</h2>
        <p style="color: var(--gray-500); margin-bottom: 48px; font-size: 18px;">How do you plan to use Dwello? This helps us customize your experience.</p>

        <form action="{{ route('role.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; gap: 24px; text-align: left;">
                <!-- Landlord Option -->
                <label style="cursor: pointer; display: flex; align-items: start; gap: 20px; padding: 24px; border: 2px solid var(--gray-200); border-radius: 12px; transition: all 0.2s ease;" class="role-card">
                    <input type="radio" name="role" value="landlord" style="margin-top: 6px; width: 20px; height: 20px; accent-color: var(--dwello-primary);">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">I am a Landlord</h3>
                        <p style="color: var(--gray-500); font-size: 14px; margin: 0;">I have a room, apartment, or house to rent out.</p>
                    </div>
                </label>

                <!-- Seeker Option -->
                <label style="cursor: pointer; display: flex; align-items: start; gap: 20px; padding: 24px; border: 2px solid var(--gray-200); border-radius: 12px; transition: all 0.2s ease;" class="role-card">
                    <input type="radio" name="role" value="seeker" style="margin-top: 6px; width: 20px; height: 20px; accent-color: var(--dwello-primary);">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">I am Looking for a Place</h3>
                        <p style="color: var(--gray-500); font-size: 14px; margin: 0;">I am searching for a room to rent or a flatmate to share with.</p>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 48px; width: 100%; padding: 16px; font-size: 16px;">Continue</button>
        </form>
    </div>
</div>

<style>
    .role-card:hover {
        border-color: var(--dwello-primary) !important;
        background-color: rgba(var(--dwello-primary-rgb), 0.02);
    }
    .role-card:has(input:checked) {
        border-color: var(--dwello-primary) !important;
        background-color: rgba(var(--dwello-primary-rgb), 0.05);
        box-shadow: 0 4px 6px -1px rgba(var(--dwello-primary-rgb), 0.1);
    }
</style>
@endsection
