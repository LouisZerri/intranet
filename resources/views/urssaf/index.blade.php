@extends('layouts.app')

@section('title', 'Récapitulatif URSSAF')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Récapitulatif URSSAF</h1>
                <p class="mt-1 text-sm text-gray-500">Générez vos déclarations de revenus pour l'URSSAF</p>
            </div>
            @if(Auth::user()->isAdministrateur() || Auth::user()->isManager())
                <a href="{{ route('urssaf.all-mandataires') }}"
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Vue tous les mandataires
                </a>
            @endif
        </div>

        {{-- Formulaire de génération --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">🔍 Sélectionnez la période</h2>

            <form method="POST" action="{{ route('urssaf.generate') }}" id="urssafForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Type de période --}}
                    <div>
                        <label for="period_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de période <span class="text-red-500">*</span>
                        </label>
                        <select name="period_type" id="period_type" required
                            class="block w-full px-3 py-2 border @error('period_type') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            onchange="updatePeriodFields()">
                            <option value="">Choisir...</option>
                            <option value="month" {{ old('period_type') === 'month' ? 'selected' : '' }}>Mensuel</option>
                            <option value="quarter" {{ old('period_type') === 'quarter' ? 'selected' : '' }}>Trimestriel</option>
                            <option value="year" {{ old('period_type') === 'year' ? 'selected' : '' }}>Annuel</option>
                        </select>
                        @error('period_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Année --}}
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                            Année <span class="text-red-500">*</span>
                        </label>
                        <select name="year" id="year" required
                            class="block w-full px-3 py-2 border @error('year') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ old('year', now()->year) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        @error('year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mois (conditionnel) --}}
                    <div id="month-field" style="display: none;">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                            Mois <span class="text-red-500">*</span>
                        </label>
                        <select name="month" id="month"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Choisir...</option>
                            <option value="1">Janvier</option>
                            <option value="2">Février</option>
                            <option value="3">Mars</option>
                            <option value="4">Avril</option>
                            <option value="5">Mai</option>
                            <option value="6">Juin</option>
                            <option value="7">Juillet</option>
                            <option value="8">Août</option>
                            <option value="9">Septembre</option>
                            <option value="10">Octobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Décembre</option>
                        </select>
                        @error('month')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Trimestre (conditionnel) --}}
                    <div id="quarter-field" style="display: none;">
                        <label for="quarter" class="block text-sm font-medium text-gray-700 mb-2">
                            Trimestre <span class="text-red-500">*</span>
                        </label>
                        <select name="quarter" id="quarter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Choisir...</option>
                            <option value="1">T1 (Janvier - Mars)</option>
                            <option value="2">T2 (Avril - Juin)</option>
                            <option value="3">T3 (Juillet - Septembre)</option>
                            <option value="4">T4 (Octobre - Décembre)</option>
                        </select>
                        @error('quarter')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Générer le récapitulatif
                    </button>

                    <button type="button" onclick="exportPdf()"
                        class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        Télécharger PDF
                    </button>

                    <button type="button" onclick="exportExcel()"
                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger Excel
                    </button>
                </div>
            </form>
        </div>

        {{-- Aide --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-sm font-semibold text-blue-900 mb-3">💡 Informations</h3>
            <ul class="space-y-2 text-sm text-blue-800">
                <li>• Le récapitulatif inclut uniquement les <strong>factures payées</strong> durant la période sélectionnée</li>
                <li>• Les montants sont calculés en fonction de la <strong>date de paiement effective</strong></li>
                <li>• Vous pouvez générer des récapitulatifs mensuels, trimestriels ou annuels</li>
                <li>• Les exports PDF et Excel contiennent le détail de toutes les factures</li>
                <li>• Ce document est nécessaire pour vos déclarations URSSAF</li>
            </ul>
        </div>
    </div>

    <script>
        function updatePeriodFields() {
            const periodType = document.getElementById('period_type').value;
            const monthField = document.getElementById('month-field');
            const quarterField = document.getElementById('quarter-field');
            const monthSelect = document.getElementById('month');
            const quarterSelect = document.getElementById('quarter');

            // Réinitialiser
            monthField.style.display = 'none';
            quarterField.style.display = 'none';
            monthSelect.removeAttribute('required');
            quarterSelect.removeAttribute('required');

            // Afficher le champ approprié
            if (periodType === 'month') {
                monthField.style.display = 'block';
                monthSelect.setAttribute('required', 'required');
            } else if (periodType === 'quarter') {
                quarterField.style.display = 'block';
                quarterSelect.setAttribute('required', 'required');
            }
        }

        function exportPdf() {
            const form = document.getElementById('urssafForm');
            const originalAction = form.action;
            form.action = '{{ route("urssaf.export-pdf") }}';
            form.submit();
            form.action = originalAction;
        }

        function exportExcel() {
            const form = document.getElementById('urssafForm');
            const originalAction = form.action;
            form.action = '{{ route("urssaf.export-excel") }}';
            form.submit();
            form.action = originalAction;
        }

        // Initialiser l'affichage au chargement
        document.addEventListener('DOMContentLoaded', function() {
            updatePeriodFields();
        });
    </script>
@endsection