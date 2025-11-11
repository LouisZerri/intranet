<!-- collaborateur-content.blade.php -->

<!-- Section Devis & Factures récents -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Devis récents -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    📄 Mes devis récents
                </h2>
                <a href="{{ route('quotes.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Voir tout →
                </a>
            </div>
        </div>
        <div class="p-6">
            @if(isset($recent_quotes) && $recent_quotes->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_quotes->take(5) as $quote)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-sm text-gray-900">{{ $quote->quote_number }}</div>
                                    <div class="text-xs text-gray-600 mt-1">{{ $quote->client->display_name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ number_format($quote->total_ht, 2, ',', ' ') }}€ HT
                                    </div>
                                </div>
                                <div class="ml-3 flex flex-col items-end">
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                        {{ $quote->status_label }}
                                    </span>
                                    <span class="text-xs text-gray-400 mt-1">{{ $quote->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <span class="text-4xl">📄</span>
                    <p class="text-gray-500 mt-2 text-sm">Aucun devis récent</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Factures récentes -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    💰 Mes factures récentes
                </h2>
                <a href="{{ route('invoices.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Voir tout →
                </a>
            </div>
        </div>
        <div class="p-6">
            @if(isset($recent_invoices) && $recent_invoices->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_invoices->take(5) as $invoice)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-sm text-gray-900">{{ $invoice->invoice_number }}</div>
                                    <div class="text-xs text-gray-600 mt-1">{{ $invoice->client->display_name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ number_format($invoice->total_ht, 2, ',', ' ') }}€ HT
                                        @if($invoice->due_date)
                                            • Échéance: {{ $invoice->due_date->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-3 flex flex-col items-end">
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                                        {{ $invoice->status_label }}
                                    </span>
                                    <span class="text-xs text-gray-400 mt-1">{{ $invoice->issued_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <span class="text-4xl">💰</span>
                    <p class="text-gray-500 mt-2 text-sm">Aucune facture récente</p>
                </div>
            @endif
        </div>
    </div>
</div>

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

<!-- Section Formations et Communication -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- Section Formations -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    📚 Mes formations
                </h2>
                <a href="{{ route('formations.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Catalogue →
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
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

                @if(isset($recent_formations) && $recent_formations->count() > 0)
                    <div class="space-y-3 mt-4">
                        @foreach($recent_formations->take(2) as $formationRequest)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="font-medium text-sm text-gray-900">{{ $formationRequest->formation->title }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $formationRequest->formation->duration_hours }}h
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $formationRequest->status_color === 'green' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $formationRequest->status_label }}
                                    </span>
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
                    <div class="text-center py-4 bg-gray-50 rounded-lg">
                        <span class="text-3xl">📚</span>
                        <p class="text-gray-500 mt-2 text-sm">Aucune formation récente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Communication -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    📦 Mes commandes
                </h2>
                <a href="{{ route('communication.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Catalogue →
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-900">{{ $kpis['commandes_ce_mois'] ?? 0 }}</div>
                            <div class="text-sm text-green-600">Ce mois</div>
                        </div>
                    </div>
                    
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-900">{{ number_format($kpis['montant_commandes_mois'] ?? 0, 0, ',', ' ') }}€</div>
                            <div class="text-sm text-indigo-600">Montant total</div>
                        </div>
                    </div>
                </div>

                @if(isset($recent_orders) && $recent_orders->count() > 0)
                    <div class="space-y-3 mt-4">
                        @foreach($recent_orders->take(2) as $order)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="font-medium text-sm text-gray-900">{{ $order->order_number }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $order->items->count() }} article(s) • {{ number_format($order->total_amount, 2, ',', ' ') }}€
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $order->ordered_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('communication.my-orders') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Voir toutes mes commandes →
                        </a>
                    </div>
                @else
                    <div class="text-center py-4 bg-gray-50 rounded-lg">
                        <span class="text-3xl">📦</span>
                        <p class="text-gray-500 mt-2 text-sm">Aucune commande récente</p>
                    </div>
                @endif

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mt-4">
                    <div class="text-center">
                        <a href="{{ route('communication.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            <span class="mr-2">🛒</span>
                            Commander des produits
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>