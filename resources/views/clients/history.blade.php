@extends('layouts.app')

@section('title', 'Historique - ' . $client->display_name)

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📜 Historique complet</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $client->display_name }}</p>
            </div>
            <a href="{{ route('clients.show', $client) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la fiche
            </a>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg border border-indigo-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📄</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total devis</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $quotes->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📑</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total factures</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $invoices->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg border border-yellow-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">💰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">CA Total</dt>
                                <dd class="text-xl font-bold text-gray-900">{{ number_format($client->getTotalRevenue(), 0, ',', ' ') }} €</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg border border-red-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">⚠️</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Impayés</dt>
                                <dd class="text-xl font-bold text-red-600">
                                    {{ number_format($invoices->where('status', 'en_retard')->sum('remaining_amount'), 0, ',', ' ') }} €
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Onglets --}}
        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex">
                    <button id="tab-devis" onclick="switchTab('devis')" 
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors border-indigo-500 text-indigo-600">
                        📄 Devis ({{ $quotes->count() }})
                    </button>
                    <button id="tab-factures" onclick="switchTab('factures')" 
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        📑 Factures ({{ $invoices->count() }})
                    </button>
                </nav>
            </div>

            {{-- Contenu Devis - IMPORTANT: display inline --}}
            <div id="content-devis" class="p-6" style="display: block;">
                @if($quotes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Devis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date création</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant HT</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant TTC</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($quotes as $quote)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('quotes.show', $quote) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ $quote->quote_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">{{ $quote->service_label }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $quote->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            {{ $quote->formatted_total_ht }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                            {{ $quote->formatted_total_ttc }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                                {{ $quote->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $quote->user->full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('quotes.show', $quote) }}"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <span class="text-6xl">📄</span>
                        <p class="mt-4 text-gray-500">Aucun devis pour ce client</p>
                        <a href="{{ route('quotes.create', ['client_id' => $client->id]) }}"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            Créer le premier devis
                        </a>
                    </div>
                @endif
            </div>

            {{-- Contenu Factures - IMPORTANT: display inline --}}
            <div id="content-factures" class="p-6" style="display: none;">
                @if($invoices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Facture</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date émission</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Échéance</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant TTC</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Payé</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Reste</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($invoices as $invoice)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}
                                            </div>
                                            @if($invoice->isOverdue())
                                                <span class="text-xs text-red-600">⚠️ En retard</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                            {{ $invoice->formatted_total_ttc }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-green-600">
                                            {{ $invoice->formatted_paid_amount }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            @if($invoice->remaining_amount > 0)
                                                <span class="text-red-600 font-medium">{{ $invoice->formatted_remaining_amount }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                                                {{ $invoice->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $invoice->user->full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('invoices.show', $invoice) }}"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <span class="text-6xl">📑</span>
                        <p class="mt-4 text-gray-500">Aucune facture pour ce client</p>
                        <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            Créer la première facture
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- JavaScript - Ce qui marche ! --}}
    <script>
        function switchTab(tabName) {
            // Masquer tous les contenus
            document.getElementById('content-devis').style.display = 'none';
            document.getElementById('content-factures').style.display = 'none';
            
            // Réinitialiser tous les boutons
            document.getElementById('tab-devis').className = 'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
            document.getElementById('tab-factures').className = 'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
            
            // Afficher le contenu sélectionné
            if (tabName === 'devis') {
                document.getElementById('content-devis').style.display = 'block';
                document.getElementById('tab-devis').className = 'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors border-indigo-500 text-indigo-600';
            } else {
                document.getElementById('content-factures').style.display = 'block';
                document.getElementById('tab-factures').className = 'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors border-indigo-500 text-indigo-600';
            }
        }
        
        // Initialiser au chargement
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('devis');
        });
    </script>
@endsection