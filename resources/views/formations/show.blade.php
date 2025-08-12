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
                        ✅ Formation disponible - {{ $stats['available_places'] }} place(s) restante(s)
                    </span>
                @else
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="text-red-800 font-medium">
                        ❌ Formation complète ou non disponible
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
                    <h1 class="text-2xl font-bold text-gray-900">{{ $formation->title }}</h1>
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

<script>
function showRequestModal() {
    document.getElementById('requestModal').classList.remove('hidden');
}

function hideRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
}

// Fermer le modal si on clique en dehors
window.addEventListener('click', function(event) {
    const modal = document.getElementById('requestModal');
    if (event.target === modal) {
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