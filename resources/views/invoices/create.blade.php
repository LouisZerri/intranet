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

                    {{-- Calculateur État des Lieux --}}
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl">🏠</span>
                                <div>
                                    <h3 class="text-lg font-semibold text-green-900">Calculateur État des Lieux</h3>
                                    <p class="text-sm text-green-700">Calculez automatiquement le tarif avec les suppléments (maison, meublé...)</p>
                                </div>
                            </div>
                            <button type="button" 
                                    onclick="openEDLCalculator()"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition shadow-md hover:shadow-lg">
                                🧮 Ouvrir le calculateur
                            </button>
                        </div>
                    </div>

                    {{-- Prestations prédéfinies --}}
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">⚡</span>
                                <h2 class="text-lg font-semibold text-indigo-900">Prestations prédéfinies</h2>
                            </div>
                            <button type="button" 
                                    onclick="togglePredefinedServices()"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                <span id="toggle-text">Afficher</span> ▼
                            </button>
                        </div>
                        
                        <p class="text-sm text-indigo-700 mb-4">
                            Cliquez sur une prestation pour l'ajouter automatiquement avec son tarif
                        </p>

                        <div id="predefined-services-list" class="hidden space-y-2 max-h-96 overflow-y-auto">
                            @if(isset($predefinedServices) && $predefinedServices->count() > 0)
                                @foreach($predefinedServices as $service)
                                    <div class="prestation-item bg-white border border-indigo-200 rounded-lg p-3 hover:bg-indigo-50 transition cursor-pointer"
                                         data-category="{{ $service->category }}"
                                         onclick="addPredefinedService(
                                             {{ Js::from($service->name) }}, 
                                             {{ $service->default_quantity }}, 
                                             {{ $service->default_price }}, 
                                             {{ $service->default_tva_rate }},
                                             {{ Js::from($service->description ?? '') }}
                                         )">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $service->name }}</p>
                                                @if($service->description)
                                                    <p class="text-xs text-gray-600 mt-1">{{ $service->description }}</p>
                                                @endif
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ $service->category_label }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">{{ $service->unit }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-indigo-600">{{ $service->formatted_price }}</p>
                                                <p class="text-xs text-gray-500">TVA {{ $service->default_tva_rate }}%</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <p>Aucune prestation prédéfinie disponible.</p>
                                    <p class="text-xs mt-2">Contactez l'administrateur pour en ajouter.</p>
                                </div>
                            @endif
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
                                Ligne vide
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
                            <li>• Utilisez le <strong>calculateur d'état des lieux</strong> pour un calcul automatique</li>
                            <li>• Cliquez sur une prestation prédéfinie pour l'ajouter rapidement</li>
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
                    <textarea name="items[INDEX][description]"
                        placeholder="Description de la prestation" required rows="2"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
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

        // Toggle affichage des prestations prédéfinies
        function togglePredefinedServices() {
            const list = document.getElementById('predefined-services-list');
            const toggleText = document.getElementById('toggle-text');
            
            list.classList.toggle('hidden');
            toggleText.textContent = list.classList.contains('hidden') ? 'Afficher' : 'Masquer';
        }

        // Ajouter une prestation prédéfinie
        function addPredefinedService(description, quantity, unit_price, tva_rate, fullDescription = '') {
            const finalDescription = fullDescription ? `${description}\n${fullDescription}` : description;
            addLine(finalDescription, quantity, unit_price, tva_rate);
            
            const btn = event.target.closest('.prestation-item');
            if (btn) {
                btn.classList.add('bg-green-100', 'border-green-400');
                setTimeout(() => {
                    btn.classList.remove('bg-green-100', 'border-green-400');
                }, 500);
            }
        }

        // Ajouter une ligne
        function addLine(description = '', quantity = 1, unit_price = '', tva_rate = 20) {
            const template = document.getElementById('line-template');
            const container = document.getElementById('items-container');

            const clone = template.content.cloneNode(true);
            const html = clone.firstElementChild.outerHTML.replaceAll('INDEX', lineIndex);
            container.insertAdjacentHTML('beforeend', html);

            const line = container.lastElementChild;
            if (description) line.querySelector('textarea[name*="[description]"]').value = description;
            if (quantity) line.querySelector('input[name*="[quantity]"]').value = quantity;
            if (unit_price) line.querySelector('input[name*="[unit_price]"]').value = unit_price;
            if (tva_rate) line.querySelector('input[name*="[tva_rate]"]').value = tva_rate;

            lineIndex++;
            updateLineNumbers();
            calculateTotals();
        }

        function removeLine(button) {
            if (document.querySelectorAll('.line-item').length <= 1) {
                alert('Vous devez garder au moins une ligne');
                return;
            }
            button.closest('.line-item').remove();
            updateLineNumbers();
            calculateTotals();
        }

        function updateLineNumbers() {
            document.querySelectorAll('.line-item').forEach((line, index) => {
                line.querySelector('.line-number').textContent = `Ligne ${index + 1}`;
            });
        }

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

        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }

        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            const itemsCount = document.querySelectorAll('.line-item').length;
            if (itemsCount === 0) {
                e.preventDefault();
                alert('Vous devez ajouter au moins une ligne à la facture.');
                return false;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            @if($quote && $quote->items->count() > 0)
                @foreach($quote->items as $item)
                    addLine(
                        {{ Js::from($item->description) }},
                        {{ $item->quantity }},
                        {{ $item->unit_price }},
                        {{ $item->tva_rate }}
                    );
                @endforeach
            @else
                addLine();
            @endif
        });
    </script>

    {{-- Script calculateur état des lieux --}}
    <script>
        function initEtatDesLieuxCalculator() {
            const modalHTML = `
                <div id="edl-calculator-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <h3 class="text-xl font-semibold text-gray-900">🏠 Calculateur État des Lieux</h3>
                            <button onclick="closeEDLCalculator()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type de bien *</label>
                                <select id="edl-type-bien" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Sélectionnez...</option>
                                    <option value="appartement">Appartement</option>
                                    <option value="maison">Maison (+80€)</option>
                                    <option value="local">Local professionnel (10€/m²)</option>
                                </select>
                            </div>

                            <div id="edl-surface-section" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Surface *</label>
                                <select id="edl-surface" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Sélectionnez...</option>
                                    <option value="150" data-label="Studio/T1 (<30m²)">Studio/T1 (<30m²) - 150€</option>
                                    <option value="170" data-label="T2 (30-50m²)">T2 (30-50m²) - 170€</option>
                                    <option value="200" data-label="T3 (50-70m²)">T3 (50-70m²) - 200€</option>
                                    <option value="240" data-label="T4 (70-90m²)">T4 (70-90m²) - 240€</option>
                                    <option value="330" data-label="T5 (jusqu'à 150m²)">T5 (jusqu'à 150m²) - 330€</option>
                                    <option value="430" data-label="T5+ (>150m²)">T5+ (>150m²) - 430€</option>
                                </select>
                            </div>

                            <div id="edl-surface-m2-section" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Surface en m² *</label>
                                <input type="number" id="edl-surface-m2" min="1" step="1" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ex: 80">
                                <p class="mt-1 text-xs text-gray-500">Tarif: 10€/m²</p>
                            </div>

                            <div id="edl-meuble-section" class="hidden">
                                <label class="flex items-center">
                                    <input type="checkbox" id="edl-meuble" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                                    <span class="text-sm font-medium text-gray-700">Logement meublé (+55€)</span>
                                </label>
                            </div>

                            <div id="edl-recap" class="hidden mt-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                                <h4 class="font-semibold text-indigo-900 mb-3">📊 Récapitulatif</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Tarif de base:</span>
                                        <span id="edl-base-price" class="font-medium">0,00 €</span>
                                    </div>
                                    <div id="edl-maison-line" class="hidden flex justify-between">
                                        <span class="text-gray-700">Supplément maison:</span>
                                        <span class="font-medium">+ 80,00 €</span>
                                    </div>
                                    <div id="edl-meuble-line" class="hidden flex justify-between">
                                        <span class="text-gray-700">Supplément meublé:</span>
                                        <span class="font-medium">+ 55,00 €</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-indigo-300">
                                        <span class="font-bold text-indigo-900">Total TTC:</span>
                                        <span id="edl-total-price" class="font-bold text-lg text-indigo-600">0,00 €</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                            <button onclick="closeEDLCalculator()" 
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium">
                                Annuler
                            </button>
                            <button onclick="addEDLToInvoice()" 
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                                Ajouter à la facture
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            document.getElementById('edl-type-bien').addEventListener('change', updateEDLFields);
            document.getElementById('edl-surface').addEventListener('change', calculateEDL);
            document.getElementById('edl-surface-m2').addEventListener('input', calculateEDL);
            document.getElementById('edl-meuble').addEventListener('change', calculateEDL);
        }

        function openEDLCalculator() {
            document.getElementById('edl-calculator-modal').classList.remove('hidden');
            resetEDLCalculator();
        }

        function closeEDLCalculator() {
            document.getElementById('edl-calculator-modal').classList.add('hidden');
        }

        function resetEDLCalculator() {
            document.getElementById('edl-type-bien').value = '';
            document.getElementById('edl-surface').value = '';
            document.getElementById('edl-surface-m2').value = '';
            document.getElementById('edl-meuble').checked = false;
            
            document.getElementById('edl-surface-section').classList.add('hidden');
            document.getElementById('edl-surface-m2-section').classList.add('hidden');
            document.getElementById('edl-meuble-section').classList.add('hidden');
            document.getElementById('edl-recap').classList.add('hidden');
        }

        function updateEDLFields() {
            const typeBien = document.getElementById('edl-type-bien').value;
            
            document.getElementById('edl-surface-section').classList.add('hidden');
            document.getElementById('edl-surface-m2-section').classList.add('hidden');
            document.getElementById('edl-meuble-section').classList.add('hidden');
            document.getElementById('edl-recap').classList.add('hidden');
            
            if (typeBien === 'appartement' || typeBien === 'maison') {
                document.getElementById('edl-surface-section').classList.remove('hidden');
                document.getElementById('edl-meuble-section').classList.remove('hidden');
            } else if (typeBien === 'local') {
                document.getElementById('edl-surface-m2-section').classList.remove('hidden');
            }
            
            calculateEDL();
        }

        function calculateEDL() {
            const typeBien = document.getElementById('edl-type-bien').value;
            
            if (!typeBien) {
                document.getElementById('edl-recap').classList.add('hidden');
                return;
            }
            
            let basePrice = 0;
            
            if (typeBien === 'local') {
                const surfaceM2 = parseFloat(document.getElementById('edl-surface-m2').value) || 0;
                if (surfaceM2 > 0) {
                    basePrice = surfaceM2 * 10;
                    document.getElementById('edl-base-price').textContent = formatCurrency(basePrice);
                    document.getElementById('edl-total-price').textContent = formatCurrency(basePrice);
                    document.getElementById('edl-maison-line').classList.add('hidden');
                    document.getElementById('edl-meuble-line').classList.add('hidden');
                    document.getElementById('edl-recap').classList.remove('hidden');
                } else {
                    document.getElementById('edl-recap').classList.add('hidden');
                }
                return;
            }
            
            const surfaceSelect = document.getElementById('edl-surface');
            if (!surfaceSelect.value) {
                document.getElementById('edl-recap').classList.add('hidden');
                return;
            }
            
            basePrice = parseFloat(surfaceSelect.value);
            let totalPrice = basePrice;
            
            if (typeBien === 'maison') {
                totalPrice += 80;
                document.getElementById('edl-maison-line').classList.remove('hidden');
            } else {
                document.getElementById('edl-maison-line').classList.add('hidden');
            }
            
            const meuble = document.getElementById('edl-meuble').checked;
            if (meuble) {
                totalPrice += 55;
                document.getElementById('edl-meuble-line').classList.remove('hidden');
            } else {
                document.getElementById('edl-meuble-line').classList.add('hidden');
            }
            
            document.getElementById('edl-base-price').textContent = formatCurrency(basePrice);
            document.getElementById('edl-total-price').textContent = formatCurrency(totalPrice);
            document.getElementById('edl-recap').classList.remove('hidden');
        }

        function addEDLToInvoice() {
            const typeBien = document.getElementById('edl-type-bien').value;
            
            if (!typeBien) {
                alert('Veuillez sélectionner un type de bien');
                return;
            }
            
            let description = '';
            let price = 0;
            
            if (typeBien === 'local') {
                const surfaceM2 = parseFloat(document.getElementById('edl-surface-m2').value) || 0;
                if (surfaceM2 <= 0) {
                    alert('Veuillez saisir une surface valide');
                    return;
                }
                description = `État des lieux - Local professionnel (${surfaceM2}m²)`;
                price = surfaceM2 * 10;
            } else {
                const surfaceSelect = document.getElementById('edl-surface');
                if (!surfaceSelect.value) {
                    alert('Veuillez sélectionner une surface');
                    return;
                }
                
                const selectedOption = surfaceSelect.options[surfaceSelect.selectedIndex];
                const basePrice = parseFloat(surfaceSelect.value);
                const surfaceLabel = selectedOption.getAttribute('data-label');
                
                description = `État des lieux - ${surfaceLabel}`;
                price = basePrice;
                
                if (typeBien === 'maison') {
                    price += 80;
                    description += '\nSupplément maison: +80€';
                }
                
                const meuble = document.getElementById('edl-meuble').checked;
                if (meuble) {
                    price += 55;
                    description += '\nSupplément meublé: +55€';
                }
            }
            
            addLine(description, 1, price, 20);
            closeEDLCalculator();
            
            const message = document.createElement('div');
            message.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            message.textContent = '✅ État des lieux ajouté à la facture';
            document.body.appendChild(message);
            
            setTimeout(() => {
                message.remove();
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            initEtatDesLieuxCalculator();
        });
    </script>
@endsection