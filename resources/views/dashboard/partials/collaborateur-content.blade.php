<!-- Missions récentes du collaborateur -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            📁 Mes missions récentes
        </h2>
    </div>
    <div class="p-6">
        @if(isset($recent_missions) && $recent_missions->count() > 0)
            <div class="space-y-4">
                @foreach($recent_missions as $mission)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 truncate">{{ $mission->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($mission->description, 100) }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-500 space-x-4">
                                    <span>Créé par {{ $mission->creator->full_name }}</span>
                                    @if($mission->due_date)
                                        <span>Échéance: {{ $mission->due_date->format('d/m/Y') }}</span>
                                    @endif
                                    @if($mission->revenue)
                                        <span>CA: {{ number_format($mission->revenue, 0, ',', ' ') }}€</span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4 flex flex-col items-end space-y-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $mission->status_color === 'green' ? 'bg-green-100 text-green-800' : ($mission->status_color === 'red' ? 'bg-red-100 text-red-800' : ($mission->status_color === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ $mission->status_label }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full {{ $mission->priority_color === 'red' ? 'bg-red-100 text-red-800' : ($mission->priority_color === 'orange' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $mission->priority_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('missions.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Voir toutes mes missions →
                </a>
            </div>
        @else
            <div class="text-center py-8">
                <span class="text-4xl">📝</span>
                <p class="text-gray-500 mt-2">Aucune mission récente</p>
                <p class="text-sm text-gray-400">Vos nouvelles missions apparaîtront ici</p>
            </div>
        @endif
    </div>
</div>

<!-- NOUVEAU : Section Formations du collaborateur -->
<div class="bg-white shadow rounded-lg">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                📚 Mes formations et demandes
            </h2>
            <a href="{{ route('formations.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                Catalogue formations →
            </a>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- KPI Formations personnels -->
            <div class="space-y-4">
                <h3 class="font-medium text-gray-900 flex items-center">
                    📊 Mon parcours formation
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-900">{{ $kpis['formations_terminees'] ?? 0 }}</div>
                            <div class="text-sm text-blue-600">Formations terminées</div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-900">{{ $kpis['heures_formation_annee'] ?? 0 }}h</div>
                            <div class="text-sm text-purple-600">Cette année</div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-yellow-800">Demandes en attente</div>
                            <div class="text-sm text-yellow-600">En cours de validation</div>
                        </div>
                        <div class="text-2xl font-bold text-yellow-900">{{ $kpis['demandes_formation_attente'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Formations récentes ou en cours -->
            <div class="space-y-4">
                <h3 class="font-medium text-gray-900 flex items-center">
                    🎓 Activité récente
                </h3>

                @if(isset($recent_formations) && $recent_formations->count() > 0)
                    <div class="space-y-3">
                        @foreach($recent_formations as $formationRequest)
                            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="font-medium text-sm text-gray-900">{{ $formationRequest->formation->title }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $formationRequest->formation->duration_hours }}h • {{ $formationRequest->formation->format_label }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Demandé le {{ $formationRequest->requested_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $formationRequest->status_color === 'green' ? 'bg-green-100 text-green-800' : ($formationRequest->status_color === 'yellow' ? 'bg-yellow-100 text-yellow-800' : ($formationRequest->status_color === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                            {{ $formationRequest->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('formations.my-requests') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Voir toutes mes demandes →
                        </a>
                    </div>
                @else
                    <div class="text-center py-6 bg-gray-50 rounded-lg">
                        <span class="text-3xl">📚</span>
                        <p class="text-gray-500 mt-2 text-sm">Aucune formation récente</p>
                        <p class="text-xs text-gray-400">Explorez le catalogue pour découvrir de nouvelles formations</p>
                    </div>
                @endif

                <!-- Action rapide -->
                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                    <div class="text-center">
                        <a href="{{ route('formations.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <span class="mr-2">➕</span>
                            Parcourir le catalogue
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>