@extends('layouts.app')

@section('title', 'Demandes internes - Intranet')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Demandes internes
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Gérez vos demandes d'achat, de documentation et de prestations selon le cahier des charges
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('requests.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nouvelle demande
                    </a>
                </div>
            </div>

            <!-- KPI -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-gray-50 overflow-hidden rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📋</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-500">Total</div>
                            <div class="text-xl font-semibold text-gray-900">{{ $stats['total'] }}</div>
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
                            <div class="text-xl font-semibold text-yellow-900">{{ $stats['en_attente'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 overflow-hidden rounded-lg p-4 border border-green-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-green-600">Validées</div>
                            <div class="text-xl font-semibold text-green-900">{{ $stats['valide'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 overflow-hidden rounded-lg p-4 border border-red-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">❌</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-red-600">Rejetées</div>
                            <div class="text-xl font-semibold text-red-900">{{ $stats['rejete'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="type" name="type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les types</option>
                    <option value="achat_produit_communication" {{ request('type') === 'achat_produit_communication' ? 'selected' : '' }}>
                        Achat produit communication
                    </option>
                    <option value="documentation_manager" {{ request('type') === 'documentation_manager' ? 'selected' : '' }}>
                        Documentation manager
                    </option>
                    <option value="prestation" {{ request('type') === 'prestation' ? 'selected' : '' }}>
                        Prestation
                    </option>
                </select>
            </div>

            <!-- Statut -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="status" name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="valide" {{ request('status') === 'valide' ? 'selected' : '' }}>Validé</option>
                    <option value="rejete" {{ request('status') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    <option value="en_cours" {{ request('status') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="termine" {{ request('status') === 'termine' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                    </svg>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('requests.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Liste des demandes -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            @if($requests->count() > 0)
                <div class="space-y-4">
                    @foreach($requests as $request)
                        <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                                <a href="{{ route('requests.show', $request) }}">{{ $request->title }}</a>
                                            </h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($request->description, 150) }}</p>
                                        </div>
                                        <div class="ml-4 flex flex-col items-end space-y-2">
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-800">
                                                {{ $request->status_label }}
                                            </span>
                                            @if($request->getUrgencyLevel() === 'high')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                    Urgent
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center mt-4 text-sm text-gray-500 space-x-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ $request->type_label }}
                                        </div>

                                        @if($request->prestation_type_label)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                                </svg>
                                                {{ $request->prestation_type_label }}
                                            </div>
                                        @endif

                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $request->requester->full_name }}
                                        </div>

                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $request->requested_at->format('d/m/Y à H:i') }}
                                        </div>

                                        @if($request->estimated_cost)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                                </svg>
                                                {{ number_format($request->estimated_cost, 2, ',', ' ') }}€
                                            </div>
                                        @endif

                                        @if($request->getDaysWaiting() > 0)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $request->getDaysWaiting() }} jour(s) d'attente
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions rapides -->
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('requests.show', $request) }}" 
                                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-full hover:bg-indigo-100">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Voir détails
                                            </a>

                                            @if($request->canBeApproved() && (Auth::user()->isManager() || Auth::user()->isAdministrateur()))
                                                <form method="POST" action="{{ route('requests.approve', $request) }}" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-600 bg-green-50 rounded-full hover:bg-green-100">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Approuver
                                                    </button>
                                                </form>
                                            @endif

                                            @if($request->isPending() && $request->requested_by === Auth::id())
                                                <a href="{{ route('requests.edit', $request) }}" 
                                                   class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-full hover:bg-blue-100">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Modifier
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Informations sur l'approbation -->
                                        @if($request->approver)
                                            <div class="text-xs text-gray-500">
                                                @if($request->isApproved())
                                                    Approuvé par {{ $request->approver->full_name }}
                                                @elseif($request->isRejected())
                                                    Rejeté par {{ $request->approver->full_name }}
                                                @endif
                                                le {{ $request->approved_at->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="mt-8">
                        {{ $requests->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">
                        @if(request()->hasAny(['search', 'type', 'status']))
                            Aucune demande trouvée
                        @else
                            Aucune demande
                        @endif
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'type', 'status']))
                            Essayez de modifier vos critères de recherche
                        @else
                            Commencez par créer votre première demande interne
                        @endif
                    </p>
                    <div class="mt-6">
                        @if(request()->hasAny(['search', 'type', 'status']))
                            <a href="{{ route('requests.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 mr-3">
                                Réinitialiser les filtres
                            </a>
                        @endif
                        <a href="{{ route('requests.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Créer une demande
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection