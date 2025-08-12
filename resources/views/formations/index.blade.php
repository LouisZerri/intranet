@extends('layouts.app')

@section('title', 'Catalogue des formations - Intranet')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- En-tête avec statistiques utilisateur -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Catalogue des formations
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Accédez au catalogue de formation et demandez votre participation selon le cahier des charges
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('formations.my-requests') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Mes demandes
                    </a>

                    @if(Auth::user()->isManager() || Auth::user()->isAdministrateur())
                        <a href="{{ route('formations.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Créer formation
                        </a>
                    @endif
                </div>
            </div>

            <!-- KPI utilisateur -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-blue-50 overflow-hidden rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📚</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-blue-600">Heures suivies</div>
                            <div class="text-xl font-semibold text-blue-900">{{ Auth::user()->getFormationHoursThisYear() }}h</div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 overflow-hidden rounded-lg p-4 border border-green-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-green-600">Terminées</div>
                            <div class="text-xl font-semibold text-green-900">{{ $userStats['completed_formations'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 overflow-hidden rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">⏳</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-yellow-600">En attente</div>
                            <div class="text-xl font-semibold text-yellow-900">{{ $userStats['pending_requests'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📋</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-600">Total demandes</div>
                            <div class="text-xl font-semibold text-gray-900">{{ $userStats['total_requests'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Recherche -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <div class="relative">
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Titre, description..."
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Catégorie -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                <select id="category" name="category" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Niveau -->
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Niveau</label>
                <select id="level" name="level" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous niveaux</option>
                    @foreach($levels as $level)
                        <option value="{{ $level }}" {{ request('level') === $level ? 'selected' : '' }}>
                            @if($level === 'debutant')
                                🌱 Débutant
                            @elseif($level === 'intermediaire')
                                🌿 Intermédiaire
                            @else
                                🌳 Avancé
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Format -->
            <div>
                <label for="format" class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                <select id="format" name="format" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous formats</option>
                    @foreach($formats as $format)
                        <option value="{{ $format }}" {{ request('format') === $format ? 'selected' : '' }}>
                            @if($format === 'presentiel')
                                🏢 Présentiel
                            @elseif($format === 'distanciel')
                                💻 Distanciel
                            @else
                                🔄 Hybride
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                    </svg>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'category', 'level', 'format']))
                    <a href="{{ route('formations.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Liste des formations -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            @if($formations->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($formations as $formation)
                        <div class="bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-all duration-200 overflow-hidden group">
                            <div class="p-6">
                                <!-- Header avec niveau et format -->
                                <div class="flex items-center justify-between mb-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $formation->level === 'debutant' ? 'green' : ($formation->level === 'intermediaire' ? 'yellow' : 'red') }}-100 text-{{ $formation->level === 'debutant' ? 'green' : ($formation->level === 'intermediaire' ? 'yellow' : 'red') }}-800">
                                        @if($formation->level === 'debutant')
                                            🌱 {{ $formation->level_label }}
                                        @elseif($formation->level === 'intermediaire')
                                            🌿 {{ $formation->level_label }}
                                        @else
                                            🌳 {{ $formation->level_label }}
                                        @endif
                                    </span>
                                    
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        @if($formation->format === 'presentiel')
                                            🏢 {{ $formation->format_label }}
                                        @elseif($formation->format === 'distanciel')
                                            💻 {{ $formation->format_label }}
                                        @else
                                            🔄 {{ $formation->format_label }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Titre et description -->
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                        <a href="{{ route('formations.show', $formation) }}">{{ $formation->title }}</a>
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-3">{{ Str::limit($formation->description, 120) }}</p>
                                </div>

                                <!-- Informations détaillées -->
                                <div class="space-y-2 mb-4 text-sm text-gray-500">
                                    @if($formation->category)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ $formation->category }}
                                        </div>
                                    @endif

                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $formation->duration_label }}
                                    </div>

                                    @if($formation->provider)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $formation->provider }}
                                        </div>
                                    @endif

                                    @if($formation->cost)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                            {{ number_format($formation->cost, 0, ',', ' ') }}€
                                        </div>
                                    @endif

                                    @if($formation->max_participants)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            {{ $formation->getAvailablePlaces() }} place(s) disponible(s)
                                        </div>
                                    @endif

                                    @if($formation->start_date)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Début : {{ $formation->start_date->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('formations.show', $formation) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Voir détails
                                    </a>

                                    @if($formation->isAvailable())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✅ Disponible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ❌ Complet
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($formations->hasPages())
                    <div class="mt-8">
                        {{ $formations->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">
                        @if(request()->hasAny(['search', 'category', 'level', 'format']))
                            Aucune formation trouvée
                        @else
                            Aucune formation disponible
                        @endif
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'category', 'level', 'format']))
                            Essayez de modifier vos critères de recherche
                        @else
                            Le catalogue sera bientôt enrichi de nouvelles formations
                        @endif
                    </p>
                    <div class="mt-6">
                        @if(request()->hasAny(['search', 'category', 'level', 'format']))
                            <a href="{{ route('formations.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 mr-3">
                                Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection