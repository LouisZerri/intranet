<!-- Performance de l'équipe AVEC formations -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            👥 Performance de mon équipe
        </h2>
    </div>
    <div class="p-6">
        @if(isset($team_performance) && count($team_performance) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collaborateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Missions terminées</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CA ce mois</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">En retard</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux réussite</th>
                            <!-- NOUVELLES COLONNES formations -->
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-500 uppercase tracking-wider">Formation</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($team_performance as $perf)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-medium">
                                            {{ substr($perf['user']->first_name, 0, 1) }}{{ substr($perf['user']->last_name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $perf['user']->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $perf['user']->position }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $perf['missions_terminees'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ number_format($perf['ca_mois'], 0, ',', ' ') }}€
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($perf['missions_en_retard'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $perf['missions_en_retard'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            0
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $perf['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600">{{ number_format($perf['completion_rate'], 1) }}%</span>
                                    </div>
                                </td>
                                <!-- NOUVELLE colonne formations -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="text-center">
                                        <div class="text-sm font-medium text-purple-700">{{ $perf['heures_formation_annee'] ?? 0 }}h</div>
                                        <div class="text-xs text-gray-500">
                                            @if($perf['est_forme_cette_annee'] ?? false)
                                                <span class="text-green-600">✓ Formé</span>
                                            @else
                                                <span class="text-gray-400">Non formé</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <span class="text-4xl">👥</span>
                <p class="text-gray-500 mt-2">Aucun collaborateur dans votre équipe</p>
                <p class="text-sm text-gray-400">Les performances de votre équipe apparaîtront ici</p>
            </div>
        @endif
    </div>
</div>

<!-- Demandes à approuver (internes) -->
@if(isset($pending_requests_to_approve) && $pending_requests_to_approve->count() > 0)
<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            ✋ Demandes internes en attente d'approbation
        </h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($pending_requests_to_approve as $request)
                <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $request->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($request->description, 120) }}</p>
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
                                En attente
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('requests.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                Traiter toutes les demandes →
            </a>
        </div>
    </div>
</div>
@endif

<!-- NOUVEAU : Demandes formations à approuver -->
@if(isset($pending_formation_requests) && $pending_formation_requests->count() > 0)
<div class="bg-white shadow rounded-lg">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            🎓 Demandes de formation en attente d'approbation
        </h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($pending_formation_requests as $formationRequest)
                <div class="border border-purple-200 bg-purple-50 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $formationRequest->formation->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($formationRequest->motivation, 120) }}</p>
                            <div class="flex items-center mt-2 text-xs text-gray-500 space-x-4">
                                <span>Par {{ $formationRequest->user->full_name }}</span>
                                <span>{{ $formationRequest->formation->duration_hours }}h</span>
                                <span>Priorité {{ $formationRequest->priority_label }}</span>
                                <span>{{ $formationRequest->requested_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                Formation
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('formations.manage') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                Traiter toutes les demandes formations →
            </a>
        </div>
    </div>
</div>
@endif