@extends('layouts.app')

@section('title', 'Gestion des factures')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Gestion des factures</h1>
                <p class="mt-1 text-sm text-gray-500">Suivi et gestion de toutes vos factures</p>
            </div>
            <a href="{{ route('invoices.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle facture
            </a>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📊</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total factures</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-600 truncate">CA ce mois</dt>
                                <dd class="text-2xl font-bold text-green-900">{{ number_format($stats['ca_month'], 0, ',', ' ') }} €</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-600 truncate">CA cette année</dt>
                                <dd class="text-2xl font-bold text-blue-900">{{ number_format($stats['ca_year'], 0, ',', ' ') }} €</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 overflow-hidden shadow rounded-lg border border-red-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">⏰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-red-600 truncate">En retard</dt>
                                <dd class="text-2xl font-bold text-red-900">{{ $stats['en_retard'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <form method="GET" action="{{ route('invoices.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Recherche --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="N° facture, client..."
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Statut --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="status"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="brouillon" {{ request('status') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                <option value="emise" {{ request('status') === 'emise' ? 'selected' : '' }}>Émise</option>
                                <option value="payee" {{ request('status') === 'payee' ? 'selected' : '' }}>Payée</option>
                                <option value="en_retard" {{ request('status') === 'en_retard' ? 'selected' : '' }}>En retard</option>
                                <option value="annulee" {{ request('status') === 'annulee' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>

                        {{-- Période --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                            <select name="period"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="this.form.submit()">
                                <option value="">Toutes</option>
                                <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Ce mois</option>
                                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Cette année</option>
                                <option value="overdue" {{ request('period') === 'overdue' ? 'selected' : '' }}>En retard</option>
                            </select>
                        </div>

                        {{-- Tri --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                            <select name="sort"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="this.form.submit()">
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date de création</option>
                                <option value="issued_at" {{ request('sort') === 'issued_at' ? 'selected' : '' }}>Date d'émission</option>
                                <option value="due_date" {{ request('sort') === 'due_date' ? 'selected' : '' }}>Date d'échéance</option>
                                <option value="total_ttc" {{ request('sort') === 'total_ttc' ? 'selected' : '' }}>Montant</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            Filtrer
                        </button>
                        @if (request()->hasAny(['search', 'status', 'period', 'sort']))
                            <a href="{{ route('invoices.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Tableau des factures --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    Liste des factures
                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                        {{ $invoices->total() }} résultat(s)
                    </span>
                </h2>
            </div>

            @if ($invoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N° Facture</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Échéance</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Montant TTC</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Restant dû</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($invoices as $invoice)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $invoice->client->display_name }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                                        {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                                        {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                        {{ $invoice->formatted_total_ttc }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium">
                                        <span class="{{ $invoice->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $invoice->formatted_remaining_amount }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                                            {{ $invoice->status_label }}
                                        </span>
                                        @if($invoice->isOverdue())
                                            <span class="block mt-1 text-xs text-red-600">
                                                {{ $invoice->days_overdue }} jour(s) de retard
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="{{ route('invoices.show', $invoice) }}"
                                                class="text-indigo-600 hover:text-indigo-900 transition" title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            @if($invoice->canBeEdited())
                                                <a href="{{ route('invoices.edit', $invoice) }}"
                                                    class="text-gray-600 hover:text-gray-900 transition" title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $invoices->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <span class="text-6xl">💰</span>
                    <p class="mt-4 text-gray-500">Aucune facture trouvée.</p>
                    <a href="{{ route('invoices.create') }}"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                        Créer la première facture
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection