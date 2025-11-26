{{-- resources/views/layouts/dwello.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
</head>
<body>
    {{-- Header --}}
    <header style="background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div class="container" style="padding: 16px 24px;">
            <div class="flex items-center justify-between">
                <div class="flex items-center" style="gap: 12px;">
                    <div style="width: 40px; height: 40px; background: var(--dwello-primary); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-family: 'Poppins', sans-serif; font-weight: bold; font-size: 18px;">D</span>
                    </div>
                    <h1 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: bold; color: var(--gray-900);">
                        Dwello
                    </h1>
                </div>

                <nav class="flex items-center" style="gap: 32px;">
                    <a href="{{ route('home') }}" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Home</a>
                    <a href="{{ route('properties.index') }}" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Search Rooms</a>
                    <a href="{{ route('roommates.index') }}" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Find Flatmate</a>

                    {{-- Not built yet → send to under-development 
                    <a href="{{ route('under-development') }}" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Messages</a>
                    --}}
                    @auth
                     <a href="{{ route('properties.create') }}" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Create Listing</a>
                    @endauth
                </nav>


                <div class="flex items-center" style="gap: 16px;">
                    @guest
                        <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('login') }}'">
                            Login
                        </button>
                    @else
                    @auth
                       <a href="{{ route('roommate-profiles.create') }}" span style="
    font-size: 14px; 
    font-weight: 600; 
    color: var(--dwello-primary); 
    border: 2px solid var(--dwello-primary); 
    padding: 6px 16px; 
    border-radius: 20px; 
    box-shadow: 0 0 10px rgba(0,0,0,0.05); /* Subtle glow */
    transition: all 0.3s ease;
">
    {{ Auth::user()->name }}
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 16px; height: 16px; margin-left: 6px; display: inline-block; vertical-align: text-bottom;">
  <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
</svg>
</span>
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
    </header>

    {{-- Page content --}}
    @yield('content')

    {{-- Footer --}}
    <footer style="background: var(--gray-900); color: white; padding: 48px 0; margin-top: 64px;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; margin-bottom: 32px;">
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
</body>
</html>