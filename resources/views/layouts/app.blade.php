<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Intranet Collaborateurs')</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>

<body class="bg-gray-50 font-sans antialiased">

    @auth
        <!-- Navigation principale -->
        <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileMenuOpen: false, commercialOpen: false, adminOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <img src="/images/logo3d.png" alt="Logo" class="h-12 sm:h-16 lg:h-20 w-auto object-contain">
                            <h1 class="text-xl font-bold text-gray-900">GEST'IMMO</h1>
                        </div>

                        <!-- Navigation links - Desktop -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            {{-- Accueil --}}
                            <a href="{{ route('dashboard') }}"
                                class="@if (request()->routeIs('dashboard')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                🏠 Accueil
                            </a>

                            {{-- Actualités (Manager/Admin) --}}
                            @if (auth()->user()->isManager() || auth()->user()->isAdministrateur())
                                <a href="{{ route('news.index') }}"
                                    class="@if (request()->routeIs('news.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    📰 Actualités
                                </a>
                            @endif

                            {{-- Missions --}}
                            <a href="{{ route('missions.index') }}"
                                class="@if (request()->routeIs('missions.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                📁 Missions
                            </a>

                            {{-- Commercial - Dropdown --}}
                            <div class="relative inline-flex items-center" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="@if (request()->routeIs('clients.*') ||
                                            request()->routeIs('quotes.*') ||
                                            request()->routeIs('invoices.*') ||
                                            request()->routeIs('urssaf.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium h-16">
                                    💼 Commercial
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                    class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                    style="top: 100%;">
                                    <div class="py-1">
                                        <a href="{{ route('clients.index') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            👥 Clients
                                        </a>
                                        <a href="{{ route('quotes.index') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            📄 Devis
                                        </a>
                                        <a href="{{ route('invoices.index') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            💰 Factures
                                        </a>
                                        <a href="{{ route('urssaf.index') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            📊 URSSAF
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Communication --}}
                            <a href="{{ route('communication.index') }}"
                                class="@if (request()->routeIs('communication.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                📋 Communication
                            </a>

                            {{-- Formations --}}
                            <a href="{{ route('formations.index') }}"
                                class="@if (request()->routeIs('formations.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                📚 Formations
                            </a>

                            {{-- Documentation --}}
                            <a href="{{ route('documentation.index') }}"
                                class="@if (request()->routeIs('documentation.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                📖 Documentation
                            </a>

                            {{-- Équipe (Manager/Admin) --}}
                            @if (auth()->user()->isManager() || auth()->user()->isAdministrateur())
                                <a href="{{ route('team.index') }}"
                                    class="@if (request()->routeIs('team.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    👥 Équipe
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Profil utilisateur - Desktop -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="bg-white flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                @if (auth()->user()->avatar)
                                    <img class="h-8 w-8 rounded-full object-cover border-2 border-gray-200"
                                        src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}"
                                        alt="Photo de profil">
                                @else
                                    <div
                                        class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                                    </div>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false"
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                        <div class="font-medium">{{ auth()->user()->full_name }}</div>
                                        <div class="text-gray-500">{{ auth()->user()->position }}</div>
                                    </div>
                                    <a href="{{ route('profile.edit') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        👤 Mon profil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            🚪 Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu mobile button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Ouvrir le menu principal</span>
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menu mobile -->
            <div x-show="mobileMenuOpen" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('dashboard') }}"
                        class="@if (request()->routeIs('dashboard')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        🏠 Accueil
                    </a>

                    @if (auth()->user()->isManager() || auth()->user()->isAdministrateur())
                        <a href="{{ route('news.index') }}"
                            class="@if (request()->routeIs('news.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            📰 Actualités
                        </a>
                    @endif

                    <a href="{{ route('missions.index') }}"
                        class="@if (request()->routeIs('missions.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        📁 Missions
                    </a>

                    {{-- Commercial (Mobile) --}}
                    <div x-data="{ commercialOpen: false }">
                        <button @click="commercialOpen = !commercialOpen"
                            class="w-full text-left border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            💼 Commercial
                        </button>
                        <div x-show="commercialOpen" class="pl-6 space-y-1">
                            <a href="{{ route('clients.index') }}"
                                class="block py-2 text-sm text-gray-600 hover:text-gray-800">👥 Clients</a>
                            <a href="{{ route('quotes.index') }}"
                                class="block py-2 text-sm text-gray-600 hover:text-gray-800">📄 Devis</a>
                            <a href="{{ route('invoices.index') }}"
                                class="block py-2 text-sm text-gray-600 hover:text-gray-800">💰 Factures</a>
                            <a href="{{ route('urssaf.index') }}"
                                class="block py-2 text-sm text-gray-600 hover:text-gray-800">📊 URSSAF</a>
                        </div>
                    </div>

                    <a href="{{ route('communication.index') }}"
                        class="@if (request()->routeIs('communication.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        📋 Communication
                    </a>

                    <a href="{{ route('formations.index') }}"
                        class="@if (request()->routeIs('formations.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        📚 Formations
                    </a>

                    <a href="{{ route('documentation.index') }}"
                        class="@if (request()->routeIs('documentation.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        📖 Documentation
                    </a>

                    @if (auth()->user()->isManager() || auth()->user()->isAdministrateur())
                        <a href="{{ route('team.index') }}"
                            class="@if (request()->routeIs('team.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            👥 Équipe
                        </a>
                    @endif
                </div>

                <!-- Profil mobile -->
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        @if (auth()->user()->avatar)
                            <img class="h-10 w-10 rounded-full object-cover"
                                src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" alt="Photo de profil">
                        @else
                            <div
                                class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-medium">
                                {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                            </div>
                        @endif
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ auth()->user()->full_name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ auth()->user()->position }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                            👤 Mon profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                🚪 Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @endauth

    <!-- Messages flash -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Contenu principal -->
    <main class="@auth max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 @endauth">
        @yield('content')
    </main>

    <!-- Alpine.js pour les interactions -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
