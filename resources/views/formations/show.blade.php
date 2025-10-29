@extends('layouts.app')

@section('title', 'Formation: ' . $formation->title . ' - Intranet')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation de retour -->
    <div class="flex items-center justify-between">
        <a href="{{ route('formations.index') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au catalogue
        </a>
        
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <span>{{ $formation->category ?? 'Formation' }}</span>
            <span>•</span>
            <span>Créé le {{ $formation->created_at->format('d/m/Y') }}</span>
        </div>
    </div>

    <!-- Statut et disponibilité -->
    <div class="bg-{{ $formation->isAvailable() ? 'green' : 'red' }}-50 border border-{{ $formation->isAvailable() ? 'green' : 'red' }}-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                @if($formation->isAvailable())
                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800 font-medium">
                        Formation disponible - {{ $stats['available_places'] }} place(s) restante(s)
                    </span>
                @else
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="text-red-800 font-medium">
                        Formation complète ou non disponible
                    </span>
                @endif
            </div>
            
            <!-- Statut de la demande utilisateur -->
            @if($userRequest)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $userRequest->status_color }}-100 text-{{ $userRequest->status_color }}-800">
                    {{ $userRequest->status_label }}
                </span>
            @endif
        </div>
    </div>

    <!-- Informations principales -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $formation->title }}</h1>
                        
                        {{-- Bouton de suppression pour les administrateurs --}}
                        @if(Auth::user()->isAdministrateur())
                            <button onclick="confirmDelete()" 
                                    class="ml-4 inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            @if($formation->format === 'presentiel')
                                🏢 {{ $formation->format_label }}
                            @elseif($formation->format === 'distanciel')
                                💻 {{ $formation->format_label }}
                            @else
                                🔄 {{ $formation->format_label }}
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center mt-3 text-sm text-gray-500 space-x-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $formation->duration_label }}
                        </div>

                        @if($formation->provider)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $formation->provider }}
                            </div>
                        @endif

                        @if($formation->cost)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                {{ number_format($formation->cost, 0, ',', ' ') }}€
                            </div>
                        @endif

                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Créé par {{ $formation->creator->full_name }}
                        </div>
                    </div>
                </div>

                @if($formation->isAvailable() && !$userRequest)
                    <div class="ml-6">
                        <button onclick="showRequestModal()" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Demander ma participation
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Description -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">📄 Description</h3>
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-line">{{ $formation->description }}</p>
                </div>
            </div>

            <!-- Objectifs -->
            @if($formation->objectives && count($formation->objectives) > 0)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">🎯 Objectifs pédagogiques</h3>
                    <ul class="space-y-2">
                        @foreach($formation->objectives as $objective)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-gray-700">{{ $objective }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Prérequis -->
            @if($formation->prerequisites && count($formation->prerequisites) > 0)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">📋 Prérequis</h3>
                    <ul class="space-y-2">
                        @foreach($formation->prerequisites as $prerequisite)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-gray-700">{{ $prerequisite }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- SECTION : Fichiers et ressources -->
            @if($formation->files->count() > 0)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📁 Ressources et fichiers</h3>
                    
                    @php
                        $publicFiles = $formation->files->where('is_public', true);
                        $privateFiles = $formation->files->where('is_public', false);
                        $userHasAccess = $userRequest && in_array($userRequest->status, ['approuve', 'termine']);
                    @endphp

                    <!-- Fichiers publics (toujours visibles) -->
                    @if($publicFiles->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m3-6V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-8"/>
                                </svg>
                                Ressources disponibles pour tous
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($publicFiles as $file)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    {!! $file->getFileTypeIcon() !!}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $file->original_name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $file->getFormattedSize() }}
                                                        @if($file->description)
                                                            • {{ Str::limit($file->description, 50) }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @if($file->isViewableInBrowser())
                                                    <a href="{{ route('formations.files.view', $file) }}" 
                                                       target="_blank"
                                                       class="text-blue-600 hover:text-blue-800 p-1"
                                                       title="Voir dans le navigateur">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <a href="{{ route('formations.files.download', $file) }}" 
                                                   class="text-green-600 hover:text-green-800 p-1"
                                                   title="Télécharger">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Fichiers privés -->
                    @if($privateFiles->count() > 0)
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Ressources pour les participants inscrits
                            </h4>
                            
                            @if($userHasAccess)
                                <!-- L'utilisateur a accès -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($privateFiles as $file)
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0">
                                                        {!! $file->getFileTypeIcon() !!}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">
                                                            {{ $file->original_name }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $file->getFormattedSize() }}
                                                            @if($file->description)
                                                                • {{ Str::limit($file->description, 50) }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    @if($file->isViewableInBrowser())
                                                        <a href="{{ route('formations.files.view', $file) }}" 
                                                           target="_blank"
                                                           class="text-blue-600 hover:text-blue-800 p-1"
                                                           title="Voir dans le navigateur">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('formations.files.download', $file) }}" 
                                                       class="text-green-600 hover:text-green-800 p-1"
                                                       title="Télécharger">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- L'utilisateur n'a pas accès -->
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.232 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-yellow-900">Accès restreint</h4>
                                            <p class="text-sm text-yellow-700 mt-1">
                                                {{ $privateFiles->count() }} fichier(s) supplémentaire(s) disponible(s) après inscription et validation de votre demande.
                                            </p>
                                            @if(!$userRequest)
                                                <p class="text-sm text-yellow-700 mt-2">
                                                    Faites une demande de participation pour y accéder.
                                                </p>
                                            @elseif($userRequest->status === 'en_attente')
                                                <p class="text-sm text-yellow-700 mt-2">
                                                    Votre demande est en attente de validation.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <!-- Message si pas de fichiers -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <h4 class="mt-2 text-sm font-medium text-gray-900">Aucun fichier disponible</h4>
                        <p class="mt-1 text-sm text-gray-500">
                            Cette formation ne contient pas encore de ressources téléchargeables.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Informations pratiques -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">📊 Informations pratiques</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($formation->start_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de début</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $formation->start_date->format('d/m/Y') }}</dd>
                        </div>
                    @endif

                    @if($formation->end_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de fin</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $formation->end_date->format('d/m/Y') }}</dd>
                        </div>
                    @endif

                    @if($formation->location)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Lieu</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $formation->location }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Participants inscrits</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stats['approved_requests'] }} / {{ $formation->max_participants ?? '∞' }}</dd>
                    </div>

                    @if($stats['average_rating'])
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Note moyenne</dt>
                            <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($stats['average_rating']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-gray-600">({{ number_format($stats['average_rating'], 1) }}/5)</span>
                                </div>
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Participants terminés</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stats['completed_participants'] }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Ma demande (si existante) -->
    @if($userRequest)
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📝 Ma demande de participation</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $userRequest->status_color }}-100 text-{{ $userRequest->status_color }}-800">
                                    {{ $userRequest->status_label }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Priorité</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $userRequest->priority_label }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Demandé le</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $userRequest->requested_at->format('d/m/Y à H:i') }}</dd>
                        </div>

                        @if($userRequest->approved_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    {{ $userRequest->isApproved() ? 'Approuvé le' : 'Traité le' }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $userRequest->approved_at->format('d/m/Y à H:i') }}
                                    @if($userRequest->approver)
                                        par {{ $userRequest->approver->full_name }}
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div>
                    @if($userRequest->motivation)
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Ma motivation</dt>
                            <dd class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $userRequest->motivation }}</dd>
                        </div>
                    @endif

                    @if($userRequest->manager_comments)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Commentaires manager</dt>
                            <dd class="text-sm text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-200">{{ $userRequest->manager_comments }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions selon le statut -->
            @if($userRequest->isApproved() && !$userRequest->isCompleted())
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-md font-medium text-gray-900 mb-4">✅ Marquer comme terminé</h4>
                    <form method="POST" action="{{ route('formation-requests.complete', $userRequest) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="hours_completed" class="block text-sm font-medium text-gray-700 mb-1">
                                    Heures suivies <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="hours_completed" name="hours_completed" 
                                       value="{{ $formation->duration_hours }}" required min="1" max="1000"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">
                                    Note (1-5)
                                </label>
                                <select id="rating" name="rating"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Non noté</option>
                                    <option value="1">1 - Très insuffisant</option>
                                    <option value="2">2 - Insuffisant</option>
                                    <option value="3">3 - Satisfaisant</option>
                                    <option value="4">4 - Bien</option>
                                    <option value="5">5 - Excellent</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Terminer
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">
                                Commentaires et feedback
                            </label>
                            <textarea id="feedback" name="feedback" rows="3"
                                      placeholder="Votre retour sur cette formation..."
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Modal de demande de participation -->
@if($formation->isAvailable() && !$userRequest)
<div id="requestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Demander ma participation</h3>
                <button onclick="hideRequestModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('formations.request', $formation) }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="motivation" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivation <span class="text-red-500">*</span>
                    </label>
                    <textarea id="motivation" name="motivation" rows="4" required
                              placeholder="Expliquez pourquoi cette formation vous intéresse et comment elle s'inscrit dans votre parcours professionnel..."
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    <p class="mt-1 text-sm text-gray-500">Minimum 50 caractères</p>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priorité <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="normale">Normale - Formation souhaitée</option>
                        <option value="haute">Haute - Formation prioritaire pour mon poste</option>
                        <option value="basse">Basse - Formation optionnelle</option>
                    </select>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900">Processus de validation</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Votre demande sera envoyée à votre manager pour validation. Vous recevrez une notification du changement de statut.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="hideRequestModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Envoyer ma demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal de confirmation de suppression (admin seulement) --}}
