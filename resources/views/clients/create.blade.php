@extends('layouts.app')

@section('title', 'Nouveau client')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">➕ Nouveau client</h1>
                <p class="mt-1 text-sm text-gray-500">Création rapide d'un client particulier ou professionnel</p>
            </div>
            <a href="{{ route('clients.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
        </div>

        {{-- Formulaire --}}
        <form method="POST" action="{{ route('clients.store') }}" class="space-y-6" id="clientForm">
            @csrf

            {{-- Type de client --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">🎯 Type de client</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-indigo-500 transition-colors {{ old('type', 'particulier') === 'particulier' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300' }}">
                        <input type="radio" name="type" value="particulier" class="sr-only" 
                               {{ old('type', 'particulier') === 'particulier' ? 'checked' : '' }}
                               onchange="toggleClientType()">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-2xl mb-2">👤</span>
                                <span class="block text-sm font-semibold text-gray-900">Particulier</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500">Client individuel</span>
                            </span>
                        </span>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-indigo-500 transition-colors {{ old('type') === 'professionnel' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300' }}">
                        <input type="radio" name="type" value="professionnel" class="sr-only"
                               {{ old('type') === 'professionnel' ? 'checked' : '' }}
                               onchange="toggleClientType()">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-2xl mb-2">🏢</span>
                                <span class="block text-sm font-semibold text-gray-900">Professionnel</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500">Entreprise ou société</span>
                            </span>
                        </span>
                    </label>
                </div>
                @error('type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Informations client --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">👤 Informations du client</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nom --}}
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('last_name') border-red-300 @enderror">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Prénom --}}
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('first_name') border-red-300 @enderror">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Informations professionnelles (si professionnel) --}}
            <div id="companyFields" class="bg-white shadow rounded-lg p-6 {{ old('type', 'particulier') === 'particulier' ? 'hidden' : '' }}">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">🏢 Informations professionnelles</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Raison sociale --}}
                    <div class="md:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Raison sociale <span class="text-red-500" id="companyRequired">*</span>
                        </label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('company_name') border-red-300 @enderror">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SIRET --}}
                    <div>
                        <label for="siret" class="block text-sm font-medium text-gray-700 mb-2">
                            SIRET
                        </label>
                        <input type="text" name="siret" id="siret" value="{{ old('siret') }}" maxlength="14"
                            placeholder="14 chiffres"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('siret') border-red-300 @enderror">
                        @error('siret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- N° TVA --}}
                    <div>
                        <label for="tva_number" class="block text-sm font-medium text-gray-700 mb-2">
                            N° TVA Intracommunautaire
                        </label>
                        <input type="text" name="tva_number" id="tva_number" value="{{ old('tva_number') }}"
                            placeholder="FR12345678901"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tva_number') border-red-300 @enderror">
                        @error('tva_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Coordonnées --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📞 Coordonnées</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Téléphone fixe --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone fixe
                        </label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                            placeholder="01 23 45 67 89"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-300 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Téléphone mobile --}}
                    <div class="md:col-span-2">
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone mobile
                        </label>
                        <input type="tel" name="mobile" id="mobile" value="{{ old('mobile') }}"
                            placeholder="06 12 34 56 78"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('mobile') border-red-300 @enderror">
                        @error('mobile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Adresse --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📍 Adresse</h2>
                
                <div class="space-y-6">
                    {{-- Adresse ligne 1 --}}
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse
                        </label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                            placeholder="Numéro et nom de rue"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('address') border-red-300 @enderror">
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Complément d'adresse --}}
                    <div>
                        <label for="address_complement" class="block text-sm font-medium text-gray-700 mb-2">
                            Complément d'adresse
                        </label>
                        <input type="text" name="address_complement" id="address_complement" value="{{ old('address_complement') }}"
                            placeholder="Bâtiment, étage, appartement..."
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('address_complement') border-red-300 @enderror">
                        @error('address_complement')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Code postal --}}
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Code postal
                            </label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                maxlength="5" placeholder="75001"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('postal_code') border-red-300 @enderror">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ville --}}
                        <div class="md:col-span-2">
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Ville
                            </label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('city') border-red-300 @enderror">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes internes --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📝 Notes internes</h2>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes (visibles uniquement en interne)
                    </label>
                    <textarea name="notes" id="notes" rows="4"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('notes') border-red-300 @enderror"
                        placeholder="Remarques, informations complémentaires...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Statut --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">✅ Statut</h2>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Client actif
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Un client inactif n'apparaîtra plus dans les suggestions lors de la création de devis/factures.</p>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 bg-white shadow rounded-lg p-6">
                <a href="{{ route('clients.index') }}"
                    class="inline-flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Annuler
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Créer le client
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function toggleClientType() {
            const type = document.querySelector('input[name="type"]:checked').value;
            const companyFields = document.getElementById('companyFields');
            const companyNameInput = document.getElementById('company_name');
            const companyRequired = document.getElementById('companyRequired');
            
            // Mettre à jour les bordures des labels
            document.querySelectorAll('label[for^="type_"]').forEach(label => {
                label.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-600');
                label.classList.add('border-gray-300');
            });
            
            const checkedInput = document.querySelector('input[name="type"]:checked');
            const checkedLabel = checkedInput.closest('label');
            checkedLabel.classList.remove('border-gray-300');
            checkedLabel.classList.add('border-indigo-600', 'ring-2', 'ring-indigo-600');
            
            if (type === 'professionnel') {
                companyFields.classList.remove('hidden');
                companyNameInput.required = true;
                if (companyRequired) companyRequired.classList.remove('hidden');
            } else {
                companyFields.classList.add('hidden');
                companyNameInput.required = false;
                if (companyRequired) companyRequired.classList.add('hidden');
            }
        }

        // Initialiser l'état au chargement
        document.addEventListener('DOMContentLoaded', function() {
            toggleClientType();
            
            // Ajouter les écouteurs d'événements sur les inputs radio
            document.querySelectorAll('input[name="type"]').forEach(input => {
                input.addEventListener('change', toggleClientType);
            });
        });
    </script>
    @endpush
@endsection