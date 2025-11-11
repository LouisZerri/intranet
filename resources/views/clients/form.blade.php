@extends('layouts.app')

@section('title', isset($client) ? 'Modifier le client' : 'Nouveau client')

@section('content')
<div class="space-y-6">
    {{-- En-tête --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isset($client) ? '✏️ Modifier le client' : '➕ Nouveau client' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">{{ isset($client) ? 'Modifier les informations' : 'Créer un nouveau client rapidement' }}</p>
        </div>
        <a href="{{ isset($client) ? route('clients.show', $client) : route('clients.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
            ← Retour
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Formulaire principal --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ isset($client) ? route('clients.update', $client) : route('clients.store') }}">
                @csrf
                @if(isset($client))
                    @method('PUT')
                @endif

                <div class="bg-white shadow rounded-lg">
                    <div class="p-6 border-b border-gray-200 bg-indigo-50">
                        <h2 class="text-lg font-semibold text-gray-900">💼 Informations du client</h2>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        {{-- Type de client --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Type de client <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <label id="label_particulier" class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none transition-all {{ old('type', $client->type ?? 'particulier') === 'particulier' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500' : 'border-gray-300 bg-white' }}">
                                    <input type="radio" 
                                           name="type" 
                                           value="particulier" 
                                           id="type_particulier"
                                           class="sr-only" 
                                           {{ old('type', $client->type ?? 'particulier') === 'particulier' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-2xl mb-2">👤</span>
                                            <span class="block text-sm font-medium text-gray-900">Particulier</span>
                                        </span>
                                    </span>
                                </label>

                                <label id="label_professionnel" class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none transition-all {{ old('type', $client->type ?? '') === 'professionnel' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500' : 'border-gray-300 bg-white' }}">
                                    <input type="radio" 
                                           name="type" 
                                           value="professionnel" 
                                           id="type_professionnel"
                                           class="sr-only"
                                           {{ old('type', $client->type ?? '') === 'professionnel' ? 'checked' : '' }}>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-2xl mb-2">🏢</span>
                                            <span class="block text-sm font-medium text-gray-900">Professionnel</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t border-gray-200 pt-6"></div>

                        {{-- Nom du contact --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom du contact <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', $client->name ?? '') }}"
                                   required
                                   class="block w-full px-3 py-2 border @error('name') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Nom et prénom">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Champs professionnels --}}
                        <div id="professional_fields" style="display: none;">
                            <div class="space-y-4 bg-green-50 border border-green-200 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-green-900">Informations professionnelles</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Nom de l'entreprise <span class="text-red-500" id="company_required">*</span>
                                        </label>
                                        <input type="text" 
                                               name="company_name" 
                                               id="company_name"
                                               value="{{ old('company_name', $client->company_name ?? '') }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="Raison sociale">
                                        @error('company_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="siret" class="block text-sm font-medium text-gray-700 mb-2">SIRET</label>
                                        <input type="text" 
                                               name="siret" 
                                               id="siret"
                                               value="{{ old('siret', $client->siret ?? '') }}"
                                               maxlength="14"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="14 chiffres">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="tva_number" class="block text-sm font-medium text-gray-700 mb-2">N° TVA Intracommunautaire</label>
                                        <input type="text" 
                                               name="tva_number" 
                                               id="tva_number"
                                               value="{{ old('tva_number', $client->tva_number ?? '') }}"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="FRxxxxxxxxxxxx">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6"></div>

                        <h3 class="text-sm font-medium text-gray-700">📞 Coordonnées</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" 
                                       name="email" 
                                       id="email"
                                       value="{{ old('email', $client->email ?? '') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="email@exemple.com">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone fixe</label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone"
                                       value="{{ old('phone', $client->phone ?? '') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="01 23 45 67 89">
                            </div>

                            <div>
                                <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Téléphone mobile</label>
                                <input type="text" 
                                       name="mobile" 
                                       id="mobile"
                                       value="{{ old('mobile', $client->mobile ?? '') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="06 12 34 56 78">
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6"></div>

                        <h3 class="text-sm font-medium text-gray-700">📍 Adresse</h3>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Adresse complète</label>
                            <textarea name="address" 
                                      id="address"
                                      rows="2"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Numéro, voie, complément">{{ old('address', $client->address ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Code postal</label>
                                <input type="text" 
                                       name="postal_code" 
                                       id="postal_code"
                                       value="{{ old('postal_code', $client->postal_code ?? '') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="75001">
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                                <input type="text" 
                                       name="city" 
                                       id="city"
                                       value="{{ old('city', $client->city ?? '') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Paris">
                            </div>

                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                                <input type="text" 
                                       name="country" 
                                       id="country"
                                       value="{{ old('country', $client->country ?? 'France') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6"></div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">📝 Notes internes</label>
                            <textarea name="notes" 
                                      id="notes"
                                      rows="3"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Notes visibles uniquement en interne">{{ old('notes', $client->notes ?? '') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Ces notes ne seront pas visibles sur les documents clients</p>
                        </div>

                        @if(isset($client))
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1"
                                       {{ old('is_active', $client->is_active ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Client actif
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 ml-6">Un client inactif n'apparaîtra plus dans les sélections</p>
                        @endif
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                        <a href="{{ isset($client) ? route('clients.show', $client) : route('clients.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            💾 {{ isset($client) ? 'Mettre à jour' : 'Créer le client' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Sidebar aide --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-3">ℹ️ Aide</h3>
                <div class="space-y-2 text-sm text-blue-800">
                    <p><strong>Création rapide :</strong> Seul le nom est obligatoire.</p>
                    <p><strong>Client professionnel :</strong> Le nom de l'entreprise devient obligatoire.</p>
                    <p><strong>Coordonnées :</strong> Plus vous renseignez d'informations, plus les documents seront complets.</p>
                </div>
            </div>

            @if(isset($client))
                <div class="bg-white shadow rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">📊 Statistiques</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Devis</span>
                            <span class="font-medium">{{ $client->quotes()->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Factures</span>
                            <span class="font-medium">{{ $client->invoices()->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">CA Total</span>
                            <span class="font-medium text-green-600">{{ number_format($client->getTotalRevenue(), 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-red-900 mb-3">⚠️ Zone dangereuse</h3>
                    <form method="POST" action="{{ route('clients.destroy', $client) }}" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible et supprimera tous les devis et factures associés.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                            🗑️ Supprimer le client
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleClientType() {
    const typeInputs = document.querySelectorAll('input[name="type"]');
    let selectedType = 'particulier';
    
    typeInputs.forEach(input => {
        if (input.checked) {
            selectedType = input.value;
        }
    });
    
    // Mise à jour des styles des labels
    const labelParticulier = document.getElementById('label_particulier');
    const labelProfessionnel = document.getElementById('label_professionnel');
    
    if (selectedType === 'particulier') {
        labelParticulier.classList.remove('border-gray-300', 'bg-white');
        labelParticulier.classList.add('border-indigo-500', 'bg-indigo-50', 'ring-2', 'ring-indigo-500');
        labelProfessionnel.classList.remove('border-indigo-500', 'bg-indigo-50', 'ring-2', 'ring-indigo-500');
        labelProfessionnel.classList.add('border-gray-300', 'bg-white');
    } else {
        labelProfessionnel.classList.remove('border-gray-300', 'bg-white');
        labelProfessionnel.classList.add('border-indigo-500', 'bg-indigo-50', 'ring-2', 'ring-indigo-500');
        labelParticulier.classList.remove('border-indigo-500', 'bg-indigo-50', 'ring-2', 'ring-indigo-500');
        labelParticulier.classList.add('border-gray-300', 'bg-white');
    }
    
    // Affichage/masquage des champs professionnels
    const professionalFields = document.getElementById('professional_fields');
    const companyNameInput = document.getElementById('company_name');
    
    if (selectedType === 'professionnel') {
        professionalFields.style.display = 'block';
        companyNameInput.setAttribute('required', 'required');
    } else {
        professionalFields.style.display = 'none';
        companyNameInput.removeAttribute('required');
    }
}

// Init au chargement
document.addEventListener('DOMContentLoaded', function() {
    toggleClientType();
    
    // Ajouter les événements change sur les radios
    const typeInputs = document.querySelectorAll('input[name="type"]');
    typeInputs.forEach(input => {
        input.addEventListener('change', toggleClientType);
    });
    
    // Ajouter les événements click sur les labels
    document.getElementById('label_particulier').addEventListener('click', function() {
        document.getElementById('type_particulier').checked = true;
        toggleClientType();
    });
    
    document.getElementById('label_professionnel').addEventListener('click', function() {
        document.getElementById('type_professionnel').checked = true;
        toggleClientType();
    });
});
</script>
@endsection