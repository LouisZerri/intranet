@extends('layouts.app')

@section('title', $mission->title . ' - Missions')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation de retour -->
    <div class="flex items-center">
        <a href="{{ route('missions.index') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux missions
        </a>
    </div>

    <!-- Mission principale -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- En-tête de la mission -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <!-- Badges et métadonnées -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <!-- Priorité -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $mission->priority === 'urgente' ? 'bg-red-100 text-red-800' : ($mission->priority === 'haute' ? 'bg-orange-100 text-orange-800' : ($mission->priority === 'normale' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                            @if($mission->priority === 'urgente')🔴 {{ $mission->priority_label }}
                            @elseif($mission->priority === 'haute')🟠 {{ $mission->priority_label }}
                            @elseif($mission->priority === 'normale')🟡 {{ $mission->priority_label }}
                            @else🟢 {{ $mission->priority_label }}
                            @endif
                        </span>

                        <!-- Statut -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $mission->status === 'termine' ? 'bg-green-100 text-green-800' : ($mission->status === 'en_retard' ? 'bg-red-100 text-red-800' : ($mission->status === 'en_cours' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                            @if($mission->status === 'termine')✅ {{ $mission->status_label }}
                            @elseif($mission->status === 'en_retard')🚨 {{ $mission->status_label }}
                            @elseif($mission->status === 'en_cours')🔄 {{ $mission->status_label }}
                            @else⏳ {{ $mission->status_label }}
                            @endif
                        </span>
                        
                        <!-- Progression -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Progression :</span>
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $mission->status === 'termine' ? 'green' : ($mission->status === 'en_retard' ? 'red' : 'blue') }}-500 h-2 rounded-full transition-all duration-300" style="width: {{ $mission->getProgressPercentage() }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $mission->getProgressPercentage() }}%</span>
                        </div>
                    </div>
                    
                    <!-- Titre -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $mission->title }}</h1>
                    
                    <!-- Informations sur l'assignation et création -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-medium mr-3">
                                    {{ substr($mission->assignedUser->first_name, 0, 1) }}{{ substr($mission->assignedUser->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $mission->assignedUser->full_name }}</div>
                                    <div class="text-gray-500">{{ $mission->assignedUser->position }} - {{ $mission->assignedUser->department }}</div>
                                </div>
                            </div>
                            
                            @if($mission->creator->id !== $mission->assignedUser->id)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span>Créée par <strong>{{ $mission->creator->full_name }}</strong></span>
                                </div>
                            @endif

                            @if($mission->manager)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Manager : <strong>{{ $mission->manager->full_name }}</strong></span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Créée le {{ $mission->created_at->format('d/m/Y à H:i') }}</span>
                            </div>

                            @if($mission->start_date)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span>Début prévu : {{ $mission->start_date->format('d/m/Y') }}</span>
                                </div>
                            @endif

                            @if($mission->due_date)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-{{ $mission->isOverdue() ? 'red' : 'gray' }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="{{ $mission->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                                        Échéance : {{ $mission->due_date->format('d/m/Y') }}
                                        @if($mission->isOverdue())
                                            ({{ abs($mission->getDaysUntilDue()) }} jour(s) de retard)
                                        @elseif($mission->getDaysUntilDue() <= 7 && $mission->getDaysUntilDue() > 0)
                                            (dans {{ $mission->getDaysUntilDue() }} jour(s))
                                        @endif
                                    </span>
                                </div>
                            @endif

                            @if($mission->completed_at)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-green-600 font-medium">Terminée le {{ $mission->completed_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            @endif

                            @if($mission->revenue)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    <span class="text-green-600 font-medium">Revenus associés : {{ number_format($mission->revenue, 0, ',', ' ') }}€</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Actions (pour les utilisateurs autorisés) -->
                @if(auth()->user()->isAdministrateur() || auth()->user()->id === $mission->created_by || (auth()->user()->isManager() && $mission->assignedUser->manager_id === auth()->user()->id) || auth()->user()->id === $mission->assigned_to)
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('missions.edit', $mission) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Modifier
                        </a>
                        
                        @if(auth()->user()->isAdministrateur() || auth()->user()->id === $mission->created_by)
                            <form method="POST" action="{{ route('missions.destroy', $mission) }}" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette mission ?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Description de la mission -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Description</h2>
            <div class="prose prose-lg max-w-none">
                {!! nl2br(e($mission->description)) !!}
            </div>
        </div>

        <!-- Notes (si présentes) -->
        @if($mission->notes)
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📝 Notes</h2>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="prose max-w-none text-yellow-800">
                        {!! nl2br(e($mission->notes)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Historique et suivi -->
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Suivi et historique</h2>
            
            <div class="space-y-4">
                <!-- Événement de création -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm">
                            <span class="font-medium text-gray-900">{{ $mission->creator->full_name }}</span>
                            <span class="text-gray-500">a créé cette mission</span>
                        </div>
                        <div class="text-xs text-gray-500">{{ $mission->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>

                <!-- Événement d'assignation (si différent du créateur) -->
                @if($mission->creator->id !== $mission->assignedUser->id)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm">
                                <span class="text-gray-500">Mission assignée à</span>
                                <span class="font-medium text-gray-900">{{ $mission->assignedUser->full_name }}</span>
                            </div>
                            <div class="text-xs text-gray-500">{{ $mission->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                @endif

                <!-- Événement de completion (si terminée) -->
                @if($mission->completed_at)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm">
                                <span class="font-medium text-green-700">Mission terminée</span>
                                @if($mission->revenue)
                                    <span class="text-gray-500">- Revenus générés : {{ number_format($mission->revenue, 0, ',', ' ') }}€</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">{{ $mission->completed_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                @endif

                <!-- Dernière modification -->
                @if($mission->updated_at->gt($mission->created_at))
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm">
                                <span class="text-gray-500">Dernière modification</span>
                            </div>
                            <div class="text-xs text-gray-500">{{ $mission->updated_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection