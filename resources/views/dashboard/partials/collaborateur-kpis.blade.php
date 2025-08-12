<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
    <!-- Dossiers en cours -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-2xl">🗂️</span>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Dossiers en cours</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $kpis['missions_en_cours'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Dossiers clôturés ce mois -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-2xl">✅</span>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Clôturés ce mois</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $kpis['missions_terminees_mois'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Chiffre d'affaires -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-2xl">📈</span>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">CA ce mois</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($kpis['chiffre_affaires'], 0, ',', ' ') }}€</dd>
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
                        <dt class="text-sm font-medium text-gray-500 truncate">En retard</dt>
                        <dd class="text-lg font-medium {{ $kpis['missions_en_retard'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $kpis['missions_en_retard'] }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- NOUVEAU : Heures de formation cette année -->
    <div class="bg-purple-50 overflow-hidden shadow rounded-lg border border-purple-200">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-2xl">📚</span>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-purple-600 truncate">Heures formation</dt>
                        <dd class="text-lg font-medium text-purple-900">{{ $kpis['heures_formation_annee'] ?? 0 }}h</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- NOUVEAU : Demandes formation en attente -->
    <div class="bg-indigo-50 overflow-hidden shadow rounded-lg border border-indigo-200">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-2xl">🎓</span>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-indigo-600 truncate">Formations en attente</dt>
                        <dd class="text-lg font-medium text-indigo-900">{{ $kpis['demandes_formation_attente'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>