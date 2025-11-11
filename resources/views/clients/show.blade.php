@extends('layouts.app')

@section('title', $client->display_name)

@section('content')
    <div class="space-y-6">
        {{-- En-tête avec actions --}}
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $client->display_name }}</h1>
                    @if($client->type === 'particulier')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            👤 Particulier
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            🏢 Professionnel
                        </span>
                    @endif
                    @if($client->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Actif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Inactif
                        </span>
                    @endif
                </div>
                @if($client->type === 'professionnel' && $client->company_name)
                    <p class="text-gray-600">{{ $client->company_name }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('clients.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour
                </a>
                <a href="{{ route('clients.edit', $client) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <div class="relative" id="dropdownContainer">
                    <button type="button" id="dropdownButton"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouveau
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <a href="{{ route('quotes.create', ['client_id' => $client->id]) }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Devis
                        </a>
                        <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 last:rounded-b-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Facture
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Messages flash --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

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
                                <dt class="text-sm font-medium text-gray-500 truncate">Devis</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['quotes_count'] }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Factures</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['invoices_count'] }}</dd>
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
                                <dd class="text-xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} €</dd>
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
                                <dd class="text-xl font-bold text-red-600">{{ number_format($stats['unpaid_amount'], 0, ',', ' ') }} €</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Colonne principale (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Indicateurs qualité client --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        📊 Indicateurs
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Taux de transformation</label>
                                <span class="text-sm font-semibold text-green-600">{{ number_format($stats['conversion_rate'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ $stats['conversion_rate'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Devis acceptés / devis envoyés</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Bon payeur</label>
                            @if($client->isGoodPayer())
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        ✅ Excellent
                                    </span>
                                    <span class="text-sm text-gray-600">Aucun impayé</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        ⚠️ À surveiller
                                    </span>
                                    <span class="text-sm text-gray-600">{{ number_format($stats['unpaid_amount'], 2, ',', ' ') }} € d'impayés</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Devis récents --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">
                            📄 Devis récents
                        </h2>
                        <a href="{{ route('clients.history', $client) }}"
                            class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                            Voir tout l'historique →
                        </a>
                    </div>
                    
                    @if($recentQuotes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Devis</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant TTC</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentQuotes as $quote)
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
                                            <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                                {{ $quote->formatted_total_ttc }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                                    {{ $quote->status_label }}
                                                </span>
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
                            <span class="text-5xl">📄</span>
                            <p class="mt-2 text-gray-500">Aucun devis pour ce client</p>
                        </div>
                    @endif
                </div>

                {{-- Factures récentes --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">
                            📑 Factures récentes
                        </h2>
                        <a href="{{ route('clients.history', $client) }}"
                            class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                            Voir tout l'historique →
                        </a>
                    </div>
                    
                    @if($recentInvoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Facture</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date émission</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Échéance</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant TTC</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentInvoices as $invoice)
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
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="font-medium">{{ $invoice->formatted_total_ttc }}</div>
                                                @if($invoice->remaining_amount > 0)
                                                    <div class="text-xs text-gray-500">Reste: {{ $invoice->formatted_remaining_amount }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                                                    {{ $invoice->status_label }}
                                                </span>
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
                            <span class="text-5xl">📑</span>
                            <p class="mt-2 text-gray-500">Aucune facture pour ce client</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar informations (1/3) --}}
            <div class="space-y-6">
                {{-- Informations de contact --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        📞 Coordonnées
                    </h2>
                    
                    <div class="space-y-3">
                        @if($client->email)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                                <a href="mailto:{{ $client->email }}" class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $client->email }}
                                </a>
                            </div>
                        @endif

                        @if($client->phone)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Téléphone fixe</label>
                                <a href="tel:{{ $client->phone }}" class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $client->phone }}
                                </a>
                            </div>
                        @endif

                        @if($client->mobile)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Téléphone mobile</label>
                                <a href="tel:{{ $client->mobile }}" class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    {{ $client->mobile }}
                                </a>
                            </div>
                        @endif

                        @if($client->full_address)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Adresse</label>
                                <div class="text-sm text-gray-900 flex items-start">
                                    <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $client->full_address }}</span>
                                </div>
                            </div>
                        @endif

                        @if(!$client->email && !$client->phone && !$client->mobile && !$client->full_address)
                            <p class="text-sm text-gray-500 italic">Aucune coordonnée renseignée</p>
                        @endif
                    </div>
                </div>

                {{-- Informations professionnelles --}}
                @if($client->type === 'professionnel')
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            🏢 Informations professionnelles
                        </h2>
                        
                        <div class="space-y-3">
                            @if($client->company_name)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Entreprise</label>
                                    <div class="text-sm font-medium text-gray-900">{{ $client->company_name }}</div>
                                </div>
                            @endif

                            @if($client->siret)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">SIRET</label>
                                    <div class="text-sm text-gray-900">{{ $client->siret }}</div>
                                </div>
                            @endif

                            @if($client->tva_number)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">N° TVA</label>
                                    <div class="text-sm text-gray-900">{{ $client->tva_number }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Notes internes --}}
                @if($client->notes)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            📝 Notes internes
                        </h2>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $client->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- JavaScript pour le dropdown - DIRECTEMENT dans la page --}}
    <script>
        // Dropdown menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Script dropdown chargé');
            
            const dropdownButton = document.getElementById('dropdownButton');
            const dropdownMenu = document.getElementById('dropdownMenu');
            const dropdownContainer = document.getElementById('dropdownContainer');

            console.log('dropdownButton:', dropdownButton);
            console.log('dropdownMenu:', dropdownMenu);
            console.log('dropdownContainer:', dropdownContainer);

            if (dropdownButton && dropdownMenu) {
                // Toggle au clic sur le bouton
                dropdownButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('Bouton dropdown cliqué');
                    
                    const isHidden = dropdownMenu.classList.contains('hidden');
                    console.log('Menu caché?', isHidden);
                    
                    if (isHidden) {
                        dropdownMenu.classList.remove('hidden');
                        console.log('Menu affiché');
                    } else {
                        dropdownMenu.classList.add('hidden');
                        console.log('Menu masqué');
                    }
                });

                // Fermer au clic n'importe où sur la page
                document.addEventListener('click', function(e) {
                    if (dropdownContainer && !dropdownContainer.contains(e.target)) {
                        dropdownMenu.classList.add('hidden');
                        console.log('Menu fermé (clic extérieur)');
                    }
                });
            } else {
                console.error('Éléments du dropdown non trouvés!');
            }
        });
    </script>
@endsection