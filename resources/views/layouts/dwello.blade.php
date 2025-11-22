{{-- resources/views/layouts/dwello.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dwello')</title>

    {{-- Google fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">

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
                    <a href="{{ route('roommates.index') }}" style="color: var(--dwello-primary); text-decoration: none; font-weight: 500;">Find Flatmate</a>

                    {{-- Not built yet → send to under-development --}}
                    <a href="{{ route('under-development') }}" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Messages</a>

                    @auth
                        <a href="{{ route('roommate-profiles.create') }}" style="color: var(--gray-700); text-decoration: none; font-weight: 500;">Profile</a>
                    @endauth
                </nav>


                <div class="flex items-center" style="gap: 16px;">
                    @guest
                        <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('login') }}'">
                            Login
                        </button>
                    @else
                        <span style="font-size: 14px; color: var(--gray-700);">
                            {{ Auth::user()->name }}
                        </span>
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

    @stack('scripts')
</body>
</html>