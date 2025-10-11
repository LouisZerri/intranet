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

            <!-- CA total -->
            <div class="bg-green-50 overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">💰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-600 truncate">CA total ce mois</dt>
                                <dd class="text-lg font-medium text-green-900">{{ number_format($kpis['ca_total_mois'], 0, ',', ' ') }}€</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NOUVEAU : Commandes ce mois (remplace taux validation) -->
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

    <!-- Graphique/Métriques supplémentaires -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg p-6">
        <h4 class="text-lg font-semibold mb-4">📊 Résumé mensuel</h4>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold">{{ $kpis['missions_ouvertes_mois'] }}</div>
                <div class="text-blue-100 text-sm">Nouvelles missions</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold">{{ number_format($kpis['ca_total_mois'], 0, ',', ' ') }}€</div>
                <div class="text-blue-100 text-sm">Chiffre d'affaires</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold">{{ $kpis['commandes_ce_mois'] ?? 0 }}</div>
                <div class="text-blue-100 text-sm">Commandes passées</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold">{{ number_format($kpis['ca_commandes_mois'] ?? 0, 0, ',', ' ') }}€</div>
                <div class="text-blue-100 text-sm">CA Communication</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold">{{ $kpis['formations_actives'] ?? 0 }}</div>
                <div class="text-blue-100 text-sm">Formations actives</div>
            </div>
        </div>
    </div>
</div>