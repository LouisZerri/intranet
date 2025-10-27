@extends('layouts.app')

@section('title', 'Profil de ' . $teamMember->full_name . ' - Intranet')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="h-16 w-16 rounded-full {{ $teamMember->is_active ? 'bg-indigo-500' : 'bg-gray-400' }} flex items-center justify-center text-white text-xl font-medium">
                    {{ substr($teamMember->first_name, 0, 1) }}{{ substr($teamMember->last_name, 0, 1) }}
                </div>
                <div class="ml-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $teamMember->full_name }}</h1>
                    <div class="flex items-center space-x-4 mt-1">
                        <span class="px-2 py-1 text-sm font-semibold rounded-full 
                            @if($teamMember->role === 'administrateur') bg-red-100 text-red-800
                            @elseif($teamMember->role === 'manager') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($teamMember->role) }}
                        </span>
                        @if($teamMember->is_active)
                            <span class="px-2 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Actif
                            </span>
                        @else
                            <span class="px-2 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                Inactif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('team.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    ← Retour
                </a>
                @if(auth()->user()->isAdministrateur())
                    <a href="{{ route('team.edit', $teamMember) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        ✏️ Modifier
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations personnelles -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails utilisateur -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">👤 Informations personnelles</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $teamMember->email }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Téléphone</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $teamMember->phone ?? 'Non renseigné' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Département</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $teamMember->department ?? 'Non renseigné' }}</div>
                        </div>

                        <!-- Localisation - NOUVEAU -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Localisation</label>
                            <div class="mt-1 text-sm text-gray-900 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $teamMember->localisation ?? 'Non renseigné' }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Poste</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $teamMember->position ?? 'Non renseigné' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Manager</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $teamMember->manager?->full_name ?? 'Aucun' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Objectif CA mensuel</label>
                            <div class="mt-1 text-sm text-gray-900">
                                @if($teamMember->revenue_target)
                                    {{ number_format($teamMember->revenue_target, 0, ',', ' ') }}€
                                @else
                                    Non défini
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Membre depuis</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $teamMember->created_at->format('d/m/Y') }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Dernière connexion</label>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ $teamMember->last_login_at ? $teamMember->last_login_at->format('d/m/Y à H:i') : 'Jamais connecté' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Équipe sous sa responsabilité -->
            @if($teamMember->subordinates->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">👥 Équipe sous sa responsabilité</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($teamMember->subordinates as $subordinate)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr($subordinate->first_name, 0, 1) }}{{ substr($subordinate->last_name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $subordinate->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $subordinate->position ?? 'Poste non défini' }}</div>
                                        @if($subordinate->localisation)
                                            <div class="text-xs text-gray-400 flex items-center mt-1">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $subordinate->localisation }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('team.show', $subordinate) }}" class="text-indigo-600 hover:text-indigo-500 text-sm">
                                    Voir profil →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Missions récentes -->
            @if($recentMissions->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📁 Missions récentes</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentMissions as $mission)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $mission->title }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($mission->description, 100) }}</p>
                                        <div class="flex items-center mt-2 text-xs text-gray-500 space-x-4">
                                            <span>Créé par {{ $mission->creator->full_name }}</span>
                                            <span>{{ $mission->updated_at->format('d/m/Y') }}</span>
                                            @if($mission->revenue)
                                                <span>CA: {{ number_format($mission->revenue, 0, ',', ' ') }}€</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="ml-4 px-2 py-1 text-xs rounded-full {{ $mission->status_color === 'green' ? 'bg-green-100 text-green-800' : ($mission->status_color === 'red' ? 'bg-red-100 text-red-800' : ($mission->status_color === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ $mission->status_label }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('missions.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Voir toutes les missions →
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- KPI utilisateur -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistiques</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Missions en cours</span>
                        <span class="text-sm font-medium text-gray-900">{{ $userStats['missions_en_cours'] }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Terminées ce mois</span>
                        <span class="text-sm font-medium text-gray-900">{{ $userStats['missions_terminees_mois'] }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">CA ce mois</span>
                        <span class="text-sm font-medium text-green-600">{{ number_format($userStats['ca_mois'], 0, ',', ' ') }}€</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Missions en retard</span>
                        <span class="text-sm font-medium {{ $userStats['missions_en_retard'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $userStats['missions_en_retard'] }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Heures formation</span>
                        <span class="text-sm font-medium text-purple-600">{{ $userStats['heures_formation_annee'] }}h</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Commandes en attente</span>
                        <span class="text-sm font-medium text-yellow-600">{{ $userStats['commandes_en_attente'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions admin -->
            @if(auth()->user()->isAdministrateur())
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">⚙️ Actions administrateur</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('team.edit', $teamMember) }}" class="w-full block text-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        ✏️ Modifier les informations
                    </a>
                    
                    <button onclick="document.getElementById('resetPasswordModal').style.display='block'" 
                            class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                        🔑 Réinitialiser mot de passe
                    </button>
                    
                    @if($teamMember->is_active)
                        <form method="POST" action="{{ route('team.deactivate', $teamMember) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                                    onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')">
                                ⏸️ Désactiver le compte
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('team.activate', $teamMember) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                ▶️ Réactiver le compte
                            </button>
                        </form>
                    @endif
                    
                    @if($teamMember->id !== auth()->user()->id)
                        <form method="POST" action="{{ route('team.destroy', $teamMember) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                                    onclick="return confirm('ATTENTION : Cette action est irréversible ! Supprimer définitivement cet utilisateur ?')">
                                🗑️ Supprimer définitivement
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal réinitialisation mot de passe -->
@if(auth()->user()->isAdministrateur())
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="display: none;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">🔑 Réinitialiser le mot de passe</h3>
            <form method="POST" action="{{ route('team.reset-password', $teamMember) }}">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('resetPasswordModal').style.display='none'"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection