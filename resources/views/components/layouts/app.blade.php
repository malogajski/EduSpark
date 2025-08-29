<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'EduSpark' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-blue-600">EduSpark</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Language Switcher -->
                    <div class="flex space-x-2">
                        <a href="?lang=sr" class="px-2 py-1 text-sm {{ app()->getLocale() === 'sr' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-800' }} rounded">SR</a>
                        <a href="?lang=en" class="px-2 py-1 text-sm {{ app()->getLocale() === 'en' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-800' }} rounded">EN</a>
                        <a href="?lang=hu" class="px-2 py-1 text-sm {{ app()->getLocale() === 'hu' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-800' }} rounded">HU</a>
                    </div>

                    @auth
                        <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->isTeacher())
                            <a href="/teacher" class="text-sm text-blue-600 hover:text-blue-800">Teacher Panel</a>
                        @else
                            <a href="/dashboard" class="text-sm text-blue-600 hover:text-blue-800">Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-800">Logout</button>
                        </form>
                    @else
                        <a href="/login" class="text-sm text-blue-600 hover:text-blue-800">Sign In</a>
                        <a href="/register" class="text-sm text-blue-600 hover:text-blue-800">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="py-8">
        {{ $slot }}
    </main>

    <footer class="bg-gray-800 text-white text-center py-4 mt-12">
        <p>&copy; {{ date('Y') }} EduSpark - Educational Quiz Platform</p>
    </footer>
</body>
</html>