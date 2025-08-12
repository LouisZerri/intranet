@extends('layouts.app')

@section('title', 'Actualités - Intranet')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec statistiques et actions -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📰 Actualités</h1>
                <p class="text-gray-600 mt-1">
                    {{ $stats['total'] }} actualité(s) disponible(s) pour vous
                </p>
            </div>
            
            @if(auth()->user()->isManager() || auth()->user()->isAdministrateur())
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('news.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer une actualité
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-600">Total</div>
            </div>
            <div class="text-center p-3 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $stats['urgent'] }}</div>
                <div class="text-sm text-red-600">Urgent</div>
            </div>
            <div class="text-center p-3 bg-orange-50 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['important'] }}</div>
                <div class="text-sm text-orange-600">Important</div>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['normal'] }}</div>
                <div class="text-sm text-blue-600">Normal</div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('news.index') }}" class="space-y-4 lg:space-y-0 lg:flex lg:items-end lg:space-x-6">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    🔍 Rechercher dans les actualités
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
                           placeholder="Rechercher par titre ou contenu..."
                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 sm:text-sm hover:border-gray-400">
                </div>
            </div>
            
            <div class="min-w-0 flex-shrink-0">
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    🏷️ Filtrer par priorité
                </label>
                <div class="relative">
                    <select id="priority" 
                            name="priority"
                            class="block w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 sm:text-sm hover:border-gray-400 bg-white appearance-none cursor-pointer">
                        <option value="">Toutes les priorités</option>
                        <option value="urgent" {{ $request->priority === 'urgent' ? 'selected' : '' }}>🚨 Urgent</option>
                        <option value="important" {{ $request->priority === 'important' ? 'selected' : '' }}>⚠️ Important</option>
                        <option value="normal" {{ $request->priority === 'normal' ? 'selected' : '' }}>ℹ️ Normal</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
                
                @if($request->search || $request->priority)
                    <a href="{{ route('news.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
        
        @if($request->search || $request->priority)
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
                        @if($request->search && $request->priority) • @endif
                        @if($request->priority)
                            <span class="font-medium">Priorité : {{ ucfirst($request->priority) }}</span>
                        @endif
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Liste des actualités -->
    <div class="space-y-4">
        @forelse($news as $article)
            <article class="bg-white shadow rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <!-- En-tête de l'article -->
                            <div class="flex items-center space-x-3 mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $article->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($article->priority === 'important' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }}">
                                    @if($article->priority === 'urgent')
                                        🚨 {{ $article->priority_label }}
                                    @elseif($article->priority === 'important')
                                        ⚠️ {{ $article->priority_label }}
                                    @else
                                        ℹ️ {{ $article->priority_label }}
                                    @endif
                                </span>
                                
                                <span class="text-sm text-gray-500">
                                    📅 {{ $article->published_at->format('d/m/Y à H:i') }}
                                </span>
                                
                                @if($article->expires_at && $article->expires_at->isFuture())
                                    <span class="text-sm text-gray-500">
                                        ⏰ Expire le {{ $article->expires_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Titre et contenu -->
                            <h2 class="text-xl font-semibold text-gray-900 mb-3">
                                <a href="{{ route('news.show', $article) }}" 
                                   class="hover:text-indigo-600 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            
                            <div class="text-gray-700 mb-4 prose prose-sm max-w-none">
                                {{ $article->getExcerpt(200) }}
                            </div>
                            
                            <!-- Métadonnées -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $article->author->full_name }}
                                    </span>
                                    
                                    @if($article->target_roles)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            {{ implode(', ', $article->target_roles) }}
                                        </span>
                                    @endif
                                    
                                    @if($article->target_departments)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ implode(', ', $article->target_departments) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('news.show', $article) }}" 
                                       class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                        Lire la suite →
                                    </a>
                                    
                                    @if((auth()->user()->isAdministrateur()) || (auth()->user()->id === $article->author_id))
                                        <div class="flex items-center space-x-1 ml-4">
                                            <a href="{{ route('news.edit', $article) }}" 
                                               class="text-gray-400 hover:text-gray-600 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('news.destroy', $article) }}" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-gray-400 hover:text-red-600 p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <div class="text-6xl mb-4">📰</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune actualité trouvée</h3>
                <p class="text-gray-500 mb-6">
                    @if($request->search || $request->priority)
                        Aucune actualité ne correspond aux critères de recherche.
                    @else
                        Il n'y a pas encore d'actualités publiées pour vous.
                    @endif
                </p>
                
                @if($request->search || $request->priority)
                    <a href="{{ route('news.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        Voir toutes les actualités
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($news->hasPages())
        <div class="bg-white shadow rounded-lg p-6">
            {{ $news->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection