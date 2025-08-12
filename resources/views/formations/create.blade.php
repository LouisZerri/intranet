@extends('layouts.app')

@section('title', 'Créer une formation - Intranet')

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
                Créer une nouvelle formation
            </h1>
            <p class="text-gray-600 mt-1">
                Ajoutez une formation au catalogue pour enrichir le parcours des collaborateurs
            </p>
        </div>

        <form method="POST" action="{{ route('formations.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Informations générales -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">📋 Informations générales</h3>

                <!-- Titre -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        📝 Titre de la formation <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               required
                               maxlength="255"
                               placeholder="Titre descriptif de la formation"
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
                        📄 Description de la formation <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <textarea id="description" 
                                  name="description" 
                                  rows="6" 
                                  required
                                  placeholder="Description détaillée de la formation, son contenu, ses bénéfices..."
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 resize-y @error('description') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Catégorie -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            🏷️ Catégorie
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="category" 
                                   name="category" 
                                   value="{{ old('category') }}"
                                   maxlength="100"
                                   placeholder="ex: Management, Technique, Sécurité..."
                                   list="categories"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('category') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            <datalist id="categories">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                        </div>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Niveau -->
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                            📊 Niveau <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="level" 
                                    name="level" 
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('level') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="">Sélectionner un niveau</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level }}" {{ old('level') === $level ? 'selected' : '' }}>
                                        @if($level === 'debutant')
                                            🌱 Débutant
                                        @elseif($level === 'intermediaire')
                                            🌿 Intermédiaire
                                        @else
                                            🌳 Avancé
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
                        @error('level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Format -->
                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                            💻 Format <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="format" 
                                    name="format" 
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('format') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="">Sélectionner un format</option>
                                @foreach($formats as $format)
                                    <option value="{{ $format }}" {{ old('format') === $format ? 'selected' : '' }}>
                                        @if($format === 'presentiel')
                                            🏢 Présentiel
                                        @elseif($format === 'distanciel')
                                            💻 Distanciel
                                        @else
                                            🔄 Hybride
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
                        @error('format')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Détails pratiques -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">⚙️ Détails pratiques</h3>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Durée -->
                    <div>
                        <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">
                            ⏰ Durée (heures) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="duration_hours" 
                                   name="duration_hours" 
                                   value="{{ old('duration_hours') }}"
                                   required
                                   min="1"
                                   max="1000"
                                   placeholder="8"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('duration_hours') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">h</span>
                            </div>
                        </div>
                        @error('duration_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Coût -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                            💰 Coût (optionnel)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="cost" 
                                   name="cost" 
                                   value="{{ old('cost') }}"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00"
                                   class="block w-full px-4 py-3 pl-8 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('cost') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">€</span>
                            </div>
                        </div>
                        @error('cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max participants -->
                    <div>
                        <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                            👥 Places max
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="max_participants" 
                                   name="max_participants" 
                                   value="{{ old('max_participants') }}"
                                   min="1"
                                   placeholder="Illimité"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('max_participants') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        </div>
                        @error('max_participants')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Laisser vide pour un nombre illimité</p>
                    </div>

                    <!-- Prestataire -->
                    <div>
                        <label for="provider" class="block text-sm font-medium text-gray-700 mb-2">
                            🏢 Prestataire
                        </label>
                        <input type="text" 
                               id="provider" 
                               name="provider" 
                               value="{{ old('provider') }}"
                               maxlength="255"
                               placeholder="Nom du formateur/organisme"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('provider') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        @error('provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dates et lieu -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">📅 Planning et lieu</h3>

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
                                   value="{{ old('start_date') }}"
                                   min="{{ now()->format('Y-m-d') }}"
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

                    <!-- Date de fin -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            🏁 Date de fin
                        </label>
                        <div class="relative">
                            <input type="date" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('end_date') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lieu -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            📍 Lieu
                        </label>
                        <input type="text" 
                               id="location" 
                               name="location" 
                               value="{{ old('location') }}"
                               maxlength="255"
                               placeholder="Salle, adresse ou plateforme"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('location') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Objectifs et prérequis -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">🎯 Contenu pédagogique</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Objectifs -->
                    <div>
                        <label for="objectives" class="block text-sm font-medium text-gray-700 mb-2">
                            🎯 Objectifs pédagogiques
                        </label>
                        <div id="objectives-container">
                            <div class="objective-input flex items-center space-x-2 mb-2">
                                <input type="text" name="objectives[]" placeholder="Objectif 1"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <button type="button" onclick="removeObjective(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addObjective()" 
                                class="mt-2 inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter un objectif
                        </button>
                    </div>

                    <!-- Prérequis -->
                    <div>
                        <label for="prerequisites" class="block text-sm font-medium text-gray-700 mb-2">
                            📋 Prérequis
                        </label>
                        <div id="prerequisites-container">
                            <div class="prerequisite-input flex items-center space-x-2 mb-2">
                                <input type="text" name="prerequisites[]" placeholder="Prérequis 1"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <button type="button" onclick="removePrerequisite(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addPrerequisite()" 
                                class="mt-2 inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter un prérequis
                        </button>
                    </div>
                </div>
            </div>

            <!-- Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Formation créée</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Une fois créée, la formation sera visible dans le catalogue et les collaborateurs pourront demander leur participation selon le workflow de validation.
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
                        <span>Formation créée le {{ now()->format('d/m/Y à H:i') }}</span>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('formations.index') }}" 
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
                            Créer la formation
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Gestion des objectifs
let objectiveCount = 1;

function addObjective() {
    objectiveCount++;
    const container = document.getElementById('objectives-container');
    const div = document.createElement('div');
    div.className = 'objective-input flex items-center space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="objectives[]" placeholder="Objectif ${objectiveCount}"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        <button type="button" onclick="removeObjective(this)" class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeObjective(button) {
    const container = document.getElementById('objectives-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}

// Gestion des prérequis
let prerequisiteCount = 1;

function addPrerequisite() {
    prerequisiteCount++;
    const container = document.getElementById('prerequisites-container');
    const div = document.createElement('div');
    div.className = 'prerequisite-input flex items-center space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="prerequisites[]" placeholder="Prérequis ${prerequisiteCount}"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        <button type="button" onclick="removePrerequisite(this)" class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removePrerequisite(button) {
    const container = document.getElementById('prerequisites-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}

// Validation des dates
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDateInput = document.getElementById('end_date');
    
    if (endDateInput.value) {
        const endDate = new Date(endDateInput.value);
        if (startDate > endDate) {
            alert('La date de début ne peut pas être postérieure à la date de fin');
            this.value = '';
        }
    }
    
    // Mettre à jour la date min pour end_date
    endDateInput.min = this.value;
});

document.getElementById('end_date').addEventListener('change', function() {
    const endDate = new Date(this.value);
    const startDateInput = document.getElementById('start_date');
    
    if (startDateInput.value) {
        const startDate = new Date(startDateInput.value);
        if (endDate < startDate) {
            alert('La date de fin ne peut pas être antérieure à la date de début');
            this.value = '';
        }
    }
});

// Auto-resize du textarea description
document.getElementById('description').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Formatage automatique du coût
document.getElementById('cost').addEventListener('blur', function() {
    if (this.value && !isNaN(this.value)) {
        this.value = parseFloat(this.value).toFixed(2);
    }
});

// Indication visuelle du niveau
document.getElementById('level').addEventListener('change', function() {
    const levelColors = {
        'debutant': 'border-green-300 bg-green-50',
        'intermediaire': 'border-yellow-300 bg-yellow-50',
        'avance': 'border-red-300 bg-red-50'
    };
    
    // Reset classes
    this.className = this.className.replace(/border-\w+-300|bg-\w+-50/g, '');
    
    if (levelColors[this.value]) {
        this.className += ' ' + levelColors[this.value];
    }
});

// Indication visuelle du format
document.getElementById('format').addEventListener('change', function() {
    const formatColors = {
        'presentiel': 'border-blue-300 bg-blue-50',
        'distanciel': 'border-purple-300 bg-purple-50',
        'hybride': 'border-indigo-300 bg-indigo-50'
    };
    
    // Reset classes
    this.className = this.className.replace(/border-\w+-300|bg-\w+-50/g, '');
    
    if (formatColors[this.value]) {
        this.className += ' ' + formatColors[this.value];
    }
});
</script>
@endsection