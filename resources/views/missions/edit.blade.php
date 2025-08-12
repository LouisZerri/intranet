@extends('layouts.app')

@section('title', 'Modifier la mission - Intranet')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation de retour -->
    <div class="flex items-center justify-between">
        <a href="{{ route('missions.show', $mission) }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à la mission
        </a>
        
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <span>Créé par {{ $mission->creator->full_name }}</span>
            <span>•</span>
            <span>{{ $mission->created_at->format('d/m/Y à H:i') }}</span>
        </div>
    </div>

    <!-- Informations d'échéance en haut -->
    @if($mission->due_date)
        <div class="bg-{{ $mission->due_color }}-50 border border-{{ $mission->due_color }}-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-{{ $mission->due_color }}-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-{{ $mission->due_color }}-800 font-medium">
                    <strong>Échéance :</strong> {{ $mission->due_date->format('d/m/Y') }} 
                    ({{ $mission->due_status }})
                </span>
            </div>
        </div>
    @endif

    <!-- Formulaire d'édition -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier la mission
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Modifiez les informations de cette mission
                    </p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $mission->priority_color }}-100 text-{{ $mission->priority_color }}-800">
                        {{ $mission->priority_label }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $mission->status_color }}-100 text-{{ $mission->status_color }}-800">
                        {{ $mission->status_label }}
                    </span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('missions.update', $mission) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Titre -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Titre de la mission <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $mission->title) }}"
                           required
                           maxlength="255"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                </div>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    📄 Description de la mission <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <textarea id="description" 
                              name="description" 
                              rows="6" 
                              required
                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 resize-y @error('description') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('description', $mission->description) }}</textarea>
                </div>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Priorité -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        🏷️ Priorité <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="priority" 
                                name="priority" 
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('priority') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            @foreach($priorities as $value => $label)
                                <option value="{{ $value }}" {{ old('priority', $mission->priority) === $value ? 'selected' : '' }}>
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
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        📊 Statut <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="status" 
                                name="status" 
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('status') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $mission->status) === $value ? 'selected' : '' }}>
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
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Assignation -->
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                    👤 Assigné à <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select id="assigned_to" 
                            name="assigned_to" 
                            required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('assigned_to') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <option value="">Sélectionner un collaborateur</option>
                        @foreach($collaborateurs as $collaborateur)
                            <option value="{{ $collaborateur->id }}" 
                                    {{ old('assigned_to', $mission->assigned_to) == $collaborateur->id ? 'selected' : '' }}>
                                {{ $collaborateur->full_name }} 
                                @if($collaborateur->department)
                                    ({{ $collaborateur->department }})
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
                @error('assigned_to')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Date de début -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        🚀 Date de début
                    </label>
                    <div class="relative">
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date', $mission->start_date?->format('Y-m-d')) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('start_date') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date d'échéance -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                        ⏰ Échéance prévue
                    </label>
                    <div class="relative">
                        <input type="date" 
                               id="due_date" 
                               name="due_date" 
                               value="{{ old('due_date', $mission->due_date?->format('Y-m-d')) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('due_date') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Chiffre d'affaires -->
                <div>
                    <label for="revenue" class="block text-sm font-medium text-gray-700 mb-2">
                        💰 Chiffre d'affaires
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="revenue" 
                               name="revenue" 
                               value="{{ old('revenue', $mission->revenue) }}"
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="block w-full px-4 py-3 pl-8 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('revenue') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 text-sm">€</span>
                        </div>
                    </div>
                    @error('revenue')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Notes et observations
                </label>
                <div class="relative">
                    <textarea id="notes" 
                              name="notes" 
                              rows="4" 
                              placeholder="Notes additionnelles, commentaires..."
                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 resize-y @error('notes') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('notes', $mission->notes) }}</textarea>
                </div>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informations de suivi -->
            <div class="bg-gray-50 -m-6 p-6 rounded-lg border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <span><strong>Modifiée le :</strong> {{ $mission->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    
                    @if($mission->completed_at)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span><strong>Terminée le :</strong> {{ $mission->completed_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Les modifications seront sauvegardées automatiquement</span>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('missions.show', $mission) }}" 
                           class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </a>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Sauvegarder les modifications
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-resize du textarea description
document.getElementById('description').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Auto-resize du textarea notes
document.getElementById('notes').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Indication visuelle de la priorité
document.getElementById('priority').addEventListener('change', function() {
    const priorityColors = {
        'urgente': 'border-red-300 bg-red-50',
        'haute': 'border-orange-300 bg-orange-50',
        'normale': 'border-blue-300 bg-blue-50',
        'basse': 'border-gray-300 bg-gray-50'
    };
    
    // Reset classes
    this.className = this.className.replace(/border-\w+-300|bg-\w+-50/g, '');
    
    if (priorityColors[this.value]) {
        this.className += ' ' + priorityColors[this.value];
    }
});

// Indication visuelle du statut
document.getElementById('status').addEventListener('change', function() {
    const statusColors = {
        'en_attente': 'border-yellow-300 bg-yellow-50',
        'en_cours': 'border-blue-300 bg-blue-50',
        'termine': 'border-green-300 bg-green-50',
        'annule': 'border-gray-300 bg-gray-50',
        'en_retard': 'border-red-300 bg-red-50'
    };
    
    // Reset classes
    this.className = this.className.replace(/border-\w+-300|bg-\w+-50/g, '');
    
    if (statusColors[this.value]) {
        this.className += ' ' + statusColors[this.value];
    }
});

// Validation des dates
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const dueDateInput = document.getElementById('due_date');
    
    if (dueDateInput.value) {
        const dueDate = new Date(dueDateInput.value);
        if (startDate > dueDate) {
            alert('La date de début ne peut pas être postérieure à l\'échéance');
            this.value = '';
        }
    }
});

document.getElementById('due_date').addEventListener('change', function() {
    const dueDate = new Date(this.value);
    const startDateInput = document.getElementById('start_date');
    
    if (startDateInput.value) {
        const startDate = new Date(startDateInput.value);
        if (dueDate < startDate) {
            alert('L\'échéance ne peut pas être antérieure à la date de début');
            this.value = '';
        }
    }
});

// Formatage automatique du chiffre d'affaires
document.getElementById('revenue').addEventListener('blur', function() {
    if (this.value && !isNaN(this.value)) {
        this.value = parseFloat(this.value).toFixed(2);
    }
});
</script>
@endsection