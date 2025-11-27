@extends('layouts.app')

@section('title', 'Nouveau candidat - Recrutement')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            <a href="{{ route('recruitment.index') }}" class="mr-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">➕ Nouveau candidat</h1>
                <p class="text-gray-600 mt-1">Ajouter une nouvelle candidature au processus de recrutement</p>
            </div>
        </div>
    </div>

    <!-- Message d'erreur Google Drive -->
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Formulaire -->
    <form method="POST" action="{{ route('recruitment.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Informations personnelles -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">👤 Informations personnelles</h2>
                <p class="text-sm text-gray-600 mt-1">Coordonnées du candidat</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prénom -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="first_name" 
                               id="first_name" 
                               value="{{ old('first_name') }}"
                               required
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('first_name') border-red-300 @enderror">
                        @error('first_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nom -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="last_name" 
                               id="last_name" 
                               value="{{ old('last_name') }}"
                               required
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('last_name') border-red-300 @enderror">
                        @error('last_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               required
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone') }}"
                               placeholder="06 12 34 56 78"
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('phone') border-red-300 @enderror">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ville -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                        <input type="text" 
                               name="city" 
                               id="city" 
                               value="{{ old('city') }}"
                               placeholder="Paris"
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('city') border-red-300 @enderror">
                        @error('city')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Département -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Département</label>
                        <select name="department" 
                                id="department" 
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('department') border-red-300 @enderror">
                            <option value="">Sélectionner un département</option>
                            @foreach($departementsFrancais as $dept)
                                <option value="{{ $dept }}" {{ old('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                        @error('department')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations professionnelles -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">💼 Informations professionnelles</h2>
                <p class="text-sm text-gray-600 mt-1">Poste recherché et disponibilité</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Poste visé -->
                    <div>
                        <label for="position_applied" class="block text-sm font-medium text-gray-700 mb-2">Poste visé</label>
                        <input type="text" 
                               name="position_applied" 
                               id="position_applied" 
                               value="{{ old('position_applied') }}"
                               placeholder="Commercial immobilier"
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('position_applied') border-red-300 @enderror">
                        @error('position_applied')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Localisation souhaitée -->
                    <div>
                        <label for="desired_location" class="block text-sm font-medium text-gray-700 mb-2">Localisation souhaitée</label>
                        <select name="desired_location" 
                                id="desired_location" 
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('desired_location') border-red-300 @enderror">
                            <option value="">Sélectionner un département</option>
                            @foreach($departementsFrancais as $dept)
                                <option value="{{ $dept }}" {{ old('desired_location') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                        @error('desired_location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Disponibilité -->
                    <div>
                        <label for="available_from" class="block text-sm font-medium text-gray-700 mb-2">Disponible à partir du</label>
                        <input type="date" 
                               name="available_from" 
                               id="available_from" 
                               value="{{ old('available_from') }}"
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('available_from') border-red-300 @enderror">
                        @error('available_from')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Source -->
                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 mb-2">Source de la candidature</label>
                        <select name="source" 
                                id="source" 
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('source') border-red-300 @enderror">
                            <option value="">Sélectionner une source</option>
                            <option value="Indeed" {{ old('source') == 'Indeed' ? 'selected' : '' }}>Indeed</option>
                            <option value="LinkedIn" {{ old('source') == 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                            <option value="Leboncoin" {{ old('source') == 'Leboncoin' ? 'selected' : '' }}>Leboncoin</option>
                            <option value="Site web" {{ old('source') == 'Site web' ? 'selected' : '' }}>Site web</option>
                            <option value="Candidature spontanée" {{ old('source') == 'Candidature spontanée' ? 'selected' : '' }}>Candidature spontanée</option>
                            <option value="Recommandation" {{ old('source') == 'Recommandation' ? 'selected' : '' }}>Recommandation</option>
                            <option value="Salon emploi" {{ old('source') == 'Salon emploi' ? 'selected' : '' }}>Salon emploi</option>
                            <option value="Autre" {{ old('source') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('source')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recruteur assigné -->
                    <div class="md:col-span-2">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Recruteur assigné</label>
                        <select name="assigned_to" 
                                id="assigned_to" 
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('assigned_to') border-red-300 @enderror">
                            <option value="">Non assigné</option>
                            @foreach($recruiters as $recruiter)
                                <option value="{{ $recruiter->id }}" {{ old('assigned_to') == $recruiter->id ? 'selected' : '' }}>
                                    {{ $recruiter->full_name }} ({{ ucfirst($recruiter->role) }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">📄 Documents</h2>
                <p class="text-sm text-gray-600 mt-1">Tous les documents du candidat (stockés sur Google Drive)</p>
            </div>
            <div class="p-6">
                <!-- Info Google Drive -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.01 1.485c-2.082 0-3.754.02-3.743.047.01.02 1.708 3.001 3.774 6.62l3.76 6.574h3.76c2.081 0 3.753-.02 3.742-.047-.01-.02-1.708-3.001-3.774-6.62l-3.76-6.574h-3.76zm-5.5 6.574c-.017.023-1.729 3.004-3.805 6.627l-3.771 6.586 1.873.01 1.873.01 1.893-3.31 1.893-3.31h7.627l-.945-1.652-.944-1.652-2.9-.01c-1.595-.005-2.906.005-2.913.017l1.88-3.31c1.034-1.82 1.88-3.313 1.88-3.32 0-.006-.846-.01-1.88-.01-1.034 0-1.88.006-1.88.014h-.006z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 font-medium">Stockage Google Drive</p>
                            <p class="text-sm text-blue-600 mt-1">Les documents seront automatiquement uploadés dans un dossier dédié au candidat sur Google Drive.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($documentTypes as $type => $config)
                    <div>
                        <label for="{{ $type }}" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <span class="mr-2">{{ $config['icon'] }}</span>
                                {{ $config['label'] }}
                            </span>
                        </label>
                        <input type="file" 
                               name="{{ $type }}" 
                               id="{{ $type }}" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition-colors @error($type) border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG - Max 5MB</p>
                        @error($type)
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">📝 Notes</h2>
                <p class="text-sm text-gray-600 mt-1">Informations complémentaires</p>
            </div>
            <div class="p-6">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes internes</label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="4"
                              placeholder="Informations supplémentaires sur le candidat..."
                              class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Boutons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('recruitment.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                Annuler
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Ajouter le candidat
            </button>
        </div>
    </form>
</div>
@endsection