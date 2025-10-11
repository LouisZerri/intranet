@extends('layouts.app')

@section('title', 'Tableau de bord - Intranet')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec bienvenue -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-3xl font-bold">
            Bonjour {{ $user->first_name }} 👋
        </h1>
        <p class="mt-2 text-indigo-100">
            Bienvenue sur votre tableau de bord {{ $role_label }}
        </p>
        <div class="mt-4 flex items-center text-sm text-indigo-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ $todayDate }}
        </div>
    </div>

    <!-- KPI selon le rôle -->
    @if($user->role === 'collaborateur')
        @include('dashboard.partials.collaborateur-kpis')
    @elseif($user->role === 'manager')
        @include('dashboard.partials.manager-kpis')
    @elseif($user->role === 'administrateur')
        @include('dashboard.partials.administrateur-kpis')
    @endif

    <!-- Actualités -->
    @if($news && $news->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                📰 Actualités
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($news->take(3) as $newsItem)
                    <div class="border-l-4 border-indigo-500 pl-4 py-2 hover:bg-gray-50 transition-colors">
                        <h3 class="font-semibold text-gray-900">{{ $newsItem->title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($newsItem->content, 150) }}</p>
                        <div class="flex items-center mt-2 text-xs text-gray-500">
                            <span>Par {{ $newsItem->author->full_name }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $newsItem->published_at->format('d/m/Y') }}</span>
                            @if($newsItem->priority)
                                <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 rounded-full">
                                    {{ $newsItem->priority_label }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('news.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Voir toutes les actualités →
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Accès rapides -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">🚀 Accès rapides</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Missions -->
                <a href="{{ route('missions.index') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="flex-shrink-0">
                        <span class="text-2xl">📁</span>
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-gray-900">Missions</div>
                        <div class="text-sm text-gray-500">
                            @if($user->role === 'collaborateur')
                                {{ $kpis['missions_en_cours'] ?? 0 }} en cours
                            @else
                                Gérer les missions
                            @endif
                        </div>
                    </div>
                </a>

                <!-- Communication (REMPLACE Demandes internes) -->
                <a href="{{ route('communication.index') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="flex-shrink-0">
                        <span class="text-2xl">📦</span>
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-gray-900">Communication</div>
                        <div class="text-sm text-gray-500">
                            @if($user->role === 'collaborateur')
                                {{ $kpis['commandes_ce_mois'] ?? 0 }} ce mois
                            @elseif($user->role === 'manager')
                                {{ $kpis['commandes_equipe_mois'] ?? 0 }} équipe
                            @else
                                {{ $kpis['commandes_ce_mois'] ?? 0 }} commandes
                            @endif
                        </div>
                    </div>
                </a>

                <!-- Formations -->
                <a href="{{ route('formations.index') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="flex-shrink-0">
                        <span class="text-2xl">📚</span>
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-gray-900">Formations</div>
                        <div class="text-sm text-gray-500">
                            @if($user->role === 'collaborateur')
                                {{ $kpis['heures_formation_annee'] ?? 0 }}h cette année
                            @elseif($user->role === 'manager')
                                {{ $kpis['formations_equipe_en_attente'] ?? 0 }} à approuver
                            @else
                                {{ $kpis['formations_actives'] ?? 0 }} actives
                            @endif
                        </div>
                    </div>
                </a>

                <!-- Documentation -->
                <a href="{{ route('documentation.index') }}" class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                    <div class="flex-shrink-0">
                        <span class="text-2xl">📖</span>
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-gray-900">Documentation</div>
                        <div class="text-sm text-gray-500">Ressources & FAQ</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Contenu spécifique selon le rôle -->
    @if($user->role === 'collaborateur')
        @include('dashboard.partials.collaborateur-content')
    @elseif($user->role === 'manager')
        @include('dashboard.partials.manager-content')
    @elseif($user->role === 'administrateur')
        @include('dashboard.partials.administrateur-content')
    @endif
</div>
@endsection