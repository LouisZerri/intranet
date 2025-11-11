@extends('layouts.app')

@section('title', 'Récapitulatif URSSAF - Tous les mandataires')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Récapitulatif URSSAF - Tous les mandataires</h1>
                <p class="mt-1 text-sm text-gray-500">Vue consolidée pour {{ $periodLabel }}</p>
            </div>
            <a href="{{ route('urssaf.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                ← Retour
            </a>
        </div>

        {{-- Filtres de période --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🔍 Filtrer par période</h2>
            
            <form method="GET" action="{{ route('urssaf.all-mandataires') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Type de période --}}
                    <div>
                        <label for="period_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de période
                        </label>
                        <select name="period_type" id="period_type"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            onchange="updatePeriodFields()">
                            <option value="year" {{ request('period_type', 'year') === 'year' ? 'selected' : '' }}>Annuel</option>
                            <option value="quarter" {{ request('period_type') === 'quarter' ? 'selected' : '' }}>Trimestriel</option>
                            <option value="month" {{ request('period_type') === 'month' ? 'selected' : '' }}>Mensuel</option>
                        </select>
                    </div>

                    {{-- Année --}}
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                            Année
                        </label>
                        <select name="year" id="year"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Mois --}}
                    <div id="month-field" style="display: none;">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                            Mois
                        </label>
                        <select name="month" id="month"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Choisir...</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2025, $m, 1)->locale('fr')->monthName }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Trimestre --}}
                    <div id="quarter-field" style="display: none;">
                        <label for="quarter" class="block text-sm font-medium text-gray-700 mb-2">
                            Trimestre
                        </label>
                        <select name="quarter" id="quarter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Choisir...</option>
                            <option value="1" {{ request('quarter') == 1 ? 'selected' : '' }}>T1 (Jan-Mar)</option>
                            <option value="2" {{ request('quarter') == 2 ? 'selected' : '' }}>T2 (Avr-Juin)</option>
                            <option value="3" {{ request('quarter') == 3 ? 'selected' : '' }}>T3 (Juil-Sep)</option>
                            <option value="4" {{ request('quarter') == 4 ? 'selected' : '' }}>T4 (Oct-Déc)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrer
                    </button>
                </div>
            </form>

            {{-- Boutons d'export en dehors du formulaire de filtrage --}}
            <div class="flex gap-3 mt-4 pt-4 border-t border-gray-200">
                <form method="POST" action="{{ route('urssaf.all-mandataires.export-pdf') }}">
                    @csrf
                    <input type="hidden" name="period_type" value="{{ request('period_type', 'year') }}">
                    <input type="hidden" name="year" value="{{ request('year', now()->year) }}">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <input type="hidden" name="quarter" value="{{ request('quarter') }}">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        Télécharger PDF
                    </button>
                </form>

                <form method="POST" action="{{ route('urssaf.all-mandataires.export-excel') }}">
                    @csrf
                    <input type="hidden" name="period_type" value="{{ request('period_type', 'year') }}">
                    <input type="hidden" name="year" value="{{ request('year', now()->year) }}">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <input type="hidden" name="quarter" value="{{ request('quarter') }}">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger Excel
                    </button>
                </form>
            </div>
        </div>

        {{-- Résumé global --}}
        @php
            $totalGlobal = [
                'invoice_count' => array_sum(array_column($mandatairesData, 'invoice_count')),
                'total_ht' => array_sum(array_column($mandatairesData, 'total_ht')),
                'total_tva' => array_sum(array_column($mandatairesData, 'total_tva')),
                'total_ttc' => array_sum(array_column($mandatairesData, 'total_ttc')),
            ];
        @endphp

        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold mb-4 text-white">💰 Résumé global</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-600">Mandataires actifs</div>
                    <div class="text-3xl font-bold mt-1 text-indigo-600">{{ count($mandatairesData) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-600">Factures payées</div>
                    <div class="text-3xl font-bold mt-1 text-indigo-600">{{ $totalGlobal['invoice_count'] }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-600">CA HT total</div>
                    <div class="text-3xl font-bold mt-1 text-indigo-600">{{ number_format($totalGlobal['total_ht'], 0, ',', ' ') }} €</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-600">CA TTC total</div>
                    <div class="text-3xl font-bold mt-1 text-indigo-600">{{ number_format($totalGlobal['total_ttc'], 0, ',', ' ') }} €</div>
                </div>
            </div>
        </div>

        {{-- Liste des mandataires --}}
        @if(count($mandatairesData) > 0)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">👥 Détail par mandataire ({{ count($mandatairesData) }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mandataire</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nb factures</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total HT</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">TVA</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total TTC</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($mandatairesData as $index => $data)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $data['user_name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $data['user_email'] ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $data['invoice_count'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                        {{ number_format($data['total_ht'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ number_format($data['total_tva'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                        {{ number_format($data['total_ttc'], 2, ',', ' ') }} €
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-sm font-bold text-gray-900 text-right">TOTAL</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $totalGlobal['invoice_count'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                    {{ number_format($totalGlobal['total_ht'], 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                    {{ number_format($totalGlobal['total_tva'], 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                    {{ number_format($totalGlobal['total_ttc'], 2, ',', ' ') }} €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-yellow-800">Aucun mandataire n'a de factures payées durant cette période.</p>
            </div>
        @endif
    </div>

    <script>
        function updatePeriodFields() {
            const periodType = document.getElementById('period_type').value;
            const monthField = document.getElementById('month-field');
            const quarterField = document.getElementById('quarter-field');

            monthField.style.display = 'none';
            quarterField.style.display = 'none';

            if (periodType === 'month') {
                monthField.style.display = 'block';
            } else if (periodType === 'quarter') {
                quarterField.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updatePeriodFields();
        });
    </script>
@endsection