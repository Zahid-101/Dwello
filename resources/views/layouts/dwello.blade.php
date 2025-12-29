{{-- resources/views/layouts/dwello.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <title>@yield('title', 'Dwello')</title>

    {{-- Google fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    {{-- Laravel / Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/mobile-overrides.css') }}">
</head>
<body>
    {{-- Header --}}
    <header style="background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: relative; z-index: 50;">
        <div class="container" style="padding: 16px 24px;">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                <div class="flex items-center" style="gap: 12px;">
                    <a href="{{ route('home') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: var(--dwello-primary); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-family: 'Poppins', sans-serif; font-weight: bold; font-size: 18px;">D</span>
                        </div>
                        <h1 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: bold; color: var(--gray-900);">
                            Dwello
                        </h1>
                    </a>
                </div>

                {{-- Mobile Menu Button (Visible only on mobile via CSS) --}}
                <div class="mobile-menu-btn" style="display: none;">
                    <button id="mobile-menu-btn" class="p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                {{-- Desktop Navigation --}}
                <div class="desktop-nav" style="display: flex; align-items: center; gap: 32px;">
                    <nav class="flex items-center" style="gap: 32px;">
                        <a href="{{ route('home') }}" class="nav-link" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Home</a>
                        <a href="{{ route('properties.index') }}" class="nav-link" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Search Rooms</a>
                        
                        @if(!auth()->check() || (auth()->user()->isSeeker()))
                            <a href="{{ route('roommates.index') }}" class="nav-link" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Find Flatmate</a>
                        @endif

                        @auth
                            <a href="{{ route('messages.index') }}" class="nav-link" style="color: var(--gray-700); text-decoration: none; font-weight: 500; display: inline-flex; align-items: center;">
                                Messages
                                @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                    <span style="background-color: #dc2626; color: white; border-radius: 9999px; padding: 2px 6px; font-size: 10px; font-weight: bold; margin-left: 4px; line-height: 1;">
                                        {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                                    </span>
                                @endif
                            </a>
                            @if(auth()->user()->isLandlord())
                                <a href="{{ route('properties.create') }}" class="nav-link" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Create Listing</a>
                            @endif

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.reviews.index') }}" class="nav-link" style="color: #4f46e5; text-decoration: none; font-weight: 600;">
                                    Admin
                                </a>
                            @endif
                        @endauth
                    </nav>

                    {{-- Auth Buttons --}}
                    <div class="flex items-center" style="gap: 16px;">
                        @guest
                            <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('login') }}'">
                                Login
                            </button>
                        @else
                            @auth
                                <a href="{{ route('roommate-profiles.create') }}" style="
                                    font-size: 14px; 
                                    font-weight: 600; 
                                    color: var(--dwello-primary); 
                                    border: 2px solid var(--dwello-primary); 
                                    padding: 6px 16px; 
                                    border-radius: 20px; 
                                    box-shadow: 0 0 10px rgba(0,0,0,0.05);
                                    transition: all 0.3s ease;
                                    display: flex;
                                    align-items: center;
                                    text-decoration: none;
                                ">
                                    {{ Auth::user()->name }}
                                    @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                        <span style="display: inline-block; width: 8px; height: 8px; background-color: #dc2626; border-radius: 50%; margin-left: 6px;"></span>
                                    @endif
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 16px; height: 16px; margin-left: 6px; display: inline-block; vertical-align: text-bottom;">
                                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endauth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline" type="submit">Logout</button>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>

            {{-- Mobile Menu (Hidden on Desktop) --}}
            <div id="mobile-menu" style="display: none;" class="mt-4 border-t border-gray-100 pt-4 pb-2">
                <nav class="flex flex-col space-y-3">
                    <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Home</a>
                    <a href="{{ route('properties.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Search Rooms</a>
                    
                    @if(!auth()->check() || (auth()->user()->isSeeker()))
                        <a href="{{ route('roommates.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Find Flatmate</a>
                    @endif

                    @auth
                        <a href="{{ route('messages.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md flex items-center">
                            Messages
                            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                <span class="ml-2 bg-red-600 text-white rounded-full px-2 py-0.5 text-xs font-bold">
                                    {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                                </span>
                            @endif
                        </a>
                        @if(auth()->user()->isLandlord())
                            <a href="{{ route('properties.create') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">Create Listing</a>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.reviews.index') }}" class="block px-3 py-2 text-base font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-md">
                                Admin Dashboard
                            </a>
                        @endif
                        
                        <div class="border-t border-gray-100 my-2"></div>
                        
                        <a href="{{ route('roommate-profiles.create') }}" class="block px-3 py-2 text-base font-medium text-dwello-primary hover:text-dwello-primary-hover hover:bg-orange-50 rounded-md">
                            My Profile ({{ Auth::user()->name }})
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left block px-3 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-md" type="submit">Logout</button>
                        </form>
                    @else
                        <div class="pt-4 pb-2">
                             <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-orange-500 hover:bg-orange-600">
                                Login
                            </a>
                        </div>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
                btn.addEventListener('click', () => {
                    if (menu.style.display === 'none' || menu.style.display === '') {
                        menu.style.display = 'block';
                    } else {
                        menu.style.display = 'none';
                    }
                });
        });
    </script>

    {{-- Page content --}}
    @yield('content')

    {{-- Footer --}}
    <footer style="background: var(--gray-900); color: white; padding: 48px 0; margin-top: 64px;">
        <div class="container">
            <div class="layout-footer-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; margin-bottom: 32px;">
                <div>
                    <div class="flex items-center" style="gap: 12px; margin-bottom: 24px;">
                        <div style="width: 40px; height: 40px; background: var(--dwello-primary); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-family: 'Poppins', sans-serif; font-weight: bold; font-size: 18px;">D</span>
                        </div>
                        <h3 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: bold;">Dwello</h3>
                    </div>
                    <p style="color: var(--gray-300);">Sri Lanka's trusted platform for room rentals and flatmate matching.</p>
                </div>
                <div>
                    <h4 style="font-weight: 500; margin-bottom: 16px;">Company</h4>
                    <ul style="list-style: none; line-height: 2;">
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">About Us</a></li>
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">How It Works</a></li>
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="font-weight: 500; margin-bottom: 16px;">Support</h4>
                    <ul style="list-style: none; line-height: 2;">
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">Contact Us</a></li>
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">Trust & Safety</a></li>
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">Help Center</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="font-weight: 500; margin-bottom: 16px;">Legal</h4>
                    <ul style="list-style: none; line-height: 2;">
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">Privacy Policy</a></li>
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">Terms of Service</a></li>
                        <li><a href="#" style="color: var(--gray-300); text-decoration: none;">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div style="border-top: 1px solid var(--gray-600); padding-top: 32px; text-align: center; color: var(--gray-300);">
                <p>&copy; {{ date('Y') }} Dwello. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    {{-- Page-specific scripts (properties map, roommates tabs, etc.) --}}
    @stack('scripts')
    @stack('map-scripts')
    @stack('form-scripts')
    <x-loading-spinner />
</body>
</html>