@extends('layouts.app')

@section('title', 'Récapitulatif URSSAF - ' . $data['period_label'])

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Récapitulatif URSSAF</h1>
                <p class="mt-1 text-sm text-gray-500">Période : {{ $data['period_label'] }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('urssaf.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    ← Retour
                </a>
                
                {{-- Export PDF (GET) --}}
                <a href="{{ route('urssaf.pdf', [
                    'period_type' => $data['period_type'],
                    'year' => request('year'),
                    'month' => request('month'),
                    'quarter' => request('quarter'),
                ]) }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Télécharger PDF
                </a>
                
                {{-- Export Excel (GET) --}}
                <a href="{{ route('urssaf.excel', [
                    'period_type' => $data['period_type'],
                    'year' => request('year'),
                    'month' => request('month'),
                    'quarter' => request('quarter'),
                ]) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Télécharger Excel
                </a>
            </div>
        </div>

        {{-- Informations déclarant --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">👤 Informations déclarant</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Nom</p>
                    <p class="font-medium text-gray-900">{{ $data['user_name'] }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-medium text-gray-900">{{ $data['user_email'] }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Téléphone</p>
                    <p class="font-medium text-gray-900">{{ $data['user_phone'] }}</p>
                </div>
                <div>
                    <p class="text-gray-500">SIRET</p>
                    <p class="font-medium text-gray-900">{{ $data['user_siret'] }}</p>
                </div>
            </div>
        </div>

        {{-- Résumé des revenus --}}
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-indigo-900 mb-4">💰 Résumé des revenus encaissés</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Factures payées</p>
                    <p class="text-3xl font-bold text-indigo-900">{{ $data['invoice_count'] }}</p>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total HT</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($data['total_ht'], 2, ',', ' ') }} €</p>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">TVA collectée</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($data['total_tva'], 2, ',', ' ') }} €</p>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total TTC</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($data['total_ttc'], 2, ',', ' ') }} €</p>
                </div>
            </div>

            {{-- Montant à déclarer --}}
            <div class="mt-6 p-4 bg-indigo-100 rounded-lg text-center">
                <p class="text-sm text-indigo-700 mb-1">Montant à déclarer à l'URSSAF (CA HT)</p>
                <p class="text-4xl font-bold text-indigo-900">{{ number_format($data['total_ht'], 2, ',', ' ') }} €</p>
            </div>
        </div>

        {{-- Ventilation par type d'activité --}}
        @if(isset($data['by_type']))
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📈 Ventilation par type d'activité</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Transaction --}}
                    <div class="border-l-4 border-blue-500 bg-blue-50 rounded-r-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-blue-600 text-xl">🏠</span>
                            <span class="font-semibold text-blue-900">Transaction</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Factures</span>
                                <span class="font-medium text-gray-900">{{ $data['by_type']['transaction']['invoice_count'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">CA HT</span>
                                <span class="font-bold text-blue-700">{{ number_format($data['by_type']['transaction']['total_ht'] ?? 0, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="border-l-4 border-green-500 bg-green-50 rounded-r-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-green-600 text-xl">🔑</span>
                            <span class="font-semibold text-green-900">Location</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Factures</span>
                                <span class="font-medium text-gray-900">{{ $data['by_type']['location']['invoice_count'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">CA HT</span>
                                <span class="font-bold text-green-700">{{ number_format($data['by_type']['location']['total_ht'] ?? 0, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                    </div>

                    {{-- Syndic --}}
                    <div class="border-l-4 border-purple-500 bg-purple-50 rounded-r-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-purple-600 text-xl">🏢</span>
                            <span class="font-semibold text-purple-900">Syndic</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Factures</span>
                                <span class="font-medium text-gray-900">{{ $data['by_type']['syndic']['invoice_count'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">CA HT</span>
                                <span class="font-bold text-purple-700">{{ number_format($data['by_type']['syndic']['total_ht'] ?? 0, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                    </div>

                    {{-- Autres --}}
                    <div class="border-l-4 border-gray-400 bg-gray-50 rounded-r-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-gray-600 text-xl">📋</span>
                            <span class="font-semibold text-gray-900">Autres</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Factures</span>
                                <span class="font-medium text-gray-900">{{ $data['by_type']['autres']['invoice_count'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">CA HT</span>
                                <span class="font-bold text-gray-700">{{ number_format($data['by_type']['autres']['total_ht'] ?? 0, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Barre de répartition --}}
                @if($data['total_ht'] > 0)
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Répartition du CA HT</h3>
                        <div class="flex h-8 rounded-full overflow-hidden bg-gray-200">
                            @php
                                $transactionPct = ($data['by_type']['transaction']['total_ht'] ?? 0) / $data['total_ht'] * 100;
                                $locationPct = ($data['by_type']['location']['total_ht'] ?? 0) / $data['total_ht'] * 100;
                                $syndicPct = ($data['by_type']['syndic']['total_ht'] ?? 0) / $data['total_ht'] * 100;
                                $autresPct = ($data['by_type']['autres']['total_ht'] ?? 0) / $data['total_ht'] * 100;
                            @endphp
                            @if($transactionPct > 0)
                                <div class="bg-blue-500 h-full flex items-center justify-center text-white text-xs font-medium" 
                                     style="width: {{ $transactionPct }}%">
                                    @if($transactionPct > 10) {{ number_format($transactionPct, 0) }}% @endif
                                </div>
                            @endif
                            @if($locationPct > 0)
                                <div class="bg-green-500 h-full flex items-center justify-center text-white text-xs font-medium" 
                                     style="width: {{ $locationPct }}%">
                                    @if($locationPct > 10) {{ number_format($locationPct, 0) }}% @endif
                                </div>
                            @endif
                            @if($syndicPct > 0)
                                <div class="bg-purple-500 h-full flex items-center justify-center text-white text-xs font-medium" 
                                     style="width: {{ $syndicPct }}%">
                                    @if($syndicPct > 10) {{ number_format($syndicPct, 0) }}% @endif
                                </div>
                            @endif
                            @if($autresPct > 0)
                                <div class="bg-gray-400 h-full flex items-center justify-center text-white text-xs font-medium" 
                                     style="width: {{ $autresPct }}%">
                                    @if($autresPct > 10) {{ number_format($autresPct, 0) }}% @endif
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-4 mt-3 text-xs">
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                                Transaction ({{ number_format($transactionPct, 1) }}%)
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                Location ({{ number_format($locationPct, 1) }}%)
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                                Syndic ({{ number_format($syndicPct, 1) }}%)
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                                Autres ({{ number_format($autresPct, 1) }}%)
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Détail des factures --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">📋 Détail des factures payées</h2>
            </div>

            @if(count($data['invoices']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N° Facture</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Date paiement</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Montant HT</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">TVA</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Montant TTC</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data['invoices'] as $invoice)
                                @php
                                    $typeColor = match($invoice['revenue_type'] ?? 'autres') {
                                        'transaction' => 'blue',
                                        'location' => 'green',
                                        'syndic' => 'purple',
                                        default => 'gray',
                                    };
                                    $typeIcon = match($invoice['revenue_type'] ?? 'autres') {
                                        'transaction' => '🏠',
                                        'location' => '🔑',
                                        'syndic' => '🏢',
                                        default => '📋',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-medium text-indigo-600">{{ $invoice['invoice_number'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $invoice['client_name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-{{ $typeColor }}-100 text-{{ $typeColor }}-800">
                                            {{ $typeIcon }} {{ $invoice['revenue_type_label'] ?? ucfirst($invoice['revenue_type'] ?? 'Autres') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                        {{ $invoice['paid_at'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        {{ number_format($invoice['total_ht'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600">
                                        {{ number_format($invoice['total_tva'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                        {{ number_format($invoice['total_ttc'], 2, ',', ' ') }} €
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-indigo-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm font-bold text-gray-900 text-right">TOTAL</td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                                    {{ number_format($data['total_ht'], 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                                    {{ number_format($data['total_tva'], 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-indigo-600">
                                    {{ number_format($data['total_ttc'], 2, ',', ' ') }} €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <span class="text-6xl">📭</span>
                    <p class="mt-4 text-gray-500">Aucune facture payée sur cette période.</p>
                </div>
            @endif
        </div>

        {{-- Rappel important --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <span class="text-2xl">⚠️</span>
                <div class="text-sm text-yellow-800">
                    <p class="font-semibold mb-1">Rappel important</p>
                    <p>
                        Ce récapitulatif est fourni à titre indicatif pour faciliter vos déclarations URSSAF. 
                        Les montants affichés correspondent aux factures effectivement encaissées (statut "Payée") sur la période sélectionnée.
                        Il vous appartient de vérifier l'exactitude des montants avant de les déclarer.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection