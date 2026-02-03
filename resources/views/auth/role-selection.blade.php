@extends('layouts.dwello')

@section('title', 'Select Role - Dwello')

@section('content')
<div class="container mx-auto px-4 py-8 md:px-6" style="min-height: calc(100vh - 160px); display:flex; align-items:center; justify-content:center;">
    <div style="width:100%; max-width:800px; text-align:center;">
        <h2 style="font-family:'Poppins',sans-serif; font-size:32px; font-weight:600; margin-bottom:16px; color:var(--gray-900);">
            How would you like to log in?
        </h2>
        <p style="color:var(--gray-600); margin-bottom:48px; font-size:16px;">
            Choose your account type to continue to your dashboard.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 600px; margin: 0 auto;">
            {{-- Tenant Option --}}
            <a href="{{ route('login', ['role' => 'tenant']) }}" class="role-card" style="text-decoration: none;">
                <div style="background:white; border-radius:20px; padding:32px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px solid transparent;">
                    <div style="width: 80px; height: 80px; background: #FFF7ED; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#EA580C" style="width: 40px; height: 40px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">Login as Tenant</h3>
                    <p style="font-size: 14px; color: var(--gray-600);">Find your perfect room and connect with flatmates</p>
                </div>
            </a>

            {{-- Landlord Option --}}
            <a href="{{ route('login', ['role' => 'landlord']) }}" class="role-card" style="text-decoration: none;">
                <div style="background:white; border-radius:20px; padding:32px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px solid transparent;">
                    <div style="width: 80px; height: 80px; background: #EEF2FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#4F46E5" style="width: 40px; height: 40px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h1.5M2.25 7.5l5.062-1.688a4.5 4.5 0 012.376 0L14.814 7.5H18a3.75 3.75 0 013.75 3.75v9.75M8.25 21h8.25" />
                        </svg>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">Login as Landlord</h3>
                    <p style="font-size: 14px; color: var(--gray-600);">Manage your properties and find great tenants</p>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .role-card:hover div {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        border-color: var(--dwello-primary);
    }
    @media (max-width: 640px) {
        .container > div > div {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
