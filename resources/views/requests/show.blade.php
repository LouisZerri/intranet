@extends('layouts.app')

@section('title', 'Demande: ' . $request->title . ' - Intranet')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation de retour -->
    <div class="flex items-center justify-between">
        <a href="{{ route('requests.index') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux demandes
        </a>
        
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <span>{{ $request->type_label }}</span>
            <span>•</span>
            <span>{{ $request->requested_at->format('d/m/Y à H:i') }}</span>
        </div>
    </div>

    <!-- Statut et actions en haut -->
    <div class="bg-{{ $request->status_color }}-50 border border-{{ $request->status_color }}-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-800">
                    {{ $request->status_label }}
                </span>
                @if($request->getUrgencyLevel() === 'high')
                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Urgent ({{ $request->getDaysWaiting() }} jours)
                    </span>
                @endif
            </div>
            
            <div class="flex space-x-2">
                @if($request->isPending() && $request->requested_by === Auth::id())
                    <a href="{{ route('requests.edit', $request) }}" 
                       class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                @endif
                
                @if((Auth::user()->isAdministrateur() || $request->requested_by === Auth::id()) && $request->status !== 'termine')
                    <form method="POST" action="{{ route('requests.destroy', $request) }}" class="inline" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 bg-red-100 rounded-md hover:bg-red-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $request->title }}</h1>
                    <div class="flex items-center mt-2 text-sm text-gray-500 space-x-4">
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
                            Demandé par {{ $request->requester->full_name }}
                        </div>

                        @if($request->estimated_cost)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                {{ number_format($request->estimated_cost, 2, ',', ' ') }}€
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Description -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">📄 Description</h3>
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-line">{{ $request->description }}</p>
                </div>
            </div>

            <!-- Commentaires -->
            @if($request->comments)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">💬 Commentaires</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $request->comments }}</p>
                    </div>
                </div>
            @endif

            <!-- Informations de statut -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">📊 Suivi de la demande</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut actuel</dt>
                            <dd class="mt-1 flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-800">
                                    {{ $request->status_label }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Temps d'attente</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $request->getDaysWaiting() }} jour(s)
                                @if($request->getDaysWaiting() > 7)
                                    <span class="text-red-600">(Attention : délai élevé)</span>
                                @endif
                            </dd>
                        </div>

                        @if($request->approver)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    @if($request->isApproved())
                                        Approuvé par
                                    @else
                                        Rejeté par
                                    @endif
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $request->approver->full_name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de traitement</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $request->approved_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif

                        @if($request->assignedUser)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Assigné à</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $request->assignedUser->full_name }}</dd>
                            </div>
                        @endif

                        @if($request->completed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Terminé le</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $request->completed_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Raison de rejet -->
            @if($request->isRejected() && $request->rejection_reason)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">❌ Raison du rejet</h3>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-700">{{ $request->rejection_reason }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions pour managers/admin -->
    @if((Auth::user()->isManager() || Auth::user()->isAdministrateur()) && $request->canBeApproved())
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">🔧 Actions de validation</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Approuver -->
                <form method="POST" action="{{ route('requests.approve', $request) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                            Assigner à (optionnel)
                        </label>
                        <select id="assigned_to" name="assigned_to" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <option value="">Sélectionner un responsable</option>
                            @foreach($approvers as $approver)
                                <option value="{{ $approver->id }}">
                                    {{ $approver->full_name }} ({{ $approver->department }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="approve_comments" class="block text-sm font-medium text-gray-700 mb-2">
                            Commentaires d'approbation (optionnel)
                        </label>
                        <textarea id="approve_comments" name="comments" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                                  placeholder="Instructions, délais, remarques..."></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approuver la demande
                    </button>
                </form>

                <!-- Rejeter -->
                <form method="POST" action="{{ route('requests.reject', $request) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Raison du rejet <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                                  placeholder="Expliquez pourquoi cette demande est rejetée..."></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Rejeter la demande
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Action pour marquer comme terminé -->
    @if($request->isApproved() && (Auth::user()->isManager() || Auth::user()->isAdministrateur() || $request->assigned_to === Auth::id()))
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">✅ Finalisation</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">
                        Marquez cette demande comme terminée une fois les actions réalisées.
                    </p>
                </div>
                <form method="POST" action="{{ route('requests.complete', $request) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            onclick="return confirm('Confirmer que cette demande est terminée ?')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Marquer comme terminé
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Historique/Timeline (futur développement) -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">📅 Historique</h3>
        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-900">
                        <strong>Demande créée</strong> par {{ $request->requester->full_name }}
                    </p>
                    <p class="text-sm text-gray-500">{{ $request->requested_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            @if($request->approved_at)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-{{ $request->isApproved() ? 'green' : 'red' }}-500 rounded-full flex items-center justify-center">
                            @if($request->isApproved())
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900">
                            <strong>Demande {{ $request->isApproved() ? 'approuvée' : 'rejetée' }}</strong> 
                            par {{ $request->approver->full_name }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $request->approved_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            @endif

            @if($request->completed_at)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900">
                            <strong>Demande terminée</strong>
                        </p>
                        <p class="text-sm text-gray-500">{{ $request->completed_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection