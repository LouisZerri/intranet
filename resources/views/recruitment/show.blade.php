@extends('layouts.app')

@section('title', $candidate->full_name . ' - Recrutement')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center">
                <a href="{{ route('recruitment.index') }}" class="mr-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="h-16 w-16 rounded-full bg-purple-500 flex items-center justify-center text-white text-2xl font-bold mr-4">
                    {{ $candidate->initials }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $candidate->full_name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $candidate->position_applied ?? 'Poste non spécifié' }}</p>
                    <div class="flex items-center mt-2 space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                        @if($candidate->source)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                via {{ $candidate->source }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <a href="{{ route('recruitment.edit', $candidate) }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
                <form action="{{ route('recruitment.destroy', $candidate) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce candidat ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 font-medium rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations de contact -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📞 Informations de contact</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <a href="mailto:{{ $candidate->email }}" class="text-purple-600 hover:text-purple-800 font-medium">{{ $candidate->email }}</a>
                            </div>
                        </div>

                        @if($candidate->phone)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Téléphone</p>
                                <a href="tel:{{ $candidate->phone }}" class="text-gray-900 font-medium">{{ $candidate->phone }}</a>
                            </div>
                        </div>
                        @endif

                        @if($candidate->city || $candidate->department)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Localisation</p>
                                <p class="text-gray-900 font-medium">
                                    {{ $candidate->city }}{{ $candidate->city && $candidate->department ? ', ' : '' }}{{ $candidate->department }}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($candidate->available_from)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Disponible à partir du</p>
                                <p class="text-gray-900 font-medium">{{ $candidate->available_from->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📄 Documents</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($candidate->cv_path)
                        <a href="{{ asset('storage/' . $candidate->cv_path) }}" 
                           target="_blank"
                           class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                            <div class="bg-purple-500 p-3 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-purple-900">CV</p>
                                <p class="text-sm text-purple-600">Cliquer pour voir</p>
                            </div>
                        </a>
                        @else
                        <div class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="bg-gray-300 p-3 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-500">CV</p>
                                <p class="text-sm text-gray-400">Non fourni</p>
                            </div>
                        </div>
                        @endif

                        @if($candidate->cover_letter_path)
                        <a href="{{ asset('storage/' . $candidate->cover_letter_path) }}" 
                           target="_blank"
                           class="flex items-center p-4 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            <div class="bg-indigo-500 p-3 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-indigo-900">Lettre de motivation</p>
                                <p class="text-sm text-indigo-600">Cliquer pour voir</p>
                            </div>
                        </a>
                        @else
                        <div class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="bg-gray-300 p-3 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-500">Lettre de motivation</p>
                                <p class="text-sm text-gray-400">Non fournie</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($candidate->notes || $candidate->interview_notes)
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📝 Notes</h2>
                </div>
                <div class="p-6 space-y-6">
                    @if($candidate->notes)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Notes internes</h3>
                        <div class="p-4 bg-gray-50 rounded-lg text-gray-700 whitespace-pre-line">{{ $candidate->notes }}</div>
                    </div>
                    @endif

                    @if($candidate->interview_notes)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Notes d'entretien</h3>
                        <div class="p-4 bg-blue-50 rounded-lg text-gray-700 whitespace-pre-line">{{ $candidate->interview_notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Évaluation -->
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 shadow rounded-lg">
                <div class="p-6 border-b border-yellow-200">
                    <h2 class="text-lg font-semibold text-yellow-900">⭐ Évaluation</h2>
                </div>
                <div class="p-6 space-y-4">
                    @php
                        $ratings = [
                            ['label' => 'Motivation', 'value' => $candidate->rating_motivation],
                            ['label' => 'Sérieux', 'value' => $candidate->rating_seriousness],
                            ['label' => 'Expérience', 'value' => $candidate->rating_experience],
                            ['label' => 'Compétences commerciales', 'value' => $candidate->rating_commercial_skills],
                        ];
                    @endphp

                    @foreach($ratings as $rating)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $rating['label'] }}</span>
                            <span class="text-sm text-gray-500">{{ $rating['value'] ?? '-' }}/5</span>
                        </div>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $rating['value'] && $i <= $rating['value'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    @endforeach

                    @if($candidate->average_rating)
                    <div class="pt-4 border-t border-yellow-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-700">Moyenne générale</span>
                            <span class="text-2xl font-bold text-yellow-600">{{ $candidate->average_rating }}/5</span>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-500 italic text-center py-4">Non encore évalué</p>
                    @endif
                </div>
            </div>

            <!-- Informations de suivi -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📊 Suivi</h2>
                </div>
                <div class="p-6 space-y-4">
                    @if($candidate->recruiter)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Recruteur assigné</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->recruiter->full_name }}</span>
                    </div>
                    @endif

                    @if($candidate->interview_date)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Date d'entretien</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->interview_date->format('d/m/Y') }}</span>
                    </div>
                    @endif

                    @if($candidate->decision_date)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Date de décision</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->decision_date->format('d/m/Y') }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Candidature reçue</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->created_at->format('d/m/Y') }}</span>
                    </div>

                    @if($candidate->creator)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Ajouté par</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->creator->full_name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Changement rapide de statut -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">🔄 Changer le statut</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('recruitment.update-status', $candidate) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="status" 
                                onchange="this.form.submit()"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="new" {{ $candidate->status == 'new' ? 'selected' : '' }}>🆕 Nouvelle candidature</option>
                            <option value="in_review" {{ $candidate->status == 'in_review' ? 'selected' : '' }}>🔍 En cours d'examen</option>
                            <option value="interview" {{ $candidate->status == 'interview' ? 'selected' : '' }}>🗓️ Entretien programmé</option>
                            <option value="recruited" {{ $candidate->status == 'recruited' ? 'selected' : '' }}>✅ Recruté</option>
                            <option value="integrated" {{ $candidate->status == 'integrated' ? 'selected' : '' }}>🎉 Intégré</option>
                            <option value="refused" {{ $candidate->status == 'refused' ? 'selected' : '' }}>❌ Refusé</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection