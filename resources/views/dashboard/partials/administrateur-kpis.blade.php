<!-- administrateur-kpis.blade.php -->
<div class="space-y-6 mb-6">
    <!-- KPI Globaux -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-3">🛠️ KPI globaux de l'organisation</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Utilisateurs actifs -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">👥</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Utilisateurs actifs</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $kpis['utilisateurs_actifs'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missions ouvertes ce mois -->
            <div class="bg-blue-50 overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📄</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-600 truncate">Missions ce mois</dt>
                                <dd class="text-lg font-medium text-blue-900">{{ $kpis['missions_ouvertes_mois'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CA total missions -->
            <div class="bg-green-50 overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">💰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-600 truncate">CA missions mois</dt>
                                <dd class="text-lg font-medium text-green-900">{{ number_format($kpis['ca_total_mois'], 0, ',', ' ') }}€</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commandes ce mois -->
            <div class="bg-cyan-50 overflow-hidden shadow rounded-lg border border-cyan-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📦</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-cyan-600 truncate">Commandes ce mois</dt>
                                <dd class="text-lg font-medium text-cyan-900">{{ $kpis['commandes_ce_mois'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formations actives -->
            <div class="bg-yellow-50 overflow-hidden shadow rounded-lg border border-yellow-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📚</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-yellow-600 truncate">Formations actives</dt>
                                <dd class="text-lg font-medium text-yellow-900">{{ $kpis['formations_actives'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heures formation délivrées -->
            <div class="bg-indigo-50 overflow-hidden shadow rounded-lg border border-indigo-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🎓</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-indigo-600 truncate">Heures délivrées</dt>
                                <dd class="text-lg font-medium text-indigo-900">{{ $kpis['heures_formation_annee'] ?? 0 }}h</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Commerciaux -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-3">💼 Performance commerciale globale</h3>
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg shadow-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <div class="text-center">
                    <div class="text-white text-sm opacity-90">Devis ce mois</div>
                    <div class="text-white text-3xl font-bold mt-1">{{ $kpis['devis_ce_mois'] ?? 0 }}</div>
                </div>
                <div class="text-center">
                    <div class="text-white text-sm opacity-90">CA facturé</div>
                    <div class="text-white text-3xl font-bold mt-1">{{ number_format($kpis['ca_facture_mois'] ?? 0, 0, ',', ' ') }}€</div>
                </div>
                <div class="text-center">
                    <div class="text-white text-sm opacity-90">CA encaissé</div>
                    <div class="text-white text-3xl font-bold mt-1">{{ number_format($kpis['ca_paye_mois'] ?? 0, 0, ',', ' ') }}€</div>
                </div>
                <div class="text-center">
                    <div class="text-white text-sm opacity-90">CA en attente</div>
                    <div class="text-white text-3xl font-bold mt-1">{{ number_format($kpis['ca_en_attente'] ?? 0, 0, ',', ' ') }}€</div>
                </div>
                <div class="text-center">
                    <div class="text-white text-sm opacity-90">Taux transformation</div>
                    <div class="text-white text-3xl font-bold mt-1">{{ $kpis['taux_transformation_global'] ?? 0 }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline commercial -->
    @if(isset($pipeline_stats))
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-3">🔄 Pipeline commercial</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="bg-gray-50 overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="p-4">
                    <div class="text-xs font-medium text-gray-500 truncate">Devis brouillon</div>
                    <div class="text-xl font-bold text-gray-900 mt-1">{{ $pipeline_stats['devis_brouillon'] }}</div>
                </div>
            </div>
            <div class="bg-blue-50 overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-4">
                    <div class="text-xs font-medium text-blue-600 truncate">Devis envoyés</div>
                    <div class="text-xl font-bold text-blue-900 mt-1">{{ $pipeline_stats['devis_envoyes'] }}</div>
                </div>
            </div>
            <div class="bg-green-50 overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-4">
                    <div class="text-xs font-medium text-green-600 truncate">Devis acceptés</div>
                    <div class="text-xl font-bold text-green-900 mt-1">{{ $pipeline_stats['devis_acceptes'] }}</div>
                </div>
            </div>
            <div class="bg-gray-50 overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="p-4">
                    <div class="text-xs font-medium text-gray-500 truncate">Factures brouillon</div>
                    <div class="text-xl font-bold text-gray-900 mt-1">{{ $pipeline_stats['factures_brouillon'] }}</div>
                </div>
            </div>
            <div class="bg-yellow-50 overflow-hidden shadow rounded-lg border border-yellow-200">
                <div class="p-4">
                    <div class="text-xs font-medium text-yellow-600 truncate">Factures émises</div>
                    <div class="text-xl font-bold text-yellow-900 mt-1">{{ $pipeline_stats['factures_emises'] }}</div>
                </div>
            </div>
            <div class="bg-green-50 overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-4">
                    <div class="text-xs font-medium text-green-600 truncate">Factures payées</div>
                    <div class="text-xl font-bold text-green-900 mt-1">{{ $pipeline_stats['factures_payees'] }}</div>
                </div>
            </div>
            <div class="bg-red-50 overflow-hidden shadow rounded-lg border border-red-200">
                <div class="p-4">
                    <div class="text-xs font-medium text-red-600 truncate">En retard</div>
                    <div class="text-xl font-bold text-red-900 mt-1">{{ $pipeline_stats['factures_en_retard'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>