@extends('layouts.app')

@section('title', 'Déclaration URSSAF')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Déclaration URSSAF</h1>
                <p class="mt-1 text-sm text-gray-500">Générez vos récapitulatifs de chiffre d'affaires pour vos déclarations</p>
            </div>
        </div>

        {{-- Formulaire de sélection de période --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📅 Sélectionner une période</h2>

            <form method="GET" action="{{ route('urssaf.report') }}" class="space-y-6">
                {{-- Type de période --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Type de période</label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-indigo-500 transition period-option" data-period="month">
                            <input type="radio" name="period_type" value="month" class="sr-only" checked>
                            <span class="flex flex-1 flex-col items-center">
                                <span class="text-3xl mb-2">📅</span>
                                <span class="block text-sm font-medium text-gray-900">Mensuel</span>
                                <span class="mt-1 flex items-center text-xs text-gray-500">Déclaration mensuelle</span>
                            </span>
                            <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-indigo-500 period-border" aria-hidden="true"></span>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-indigo-500 transition period-option" data-period="quarter">
                            <input type="radio" name="period_type" value="quarter" class="sr-only">
                            <span class="flex flex-1 flex-col items-center">
                                <span class="text-3xl mb-2">📊</span>
                                <span class="block text-sm font-medium text-gray-900">Trimestriel</span>
                                <span class="mt-1 flex items-center text-xs text-gray-500">Déclaration trimestrielle</span>
                            </span>
                            <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-transparent period-border" aria-hidden="true"></span>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-indigo-500 transition period-option" data-period="year">
                            <input type="radio" name="period_type" value="year" class="sr-only">
                            <span class="flex flex-1 flex-col items-center">
                                <span class="text-3xl mb-2">📈</span>
                                <span class="block text-sm font-medium text-gray-900">Annuel</span>
                                <span class="mt-1 flex items-center text-xs text-gray-500">Récapitulatif annuel</span>
                            </span>
                            <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-transparent period-border" aria-hidden="true"></span>
                        </label>
                    </div>
                </div>

                {{-- Sélection année --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Année *</label>
                        <select name="year" id="year" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Sélection mois (affiché si mensuel) --}}
                    <div id="month_select">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Mois *</label>
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
                                <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sélection trimestre (affiché si trimestriel) --}}
                    <div id="quarter_select" class="hidden">
                        <label for="quarter" class="block text-sm font-medium text-gray-700 mb-2">Trimestre *</label>
                        <select name="quarter" id="quarter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1" {{ ceil(now()->month / 3) == 1 ? 'selected' : '' }}>T1 (Janvier - Mars)</option>
                            <option value="2" {{ ceil(now()->month / 3) == 2 ? 'selected' : '' }}>T2 (Avril - Juin)</option>
                            <option value="3" {{ ceil(now()->month / 3) == 3 ? 'selected' : '' }}>T3 (Juillet - Septembre)</option>
                            <option value="4" {{ ceil(now()->month / 3) == 4 ? 'selected' : '' }}>T4 (Octobre - Décembre)</option>
                        </select>
                    </div>
                </div>

                {{-- Bouton de génération --}}
                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Générer le récapitulatif
                    </button>
                </div>
            </form>
        </div>

        {{-- Informations URSSAF --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">ℹ️ À propos des déclarations URSSAF</h3>
            <div class="text-sm text-blue-800 space-y-2">
                <p>
                    <strong>Micro-entrepreneur :</strong> Vous devez déclarer votre chiffre d'affaires encaissé (factures payées) 
                    mensuellement ou trimestriellement selon votre choix lors de l'inscription.
                </p>
                <p>
                    <strong>Important :</strong> Le CA à déclarer correspond aux sommes effectivement encaissées sur la période, 
                    pas aux factures émises. Ce rapport ne prend en compte que les factures avec le statut "Payée".
                </p>
            </div>
        </div>

        {{-- Légende des types d'activité --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🏷️ Types d'activité pour la ventilation URSSAF</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <span class="text-2xl">🏠</span>
                    <div>
                        <p class="font-medium text-blue-900">Transaction</p>
                        <p class="text-xs text-blue-700">Ventes immobilières, mandats de vente</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <span class="text-2xl">🔑</span>
                    <div>
                        <p class="font-medium text-green-900">Location</p>
                        <p class="text-xs text-green-700">Gestion locative, états des lieux, baux</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                    <span class="text-2xl">🏢</span>
                    <div>
                        <p class="font-medium text-purple-900">Syndic</p>
                        <p class="text-xs text-purple-700">Gestion de copropriété</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <span class="text-2xl">📋</span>
                    <div>
                        <p class="font-medium text-gray-900">Autres</p>
                        <p class="text-xs text-gray-700">Prestations diverses, conseil</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accès admin : tous les mandataires --}}
        @if(Auth::user()->isAdministrateur() || Auth::user()->isManager())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">👑 Administration</h3>
                <p class="text-sm text-yellow-800 mb-4">
                    @if(Auth::user()->isAdministrateur())
                        En tant qu'administrateur, vous pouvez générer un rapport consolidé pour tous les mandataires.
                    @else
                        En tant que manager, vous pouvez générer un rapport consolidé pour vos collaborateurs.
                    @endif
                </p>
                <a href="{{ route('urssaf.all-mandataires') }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Rapport tous mandataires
                </a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodOptions = document.querySelectorAll('.period-option');
            const monthSelect = document.getElementById('month_select');
            const quarterSelect = document.getElementById('quarter_select');

            function updatePeriodVisibility() {
                const selectedRadio = document.querySelector('input[name="period_type"]:checked');
                const selected = selectedRadio ? selectedRadio.value : 'month';
                
                // Reset all borders
                periodOptions.forEach(option => {
                    option.querySelector('.period-border').classList.remove('border-indigo-500');
                    option.querySelector('.period-border').classList.add('border-transparent');
                });

                // Set active border
                const activeOption = document.querySelector(`.period-option[data-period="${selected}"]`);
                if (activeOption) {
                    activeOption.querySelector('.period-border').classList.remove('border-transparent');
                    activeOption.querySelector('.period-border').classList.add('border-indigo-500');
                }

                // Show/hide selects
                if (selected === 'month') {
                    monthSelect.classList.remove('hidden');
                    quarterSelect.classList.add('hidden');
                } else if (selected === 'quarter') {
                    monthSelect.classList.add('hidden');
                    quarterSelect.classList.remove('hidden');
                } else {
                    monthSelect.classList.add('hidden');
                    quarterSelect.classList.add('hidden');
                }
            }

            periodOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    updatePeriodVisibility();
                });
            });

            // Initial state
            updatePeriodVisibility();
        });
    </script>
@endsection