@extends('layouts.app')

@section('title', 'URSSAF - Tous les mandataires')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Récapitulatif URSSAF - Tous les mandataires</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Vue consolidée du chiffre d'affaires de 
                    @if(Auth::user()->isAdministrateur())
                        tous les mandataires
                    @else
                        vos collaborateurs
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('urssaf.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    ← Retour
                </a>
            </div>
        </div>

        {{-- Formulaire de sélection de période --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📅 Sélectionner une période</h2>

            <form method="GET" action="{{ route('urssaf.all-mandataires') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Type de période --}}
                    <div>
                        <label for="period_type" class="block text-sm font-medium text-gray-700 mb-2">Type de période</label>
                        <select name="period_type" id="period_type"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            onchange="updatePeriodFields()">
                            <option value="year" {{ ($request->period_type ?? 'year') === 'year' ? 'selected' : '' }}>Annuel</option>
                            <option value="quarter" {{ ($request->period_type ?? '') === 'quarter' ? 'selected' : '' }}>Trimestriel</option>
                            <option value="month" {{ ($request->period_type ?? '') === 'month' ? 'selected' : '' }}>Mensuel</option>
                        </select>
                    </div>

                    {{-- Année --}}
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Année</label>
                        <select name="year" id="year"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ ($request->year ?? now()->year) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Mois --}}
                    <div id="month_field" class="{{ ($request->period_type ?? 'year') !== 'month' ? 'hidden' : '' }}">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Mois</label>
                        <select name="month" id="month"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @php
                                $months = [
                                    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                                ];
                            @endphp
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ ($request->month ?? now()->month) == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Trimestre --}}
                    <div id="quarter_field" class="{{ ($request->period_type ?? 'year') !== 'quarter' ? 'hidden' : '' }}">
                        <label for="quarter" class="block text-sm font-medium text-gray-700 mb-2">Trimestre</label>
                        <select name="quarter" id="quarter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1" {{ ($request->quarter ?? ceil(now()->month / 3)) == 1 ? 'selected' : '' }}>T1 (Jan - Mar)</option>
                            <option value="2" {{ ($request->quarter ?? ceil(now()->month / 3)) == 2 ? 'selected' : '' }}>T2 (Avr - Juin)</option>
                            <option value="3" {{ ($request->quarter ?? ceil(now()->month / 3)) == 3 ? 'selected' : '' }}>T3 (Juil - Sep)</option>
                            <option value="4" {{ ($request->quarter ?? ceil(now()->month / 3)) == 4 ? 'selected' : '' }}>T4 (Oct - Déc)</option>
                        </select>
                    </div>

                    {{-- Bouton --}}
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Actualiser
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Période affichée --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <span class="text-lg font-semibold text-yellow-900">📅 Période : {{ $periodLabel }}</span>
        </div>

        {{-- Résumé global --}}
        @php
            $totalGlobal = [
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
                'invoice_count' => 0,
            ];
            $totalsByType = [
                'transaction' => ['total_ht' => 0, 'invoice_count' => 0],
                'location' => ['total_ht' => 0, 'invoice_count' => 0],
                'syndic' => ['total_ht' => 0, 'invoice_count' => 0],
                'autres' => ['total_ht' => 0, 'invoice_count' => 0],
            ];
            
            foreach($mandatairesData as $data) {
                $totalGlobal['total_ht'] += $data['total_ht'];
                $totalGlobal['total_tva'] += $data['total_tva'];
                $totalGlobal['total_ttc'] += $data['total_ttc'];
                $totalGlobal['invoice_count'] += $data['invoice_count'];
                
                if (isset($data['by_type'])) {
                    foreach (['transaction', 'location', 'syndic', 'autres'] as $type) {
                        $totalsByType[$type]['total_ht'] += $data['by_type'][$type]['total_ht'] ?? 0;
                        $totalsByType[$type]['invoice_count'] += $data['by_type'][$type]['invoice_count'] ?? 0;
                    }
                }
            }
        @endphp

        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-indigo-900 mb-4">💰 Résumé global</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Mandataires actifs</p>
                    <p class="text-3xl font-bold text-indigo-900">{{ count($mandatairesData) }}</p>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Factures payées</p>
                    <p class="text-3xl font-bold text-indigo-900">{{ $totalGlobal['invoice_count'] }}</p>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total HT</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($totalGlobal['total_ht'], 0, ',', ' ') }} €</p>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">TVA collectée</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($totalGlobal['total_tva'], 0, ',', ' ') }} €</p>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total TTC</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($totalGlobal['total_ttc'], 0, ',', ' ') }} €</p>
                </div>
            </div>
        </div>

        {{-- Ventilation par type d'activité --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📈 Ventilation globale par type d'activité</h2>
            
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
                            <span class="font-medium text-gray-900">{{ $totalsByType['transaction']['invoice_count'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">CA HT</span>
                            <span class="font-bold text-blue-700">{{ number_format($totalsByType['transaction']['total_ht'], 0, ',', ' ') }} €</span>
                        </div>
                        @if($totalGlobal['total_ht'] > 0)
                            <div class="text-xs text-blue-600">
                                {{ number_format(($totalsByType['transaction']['total_ht'] / $totalGlobal['total_ht']) * 100, 1) }}% du CA total
                            </div>
                        @endif
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
                            <span class="font-medium text-gray-900">{{ $totalsByType['location']['invoice_count'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">CA HT</span>
                            <span class="font-bold text-green-700">{{ number_format($totalsByType['location']['total_ht'], 0, ',', ' ') }} €</span>
                        </div>
                        @if($totalGlobal['total_ht'] > 0)
                            <div class="text-xs text-green-600">
                                {{ number_format(($totalsByType['location']['total_ht'] / $totalGlobal['total_ht']) * 100, 1) }}% du CA total
                            </div>
                        @endif
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
                            <span class="font-medium text-gray-900">{{ $totalsByType['syndic']['invoice_count'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">CA HT</span>
                            <span class="font-bold text-purple-700">{{ number_format($totalsByType['syndic']['total_ht'], 0, ',', ' ') }} €</span>
                        </div>
                        @if($totalGlobal['total_ht'] > 0)
                            <div class="text-xs text-purple-600">
                                {{ number_format(($totalsByType['syndic']['total_ht'] / $totalGlobal['total_ht']) * 100, 1) }}% du CA total
                            </div>
                        @endif
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
                            <span class="font-medium text-gray-900">{{ $totalsByType['autres']['invoice_count'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">CA HT</span>
                            <span class="font-bold text-gray-700">{{ number_format($totalsByType['autres']['total_ht'], 0, ',', ' ') }} €</span>
                        </div>
                        @if($totalGlobal['total_ht'] > 0)
                            <div class="text-xs text-gray-600">
                                {{ number_format(($totalsByType['autres']['total_ht'] / $totalGlobal['total_ht']) * 100, 1) }}% du CA total
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Barre de répartition --}}
            @if($totalGlobal['total_ht'] > 0)
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Répartition du CA HT global</h3>
                    <div class="flex h-8 rounded-full overflow-hidden bg-gray-200">
                        @php
                            $transactionPct = ($totalsByType['transaction']['total_ht'] / $totalGlobal['total_ht']) * 100;
                            $locationPct = ($totalsByType['location']['total_ht'] / $totalGlobal['total_ht']) * 100;
                            $syndicPct = ($totalsByType['syndic']['total_ht'] / $totalGlobal['total_ht']) * 100;
                            $autresPct = ($totalsByType['autres']['total_ht'] / $totalGlobal['total_ht']) * 100;
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

        {{-- Top 5 des mandataires --}}
        @if(count($mandatairesData) > 0)
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-green-900 mb-4">🏆 Top 5 des mandataires</h2>
                <div class="space-y-3">
                    @foreach(array_slice($mandatairesData, 0, 5) as $index => $data)
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-800 font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $data['user_name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $data['invoice_count'] }} facture(s)</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-700">{{ number_format($data['total_ht'], 2, ',', ' ') }} € HT</p>
                                <p class="text-xs text-gray-500">{{ number_format($data['total_ttc'], 2, ',', ' ') }} € TTC</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tableau détaillé des mandataires --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">📋 Détail par mandataire</h2>
                
                {{-- Boutons d'export --}}
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('urssaf.all-mandataires.export-pdf') }}" class="inline">
                        @csrf
                        <input type="hidden" name="period_type" value="{{ $request->period_type ?? 'year' }}">
                        <input type="hidden" name="year" value="{{ $request->year ?? now()->year }}">
                        <input type="hidden" name="month" value="{{ $request->month }}">
                        <input type="hidden" name="quarter" value="{{ $request->quarter }}">
                        <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PDF
                        </button>
                    </form>
                    <form method="POST" action="{{ route('urssaf.all-mandataires.export-excel') }}" class="inline">
                        @csrf
                        <input type="hidden" name="period_type" value="{{ $request->period_type ?? 'year' }}">
                        <input type="hidden" name="year" value="{{ $request->year ?? now()->year }}">
                        <input type="hidden" name="month" value="{{ $request->month }}">
                        <input type="hidden" name="quarter" value="{{ $request->quarter }}">
                        <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Excel
                        </button>
                    </form>
                </div>
            </div>

            @if(count($mandatairesData) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mandataire</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Factures</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total HT</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">TVA</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total TTC</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase" title="Transaction">🏠</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase" title="Location">🔑</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase" title="Syndic">🏢</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase" title="Autres">📋</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($mandatairesData as $index => $data)
                                @php
                                    $transHT = $data['by_type']['transaction']['total_ht'] ?? 0;
                                    $locHT = $data['by_type']['location']['total_ht'] ?? 0;
                                    $syndHT = $data['by_type']['syndic']['total_ht'] ?? 0;
                                    $autresHT = $data['by_type']['autres']['total_ht'] ?? 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $data['user_name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $data['user_email'] ?? '' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $data['invoice_count'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900">
                                        {{ number_format($data['total_ht'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-600">
                                        {{ number_format($data['total_tva'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-indigo-600">
                                        {{ number_format($data['total_ttc'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-blue-600">
                                        @if($transHT > 0)
                                            {{ number_format($transHT, 0, ',', ' ') }} €
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-green-600">
                                        @if($locHT > 0)
                                            {{ number_format($locHT, 0, ',', ' ') }} €
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-purple-600">
                                        @if($syndHT > 0)
                                            {{ number_format($syndHT, 0, ',', ' ') }} €
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-600">
                                        @if($autresHT > 0)
                                            {{ number_format($autresHT, 0, ',', ' ') }} €
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-indigo-50">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-900 text-right">TOTAL</td>
                                <td class="px-4 py-3 text-center font-bold text-gray-900">{{ $totalGlobal['invoice_count'] }}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">{{ number_format($totalGlobal['total_ht'], 2, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">{{ number_format($totalGlobal['total_tva'], 2, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-600">{{ number_format($totalGlobal['total_ttc'], 2, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-bold text-blue-600">{{ number_format($totalsByType['transaction']['total_ht'], 0, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-bold text-green-600">{{ number_format($totalsByType['location']['total_ht'], 0, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-bold text-purple-600">{{ number_format($totalsByType['syndic']['total_ht'], 0, ',', ' ') }} €</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-600">{{ number_format($totalsByType['autres']['total_ht'], 0, ',', ' ') }} €</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <span class="text-6xl">📭</span>
                    <p class="mt-4 text-gray-500">Aucune donnée pour cette période.</p>
                    <p class="text-sm text-gray-400 mt-2">
                        @if(count($mandataires) == 0)
                            Aucun mandataire trouvé.
                        @else
                            {{ count($mandataires) }} mandataire(s) sans facture payée sur cette période.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Statistiques --}}
        @if(count($mandatairesData) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- CA moyen --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistiques</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">CA moyen par mandataire</span>
                            <span class="font-bold text-gray-900">{{ number_format($totalGlobal['total_ht'] / count($mandatairesData), 2, ',', ' ') }} € HT</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Factures par mandataire (moy.)</span>
                            <span class="font-bold text-gray-900">{{ number_format($totalGlobal['invoice_count'] / count($mandatairesData), 1, ',', ' ') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Montant moyen par facture</span>
                            <span class="font-bold text-gray-900">
                                @if($totalGlobal['invoice_count'] > 0)
                                    {{ number_format($totalGlobal['total_ht'] / $totalGlobal['invoice_count'], 2, ',', ' ') }} € HT
                                @else
                                    0,00 € HT
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Répartition par tranche --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Répartition par tranche de CA</h3>
                    @php
                        $tranches = [
                            '0-5000' => 0,
                            '5000-10000' => 0,
                            '10000-20000' => 0,
                            '20000+' => 0,
                        ];
                        
                        foreach($mandatairesData as $data) {
                            $ca = $data['total_ht'];
                            if ($ca < 5000) $tranches['0-5000']++;
                            elseif ($ca < 10000) $tranches['5000-10000']++;
                            elseif ($ca < 20000) $tranches['10000-20000']++;
                            else $tranches['20000+']++;
                        }
                    @endphp
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">0 - 5 000 €</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ count($mandatairesData) > 0 ? ($tranches['0-5000'] / count($mandatairesData)) * 100 : 0 }}%"></div>
                                </div>
                                <span class="font-bold text-gray-900 w-8 text-right">{{ $tranches['0-5000'] }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">5 000 - 10 000 €</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ count($mandatairesData) > 0 ? ($tranches['5000-10000'] / count($mandatairesData)) * 100 : 0 }}%"></div>
                                </div>
                                <span class="font-bold text-gray-900 w-8 text-right">{{ $tranches['5000-10000'] }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">10 000 - 20 000 €</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ count($mandatairesData) > 0 ? ($tranches['10000-20000'] / count($mandatairesData)) * 100 : 0 }}%"></div>
                                </div>
                                <span class="font-bold text-gray-900 w-8 text-right">{{ $tranches['10000-20000'] }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Plus de 20 000 €</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ count($mandatairesData) > 0 ? ($tranches['20000+'] / count($mandatairesData)) * 100 : 0 }}%"></div>
                                </div>
                                <span class="font-bold text-gray-900 w-8 text-right">{{ $tranches['20000+'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Note importante --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <span class="text-2xl">⚠️</span>
                <div class="text-sm text-yellow-800">
                    <p class="font-semibold mb-1">Rappel important</p>
                    <p>
                        Ce récapitulatif consolide les revenus encaissés de tous les mandataires. 
                        Les montants correspondent aux factures effectivement payées sur la période sélectionnée.
                        Document confidentiel - Usage interne uniquement.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updatePeriodFields() {
            const periodType = document.getElementById('period_type').value;
            const monthField = document.getElementById('month_field');
            const quarterField = document.getElementById('quarter_field');

            monthField.classList.add('hidden');
            quarterField.classList.add('hidden');

            if (periodType === 'month') {
                monthField.classList.remove('hidden');
            } else if (periodType === 'quarter') {
                quarterField.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updatePeriodFields();
        });
    </script>
@endsection