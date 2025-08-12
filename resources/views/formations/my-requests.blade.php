@extends('layouts.app')

@section('title', 'Mes formations - Intranet')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Mes demandes de formation
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Suivi de votre parcours de formation personnel selon le cahier des charges
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('formations.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Catalogue formations
                    </a>
                </div>
            </div>

            <!-- KPI personnels -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-gray-50 overflow-hidden rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📋</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-500">Total demandes</div>
                            <div class="text-xl font-semibold text-gray-900">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 overflow-hidden rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">⏳</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-yellow-600">En attente</div>
                            <div class="text-xl font-semibold text-yellow-900">{{ $stats['pending'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 overflow-hidden rounded-lg p-4 border border-green-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-green-600">Approuvées</div>
                            <div class="text-xl font-semibold text-green-900">{{ $stats['approved'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 overflow-hidden rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🎓</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-blue-600">Terminées</div>
                            <div class="text-xl font-semibold text-blue-900">{{ $stats['completed'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heures de formation -->
            @if($stats['hours_total'] > 0)
                <div class="mt-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium">📚 Parcours de formation</h3>
                            <p class="text-indigo-100">Total des heures de formation suivies</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">{{ $stats['hours_total'] }}h</div>
                            <div class="text-sm text-indigo-200">cette année</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Liste des demandes -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            @if($requests->count() > 0)
                <div class="space-y-6">
                    @foreach($requests as $request)
                        <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                                <a href="{{ route('formations.show', $request->formation) }}">
                                                    {{ $request->formation->title }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($request->formation->description, 120) }}</p>

                                            <div class="flex items-center mt-3 text-sm text-gray-500 space-x-4">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"/>
                                                    </svg>
                                                    {{ $request->formation->category ?? 'Formation' }}
                                                </div>

                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $request->formation->duration_label }}
                                                </div>

                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    Demandé le {{ $request->requested_at->format('d/m/Y') }}
                                                </div>

                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $request->priority === 'haute' ? 'red' : ($request->priority === 'normale' ? 'blue' : 'gray') }}-100 text-{{ $request->priority === 'haute' ? 'red' : ($request->priority === 'normale' ? 'blue' : 'gray') }}-800">
                                                        {{ $request->priority_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ml-6 flex flex-col items-end space-y-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-800">
                                                {{ $request->status_label }}
                                            </span>

                                            @if($request->getDaysWaiting() > 7 && $request->isPending())
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $request->getDaysWaiting() }} jours
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Motivation -->
                                    @if($request->motivation)
                                        <div class="mt-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-1">💭 Ma motivation :</h4>
                                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ Str::limit($request->motivation, 200) }}</p>
                                        </div>
                                    @endif

                                    <!-- Commentaires manager -->
                                    @if($request->manager_comments)
                                        <div class="mt-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-1">
                                                💬 Commentaires 
                                                @if($request->approver)
                                                    de {{ $request->approver->full_name }}
                                                @endif
                                                :
                                            </h4>
                                            <p class="text-sm text-gray-600 bg-blue-50 p-3 rounded-lg border border-blue-200">{{ $request->manager_comments }}</p>
                                        </div>
                                    @endif

                                    <!-- Informations de completion -->
                                    @if($request->isCompleted())
                                        <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                                            <div class="flex items-start space-x-3">
                                                <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-medium text-green-800">Formation terminée</h4>
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2 text-sm">
                                                        <div>
                                                            <span class="text-green-700">Terminé le :</span>
                                                            <span class="font-medium">{{ $request->completed_at->format('d/m/Y') }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-green-700">Heures suivies :</span>
                                                            <span class="font-medium">{{ $request->hours_completed }}h</span>
                                                        </div>
                                                        @if($request->rating)
                                                            <div>
                                                                <span class="text-green-700">Ma note :</span>
                                                                <div class="flex items-center">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <svg class="w-4 h-4 {{ $i <= $request->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                        </svg>
                                                                    @endfor
                                                                    <span class="ml-1 text-green-800">({{ $request->rating }}/5)</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($request->feedback)
                                                        <div class="mt-2">
                                                            <span class="text-green-700">Mon feedback :</span>
                                                            <p class="text-green-800 italic">{{ $request->feedback }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('formations.show', $request->formation) }}" 
                                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-full hover:bg-indigo-100">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Voir la formation
                                            </a>
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            @if($request->approved_at)
                                                {{ $request->isApproved() ? 'Approuvé' : 'Traité' }} le {{ $request->approved_at->format('d/m/Y') }}
                                                @if($request->approver)
                                                    par {{ $request->approver->full_name }}
                                                @endif
                                            @else
                                                En attente depuis {{ $request->getDaysWaiting() }} jour(s)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="mt-8">
                        {{ $requests->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune demande de formation</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Commencez votre parcours de formation en explorant notre catalogue
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('formations.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Découvrir les formations
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection