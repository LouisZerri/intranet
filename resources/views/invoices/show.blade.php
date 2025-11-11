@extends('layouts.app')

@section('title', 'Facture ' . $invoice->invoice_number)

@section('content')
    <div class="space-y-6">
        {{-- En-tête avec actions --}}
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">Facture {{ $invoice->invoice_number }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                        {{ $invoice->status_label }}
                    </span>
                    @if($invoice->isOverdue())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            ⏰ {{ $invoice->days_overdue }} jour(s) de retard
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500">Créée le {{ $invoice->created_at->format('d/m/Y à H:i') }} par {{ $invoice->user->full_name }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('invoices.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    ← Retour
                </a>

                {{-- Actions selon le statut --}}
                @if($invoice->canBeEdited())
                    <a href="{{ route('invoices.edit', $invoice) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        ✏️ Modifier
                    </a>
                @endif

                @if($invoice->status === 'brouillon')
                    <form method="POST" action="{{ route('invoices.issue', $invoice) }}" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Émettre cette facture ? Elle ne pourra plus être supprimée.')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            📤 Émettre la facture
                        </button>
                    </form>
                @endif

                @if(in_array($invoice->status, ['emise', 'en_retard']) && $invoice->remaining_amount > 0)
                    <button type="button" onclick="openPaymentModal()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                        💳 Enregistrer un paiement
                    </button>
                @endif

                @if($invoice->isOverdue())
                    <form method="POST" action="{{ route('invoices.reminder', $invoice) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition">
                            📧 Envoyer un rappel
                        </button>
                    </form>
                @endif

                {{-- Dropdown Plus --}}
                <div style="position: relative; display: inline-block;" id="dropdownContainer">
                    <button type="button" id="dropdownButton"
                        style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #374151; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer; transition: background-color 0.15s;"
                        onmouseover="this.style.backgroundColor='#1f2937'"
                        onmouseout="this.style.backgroundColor='#374151'">
                        <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                        Plus
                    </button>
                    <div id="dropdownMenu"
                        style="display: none; position: absolute; right: 0; margin-top: 0.5rem; width: 14rem; background-color: white; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; z-index: 9999;">
                        <div style="padding: 0.25rem 0;">
                            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank"
                                style="display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;"
                                onmouseover="this.style.backgroundColor='#f3f4f6'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <svg style="width: 1rem; height: 1rem; margin-right: 0.75rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Visualiser le PDF
                            </a>
                            <a href="{{ route('invoices.pdf.download', $invoice) }}"
                                style="display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;"
                                onmouseover="this.style.backgroundColor='#f3f4f6'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <svg style="width: 1rem; height: 1rem; margin-right: 0.75rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Télécharger le PDF
                            </a>
                            <div style="border-top: 1px solid #e5e7eb; margin: 0.25rem 0;"></div>
                            <a href="{{ route('clients.show', $invoice->client) }}"
                                style="display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;"
                                onmouseover="this.style.backgroundColor='#f3f4f6'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <svg style="width: 1rem; height: 1rem; margin-right: 0.75rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Voir le client
                            </a>
                            @if($invoice->quote)
                                <a href="{{ route('quotes.show', $invoice->quote) }}"
                                    style="display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;"
                                    onmouseover="this.style.backgroundColor='#f3f4f6'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <svg style="width: 1rem; height: 1rem; margin-right: 0.75rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Voir le devis source
                                </a>
                            @endif
                            @if(Auth::user()->isAdministrateur() && $invoice->status !== 'payee')
                                <div style="border-top: 1px solid #e5e7eb; margin: 0.25rem 0;"></div>
                                <form method="POST" action="{{ route('invoices.cancel', $invoice) }}"
                                    onsubmit="return confirm('⚠️ Annuler définitivement cette facture ?')" style="display: block;">
                                    @csrf
                                    <button type="submit"
                                        style="width: 100%; text-align: left; display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; color: #dc2626; background: transparent; border: none; cursor: pointer;"
                                        onmouseover="this.style.backgroundColor='#fef2f2'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                        <svg style="width: 1rem; height: 1rem; margin-right: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Annuler la facture
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Messages flash --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informations client --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">👤 Client</h2>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-lg font-medium text-gray-900">{{ $invoice->client->display_name }}</p>
                            @if($invoice->client->type === 'professionnel' && $invoice->client->company_name)
                                <p class="text-sm text-gray-600">{{ $invoice->client->company_name }}</p>
                            @endif
                            @if($invoice->client->email)
                                <p class="text-sm text-gray-600 mt-2">📧 {{ $invoice->client->email }}</p>
                            @endif
                            @if($invoice->client->phone)
                                <p class="text-sm text-gray-600">📞 {{ $invoice->client->phone }}</p>
                            @endif
                        </div>
                        <a href="{{ route('clients.show', $invoice->client) }}"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            Voir la fiche →
                        </a>
                    </div>
                </div>

                {{-- Détails de la facture --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Détails de la facture</h2>

                    {{-- Lignes --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qté</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">P.U. HT</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">TVA</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total TTC</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($invoice->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $item->description }}</td>
                                        <td class="px-4 py-3 text-sm text-center">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-right">{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                                        <td class="px-4 py-3 text-sm text-center">{{ $item->tva_rate }}%</td>
                                        <td class="px-4 py-3 text-sm text-right font-medium">{{ $item->formatted_total_ttc }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Total HT</td>
                                    <td class="px-4 py-3 text-sm font-medium text-right">{{ $invoice->formatted_total_ht }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Total TVA</td>
                                    <td class="px-4 py-3 text-sm font-medium text-right">{{ number_format($invoice->total_tva, 2, ',', ' ') }} €</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-base font-bold text-gray-900 text-right">Total TTC</td>
                                    <td class="px-4 py-3 text-lg font-bold text-indigo-600 text-right">{{ $invoice->formatted_total_ttc }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Historique des paiements --}}
                @if($invoice->payments->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">💳 Historique des paiements</h2>
                        <div class="space-y-3">
                            @foreach($invoice->payments()->orderBy('payment_date', 'desc')->get() as $payment)
                                <div class="flex justify-between items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div>
                                        <p class="font-medium text-green-900">{{ $payment->formatted_amount }}</p>
                                        <p class="text-sm text-green-700">
                                            {{ $payment->payment_method_label }} 
                                            @if($payment->payment_reference)
                                                - Réf: {{ $payment->payment_reference }}
                                            @endif
                                        </p>
                                        @if($payment->notes)
                                            <p class="text-sm text-green-600 mt-1">{{ $payment->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-green-700">{{ $payment->payment_date->format('d/m/Y') }}</p>
                                        <p class="text-xs text-green-600">{{ $payment->payment_date->format('H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notes --}}
                @if($invoice->internal_notes)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-yellow-900 mb-2">📝 Notes internes</h3>
                        <p class="text-sm text-yellow-800 whitespace-pre-line">{{ $invoice->internal_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-4">
                {{-- Statut et informations --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">ℹ️ Informations</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                                    {{ $invoice->status_label }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Créée le</dt>
                            <dd class="text-gray-900 font-medium">{{ $invoice->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        @if($invoice->issued_at)
                            <div>
                                <dt class="text-gray-500">Émise le</dt>
                                <dd class="text-gray-900 font-medium">{{ $invoice->issued_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                        @if($invoice->due_date)
                            <div>
                                <dt class="text-gray-500">Échéance</dt>
                                <dd class="text-gray-900 font-medium">{{ $invoice->due_date->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                        @if($invoice->paid_at)
                            <div>
                                <dt class="text-gray-500">Payée le</dt>
                                <dd class="text-gray-900 font-medium">{{ $invoice->paid_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-gray-500">Par</dt>
                            <dd class="text-gray-900 font-medium">{{ $invoice->user->full_name }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Montants --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-indigo-900 mb-4">💰 Montants</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-indigo-700">Total HT</dt>
                            <dd class="font-medium text-indigo-900">{{ $invoice->formatted_total_ht }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-indigo-700">TVA</dt>
                            <dd class="font-medium text-indigo-900">{{ number_format($invoice->total_tva, 2, ',', ' ') }} €</dd>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-indigo-200">
                            <dt class="font-semibold text-indigo-900">Total TTC</dt>
                            <dd class="text-lg font-bold text-indigo-900">{{ $invoice->formatted_total_ttc }}</dd>
                        </div>
                        @if($invoice->payments->count() > 0)
                            <div class="flex justify-between pt-2 border-t border-indigo-200">
                                <dt class="text-green-700">Payé</dt>
                                <dd class="font-medium text-green-900">{{ number_format($invoice->payments->sum('amount'), 2, ',', ' ') }} €</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-semibold text-red-700">Restant dû</dt>
                                <dd class="text-lg font-bold text-red-900">{{ $invoice->formatted_remaining_amount }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Rappels --}}
                @if($invoice->reminder_count > 0)
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-orange-900 mb-2">📧 Rappels</h3>
                        <p class="text-sm text-orange-800">
                            {{ $invoice->reminder_count }} rappel(s) envoyé(s)
                        </p>
                        @if($invoice->reminder_sent_at)
                            <p class="text-xs text-orange-600 mt-1">
                                Dernier envoi : {{ $invoice->reminder_sent_at->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal d'enregistrement de paiement --}}
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">💳 Enregistrer un paiement</h3>
                <form method="POST" action="{{ route('invoices.payment', $invoice) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Montant *</label>
                            <input type="number" name="amount" step="0.01" min="0.01" max="{{ $invoice->remaining_amount }}" required
                                value="{{ $invoice->remaining_amount }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Maximum: {{ $invoice->formatted_remaining_amount }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Moyen de paiement *</label>
                            <select name="payment_method" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="virement">Virement</option>
                                <option value="cheque">Chèque</option>
                                <option value="carte">Carte bancaire</option>
                                <option value="especes">Espèces</option>
                                <option value="prelevement">Prélèvement</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Référence</label>
                            <input type="text" name="payment_reference"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="N° de chèque, référence virement...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date du paiement</label>
                            <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="2"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button type="submit"
                            class="flex-1 inline-flex justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                            Enregistrer
                        </button>
                        <button type="button" onclick="closePaymentModal()"
                            class="flex-1 inline-flex justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownButton = document.getElementById('dropdownButton');
            const dropdownMenu = document.getElementById('dropdownMenu');

            if (dropdownButton && dropdownMenu) {
                dropdownButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
                });

                document.addEventListener('click', function(e) {
                    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.style.display = 'none';
                    }
                });
            }
        });

        // Modal paiement
        function openPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
@endsection