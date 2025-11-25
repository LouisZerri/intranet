@extends('layouts.app')

@section('title', $teamMember->full_name . ' - Intranet')

@section('content')
<div class="space-y-6">
    <!-- En-tête du profil -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Bannière colorée -->
        <div class="h-32 bg-gradient-to-r from-indigo-600 to-purple-600"></div>
        
        <!-- Informations principales -->
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-end -mt-16 mb-4">
                <!-- Avatar -->
                <div class="relative">
                    <div class="h-32 w-32 rounded-full border-4 border-white {{ $teamMember->is_active ? 'bg-indigo-500' : 'bg-gray-400' }} flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                        {{ substr($teamMember->first_name, 0, 1) }}{{ substr($teamMember->last_name, 0, 1) }}
                    </div>
                    @if($teamMember->is_active)
                        <div class="absolute bottom-2 right-2 h-6 w-6 bg-green-500 rounded-full border-4 border-white"></div>
                    @else
                        <div class="absolute bottom-2 right-2 h-6 w-6 bg-red-500 rounded-full border-4 border-white"></div>
                    @endif
                </div>

                <!-- Nom et actions -->
                <div class="mt-4 sm:mt-0 sm:ml-6 flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $teamMember->full_name }}</h1>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <!-- Rôle -->
                                @if($teamMember->role === 'administrateur')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Administrateur
                                    </span>
                                @elseif($teamMember->role === 'manager')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Manager
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Collaborateur
                                    </span>
                                @endif

                                <!-- Statut -->
                                @if($teamMember->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Inactif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="mt-4 sm:mt-0 flex space-x-3">
                            <a href="{{ route('team.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Retour
                            </a>
                            @if(auth()->user()->role === 'administrateur' || (auth()->user()->role === 'manager' && $teamMember->manager_id === auth()->id()))
                                <a href="{{ route('team.edit', $teamMember) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Modifier
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations de contact -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Informations de contact
                </h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Email
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $teamMember->email }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ $teamMember->email }}
                            </a>
                        </dd>
                    </div>

                    @if($teamMember->phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Téléphone
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="tel:{{ $teamMember->phone }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ $teamMember->phone }}
                            </a>
                        </dd>
                    </div>
                    @endif

                    @if($teamMember->professional_email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0h4.586a1 1 0 01.707 1.707l-4.586 4.586a1 1 0 01-.707.293h-4.586a1 1 0 01-.707-.293l-4.586-4.586A1 1 0 013.414 6H8"/>
                            </svg>
                            Email professionnel
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $teamMember->professional_email }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ $teamMember->professional_email }}
                            </a>
                        </dd>
                    </div>
                    @endif

                    @if($teamMember->professional_phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Téléphone professionnel
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="tel:{{ $teamMember->professional_phone }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ $teamMember->professional_phone }}
                            </a>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Informations professionnelles -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0h4.586a1 1 0 01.707 1.707l-4.586 4.586a1 1 0 01-.707.293h-4.586a1 1 0 01-.707-.293l-4.586-4.586A1 1 0 013.414 6H8"/>
                    </svg>
                    Informations professionnelles
                </h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($teamMember->position)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Poste</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $teamMember->position }}</dd>
                    </div>
                    @endif

                    @if($teamMember->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Département</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $teamMember->department }}</dd>
                    </div>
                    @endif

                    @if($teamMember->localisation)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Localisation</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $teamMember->localisation }}</dd>
                    </div>
                    @endif

                    @if($teamMember->manager)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Manager</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('team.show', $teamMember->manager) }}" class="text-indigo-600 hover:text-indigo-800 flex items-center">
                                {{ $teamMember->manager->full_name }}
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </dd>
                    </div>
                    @endif

                    @if($teamMember->revenue_target)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Objectif CA mensuel</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($teamMember->revenue_target, 2, ',', ' ') }} €</dd>
                    </div>
                    @endif

                    @if($teamMember->rsac_number)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Numéro RSAC</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $teamMember->rsac_number }}</dd>
                    </div>
                    @endif
                </dl>

                @if($teamMember->professional_address || $teamMember->professional_city || $teamMember->professional_postal_code)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <dt class="text-sm font-medium text-gray-500 mb-2">Adresse professionnelle</dt>
                    <dd class="text-sm text-gray-900">
                        @if($teamMember->professional_address)
                            {{ $teamMember->professional_address }}<br>
                        @endif
                        @if($teamMember->professional_postal_code || $teamMember->professional_city)
                            {{ $teamMember->professional_postal_code }} {{ $teamMember->professional_city }}
                        @endif
                    </dd>
                </div>
                @endif
            </div>

            <!-- Départements gérés (pour Managers et Administrateurs) -->
            @if(in_array($teamMember->role, ['manager', 'administrateur']))
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Départements gérés
                </h2>
                
                @if($teamMember->managesAllDepartments())
                    <div class="flex items-start p-4 bg-green-50 border border-green-200 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-green-800">Tous les départements</h3>
                            <p class="mt-1 text-sm text-green-700">
                                Cet utilisateur a accès à la gestion de tous les départements français (101 départements).
                            </p>
                        </div>
                    </div>
                @elseif($teamMember->managed_departments && count($teamMember->managed_departments) > 0)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                Cet utilisateur gère <span class="font-semibold text-gray-900">{{ count($teamMember->managed_departments) }}</span> département(s) en plus de son équipe directe.
                            </p>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach($teamMember->managed_departments as $dept)
                                <div class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $dept }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="flex items-start p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <svg class="w-6 h-6 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-gray-800">Aucun département géré</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Cet utilisateur gère uniquement son équipe directe (collaborateurs dont il est le manager).
                            </p>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- Équipe sous responsabilité -->
            @if($teamMember->subordinates->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Équipe directe ({{ $teamMember->subordinates->count() }})
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($teamMember->subordinates as $subordinate)
                        <a href="{{ route('team.show', $subordinate) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="h-10 w-10 rounded-full {{ $subordinate->is_active ? 'bg-indigo-500' : 'bg-gray-400' }} flex items-center justify-center text-white font-medium flex-shrink-0">
                                {{ substr($subordinate->first_name, 0, 1) }}{{ substr($subordinate->last_name, 0, 1) }}
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $subordinate->full_name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ $subordinate->position ?? $subordinate->department }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Mentions légales et footer -->
            @if($teamMember->legal_mentions || $teamMember->footer_text)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informations pour documents
                </h2>
                <dl class="space-y-4">
                    @if($teamMember->legal_mentions)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">Mentions légales</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg whitespace-pre-line">{{ $teamMember->legal_mentions }}</dd>
                    </div>
                    @endif

                    @if($teamMember->footer_text)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">Texte de pied de page</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg whitespace-pre-line">{{ $teamMember->footer_text }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Statistiques rapides -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations système</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <dt class="text-sm text-gray-500">Compte créé</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $teamMember->created_at->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <dt class="text-sm text-gray-500">Dernière modification</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $teamMember->updated_at->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <dt class="text-sm text-gray-500">Dernière connexion</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ $teamMember->last_login_at ? $teamMember->last_login_at->format('d/m/Y à H:i') : 'Jamais' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Actions rapides -->
            @if(auth()->user()->role === 'administrateur' || auth()->id() === $teamMember->id)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h2>
                <div class="space-y-2">
                    @if(auth()->user()->role === 'administrateur' || (auth()->user()->role === 'manager' && $teamMember->manager_id === auth()->id()))
                        <a href="{{ route('team.edit', $teamMember) }}" class="block w-full px-4 py-2 text-sm text-center font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            Modifier le profil
                        </a>
                    @endif

                    @if(auth()->user()->role === 'administrateur' && $teamMember->id !== auth()->id())
                        @if($teamMember->is_active)
                            <form method="POST" action="{{ route('team.update', $teamMember) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="0">
                                <button type="submit" class="block w-full px-4 py-2 text-sm text-center font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors">
                                    Désactiver le compte
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('team.update', $teamMember) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit" class="block w-full px-4 py-2 text-sm text-center font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                    Activer le compte
                                </button>
                            </form>
                        @endif

                        <form method="POST" 
                              action="{{ route('team.destroy', $teamMember) }}" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce membre ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full px-4 py-2 text-sm text-center font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                Supprimer le compte
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

            <!-- Avertissement si compte inactif -->
            @if(!$teamMember->is_active)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Compte inactif</h3>
                        <p class="mt-1 text-sm text-red-700">
                            Ce compte est actuellement désactivé. L'utilisateur ne peut pas se connecter.
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection