@extends('layouts.app')

@section('title', 'Nouvelle demande interne - Intranet')

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
            <span>{{ Auth::user()->full_name }}</span>
            <span>•</span>
            <span>{{ now()->format('d/m/Y à H:i') }}</span>
        </div>
    </div>

    <!-- Formulaire de création -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle demande interne
            </h1>
            <p class="text-gray-600 mt-1">
                Créez une demande d'achat produit communication, de documentation manager ou de prestation
            </p>
        </div>

        <form method="POST" action="{{ route('requests.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Type de demande -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    🏷️ Type de demande <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select id="type" 
                            name="type" 
                            required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('type') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <option value="">Sélectionner un type de demande</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>
                                @if($value === 'achat_produit_communication')
                                    📢 {{ $label }}
                                @elseif($value === 'documentation_manager')
                                    📋 {{ $label }}
                                @else
                                    🔧 {{ $label }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                @error('type')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Type de prestation (conditionnel) -->
            <div id="prestation-type-container" style="display: none;">
                <label for="prestation_type" class="block text-sm font-medium text-gray-700 mb-2">
                    🔧 Type de prestation <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select id="prestation_type" 
                            name="prestation_type"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('prestation_type') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <option value="">Sélectionner un type de prestation</option>
                        @foreach($prestationTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('prestation_type') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                @error('prestation_type')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Titre -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Titre de la demande <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           required
                           maxlength="255"
                           placeholder="Titre descriptif de votre demande"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                </div>
                @error('title')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    📄 Description détaillée <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <textarea id="description" 
                              name="description" 
                              rows="6" 
                              required
                              placeholder="Décrivez précisément votre demande, son contexte et son objectif..."
                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 resize-y @error('description') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('description') }}</textarea>
                    <div class="absolute bottom-3 right-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </div>
                </div>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Commentaires -->
                <div>
                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">
                        💬 Commentaires additionnels
                    </label>
                    <div class="relative">
                        <textarea id="comments" 
                                  name="comments" 
                                  rows="4" 
                                  placeholder="Informations complémentaires, contraintes, délais..."
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 resize-y @error('comments') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('comments') }}</textarea>
                    </div>
                    @error('comments')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Coût estimé -->
                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        💰 Coût estimé (optionnel)
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="estimated_cost" 
                               name="estimated_cost" 
                               value="{{ old('estimated_cost') }}"
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="block w-full px-4 py-3 pl-8 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('estimated_cost') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 text-sm">€</span>
                        </div>
                    </div>
                    @error('estimated_cost')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Si vous avez une estimation du coût de cette demande
                    </p>
                </div>
            </div>

            <!-- Informations sur le workflow -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Processus de validation selon le cahier des charges</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Une fois créée, votre demande sera <strong>en attente de validation</strong> par votre manager ou un administrateur. 
                            Le workflow de validation est paramétrable par profil comme spécifié dans le cahier des charges.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Demande créée le {{ now()->format('d/m/Y à H:i') }}</span>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('requests.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </a>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Créer la demande
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Afficher/masquer le type de prestation selon le type sélectionné
document.getElementById('type').addEventListener('change', function() {
    const prestationContainer = document.getElementById('prestation-type-container');
    const prestationSelect = document.getElementById('prestation_type');
    
    if (this.value === 'prestation') {
        prestationContainer.style.display = 'block';
        prestationSelect.required = true;
    } else {
        prestationContainer.style.display = 'none';
        prestationSelect.required = false;
        prestationSelect.value = '';
    }
});

// Déclencher l'événement au chargement si déjà sélectionné
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    if (typeSelect.value === 'prestation') {
        typeSelect.dispatchEvent(new Event('change'));
    }
});

// Auto-resize des textareas
document.getElementById('description').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

if (document.getElementById('comments')) {
    document.getElementById('comments').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
}

// Indication visuelle du type sélectionné
document.getElementById('type').addEventListener('change', function() {
    const typeColors = {
        'achat_produit_communication': 'border-blue-300 bg-blue-50',
        'documentation_manager': 'border-purple-300 bg-purple-50',
        'prestation': 'border-green-300 bg-green-50'
    };
    
    // Reset classes
    this.className = this.className.replace(/border-\w+-300|bg-\w+-50/g, '');
    
    if (typeColors[this.value]) {
        this.className += ' ' + typeColors[this.value];
    }
});

// Formatage automatique du coût estimé
document.getElementById('estimated_cost').addEventListener('blur', function() {
    if (this.value && !isNaN(this.value)) {
        this.value = parseFloat(this.value).toFixed(2);
    }
});

// Validation en temps réel
document.querySelectorAll('input[required], textarea[required], select[required]').forEach(function(field) {
    field.addEventListener('blur', function() {
        if (this.value.trim()) {
            this.classList.remove('border-red-300');
            this.classList.add('border-green-300');
        } else {
            this.classList.remove('border-green-300');
            this.classList.add('border-red-300');
        }
    });
});
</script>
@endsection