@if(Auth::user()->isAdministrateur())
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">
                Confirmer la suppression
            </h3>
            
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-600 text-center mb-4">
                    Êtes-vous sûr de vouloir supprimer définitivement cette formation ?
                </p>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-sm text-red-800">
                            <p class="font-semibold mb-1">⚠️ Action irréversible</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Tous les fichiers associés seront supprimés</li>
                                <li>Les demandes de participation seront conservées pour historique</li>
                                <li>Cette action ne peut pas être annulée</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <p class="text-sm text-gray-700 text-center font-medium mb-4">
                    Formation : <span class="text-gray-900">"{{ $formation->title }}"</span>
                </p>
            </div>
            
            <div class="flex justify-center space-x-3 px-4 py-3">
                <button onclick="hideDeleteModal()" 
                        type="button"
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Annuler
                </button>
                
                <form method="POST" action="{{ route('formations.destroy', $formation) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Oui, supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
function showRequestModal() {
    document.getElementById('requestModal').classList.remove('hidden');
}

function hideRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
}

// Fonctions pour le modal de suppression
@if(Auth::user()->isAdministrateur())
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Fermer le modal de suppression si on clique en dehors
window.addEventListener('click', function(event) {
    const deleteModal = document.getElementById('deleteModal');
    if (event.target === deleteModal) {
        hideDeleteModal();
    }
});
@endif

// Fermer le modal de demande si on clique en dehors
window.addEventListener('click', function(event) {
    const requestModal = document.getElementById('requestModal');
    if (event.target === requestModal) {
        hideRequestModal();
    }
});

// Validation du textarea motivation
document.getElementById('motivation')?.addEventListener('input', function() {
    const minLength = 50;
    const currentLength = this.value.length;
    const helpText = this.parentNode.querySelector('.text-gray-500');
    
    if (currentLength < minLength) {
        helpText.textContent = `${minLength - currentLength} caractères restants (minimum ${minLength})`;
        helpText.className = 'mt-1 text-sm text-red-500';
    } else {
        helpText.textContent = `${currentLength} caractères - Parfait !`;
        helpText.className = 'mt-1 text-sm text-green-500';
    }
});
</script>
@endsection