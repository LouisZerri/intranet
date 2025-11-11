@extends('layouts.app')

@section('title', 'Récapitulatif URSSAF - ' . $data['period_label'])

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Récapitulatif URSSAF</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $data['period_label'] }}</p>
            </div>
            <a href="{{ route('urssaf.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                ← Retour
            </a>
        </div>

        {{-- Résumé --}}
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
            <h2 class="text-lg font-semibold mb-4">💰 Résumé des revenus</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-sm opacity-90">Factures payées</div>
                    <div class="text-3xl font-bold mt-1">{{ $data['invoice_count'] }}</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-sm opacity-90">Total HT</div>
                    <div class="text-3xl font-bold mt-1">{{ number_format($data['total_ht'], 0, ',', ' ') }} €</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-sm opacity-90">TVA collectée</div>
                    <div class="text-3xl font-bold mt-1">{{ number_format($data['total_tva'], 0, ',', ' ') }} €</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-sm opacity-90">Total TTC</div>
                    <div class="text-3xl font-bold mt-1">{{ number_format($data['total_ttc'], 0, ',', ' ') }} €</div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <form method="POST" action="{{ route('urssaf.export-pdf') }}">
                @csrf
                <input type="hidden" name="period_type" value="{{ $data['period_type'] }}">
                <input type="hidden" name="year" value="{{ request('year') }}">
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

            <form method="POST" action="{{ route('urssaf.export-excel') }}">
                @csrf
                <input type="hidden" name="period_type" value="{{ $data['period_type'] }}">
                <input type="hidden" name="year" value="{{ request('year') }}">
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

        {{-- Détail des factures --}}
        @if(count($data['invoices']) > 0)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">📋 Détail des factures ({{ count($data['invoices']) }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Facture</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Date paiement</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant HT</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">TVA</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant TTC</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data['invoices'] as $invoice)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $invoice['invoice_number'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $invoice['client_name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ $invoice['paid_at'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($invoice['total_ht'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ number_format($invoice['total_tva'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                        {{ number_format($invoice['total_ttc'], 2, ',', ' ') }} €
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-yellow-800">Aucune facture payée durant cette période.</p>
            </div>
        @endif
    </div>
@endsection