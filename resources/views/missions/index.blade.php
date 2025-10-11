@extends('layouts.app')

@section('title', 'Missions - Intranet')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec statistiques et actions -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📁 Mes missions</h1>
                <p class="text-gray-600 mt-1">
                    {{ $stats['total'] }} mission(s) {{ auth()->user()->isCollaborateur() ? 'assignée(s) ou créée(s)' : 'sous votre responsabilité' }}
                </p>
            </div>
            
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('missions.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Créer une mission
                </a>
            </div>
        </div>
        
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                <div class="text-sm text-blue-600">Total</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['en_cours'] }}</div>
                <div class="text-sm text-yellow-600">En cours</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                <div class="text-2xl font-bold text-green-600">{{ $stats['termine'] }}</div>
                <div class="text-sm text-green-600">Terminées</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                <div class="text-2xl font-bold text-red-600">{{ $stats['en_retard'] }}</div>
                <div class="text-sm text-red-600">En retard</div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('missions.index') }}" class="space-y-4 lg:space-y-0 lg:flex lg:items-end lg:space-x-6">
            <!-- Recherche -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    🔍 Rechercher dans les missions
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ $request->search }}"
                           placeholder="Rechercher par titre ou description..."
                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 sm:text-sm hover:border-gray-400">
                </div>
            </div>
            
            <!-- Catégorie -->
            <div class="min-w-0 flex-shrink-0">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    📁 Catégorie
                </label>
                <div class="relative">
                    <select id="category" 
                            name="category"
                            class="block w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 sm:text-sm hover:border-gray-400 bg-white appearance-none cursor-pointer">
                        <option value="">Toutes les catégories</option>
                        @if(isset($categories))
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ $request->category === $value ? 'selected' : '' }}>
                                    @if($value === 'location')🏠 {{ $label }}
                                    @elseif($value === 'syndic')🏢 {{ $label }}
                                    @else📋 {{ $label }}
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="min-w-0 flex-shrink-0">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    📊 Statut
                </label>
                <div class="relative">
                    <select id="status" 
                            name="status"
                            class="block w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 sm:text-sm hover:border-gray-400 bg-white appearance-none cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ $request->status === 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="en_cours" {{ $request->status === 'en_cours' ? 'selected' : '' }}>🔄 En cours</option>
                        <option value="termine" {{ $request->status === 'termine' ? 'selected' : '' }}>✅ Terminé</option>
                        <option value="en_retard" {{ $request->status === 'en_retard' ? 'selected' : '' }}>🚨 En retard</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Priorité -->
            <div class="min-w-0 flex-shrink-0">
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    🏷️ Priorité
                </label>
                <div class="relative">
                    <select id="priority" 
                            name="priority"
                            class="block w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 sm:text-sm hover:border-gray-400 bg-white appearance-none cursor-pointer">
                        <option value="">Toutes les priorités</option>
                        <option value="urgente" {{ $request->priority === 'urgente' ? 'selected' : '' }}>🔴 Urgente</option>
                        <option value="haute" {{ $request->priority === 'haute' ? 'selected' : '' }}>🟠 Haute</option>
                        <option value="normale" {{ $request->priority === 'normale' ? 'selected' : '' }}>🟡 Normale</option>
                        <option value="basse" {{ $request->priority === 'basse' ? 'selected' : '' }}>🟢 Basse</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Collaborateur (pour managers/admin) -->
            @if((auth()->user()->isManager() || auth()->user()->isAdministrateur()) && $collaborateurs->count() > 0)
                <div class="min-w-0 flex-shrink-0">
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                        👤 Collaborateur
                    </label>
                    <div class="relative">
                        <select id="assigned_to" 
                                name="assigned_to"
                                class="block w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 sm:text-sm hover:border-gray-400 bg-white appearance-none cursor-pointer">
                            <option value="">Tous les collaborateurs</option>
                            @foreach($collaborateurs as $collaborateur)
                                <option value="{{ $collaborateur->id }}" {{ $request->assigned_to == $collaborateur->id ? 'selected' : '' }}>
                                    {{ $collaborateur->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Boutons d'action -->
            <div class="flex space-x-3">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
                
                @if($request->search || $request->status || $request->priority || $request->assigned_to || $request->category)
                    <a href="{{ route('missions.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
        
        <!-- Indicateur de filtres actifs -->
        @if($request->search || $request->status || $request->priority || $request->assigned_to || $request->category)
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm text-blue-800">
                        Filtres actifs :
                        @if($request->search)
                            <span class="font-medium">Recherche : "{{ $request->search }}"</span>
                        @endif
                        @if($request->search && ($request->status || $request->priority || $request->assigned_to || $request->category)) • @endif
                        @if($request->category)
                            <span class="font-medium">Catégorie : {{ $categories[$request->category] ?? 'Inconnue' }}</span>
                        @endif
                        @if($request->category && ($request->status || $request->priority || $request->assigned_to)) • @endif
                        @if($request->status)
                            <span class="font-medium">Statut : {{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                        @endif
                        @if($request->status && ($request->priority || $request->assigned_to)) • @endif
                        @if($request->priority)
                            <span class="font-medium">Priorité : {{ ucfirst($request->priority) }}</span>
                        @endif
                        @if($request->priority && $request->assigned_to) • @endif
                        @if($request->assigned_to)
                            <span class="font-medium">Collaborateur : {{ $collaborateurs->find($request->assigned_to)?->full_name }}</span>
                        @endif
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Liste des missions -->
    <div class="space-y-4">
        @forelse($missions as $mission)
            <div class="bg-white shadow rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <!-- En-tête de la mission -->
                            <div class="flex items-center flex-wrap gap-3 mb-3">
                                <!-- Badge de catégorie -->
                                @if($mission->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        @if($mission->category === 'location')🏠
                                        @elseif($mission->category === 'syndic')🏢
                                        @else📋
                                        @endif
                                        {{ $mission->category_label }}
                                    </span>
                                @endif

                                <!-- Badge de sous-catégorie -->
                                @if($mission->subcategory)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ $mission->subcategory_label }}
                                    </span>
                                @endif

                                <!-- Badge de priorité -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mission->priority === 'urgente' ? 'bg-red-100 text-red-800' : ($mission->priority === 'haute' ? 'bg-orange-100 text-orange-800' : ($mission->priority === 'normale' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                    @if($mission->priority === 'urgente')🔴
                                    @elseif($mission->priority === 'haute')🟠
                                    @elseif($mission->priority === 'normale')🟡
                                    @else🟢
                                    @endif
                                    {{ $mission->priority_label }}
                                </span>

                                <!-- Badge de statut -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mission->status === 'termine' ? 'bg-green-100 text-green-800' : ($mission->status === 'en_retard' ? 'bg-red-100 text-red-800' : ($mission->status === 'en_cours' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                    @if($mission->status === 'termine')✅
                                    @elseif($mission->status === 'en_retard')🚨
                                    @elseif($mission->status === 'en_cours')🔄
                                    @else⏳
                                    @endif
                                    {{ $mission->status_label }}
                                </span>

                                <!-- Échéance -->
                                @if($mission->due_date)
                                    <span class="text-sm text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        @if($mission->isOverdue())
                                            <span class="text-red-600 font-medium">Échéance dépassée : {{ $mission->due_date->format('d/m/Y') }}</span>
                                        @else
                                            Échéance : {{ $mission->due_date->format('d/m/Y') }}
                                        @endif
                                    </span>
                                @endif

                                <!-- Revenus -->
                                @if($mission->revenue)
                                    <span class="text-sm text-green-600 font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        {{ number_format($mission->revenue, 0, ',', ' ') }}€
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Titre et description -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="{{ route('missions.show', $mission) }}" 
                                   class="hover:text-indigo-600 transition-colors">
                                    {{ $mission->title }}
                                </a>
                            </h3>
                            
                            <p class="text-gray-700 mb-4">
                                {{ Str::limit($mission->description, 200) }}
                            </p>
                            
                            <!-- Métadonnées -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Assigné à {{ $mission->assignedUser->full_name }}
                                    </span>
                                    
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Créé le {{ $mission->created_at->format('d/m/Y') }}
                                    </span>

                                    @if($mission->creator->id !== $mission->assignedUser->id)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            par {{ $mission->creator->full_name }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('missions.show', $mission) }}" 
                                       class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                        Voir détails →
                                    </a>
                                    
                                    @if(auth()->user()->isAdministrateur() || auth()->user()->id === $mission->created_by || (auth()->user()->isManager() && $mission->assignedUser->manager_id === auth()->user()->id))
                                        <div class="flex items-center space-x-1 ml-4">
                                            <a href="{{ route('missions.edit', $mission) }}" 
                                               class="text-gray-400 hover:text-gray-600 p-1 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            
                                            @if(auth()->user()->isAdministrateur() || auth()->user()->id === $mission->created_by)
                                                <form method="POST" action="{{ route('missions.destroy', $mission) }}" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette mission ?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-gray-400 hover:text-red-600 p-1 rounded">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <div class="text-6xl mb-4">📁</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune mission trouvée</h3>
                <p class="text-gray-500 mb-6">
                    @if($request->search || $request->status || $request->priority || $request->assigned_to || $request->category)
                        Aucune mission ne correspond aux critères de recherche.
                    @else
                        {{ auth()->user()->isCollaborateur() ? 'Vous n\'avez pas encore de missions assignées ou créées.' : 'Aucune mission sous votre responsabilité.' }}
                    @endif
                </p>
                
                @if($request->search || $request->status || $request->priority || $request->assigned_to || $request->category)
                    <a href="{{ route('missions.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        Voir toutes les missions
                    </a>
                @else
                    <a href="{{ route('missions.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        Créer la première mission
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($missions->hasPages())
        <div class="bg-white shadow rounded-lg p-6">
            {{ $missions->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection