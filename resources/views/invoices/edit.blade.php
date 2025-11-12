@extends('layouts.app')

@section('title', 'Modifier la facture ' . $invoice->invoice_number)

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">✏️ Modifier la facture</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                        {{ $invoice->status_label }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">Facture {{ $invoice->invoice_number }}</p>
            </div>
            <a href="{{ route('invoices.show', $invoice) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                ← Retour
            </a>
        </div>

        <form method="POST" action="{{ route('invoices.update', $invoice) }}" id="invoiceForm">
            @csrf
            @method('PUT')

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
                                                {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
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
                                    value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Conditions de paiement --}}
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">
                                    Conditions de paiement
                                </label>
                                <input type="text" name="payment_terms" id="payment_terms"
                                    value="{{ old('payment_terms', $invoice->payment_terms) }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    {{-- Prestations prédéfinies --}}
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">⚡</span>
                                <h2 class="text-lg font-semibold text-green-900">Prestations prédéfinies</h2>
                            </div>
                            <button type="button" 
                                    onclick="togglePredefinedServices()"
                                    class="text-green-600 hover:text-green-800 text-sm font-medium">
                                <span id="toggle-text">Afficher</span> ▼
                            </button>
                        </div>
                        
                        <p class="text-sm text-green-700 mb-4">
                            Cliquez sur une prestation pour l'ajouter automatiquement avec son tarif
                        </p>

                        <div id="predefined-services-list" class="hidden space-y-2 max-h-96 overflow-y-auto">
                            @if(isset($predefinedServices) && $predefinedServices->count() > 0)
                                @foreach($predefinedServices as $service)
                                    <div class="prestation-item bg-white border border-green-200 rounded-lg p-3 hover:bg-green-50 transition cursor-pointer"
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
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $service->category_label }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">{{ $service->unit }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-green-600">{{ $service->formatted_price }}</p>
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
                                placeholder="Notes internes pour votre équipe...">{{ old('internal_notes', $invoice->internal_notes) }}</textarea>
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
                                Enregistrer les modifications
                            </button>
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                                Annuler
                            </a>
                        </div>
                    </div>

                    {{-- Aide --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-3">💡 Aide</h3>
                        <ul class="space-y-2 text-sm text-blue-800">
                            <li>• Cliquez sur une prestation prédéfinie pour l'ajouter rapidement</li>
                            <li>• Le client est obligatoire</li>
                            <li>• Ajoutez au moins une ligne à la facture</li>
                            <li>• Les totaux se calculent automatiquement</li>
                            <li>• TVA par défaut : 20%</li>
                            <li>• La facture doit être en brouillon ou émise</li>
                            <li>• Les modifications seront enregistrées</li>
                        </ul>
                    </div>
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

        // Toggle affichage des prestations prédéfinies
        function togglePredefinedServices() {
            const list = document.getElementById('predefined-services-list');
            const toggleText = document.getElementById('toggle-text');
            
            list.classList.toggle('hidden');
            toggleText.textContent = list.classList.contains('hidden') ? 'Afficher' : 'Masquer';
        }

        // Ajouter une prestation prédéfinie
        function addPredefinedService(description, quantity, unit_price, tva_rate, fullDescription = '') {
            // Si on a une description complète, on la combine avec le nom
            const finalDescription = fullDescription ? `${description}\n${fullDescription}` : description;
            addLine(finalDescription, quantity, unit_price, tva_rate);
            
            // Notification visuelle
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
            @if($invoice->items->count() > 0)
                // Charger les lignes existantes
                @foreach($invoice->items as $item)
                    addLine(
                        {{ Js::from($item->description) }},
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