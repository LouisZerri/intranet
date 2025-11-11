@extends('layouts.app')

@section('title', 'Créer une facture')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Créer une facture</h1>
                <p class="mt-1 text-sm text-gray-500">Créez une nouvelle facture pour un client</p>
            </div>
            <a href="{{ route('invoices.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                ← Retour
            </a>
        </div>

        <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Colonne principale --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Informations générales --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Informations générales</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Client --}}
                            <div class="md:col-span-2">
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Client <span class="text-red-500">*</span>
                                </label>
                                <select name="client_id" id="client_id" required
                                    class="block w-full px-3 py-2 border @error('client_id') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Sélectionnez un client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                                {{ old('client_id', $quote?->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->display_name }}
                                            @if($client->type === 'professionnel' && $client->company_name)
                                                - {{ $client->company_name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date d'échéance --}}
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date d'échéance
                                </label>
                                <input type="date" name="due_date" id="due_date"
                                    value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Conditions de paiement --}}
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">
                                    Conditions de paiement
                                </label>
                                <input type="text" name="payment_terms" id="payment_terms"
                                    value="{{ old('payment_terms', $quote?->payment_terms ?? 'Paiement à 30 jours fin de mois') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    {{-- Lignes de la facture --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">📝 Lignes de la facture</h2>
                            <button type="button" onclick="addLine()"
                                class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Ajouter une ligne
                            </button>
                        </div>

                        @error('items')
                            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            </div>
                        @enderror

                        <div id="items-container" class="space-y-3">
                            {{-- Les lignes seront ajoutées ici dynamiquement --}}
                        </div>

                        {{-- Totaux --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total HT</span>
                                    <span id="display-total-ht" class="font-medium">0,00 €</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total TVA</span>
                                    <span id="display-total-tva" class="font-medium">0,00 €</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                                    <span>Total TTC</span>
                                    <span id="display-total-ttc" class="text-indigo-600">0,00 €</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Notes</h2>

                        <div>
                            <label for="internal_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes internes (non visibles sur la facture)
                            </label>
                            <textarea name="internal_notes" id="internal_notes" rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Notes internes pour votre équipe...">{{ old('internal_notes', $quote?->internal_notes) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-4">
                    {{-- Actions --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Créer la facture
                            </button>
                            <a href="{{ route('invoices.index') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                                Annuler
                            </a>
                        </div>
                    </div>

                    {{-- Aide --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-3">💡 Aide</h3>
                        <ul class="space-y-2 text-sm text-blue-800">
                            <li>• Le client est obligatoire</li>
                            <li>• Ajoutez au moins une ligne à la facture</li>
                            <li>• Les totaux se calculent automatiquement</li>
                            <li>• TVA par défaut : 20%</li>
                            <li>• La facture sera créée en brouillon</li>
                        </ul>
                    </div>

                    @if($quote)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-green-900 mb-3">📄 Devis source</h3>
                            <p class="text-sm text-green-800">
                                Cette facture est créée depuis le devis <strong>{{ $quote->quote_number }}</strong>
                            </p>
                            <a href="{{ route('quotes.show', $quote) }}"
                               class="mt-2 text-sm text-green-700 hover:text-green-900 underline">
                                Voir le devis →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Template pour une ligne --}}
    <template id="line-template">
        <div class="line-item border border-gray-300 rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition">
            <div class="flex justify-between items-start mb-3">
                <span class="line-number text-sm font-medium text-gray-700"></span>
                <button type="button" onclick="removeLine(this)"
                    class="text-red-600 hover:text-red-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-12 gap-3">
                <div class="col-span-12">
                    <input type="text" name="items[INDEX][description]"
                        placeholder="Description de la prestation" required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-3">
                    <input type="number" name="items[INDEX][quantity]"
                        placeholder="Qté" step="0.01" min="0.01" value="1" required
                        onchange="calculateLine(this)"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-3">
                    <input type="number" name="items[INDEX][unit_price]"
                        placeholder="Prix unit." step="0.01" min="0" required
                        onchange="calculateLine(this)"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-3">
                    <input type="number" name="items[INDEX][tva_rate]"
                        placeholder="TVA %" step="0.01" min="0" max="100" value="20" required
                        onchange="calculateLine(this)"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-3">
                    <input type="text" readonly
                        class="line-total block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-right"
                        value="0,00 €">
                </div>
            </div>
        </div>
    </template>

    <script>
        let lineIndex = 0;

        // Ajouter une ligne
        function addLine(description = '', quantity = 1, unit_price = '', tva_rate = 20) {
            const template = document.getElementById('line-template');
            const container = document.getElementById('items-container');

            const clone = template.content.cloneNode(true);
            const html = clone.firstElementChild.outerHTML.replaceAll('INDEX', lineIndex);
            container.insertAdjacentHTML('beforeend', html);

            // Remplir les valeurs si fournies
            const line = container.lastElementChild;
            if (description) line.querySelector('input[name*="[description]"]').value = description;
            if (quantity) line.querySelector('input[name*="[quantity]"]').value = quantity;
            if (unit_price) line.querySelector('input[name*="[unit_price]"]').value = unit_price;
            if (tva_rate) line.querySelector('input[name*="[tva_rate]"]').value = tva_rate;

            lineIndex++;
            updateLineNumbers();
            calculateTotals();
        }

        // Supprimer une ligne
        function removeLine(button) {
            if (document.querySelectorAll('.line-item').length <= 1) {
                alert('Vous devez garder au moins une ligne');
                return;
            }
            button.closest('.line-item').remove();
            updateLineNumbers();
            calculateTotals();
        }

        // Mettre à jour les numéros de ligne
        function updateLineNumbers() {
            document.querySelectorAll('.line-item').forEach((line, index) => {
                line.querySelector('.line-number').textContent = `Ligne ${index + 1}`;
            });
        }

        // Calculer le total d'une ligne
        function calculateLine(input) {
            const line = input.closest('.line-item');
            const quantity = parseFloat(line.querySelector('input[name*="[quantity]"]').value) || 0;
            const unitPrice = parseFloat(line.querySelector('input[name*="[unit_price]"]').value) || 0;
            const tvaRate = parseFloat(line.querySelector('input[name*="[tva_rate]"]').value) || 0;

            const totalHT = quantity * unitPrice;
            const totalTTC = totalHT * (1 + tvaRate / 100);

            line.querySelector('.line-total').value = formatCurrency(totalTTC);

            calculateTotals();
        }

        // Calculer les totaux généraux
        function calculateTotals() {
            let totalHT = 0;
            let totalTVA = 0;

            document.querySelectorAll('.line-item').forEach(line => {
                const quantity = parseFloat(line.querySelector('input[name*="[quantity]"]').value) || 0;
                const unitPrice = parseFloat(line.querySelector('input[name*="[unit_price]"]').value) || 0;
                const tvaRate = parseFloat(line.querySelector('input[name*="[tva_rate]"]').value) || 0;

                const lineHT = quantity * unitPrice;
                const lineTVA = lineHT * (tvaRate / 100);

                totalHT += lineHT;
                totalTVA += lineTVA;
            });

            const totalTTC = totalHT + totalTVA;

            document.getElementById('display-total-ht').textContent = formatCurrency(totalHT);
            document.getElementById('display-total-tva').textContent = formatCurrency(totalTVA);
            document.getElementById('display-total-ttc').textContent = formatCurrency(totalTTC);
        }

        // Formater en devise
        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }

        // Validation du formulaire
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            const itemsCount = document.querySelectorAll('.line-item').length;
            if (itemsCount === 0) {
                e.preventDefault();
                alert('Vous devez ajouter au moins une ligne à la facture.');
                return false;
            }
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            @if($quote && $quote->items->count() > 0)
                // Charger les lignes depuis le devis
                @foreach($quote->items as $item)
                    addLine(
                        @json($item->description),
                        {{ $item->quantity }},
                        {{ $item->unit_price }},
                        {{ $item->tva_rate }}
                    );
                @endforeach
            @else
                // Ajouter une ligne vide par défaut
                addLine();
            @endif
        });
    </script>
@endsection