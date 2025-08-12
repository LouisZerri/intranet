@extends('layouts.app')

@section('title', 'Tableau de bord - Intranet')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec informations utilisateur -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Bonjour {{ $user->first_name }} ! 👋
                </h1>
                <p class="text-gray-600">
                    {{ $role_label }} - {{ $user->department }} | {{ $todayDate }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Dernière connexion</div>
                <div class="font-medium">
                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : 'Première connexion' }}
                </div>
            </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Actualités prioritaires -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        📰 Actualités importantes
                    </h2>
                </div>
                <div class="p-6">
                    @if($news->count() > 0)
                        <div class="space-y-4">
                            @foreach($news as $article)
                                <div class="border-l-4 @if($article->priority === 'urgent') border-red-500 @elseif($article->priority === 'important') border-orange-500 @else border-blue-500 @endif pl-4 py-2">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900">{{ $article->title }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $article->getExcerpt(120) }}</p>
                                            <div class="flex items-center mt-2 text-xs text-gray-500">
                                                <span>Par {{ $article->author->full_name }}</span>
                                                <span class="mx-2">•</span>
                                                <span>{{ $article->published_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        <span class="ml-4 px-2 py-1 text-xs rounded-full @if($article->priority === 'urgent') bg-red-100 text-red-800 @elseif($article->priority === 'important') bg-orange-100 text-orange-800 @else bg-blue-100 text-blue-800 @endif">
                                            {{ $article->priority_label }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('news.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Voir toutes les actualités →
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucune actualité récente.</p>
                    @endif
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

        <!-- Sidebar (1/3) -->
        <div class="space-y-6">
            
            <!-- Accès rapides -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🚀 Accès rapides</h3>
                <div class="space-y-3">
                    <a href="{{ route('missions.index') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📁</span>
                        </div>
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">Mes missions</div>
                            <div class="text-sm text-gray-500">
                                @if($user->role === 'collaborateur')
                                    {{ $kpis['missions_en_cours'] ?? 0 }} en cours
                                @elseif($user->role === 'manager')
                                    {{ $kpis['missions_en_cours'] ?? 0 }} personnelles en cours
                                @else
                                    {{ $kpis['missions_ouvertes_mois'] ?? 0 }} ouvertes ce mois
                                @endif
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('requests.index') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📋</span>
                        </div>
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">Demandes internes</div>
                            <div class="text-sm text-gray-500">
                                @if($user->role === 'collaborateur')
                                    {{ $kpis['demandes_en_attente'] ?? 0 }} en attente
                                @elseif($user->role === 'manager')
                                    {{ $kpis['demandes_equipe_en_attente'] ?? 0 }} équipe à traiter
                                @else
                                    {{ isset($pending_requests) ? $pending_requests->count() : 0 }} à traiter
                                @endif
                            </div>
                        </div>
                    </a>

                    @if($user->isManager() || $user->isAdministrateur())
                        <a href="{{ route('team.index') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <div class="flex-shrink-0">
                                <span class="text-2xl">👥</span>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">
                                    @if($user->isManager())
                                        Mon équipe
                                    @else
                                        Tous les utilisateurs
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    @if($user->isManager())
                                        {{ $kpis['equipe_size'] ?? 0 }} collaborateurs
                                    @else
                                        {{ $kpis['utilisateurs_actifs'] ?? 0 }} utilisateurs actifs
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif

                    <!-- Accès rapide formations - MAINTENANT DISPONIBLE -->
                    <a href="{{ route('formations.index') }}" class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
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
                                    {{ $kpis['formations_actives'] ?? 0 }} formations actives
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Notifications/Alertes -->
            @php
                $missionsEnRetard = 0;
                $missionsEquipeEnRetard = 0;
                
                if($user->role === 'collaborateur') {
                    $missionsEnRetard = $kpis['missions_en_retard'] ?? 0;
                } elseif($user->role === 'manager') {
                    $missionsEnRetard = $kpis['missions_en_retard'] ?? 0;
                    $missionsEquipeEnRetard = $kpis['missions_equipe_en_retard'] ?? 0;
                } else {
                    // Pour admin, on peut afficher les retards globaux si nécessaire
                    $missionsEnRetard = 0; // À définir selon les besoins
                }
                
                $hasAlerts = $missionsEnRetard > 0 || $missionsEquipeEnRetard > 0;
            @endphp

            @if($hasAlerts)
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-red-800 mb-4">⚠️ Alertes</h3>
                    <div class="space-y-3">
                        @if($missionsEnRetard > 0)
                            <div class="flex items-center text-red-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-medium">{{ $missionsEnRetard }}</span>
                                <span class="ml-2">
                                    @if($user->role === 'manager')
                                        mission(s) personnelle(s) en retard
                                    @else
                                        mission(s) en retard
                                    @endif
                                </span>
                            </div>
                        @endif
                        @if($missionsEquipeEnRetard > 0)
                            <div class="flex items-center text-red-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span class="font-medium">{{ $missionsEquipeEnRetard }}</span>
                                <span class="ml-2">mission(s) d'équipe en retard</span>
                            </div>
                        @endif
                        <div class="mt-3 pt-3 border-t border-red-200">
                            <a href="{{ route('missions.index', ['status' => 'en_retard']) }}" class="text-red-600 hover:text-red-500 text-sm font-medium">
                                Voir les missions en retard →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Planning/Échéances -->
            @if($user->role === 'collaborateur' && isset($upcoming_deadlines) && $upcoming_deadlines->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Échéances à venir</h3>
                    <div class="space-y-3">
                        @foreach($upcoming_deadlines as $mission)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div class="flex-1">
                                    <div class="font-medium text-sm text-gray-900">{{ Str::limit($mission->title, 30) }}</div>
                                    <div class="text-xs text-gray-500">{{ $mission->due_date->format('d/m/Y') }}</div>
                                </div>
                                <div class="text-xs px-2 py-1 rounded-full {{ $mission->due_color === 'red' ? 'bg-red-100 text-red-800' : ($mission->due_color === 'orange' ? 'bg-orange-100 text-orange-800' : ($mission->due_color === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                    {{ $mission->due_status }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('missions.index', ['due_soon' => '1']) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Voir toutes les échéances →
                        </a>
                    </div>
                </div>
            @endif

            <!-- Statistiques rapides pour managers/admin -->
            @if($user->isManager() || $user->isAdministrateur())
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">📊 Vue d'ensemble</h3>
                    <div class="space-y-3">
                        @if($user->isManager())
                            <div class="flex justify-between items-center">
                                <span class="text-indigo-100">CA équipe ce mois</span>
                                <span class="font-bold">{{ number_format($kpis['chiffre_affaires_equipe'] ?? 0, 0, ',', ' ') }}€</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-indigo-100">CA personnel</span>
                                <span class="font-bold">{{ number_format($kpis['chiffre_affaires_perso'] ?? 0, 0, ',', ' ') }}€</span>
                            </div>
                        @else
                            <div class="flex justify-between items-center">
                                <span class="text-indigo-100">CA total ce mois</span>
                                <span class="font-bold">{{ number_format($kpis['ca_total_mois'] ?? 0, 0, ',', ' ') }}€</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-indigo-100">Utilisateurs actifs</span>
                                <span class="font-bold">{{ $kpis['utilisateurs_actifs'] ?? 0 }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Raccourcis actions rapides -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3">⚡ Actions rapides</h4>
                <div class="grid grid-cols-2 gap-2">
                    @if($user->isManager() || $user->isAdministrateur())
                        <a href="{{ route('missions.create') }}" class="text-center p-2 bg-white rounded border hover:bg-gray-50 transition-colors">
                            <div class="text-lg">➕</div>
                            <div class="text-xs text-gray-600">Créer mission</div>
                        </a>
                    @endif
                    
                    <a href="{{ route('requests.create') }}" class="text-center p-2 bg-white rounded border hover:bg-gray-50 transition-colors">
                        <div class="text-lg">📝</div>
                        <div class="text-xs text-gray-600">Demande interne</div>
                    </a>

                    @if($user->isManager() || $user->isAdministrateur())
                        <a href="{{ route('news.create') }}" class="text-center p-2 bg-white rounded border hover:bg-gray-50 transition-colors">
                            <div class="text-lg">📰</div>
                            <div class="text-xs text-gray-600">Actualité</div>
                        </a>
                    @endif

                    <a href="{{ route('profile.edit') }}" class="text-center p-2 bg-white rounded border hover:bg-gray-50 transition-colors">
                        <div class="text-lg">⚙️</div>
                        <div class="text-xs text-gray-600">Profil</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection