<!-- administrateur-content.blade.php -->

<!-- Top Performers -->
@if(isset($top_performers) && count($top_performers) > 0)
<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            🏆 Top Performers ce mois
        </h2>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collaborateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-emerald-500 uppercase tracking-wider">CA facturé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-blue-500 uppercase tracking-wider">Devis créés</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($top_performers as $index => $performer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                @if($index == 0)
                                    <span class="text-2xl">🥇</span>
                                @elseif($index == 1)
                                    <span class="text-2xl">🥈</span>
                                @elseif($index == 2)
                                    <span class="text-2xl">🥉</span>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-medium">
                                        {{ substr($performer['user']->first_name, 0, 1) }}{{ substr($performer['user']->last_name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $performer['user']->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $performer['user']->position }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                                {{ number_format($performer['ca_facture'], 0, ',', ' ') }}€
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $performer['devis_count'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Devis & Factures récents -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Devis récents -->
    @if(isset($recent_quotes) && $recent_quotes->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                📄 Devis récents
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($recent_quotes->take(5) as $quote)
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="font-medium text-sm text-gray-900">{{ $quote->quote_number }}</div>
                                <div class="text-xs text-gray-600 mt-1">{{ $quote->client->display_name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Par {{ $quote->user->full_name }} • {{ number_format($quote->total_ht, 2, ',', ' ') }}€ HT
                                </div>
                            </div>
                            <div class="ml-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                    {{ $quote->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('quotes.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Voir tous les devis →
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Factures en retard -->
    @if(isset($overdue_invoices) && $overdue_invoices->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                ⚠️ Factures en retard
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($overdue_invoices->take(5) as $invoice)
                    <div class="border border-red-200 bg-red-50 rounded-lg p-3">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="font-medium text-sm text-gray-900">{{ $invoice->invoice_number }}</div>
                                <div class="text-xs text-gray-600 mt-1">{{ $invoice->client->display_name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Par {{ $invoice->user->full_name }} • {{ number_format($invoice->total_ht, 2, ',', ' ') }}€ HT
                                    • Échéance: {{ $invoice->due_date->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    En retard
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('invoices.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Voir toutes les factures en retard →
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Statistiques par localisation -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            🗺️ Statistiques par localisation
        </h2>
    </div>
    <div class="p-6">
        @if(isset($localisation_stats) && count($localisation_stats) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($localisation_stats as $loc)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <h3 class="font-semibold text-gray-900 mb-3">{{ $loc['name'] }}</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Collaborateurs</span>
                                <span class="font-medium">{{ $loc['users'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Missions ce mois</span>
                                <span class="font-medium">{{ $loc['missions_mois'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">CA missions</span>
                                <span class="font-medium text-green-600">{{ number_format($loc['ca_mois'], 0, ',', ' ') }}€</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-emerald-600">CA facturé</span>
                                <span class="font-medium text-emerald-700">{{ number_format($loc['ca_facture_mois'] ?? 0, 0, ',', ' ') }}€</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-600">Heures formation</span>
                                <span class="font-medium text-purple-700">{{ $loc['heures_formation_annee'] ?? 0 }}h</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-indigo-600">Taux formés</span>
                                <span class="font-medium text-indigo-700">{{ $loc['taux_formes_annee'] ?? 0 }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-600">Commandes ce mois</span>
                                <span class="font-medium text-blue-700">{{ $loc['commandes_mois'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <span class="text-4xl">🗺️</span>
                <p class="text-gray-500 mt-2">Aucune donnée de localisation</p>
            </div>
        @endif
    </div>
</div>

<!-- Activités récentes -->
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

<!-- Formations populaires -->
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

<!-- Commandes récentes -->
@if(isset($recent_orders) && $recent_orders->count() > 0)
<div class="bg-white shadow rounded-lg">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            📦 Commandes récentes
        </h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($recent_orders->take(5) as $order)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $order->items->count() }} article(s) • {{ number_format($order->total_amount, 2, ',', ' ') }}€
                            </p>
                            <div class="flex items-center mt-2 text-xs text-gray-500 space-x-4">
                                <span>Par {{ $order->user->full_name }}</span>
                                <span>{{ $order->ordered_at->format('d/m/Y à H:i') }}</span>
                                <span>{{ $order->user->department }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('admin.communication.orders') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                Voir toutes les commandes ({{ $recent_orders->count() }}) →
            </a>
        </div>
    </div>
</div>
@endif