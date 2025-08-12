<!-- KPI Manager AVEC formations intégrées -->
<div class="space-y-6 mb-6">
    <!-- KPI Personnels -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-3">📊 Mes KPI personnels</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Missions en cours -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🗂️</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mes missions en cours</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $kpis['missions_en_cours'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missions terminées -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Terminées ce mois</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $kpis['missions_terminees_mois'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CA Personnel -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">💰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mon CA ce mois</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($kpis['chiffre_affaires_perso'], 0, ',', ' ') }}€</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missions en retard -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🔔</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mes retards</dt>
                                <dd class="text-lg font-medium {{ $kpis['missions_en_retard'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $kpis['missions_en_retard'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NOUVEAU : Heures formation personnelles -->
            <div class="bg-purple-50 overflow-hidden shadow rounded-lg border border-purple-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📚</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-purple-600 truncate">Mes heures formation</dt>
                                <dd class="text-lg font-medium text-purple-900">{{ $kpis['heures_formation_annee'] ?? 0 }}h</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Équipe -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-3">👥 KPI de mon équipe</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- CA Équipe -->
            <div class="bg-blue-50 overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📈</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-600 truncate">CA équipe ce mois</dt>
                                <dd class="text-lg font-medium text-blue-900">{{ number_format($kpis['chiffre_affaires_equipe'], 0, ',', ' ') }}€</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Taille équipe -->
            <div class="bg-green-50 overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">👤</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-600 truncate">Collaborateurs</dt>
                                <dd class="text-lg font-medium text-green-900">{{ $kpis['equipe_size'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missions équipe en retard -->
            <div class="bg-orange-50 overflow-hidden shadow rounded-lg border border-orange-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">⌛</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-orange-600 truncate">Équipe en retard</dt>
                                <dd class="text-lg font-medium {{ $kpis['missions_equipe_en_retard'] > 0 ? 'text-red-600' : 'text-orange-900' }}">
                                    {{ $kpis['missions_equipe_en_retard'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demandes équipe -->
            <div class="bg-purple-50 overflow-hidden shadow rounded-lg border border-purple-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🧾</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-purple-600 truncate">Demandes à traiter</dt>
                                <dd class="text-lg font-medium text-purple-900">{{ $kpis['demandes_equipe_en_attente'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NOUVEAU : Taux collaborateurs formés -->
            <div class="bg-indigo-50 overflow-hidden shadow rounded-lg border border-indigo-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🎓</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-indigo-600 truncate">Équipe formée</dt>
                                <dd class="text-lg font-medium text-indigo-900">{{ $kpis['taux_collaborateurs_formes'] ?? 0 }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 overflow-hidden shadow rounded-lg border border-yellow-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📝</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-yellow-600 truncate">Formations à approuver</dt>
                                <dd class="text-lg font-medium text-yellow-900">{{ $kpis['formations_equipe_en_attente'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>