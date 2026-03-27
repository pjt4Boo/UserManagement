<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Management') - User Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @auth
        {{-- Navigation Bar --}}
        <nav class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-8">
                        <a href="/" class="text-xl font-bold text-blue-600 hover:text-blue-800 transition">
                            User Management
                        </a>
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-blue-600 transition">
                                    Users
                                </a>
                            @endif
                        @endauth
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            Welcome, <span class="font-semibold">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->isAdmin())
                                <span class="ml-2 inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">
                                    Admin
                                </span>
                            @else
                                <span class="ml-2 inline-block px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded">
                                    User
                                </span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-red-600 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="text-green-800">
                        <span class="font-semibold">Success!</span> {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="text-red-800">
                        <span class="font-semibold">Error!</span> {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    @else
        @yield('content')
    @endauth

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-600 text-sm">
                © 2024 User Management System. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
