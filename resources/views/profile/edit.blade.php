@extends('layouts.app')

@section('title', 'Mon profil - Intranet')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            @if(auth()->user()->avatar)
                <img class="h-12 w-12 rounded-full object-cover border-2 border-indigo-200" 
                     src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" 
                     alt="Photo de profil">
            @else
                <div class="h-12 w-12 rounded-full bg-indigo-500 flex items-center justify-center text-white text-lg font-medium">
                    {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                </div>
            @endif
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-gray-900">👤 Mon profil</h1>
                <p class="text-gray-600 mt-1">
                    @if(auth()->user()->isAdministrateur())
                        Gérez vos informations personnelles et professionnelles
                    @else
                        Modifiez vos informations autorisées
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Message d'information pour non-admins -->
    @if(!auth()->user()->isAdministrateur())
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">ℹ️ Informations</h3>
                <div class="mt-2 text-sm text-blue-700">
                    Vous pouvez modifier votre téléphone, votre photo de profil, votre signature et vos informations professionnelles. Pour modifier d'autres informations, contactez un administrateur.
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Alerte si infos professionnelles incomplètes -->
    @if(!auth()->user()->hasProfessionalInfoComplete())
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">⚠️ Informations professionnelles incomplètes</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    Pour que vos devis et factures soient correctement personnalisés, veuillez renseigner vos informations professionnelles (RSAC, adresse, etc.).
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations personnelles -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Gestion photo de profil -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📸 Photo de profil</h2>
                    <p class="text-sm text-gray-600 mt-1">Modifiez votre photo de profil</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-6">
                        <!-- Photo actuelle -->
                        <div class="flex-shrink-0">
                            @if(auth()->user()->avatar)
                                <img class="h-20 w-20 rounded-full object-cover border-4 border-gray-200" 
                                     src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" 
                                     alt="Photo de profil">
                            @else
                                <div class="h-20 w-20 rounded-full bg-indigo-500 flex items-center justify-center text-white text-2xl font-medium border-4 border-gray-200">
                                    {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Actions photo -->
                        <div class="flex-1">
                            <!-- FORMULAIRE POUR UPLOAD AVATAR -->
                            <form method="POST" action="{{ route('profile.update-avatar') }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                
                                <div>
                                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Choisir une nouvelle photo
                                        </span>
                                    </label>
                                    <input type="file" 
                                           name="avatar" 
                                           id="avatar" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors @error('avatar') border-red-300 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF jusqu'à 2MB</p>
                                    @error('avatar')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Télécharger
                                </button>
                            </form>
                            
                            <!-- FORMULAIRE SÉPARÉ POUR SUPPRIMER AVATAR -->
                            @if(auth()->user()->avatar)
                            <form method="POST" action="{{ route('profile.remove-avatar') }}" class="mt-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gestion signature/cachet -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">✍️ Signature / Cachet</h2>
                    <p class="text-sm text-gray-600 mt-1">Image de signature ou cachet pour vos documents (devis, factures)</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-6">
                        <!-- Signature actuelle -->
                        <div class="flex-shrink-0">
                            @if(auth()->user()->signature_image)
                                <img class="h-20 w-32 object-contain border-2 border-gray-200 rounded" 
                                     src="{{ asset('storage/signatures/' . auth()->user()->signature_image) }}" 
                                     alt="Signature">
                            @else
                                <div class="h-20 w-32 bg-gray-100 flex items-center justify-center text-gray-400 text-sm border-2 border-gray-200 rounded">
                                    Aucune signature
                                </div>
                            @endif
                        </div>
                        
                        <!-- Actions signature -->
                        <div class="flex-1">
                            <form method="POST" action="{{ route('profile.update-signature') }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                
                                <div>
                                    <label for="signature_image" class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Choisir une signature/cachet
                                        </span>
                                    </label>
                                    <input type="file" 
                                           name="signature_image" 
                                           id="signature_image" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-colors @error('signature_image') border-red-300 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF jusqu'à 2MB - Taille recommandée : 300x150px</p>
                                    @error('signature_image')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Télécharger
                                </button>
                            </form>
                            
                            @if(auth()->user()->signature_image)
                            <form method="POST" action="{{ route('profile.remove-signature') }}" class="mt-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre signature ?')"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire informations personnelles -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📝 Informations personnelles</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        @if(auth()->user()->isAdministrateur())
                            Modifiez vos informations de base
                        @else
                            Seuls les champs autorisés peuvent être modifiés
                        @endif
                    </p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Prénom -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prénom
                                    @if(auth()->user()->isAdministrateur())
                                        <span class="text-red-500">*</span>
                                    @else
                                        <span class="text-gray-400 text-xs ml-2">(lecture seule)</span>
                                    @endif
                                </label>
                                <input type="text" 
                                       name="first_name" 
                                       id="first_name" 
                                       value="{{ old('first_name', auth()->user()->first_name) }}"
                                       @if(!auth()->user()->isAdministrateur()) readonly @endif
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors 
                                       @if(!auth()->user()->isAdministrateur()) bg-gray-50 text-gray-600 cursor-not-allowed @endif
                                       @error('first_name') border-red-300 ring-red-500 @enderror">
                                @error('first_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nom -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom
                                    @if(auth()->user()->isAdministrateur())
                                        <span class="text-red-500">*</span>
                                    @else
                                        <span class="text-gray-400 text-xs ml-2">(lecture seule)</span>
                                    @endif
                                </label>
                                <input type="text" 
                                       name="last_name" 
                                       id="last_name" 
                                       value="{{ old('last_name', auth()->user()->last_name) }}"
                                       @if(!auth()->user()->isAdministrateur()) readonly @endif
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors 
                                       @if(!auth()->user()->isAdministrateur()) bg-gray-50 text-gray-600 cursor-not-allowed @endif
                                       @error('last_name') border-red-300 ring-red-500 @enderror">
                                @error('last_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                    @if(auth()->user()->isAdministrateur())
                                        <span class="text-red-500">*</span>
                                    @else
                                        <span class="text-gray-400 text-xs ml-2">(lecture seule)</span>
                                    @endif
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email', auth()->user()->email) }}"
                                       @if(!auth()->user()->isAdministrateur()) readonly @endif
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors 
                                       @if(!auth()->user()->isAdministrateur()) bg-gray-50 text-gray-600 cursor-not-allowed @endif
                                       @error('email') border-red-300 ring-red-500 @enderror">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Téléphone - Modifiable par tous -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Téléphone
                                    @if(!auth()->user()->isAdministrateur())
                                        <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                    @endif
                                </label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       placeholder="06 12 34 56 78"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('phone') border-red-300 ring-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(auth()->user()->isAdministrateur())
                            <!-- Poste - Uniquement pour admins -->
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Poste</label>
                                <input type="text" 
                                       name="position" 
                                       id="position" 
                                       value="{{ old('position', auth()->user()->position) }}"
                                       placeholder="Votre poste actuel"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('position') border-red-300 ring-red-500 @enderror">
                                @error('position')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Département - Uniquement pour admins -->
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Département</label>
                                <input type="text" 
                                       name="department" 
                                       id="department" 
                                       value="{{ old('department', auth()->user()->department) }}"
                                       placeholder="Votre département"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('department') border-red-300 ring-red-500 @enderror">
                                @error('department')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @else
                            <!-- Affichage en lecture seule pour non-admins -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Département <span class="text-gray-400 text-xs ml-2">(lecture seule)</span>
                                </label>
                                <div class="text-sm text-gray-600 py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    {{ auth()->user()->department ?? 'Non renseigné' }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 mt-6">
                            <button type="button" 
                                    onclick="window.location.reload()" 
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Annuler
                            </button>
                            <button type="submit" 
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- NOUVEAU : Informations professionnelles (pour les devis/factures) -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 shadow rounded-lg">
                <div class="p-6 border-b border-green-200">
                    <h2 class="text-lg font-semibold text-green-900">🏢 Informations professionnelles</h2>
                    <p class="text-sm text-green-700 mt-1">Ces informations apparaîtront automatiquement sur vos devis et factures</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update-professional') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Numéro RSAC -->
                            <div class="md:col-span-2">
                                <label for="rsac_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Numéro RSAC
                                    <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                </label>
                                <input type="text" 
                                       name="rsac_number" 
                                       id="rsac_number" 
                                       value="{{ old('rsac_number', auth()->user()->rsac_number) }}"
                                       placeholder="Ex: 123456789"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('rsac_number') border-red-300 ring-red-500 @enderror">
                                @error('rsac_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Adresse professionnelle -->
                            <div class="md:col-span-2">
                                <label for="professional_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adresse professionnelle
                                    <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                </label>
                                <textarea name="professional_address" 
                                          id="professional_address" 
                                          rows="2"
                                          placeholder="Ex: 35 Rue de l'Exemple"
                                          class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('professional_address') border-red-300 ring-red-500 @enderror">{{ old('professional_address', auth()->user()->professional_address) }}</textarea>
                                @error('professional_address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Code postal -->
                            <div>
                                <label for="professional_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Code postal
                                    <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                </label>
                                <input type="text" 
                                       name="professional_postal_code" 
                                       id="professional_postal_code" 
                                       value="{{ old('professional_postal_code', auth()->user()->professional_postal_code) }}"
                                       placeholder="Ex: 75001"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('professional_postal_code') border-red-300 ring-red-500 @enderror">
                                @error('professional_postal_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ville -->
                            <div>
                                <label for="professional_city" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ville
                                    <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                </label>
                                <input type="text" 
                                       name="professional_city" 
                                       id="professional_city" 
                                       value="{{ old('professional_city', auth()->user()->professional_city) }}"
                                       placeholder="Ex: Paris"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('professional_city') border-red-300 ring-red-500 @enderror">
                                @error('professional_city')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email professionnel (optionnel) -->
                            <div>
                                <label for="professional_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email professionnel (optionnel)
                                    <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                </label>
                                <input type="email" 
                                       name="professional_email" 
                                       id="professional_email" 
                                       value="{{ old('professional_email', auth()->user()->professional_email) }}"
                                       placeholder="Si différent de l'email principal"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('professional_email') border-red-300 ring-red-500 @enderror">
                                @error('professional_email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Téléphone professionnel (optionnel) -->
                            <div>
                                <label for="professional_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Téléphone professionnel (optionnel)
                                    <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                </label>
                                <input type="text" 
                                       name="professional_phone" 
                                       id="professional_phone" 
                                       value="{{ old('professional_phone', auth()->user()->professional_phone) }}"
                                       placeholder="Si différent du téléphone principal"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('professional_phone') border-red-300 ring-red-500 @enderror">
                                @error('professional_phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mentions légales -->
                            <div class="md:col-span-2">
                                <label for="legal_mentions" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mentions légales personnalisées
                                    <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                </label>
                                <textarea name="legal_mentions" 
                                          id="legal_mentions" 
                                          rows="4"
                                          placeholder="Ex: Assurance RC Pro n°... - TVA non applicable, article 293 B du CGI..."
                                          class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('legal_mentions') border-red-300 ring-red-500 @enderror">{{ old('legal_mentions', auth()->user()->legal_mentions) }}</textarea>
                                @error('legal_mentions')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Texte de pied de page -->
                            <div class="md:col-span-2">
                                <label for="footer_text" class="block text-sm font-medium text-gray-700 mb-2">
                                    Texte de pied de page
                                    <span class="text-green-600 text-xs ml-2">(modifiable)</span>
                                </label>
                                <textarea name="footer_text" 
                                          id="footer_text" 
                                          rows="3"
                                          placeholder="Ex: Merci de votre confiance - N'hésitez pas à me contacter pour tout renseignement"
                                          class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('footer_text') border-red-300 ring-red-500 @enderror">{{ old('footer_text', auth()->user()->footer_text) }}</textarea>
                                @error('footer_text')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-green-200 mt-6">
                            <button type="button" 
                                    onclick="window.location.reload()" 
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                Annuler
                            </button>
                            <button type="submit" 
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                Enregistrer les informations professionnelles
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Changement de mot de passe -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">🔒 Changer le mot de passe</h2>
                    <p class="text-sm text-gray-600 mt-1">Modifiez votre mot de passe pour sécuriser votre compte</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <!-- Mot de passe actuel -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mot de passe actuel <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('current_password') border-red-300 ring-red-500 @enderror">
                                @error('current_password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nouveau mot de passe -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nouveau mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('password') border-red-300 ring-red-500 @enderror">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p class="mt-2 text-xs text-gray-500">Au minimum 8 caractères</p>
                                @enderror
                            </div>

                            <!-- Confirmation -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmer le mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            </div>
                        </div>

                        <!-- Bouton mot de passe -->
                        <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                            <button type="submit" 
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations -->
        <div class="space-y-6">
            <!-- Informations du compte -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du compte</h3>
                
                <div class="space-y-4">
                    <!-- Avatar et nom -->
                    <div class="flex items-center">
                        @if(auth()->user()->avatar)
                            <img class="h-12 w-12 rounded-full object-cover border-2 border-indigo-200" 
                                 src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" 
                                 alt="Photo de profil">
                        @else
                            <div class="h-12 w-12 rounded-full bg-indigo-500 flex items-center justify-center text-white text-lg font-medium">
                                {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                            </div>
                        @endif
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">{{ auth()->user()->full_name }}</div>
                            <div class="text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if(auth()->user()->role === 'administrateur') bg-red-100 text-red-800
                                    @elseif(auth()->user()->role === 'manager') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Détails du compte -->
                    <div class="border-t pt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Membre depuis</span>
                            <span class="text-gray-900 font-medium">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Dernière connexion</span>
                            <span class="text-gray-900 font-medium">
                                {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d/m/Y H:i') : 'Première fois' }}
                            </span>
                        </div>
                        @if(auth()->user()->manager)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Manager</span>
                            <span class="text-gray-900 font-medium">{{ auth()->user()->manager->full_name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            @if(!auth()->user()->isAdministrateur())
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-orange-800">Accès limité</h3>
                        <div class="mt-2 text-sm text-orange-700">
                            <p>En tant que {{ auth()->user()->role }}, vous pouvez modifier :</p>
                            <ul class="list-disc list-inside mt-1">
                                <li>Votre téléphone</li>
                                <li>Votre photo de profil</li>
                                <li>Votre signature/cachet</li>
                                <li>Vos infos professionnelles</li>
                                <li>Votre mot de passe</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Mes statistiques -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mes statistiques</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-blue-700 font-medium">Missions en cours</span>
                        <span class="text-lg font-bold text-blue-900">
                            {{ auth()->user()->assignedMissions()->inProgress()->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-green-700 font-medium">Terminées ce mois</span>
                        <span class="text-lg font-bold text-green-900">
                            {{ auth()->user()->getCompletedMissionsThisMonth() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm text-yellow-700 font-medium">CA ce mois</span>
                        <span class="text-lg font-bold text-yellow-900">
                            {{ number_format(auth()->user()->getCurrentMonthRevenue(), 0, ',', ' ') }}€
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm text-purple-700 font-medium">Heures formation</span>
                        <span class="text-lg font-bold text-purple-900">
                            {{ auth()->user()->getFormationHoursThisYear() }}h
                        </span>
                    </div>
                </div>
            </div>

            <!-- Conseils sécurité -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Conseils de sécurité</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Changez votre mot de passe régulièrement</li>
                                <li>Utilisez un mot de passe fort (min. 8 caractères)</li>
                                <li>Ne partagez jamais vos identifiants</li>
                                <li>Déconnectez-vous des ordinateurs partagés</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection