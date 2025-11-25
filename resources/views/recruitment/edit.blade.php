@extends('layouts.app')

@section('title', 'Modifier ' . $candidate->full_name . ' - Recrutement')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('recruitment.show', $candidate) }}" class="mr-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="h-12 w-12 rounded-full bg-purple-500 flex items-center justify-center text-white text-lg font-medium mr-4">
                    {{ $candidate->initials }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">✏️ Modifier {{ $candidate->full_name }}</h1>
                    <p class="text-gray-600 mt-1">Mettre à jour les informations et évaluer le candidat</p>
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @switch($candidate->status)
                    @case('new') bg-blue-100 text-blue-800 @break
                    @case('in_review') bg-yellow-100 text-yellow-800 @break
                    @case('interview') bg-purple-100 text-purple-800 @break
                    @case('recruited') bg-green-100 text-green-800 @break
                    @case('integrated') bg-emerald-100 text-emerald-800 @break
                    @case('refused') bg-red-100 text-red-800 @break
                    @default bg-gray-100 text-gray-800
                @endswitch
            ">
                {{ $candidate->status_label }}
            </span>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('recruitment.update', $candidate) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations personnelles -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">👤 Informations personnelles</h2>
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
                                       value="{{ old('first_name', $candidate->first_name) }}"
                                       required
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('first_name') border-red-300 @enderror">
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
                                       value="{{ old('last_name', $candidate->last_name) }}"
                                       required
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('last_name') border-red-300 @enderror">
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
                                       value="{{ old('email', $candidate->email) }}"
                                       required
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('email') border-red-300 @enderror">
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
                                       value="{{ old('phone', $candidate->phone) }}"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('phone') border-red-300 @enderror">
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
                                       value="{{ old('city', $candidate->city) }}"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('city') border-red-300 @enderror">
                                @error('city')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Département -->
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Département</label>
                                <select name="department" id="department" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Sélectionner</option>
                                    @foreach($departementsFrancais as $dept)
                                        <option value="{{ $dept }}" {{ old('department', $candidate->department) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations professionnelles -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">💼 Informations professionnelles</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Poste visé -->
                            <div>
                                <label for="position_applied" class="block text-sm font-medium text-gray-700 mb-2">Poste visé</label>
                                <input type="text" 
                                       name="position_applied" 
                                       id="position_applied" 
                                       value="{{ old('position_applied', $candidate->position_applied) }}"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>

                            <!-- Localisation souhaitée -->
                            <div>
                                <label for="desired_location" class="block text-sm font-medium text-gray-700 mb-2">Localisation souhaitée</label>
                                <select name="desired_location" id="desired_location" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Sélectionner</option>
                                    @foreach($departementsFrancais as $dept)
                                        <option value="{{ $dept }}" {{ old('desired_location', $candidate->desired_location) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Disponibilité -->
                            <div>
                                <label for="available_from" class="block text-sm font-medium text-gray-700 mb-2">Disponible à partir du</label>
                                <input type="date" 
                                       name="available_from" 
                                       id="available_from" 
                                       value="{{ old('available_from', $candidate->available_from?->format('Y-m-d')) }}"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>

                            <!-- Source -->
                            <div>
                                <label for="source" class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                                <select name="source" id="source" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Sélectionner</option>
                                    @foreach(['Indeed', 'LinkedIn', 'Leboncoin', 'Site web', 'Candidature spontanée', 'Recommandation', 'Salon emploi', 'Autre'] as $src)
                                        <option value="{{ $src }}" {{ old('source', $candidate->source) == $src ? 'selected' : '' }}>{{ $src }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Recruteur assigné -->
                            <div>
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Recruteur assigné</label>
                                <select name="assigned_to" id="assigned_to" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Non assigné</option>
                                    @foreach($recruiters as $recruiter)
                                        <option value="{{ $recruiter->id }}" {{ old('assigned_to', $candidate->assigned_to) == $recruiter->id ? 'selected' : '' }}>
                                            {{ $recruiter->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date entretien -->
                            <div>
                                <label for="interview_date" class="block text-sm font-medium text-gray-700 mb-2">Date d'entretien</label>
                                <input type="date" 
                                       name="interview_date" 
                                       id="interview_date" 
                                       value="{{ old('interview_date', $candidate->interview_date?->format('Y-m-d')) }}"
                                       class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">📄 Documents</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- CV -->
                            <div>
                                <label for="cv" class="block text-sm font-medium text-gray-700 mb-2">CV</label>
                                @if($candidate->cv_path)
                                    <div class="mb-2 p-3 bg-purple-50 rounded-lg flex items-center justify-between">
                                        <span class="text-sm text-purple-700">CV actuel</span>
                                        <a href="{{ asset('storage/' . $candidate->cv_path) }}" target="_blank" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                            Voir le CV
                                        </a>
                                    </div>
                                @endif
                                <input type="file" 
                                       name="cv" 
                                       id="cv" 
                                       accept=".pdf,.doc,.docx"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="mt-1 text-xs text-gray-500">Laisser vide pour conserver l'actuel</p>
                            </div>

                            <!-- Lettre de motivation -->
                            <div>
                                <label for="cover_letter" class="block text-sm font-medium text-gray-700 mb-2">Lettre de motivation</label>
                                @if($candidate->cover_letter_path)
                                    <div class="mb-2 p-3 bg-purple-50 rounded-lg flex items-center justify-between">
                                        <span class="text-sm text-purple-700">Lettre actuelle</span>
                                        <a href="{{ asset('storage/' . $candidate->cover_letter_path) }}" target="_blank" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                            Voir la lettre
                                        </a>
                                    </div>
                                @endif
                                <input type="file" 
                                       name="cover_letter" 
                                       id="cover_letter" 
                                       accept=".pdf,.doc,.docx"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="mt-1 text-xs text-gray-500">Laisser vide pour conserver l'actuelle</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">📝 Notes</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes internes</label>
                            <textarea name="notes" id="notes" rows="3" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">{{ old('notes', $candidate->notes) }}</textarea>
                        </div>
                        <div>
                            <label for="interview_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes d'entretien</label>
                            <textarea name="interview_notes" id="interview_notes" rows="4" placeholder="Compte-rendu de l'entretien, impressions, points forts/faibles..." class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">{{ old('interview_notes', $candidate->interview_notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Évaluation et Statut -->
            <div class="space-y-6">
                <!-- Statut -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">📊 Statut</h2>
                    </div>
                    <div class="p-6">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Statut de la candidature <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                required
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="new" {{ old('status', $candidate->status) == 'new' ? 'selected' : '' }}>🆕 Nouvelle candidature</option>
                            <option value="in_review" {{ old('status', $candidate->status) == 'in_review' ? 'selected' : '' }}>🔍 En cours d'examen</option>
                            <option value="interview" {{ old('status', $candidate->status) == 'interview' ? 'selected' : '' }}>🗓️ Entretien programmé</option>
                            <option value="recruited" {{ old('status', $candidate->status) == 'recruited' ? 'selected' : '' }}>✅ Recruté</option>
                            <option value="integrated" {{ old('status', $candidate->status) == 'integrated' ? 'selected' : '' }}>🎉 Intégré</option>
                            <option value="refused" {{ old('status', $candidate->status) == 'refused' ? 'selected' : '' }}>❌ Refusé</option>
                        </select>

                        <div class="mt-4">
                            <label for="decision_date" class="block text-sm font-medium text-gray-700 mb-2">Date de décision</label>
                            <input type="date" 
                                   name="decision_date" 
                                   id="decision_date" 
                                   value="{{ old('decision_date', $candidate->decision_date?->format('Y-m-d')) }}"
                                   class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>
                </div>

                <!-- Évaluation par étoiles -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 shadow rounded-lg">
                    <div class="p-6 border-b border-yellow-200">
                        <h2 class="text-lg font-semibold text-yellow-900">⭐ Évaluation</h2>
                        <p class="text-sm text-yellow-700 mt-1">Notez le candidat sur chaque critère</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Motivation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Motivation</label>
                            <div class="flex items-center space-x-1" id="rating-motivation">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('motivation', {{ $i }})"
                                            class="star-btn p-1 hover:scale-110 transition-transform"
                                            data-rating="{{ $i }}">
                                        <svg class="w-8 h-8 {{ $i <= old('rating_motivation', $candidate->rating_motivation) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating_motivation" id="input-rating-motivation" value="{{ old('rating_motivation', $candidate->rating_motivation) }}">
                        </div>

                        <!-- Sérieux -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sérieux</label>
                            <div class="flex items-center space-x-1" id="rating-seriousness">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('seriousness', {{ $i }})"
                                            class="star-btn p-1 hover:scale-110 transition-transform"
                                            data-rating="{{ $i }}">
                                        <svg class="w-8 h-8 {{ $i <= old('rating_seriousness', $candidate->rating_seriousness) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating_seriousness" id="input-rating-seriousness" value="{{ old('rating_seriousness', $candidate->rating_seriousness) }}">
                        </div>

                        <!-- Expérience -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expérience</label>
                            <div class="flex items-center space-x-1" id="rating-experience">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('experience', {{ $i }})"
                                            class="star-btn p-1 hover:scale-110 transition-transform"
                                            data-rating="{{ $i }}">
                                        <svg class="w-8 h-8 {{ $i <= old('rating_experience', $candidate->rating_experience) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating_experience" id="input-rating-experience" value="{{ old('rating_experience', $candidate->rating_experience) }}">
                        </div>

                        <!-- Compétences commerciales -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Compétences commerciales</label>
                            <div class="flex items-center space-x-1" id="rating-commercial_skills">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('commercial_skills', {{ $i }})"
                                            class="star-btn p-1 hover:scale-110 transition-transform"
                                            data-rating="{{ $i }}">
                                        <svg class="w-8 h-8 {{ $i <= old('rating_commercial_skills', $candidate->rating_commercial_skills) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating_commercial_skills" id="input-rating-commercial_skills" value="{{ old('rating_commercial_skills', $candidate->rating_commercial_skills) }}">
                        </div>

                        <!-- Moyenne -->
                        @if($candidate->average_rating)
                        <div class="pt-4 border-t border-yellow-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Moyenne</span>
                                <span class="text-2xl font-bold text-yellow-600">{{ $candidate->average_rating }}/5</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex flex-col space-y-3">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('recruitment.show', $candidate) }}" 
                       class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function setRating(type, value) {
    // Mettre à jour la valeur cachée
    document.getElementById('input-rating-' + type).value = value;
    
    // Mettre à jour les étoiles visuellement
    const container = document.getElementById('rating-' + type);
    const stars = container.querySelectorAll('svg');
    
    stars.forEach((star, index) => {
        if (index < value) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}
</script>
@endsection