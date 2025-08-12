<!-- Statistiques par département AVEC formations -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            🏢 Statistiques par département
        </h2>
    </div>
    <div class="p-6">
        @if(isset($department_stats) && count($department_stats) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($department_stats as $dept)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <h3 class="font-semibold text-gray-900 mb-3">{{ $dept['name'] }}</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Collaborateurs</span>
                                <span class="font-medium">{{ $dept['users'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Missions ce mois</span>
                                <span class="font-medium">{{ $dept['missions_mois'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">CA ce mois</span>
                                <span class="font-medium text-green-600">{{ number_format($dept['ca_mois'], 0, ',', ' ') }}€</span>
                            </div>
                            <!-- NOUVEAU : Stats formations -->
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-600">Heures formation</span>
                                <span class="font-medium text-purple-700">{{ $dept['heures_formation_annee'] ?? 0 }}h</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-indigo-600">Taux formés</span>
                                <span class="font-medium text-indigo-700">{{ $dept['taux_formes_annee'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <span class="text-4xl">🏢</span>
                <p class="text-gray-500 mt-2">Aucune donnée départementale</p>
            </div>
        @endif
    </div>
</div>

<!-- Activités récentes AVEC formations -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            🔄 Activités récentes
        </h2>
    </div>
    <div class="p-6">
        @if(isset($recent_activities) && count($recent_activities) > 0)
            <div class="space-y-4">
                @foreach($recent_activities as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span class="text-xl">{{ $activity['icon'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['date']->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800">
                                {{ $activity['type'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <span class="text-4xl">📊</span>
                <p class="text-gray-500 mt-2">Aucune activité récente</p>
            </div>
        @endif
    </div>
</div>

<!-- Formations populaires - NOUVEAU BLOC -->
@if(isset($popular_formations) && $popular_formations->count() > 0)
<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            📚 Formations populaires
        </h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($popular_formations as $formation)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $formation->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($formation->description, 100) }}</p>
                            <div class="flex items-center mt-2 text-xs text-gray-500 space-x-4">
                                <span>{{ $formation->category ?? 'Non classé' }}</span>
                                <span>{{ $formation->duration_hours }}h</span>
                                <span>{{ $formation->format_label }}</span>
                            </div>
                        </div>
                        <div class="ml-4 text-center">
                            <div class="text-lg font-bold text-purple-600">{{ $formation->participants_count }}</div>
                            <div class="text-xs text-gray-500">participants</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('admin.formations.stats') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                Voir toutes les statistiques formations →
            </a>
        </div>
    </div>
</div>
@endif

<!-- Demandes en attente globales -->
@if(isset($pending_requests) && $pending_requests->count() > 0)
<div class="bg-white shadow rounded-lg">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            📋 Demandes en attente de traitement
        </h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($pending_requests->take(5) as $request)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $request->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($request->description, 100) }}</p>
                            <div class="flex items-center mt-2 text-xs text-gray-500 space-x-4">
                                <span>Par {{ $request->requester->full_name }}</span>
                                <span>{{ $request->type_label }}</span>
                                <span>{{ $request->requested_at->format('d/m/Y') }}</span>
                                @if($request->estimated_cost)
                                    <span>{{ number_format($request->estimated_cost, 2) }}€</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                {{ $request->getDaysWaiting() }} jour(s)
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('requests.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                Voir toutes les demandes ({{ $pending_requests->count() }}) →
            </a>
        </div>
    </div>
</div>
@endif