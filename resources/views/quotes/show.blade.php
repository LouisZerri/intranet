@extends('layouts.app')

@section('title', 'Devis ' . $quote->quote_number)

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $quote->quote_number }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                        {{ $quote->status_label }}
                    </span>
                    {{-- Badge type d'activité --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @switch($quote->revenue_type)
                            @case('transaction') bg-blue-100 text-blue-800 @break
                            @case('location') bg-green-100 text-green-800 @break
                            @case('syndic') bg-purple-100 text-purple-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch
                    ">
                        {{ $quote->revenue_type_icon }} {{ $quote->revenue_type_label }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Créé le {{ $quote->created_at->format('d/m/Y à H:i') }}
                    @if($quote->validity_date)
                        • Valide jusqu'au <span class="{{ $quote->validity_date->isPast() ? 'text-red-600 font-medium' : '' }}">{{ $quote->validity_date->format('d/m/Y') }}</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('quotes.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    ← Retour
                </a>
                <a href="{{ route('quotes.edit', $quote) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                    ✏️ Modifier
                </a>
                <a href="{{ route('quotes.pdf', $quote) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    📄 PDF
                </a>
            </div>
        </div>

        {{-- Alerte expiration --}}
        @if($quote->validity_date && $quote->validity_date->isPast() && $quote->status !== 'accepte' && $quote->status !== 'refuse')
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Ce devis a expiré le {{ $quote->validity_date->format('d/m/Y') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informations client --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">👤 Client</h2>
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-lg font-medium text-gray-900">{{ $quote->client->display_name }}</p>
                            @if($quote->client->company_name)
                                <p class="text-sm text-gray-600">{{ $quote->client->company_name }}</p>
                            @endif
                            @if($quote->client->email)
                                <p class="text-sm text-gray-500 mt-1">
                                    <a href="mailto:{{ $quote->client->email }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ $quote->client->email }}
                                    </a>
                                </p>
                            @endif
                            @if($quote->client->phone)
                                <p class="text-sm text-gray-500">{{ $quote->client->phone }}</p>
                            @endif
                            @if($quote->client->full_address)
                                <p class="text-sm text-gray-500 mt-2">{{ $quote->client->full_address }}</p>
                            @endif
                        </div>
                        <a href="{{ route('clients.show', $quote->client) }}" 
                           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Voir la fiche →
                        </a>
                    </div>
                </div>

                {{-- Lignes du devis --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">📝 Détail des prestations</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Qté
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prix unit. HT
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        TVA
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total TTC
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($quote->items as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 whitespace-pre-line">{{ $item->description }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format($item->quantity, 2, ',', ' ') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format($item->unit_price, 2, ',', ' ') }} €
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ $item->tva_rate }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                            {{ number_format($item->total_ttc, 2, ',', ' ') }} €
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-700">
                                        Total HT
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                                        {{ $quote->formatted_total_ht }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-700">
                                        Total TVA
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                                        {{ $quote->formatted_total_tva }}
                                    </td>
                                </tr>
                                <tr class="bg-indigo-50">
                                    <td colspan="4" class="px-6 py-4 text-right text-base font-bold text-indigo-900">
                                        Total TTC
                                    </td>
                                    <td class="px-6 py-4 text-right text-lg font-bold text-indigo-600">
                                        {{ $quote->formatted_total_ttc }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Notes et conditions --}}
                @if($quote->client_notes || $quote->payment_terms || $quote->delivery_terms)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Notes et conditions</h2>
                        <div class="space-y-4">
                            @if($quote->client_notes)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-1">Notes client</h3>
                                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $quote->client_notes }}</p>
                                </div>
                            @endif
                            @if($quote->payment_terms)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-1">Conditions de paiement</h3>
                                    <p class="text-sm text-gray-600">{{ $quote->payment_terms }}</p>
                                </div>
                            @endif
                            @if($quote->delivery_terms)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-1">Conditions de livraison</h3>
                                    <p class="text-sm text-gray-600">{{ $quote->delivery_terms }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Notes internes --}}
                @if($quote->internal_notes)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-yellow-900 mb-2">🔒 Notes internes (non visibles sur le devis)</h3>
                        <p class="text-sm text-yellow-800 whitespace-pre-line">{{ $quote->internal_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-4">
                {{-- Actions rapides --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">⚡ Actions rapides</h3>
                    <div class="space-y-2">
                        @if($quote->status === 'brouillon')
                            <form method="POST" action="{{ route('quotes.send', $quote) }}" class="w-full">
                                @csrf
                                <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                    📤 Marquer comme envoyé
                                </button>
                            </form>
                        @endif

                        @if($quote->status === 'envoye' || $quote->status === 'brouillon')
                            <form method="POST" action="{{ route('quotes.accept', $quote) }}" class="w-full">
                                @csrf
                                <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                    ✅ Accepter
                                </button>
                            </form>
                            <form method="POST" action="{{ route('quotes.refuse', $quote) }}" class="w-full">
                                @csrf
                                <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                    ❌ Refuser
                                </button>
                            </form>
                        @endif

                        @if($quote->status === 'accepte' && !$quote->invoice)
                            <a href="{{ route('invoices.create', ['quote_id' => $quote->id]) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                🧾 Créer la facture
                            </a>
                        @endif


                    </div>
                </div>

                {{-- Informations --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">ℹ️ Informations</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs text-gray-500">Numéro</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $quote->quote_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Type d'activité</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @switch($quote->revenue_type)
                                        @case('transaction') bg-blue-100 text-blue-800 @break
                                        @case('location') bg-green-100 text-green-800 @break
                                        @case('syndic') bg-purple-100 text-purple-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ $quote->revenue_type_icon }} {{ $quote->revenue_type_label }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                    {{ $quote->status_label }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Créé le</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $quote->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Par</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $quote->user->full_name }}</dd>
                        </div>
                        @if($quote->validity_date)
                            <div>
                                <dt class="text-xs text-gray-500">Valide jusqu'au</dt>
                                <dd class="text-sm font-medium {{ $quote->validity_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $quote->validity_date->format('d/m/Y') }}
                                </dd>
                            </div>
                        @endif
                        @if($quote->sent_at)
                            <div>
                                <dt class="text-xs text-gray-500">Envoyé le</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $quote->sent_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                        @if($quote->accepted_at)
                            <div>
                                <dt class="text-xs text-gray-500">Accepté le</dt>
                                <dd class="text-sm font-medium text-green-600">{{ $quote->accepted_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                        @if($quote->refused_at)
                            <div>
                                <dt class="text-xs text-gray-500">Refusé le</dt>
                                <dd class="text-sm font-medium text-red-600">{{ $quote->refused_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Facture liée --}}
                @if($quote->invoice)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-green-900 mb-2">🧾 Facture liée</h3>
                        <a href="{{ route('invoices.show', $quote->invoice) }}" 
                           class="text-green-700 hover:text-green-900 font-medium">
                            {{ $quote->invoice->invoice_number }} →
                        </a>
                        <p class="text-xs text-green-600 mt-1">{{ $quote->invoice->formatted_total_ttc }}</p>
                    </div>
                @endif

                {{-- Légende types d'activité --}}
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-amber-900 mb-3">📊 Types d'activité URSSAF</h3>
                    <ul class="space-y-2 text-sm text-amber-800">
                        <li><span class="font-medium">🏠 Transaction</span> : Ventes immobilières</li>
                        <li><span class="font-medium">🔑 Location</span> : Gestion locative, honoraires</li>
                        <li><span class="font-medium">🏢 Syndic</span> : Gestion de copropriété</li>
                        <li><span class="font-medium">📋 Autres</span> : Autres prestations</li>
                    </ul>
                </div>

                {{-- Danger zone --}}
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-red-900 mb-2">⚠️ Zone de danger</h3>
                    <form method="POST" action="{{ route('quotes.destroy', $quote) }}" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce devis ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                            🗑️ Supprimer le devis
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection