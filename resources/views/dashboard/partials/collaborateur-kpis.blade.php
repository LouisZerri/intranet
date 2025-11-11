<!-- collaborateur-kpis.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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

    <!-- Heures de formation cette année -->
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
</div>

<!-- KPI Commerciaux -->
<div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4 text-white">💼 Mon activité commerciale ce mois</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Devis ce mois -->
        <div class="bg-white rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-gray-600">Devis créés</div>
            <div class="text-3xl font-bold mt-1 text-green-600">{{ $kpis['devis_ce_mois'] ?? 0 }}</div>
        </div>

        <!-- CA facturé -->
        <div class="bg-white rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-gray-600">CA facturé</div>
            <div class="text-3xl font-bold mt-1 text-green-600">{{ number_format($kpis['ca_facture_mois'] ?? 0, 0, ',', ' ') }}€</div>
        </div>

        <!-- CA payé -->
        <div class="bg-white rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-gray-600">CA encaissé</div>
            <div class="text-3xl font-bold mt-1 text-green-600">{{ number_format($kpis['ca_paye_mois'] ?? 0, 0, ',', ' ') }}€</div>
        </div>

        <!-- CA en attente -->
        <div class="bg-white rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-gray-600">CA en attente</div>
            <div class="text-3xl font-bold mt-1 text-orange-600">{{ number_format($kpis['ca_en_attente'] ?? 0, 0, ',', ' ') }}€</div>
        </div>
    </div>
</div>

<!-- Actions rapides commerciales -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('quotes.create') }}" class="bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 transition-colors">
        <div class="flex items-center">
            <span class="text-3xl mr-3">📄</span>
            <div>
                <div class="font-semibold text-blue-900">Créer un devis</div>
                <div class="text-sm text-blue-600">Nouveau devis client</div>
            </div>
        </div>
    </a>

    <a href="{{ route('invoices.index') }}" class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 transition-colors">
        <div class="flex items-center">
            <span class="text-3xl mr-3">💰</span>
            <div>
                <div class="font-semibold text-green-900">Mes factures</div>
                <div class="text-sm text-green-600">Gérer les factures</div>
            </div>
        </div>
    </a>

    <a href="{{ route('urssaf.index') }}" class="bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg p-4 transition-colors">
        <div class="flex items-center">
            <span class="text-3xl mr-3">📊</span>
            <div>
                <div class="font-semibold text-purple-900">Récap URSSAF</div>
                <div class="text-sm text-purple-600">Export mensuel</div>
            </div>
        </div>
    </a>
</div>