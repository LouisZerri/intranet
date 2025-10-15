@extends('layouts.app')

@section('title', 'Gestion des demandes de formation')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Gestion des demandes de formation</h1>
        <p class="mt-2 text-gray-600">Approuver ou rejeter les demandes de formation de votre équipe</p>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <span class="text-3xl mr-4">📋</span>
                <div>
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 p-6 rounded-lg shadow border border-yellow-200">
            <div class="flex items-center">
                <span class="text-3xl mr-4">⏳</span>
                <div>
                    <p class="text-sm text-yellow-700">En attente</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 p-6 rounded-lg shadow border border-green-200">
            <div class="flex items-center">
                <span class="text-3xl mr-4">✅</span>
                <div>
                    <p class="text-sm text-green-700">Approuvées</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-200">
            <div class="flex items-center">
                <span class="text-3xl mr-4">🎓</span>
                <div>
                    <p class="text-sm text-blue-700">Terminées</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form method="GET" action="{{ route('formations.manage') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="approuve" {{ request('status') === 'approuve' ? 'selected' : '' }}>Approuvé</option>
                    <option value="rejete" {{ request('status') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    <option value="termine" {{ request('status') === 'termine' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Priorité</label>
                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes les priorités</option>
                    <option value="haute" {{ request('priority') === 'haute' ? 'selected' : '' }}>Haute</option>
                    <option value="normale" {{ request('priority') === 'normale' ? 'selected' : '' }}>Normale</option>
                    <option value="basse" {{ request('priority') === 'basse' ? 'selected' : '' }}>Basse</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Formation</label>
                <select name="formation_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes les formations</option>
                    @foreach($formations as $formation)
                        <option value="{{ $formation->id }}" {{ request('formation_id') == $formation->id ? 'selected' : '' }}>
                            {{ $formation->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3 flex justify-end space-x-2">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Filtrer
                </button>
                @if(request()->hasAny(['status', 'priority', 'formation_id']))
                    <a href="{{ route('formations.manage') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Liste des demandes -->
    @if($requests->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="divide-y divide-gray-200">
                @foreach($requests as $formationRequest)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $formationRequest->formation->title }}
                                    </h3>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $formationRequest->status_color === 'yellow' ? 'bg-yellow-100 text-yellow-800' : ($formationRequest->status_color === 'green' ? 'bg-green-100 text-green-800' : ($formationRequest->status_color === 'red' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                        {{ $formationRequest->status_label }}
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $formationRequest->priority_color === 'red' ? 'bg-red-100 text-red-800' : ($formationRequest->priority_color === 'orange' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                        Priorité {{ $formationRequest->priority_label }}
                                    </span>
                                </div>

                                <p class="text-gray-600 mb-3">{{ $formationRequest->motivation }}</p>

                                <div class="flex items-center space-x-6 text-sm text-gray-500">
                                    <span>👤 {{ $formationRequest->user->full_name }}</span>
                                    <span>📅 Demandé le {{ $formationRequest->requested_at->format('d/m/Y') }}</span>
                                    <span>⏱️ {{ $formationRequest->formation->duration_hours }}h</span>
                                    @if($formationRequest->formation->cost)
                                        <span>💰 {{ number_format($formationRequest->formation->cost, 2, ',', ' ') }}€</span>
                                    @endif
                                </div>

                                @if($formationRequest->manager_comments)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <p class="text-sm text-gray-700"><strong>Commentaire manager:</strong> {{ $formationRequest->manager_comments }}</p>
                                    </div>
                                @endif

                                @if($formationRequest->status === 'termine' && $formationRequest->feedback)
                                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <p class="text-sm text-blue-900">
                                            <strong>Retour d'expérience:</strong> {{ $formationRequest->feedback }}
                                            @if($formationRequest->rating)
                                                <span class="ml-2">⭐ {{ $formationRequest->rating }}/5</span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>

                            @if($formationRequest->status === 'en_attente')
                                <div class="ml-4 flex flex-col space-y-2">
                                    <button 
                                        onclick="approveRequest({{ $formationRequest->id }})"
                                        class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                        ✓ Approuver
                                    </button>
                                    <button 
                                        onclick="rejectRequest({{ $formationRequest->id }})"
                                        class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                        ✗ Rejeter
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <span class="text-6xl">📚</span>
            <p class="mt-4 text-xl text-gray-600">Aucune demande de formation</p>
            <p class="mt-2 text-gray-500">Les demandes apparaîtront ici</p>
        </div>
    @endif
</div>

<!-- Modal d'approbation -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Approuver la demande</h3>
        <form id="approveForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire (optionnel)</label>
                <textarea 
                    name="manager_comments" 
                    rows="3" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                    placeholder="Ajoutez un commentaire..."></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button 
                    type="button" 
                    onclick="closeModal('approveModal')" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de rejet -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rejeter la demande</h3>
        <form id="rejectForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motif du rejet *</label>
                <textarea 
                    name="manager_comments" 
                    rows="3" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                    placeholder="Expliquez pourquoi cette demande est rejetée..." 
                    required></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button 
                    type="button" 
                    onclick="closeModal('rejectModal')" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Confirmer le rejet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function approveRequest(requestId) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    form.action = `/formation-requests/${requestId}/approve`;
    modal.classList.remove('hidden');
}

function rejectRequest(requestId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/formation-requests/${requestId}/reject`;
    modal.classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Fermer la modal en cliquant en dehors
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('approveModal');
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('rejectModal');
    }
});
</script>
@endsection