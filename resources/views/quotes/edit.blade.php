@extends('layouts.app')

@section('title', 'Modifier le devis ' . $quote->quote_number)

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">✏️ Modifier le devis</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                        {{ $quote->status_label }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">Devis {{ $quote->quote_number }}</p>
            </div>
            <a href="{{ route('quotes.show', $quote) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                ← Retour
            </a>
        </div>

        {{-- Avertissement si devis envoyé --}}
        @if($quote->status === 'envoye')
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-medium">Ce devis a déjà été envoyé au client</p>
                        <p class="text-sm mt-1">Les modifications seront visibles mais le client devra recevoir une nouvelle version.</p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('quotes.update', $quote) }}" id="quoteForm">
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
                                                {{ old('client_id', $quote->client_id) == $client->id ? 'selected' : '' }}>
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

                            {{-- Date de validité --}}
                            <div>
                                <label for="validity_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Valide jusqu'au
                                </label>
                                <input type="date" name="validity_date" id="validity_date"
                                    value="{{ old('validity_date', $quote->validity_date?->format('Y-m-d')) }}"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
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

                    {{-- Lignes du devis --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">📝 Lignes du devis</h2>
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

                    {{-- Notes et conditions --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Notes et conditions</h2>
                        
                        <div class="space-y-4">
                            {{-- Notes client (visibles sur le devis) --}}
                            <div>
                                <label for="client_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes client (visibles sur le devis)
                                </label>
                                <textarea name="client_notes" id="client_notes" rows="3"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Notes qui apparaîtront sur le devis...">{{ old('client_notes', $quote->client_notes) }}</textarea>
                            </div>

                            {{-- Conditions de paiement --}}
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">
                                    Conditions de paiement
                                </label>
                                <input type="text" name="payment_terms" id="payment_terms"
                                    value="{{ old('payment_terms', $quote->payment_terms ?? 'Paiement à 30 jours fin de mois') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Conditions de livraison --}}
                            <div>
                                <label for="delivery_terms" class="block text-sm font-medium text-gray-700 mb-2">
                                    Conditions de livraison
                                </label>
                                <input type="text" name="delivery_terms" id="delivery_terms"
                                    value="{{ old('delivery_terms', $quote->delivery_terms) }}"
                                    placeholder="Ex: Livraison sous 15 jours ouvrés"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Notes internes (non visibles) --}}
                            <div>
                                <label for="internal_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes internes (non visibles sur le devis)
                                </label>
                                <textarea name="internal_notes" id="internal_notes" rows="2"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Notes internes pour votre équipe...">{{ old('internal_notes', $quote->internal_notes) }}</textarea>
                            </div>
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
                            <a href="{{ route('quotes.show', $quote) }}" 
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
                            <li>• Ajoutez au moins une ligne au devis</li>
                            <li>• Les totaux se calculent automatiquement</li>
                            <li>• TVA par défaut : 20%</li>
                        </ul>
                    </div>

                    {{-- Informations --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">ℹ️ Informations</h3>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500">Créé le</dt>
                                <dd class="text-gray-900 font-medium">{{ $quote->created_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Par</dt>
                                <dd class="text-gray-900 font-medium">{{ $quote->user->full_name }}</dd>
                            </div>
                            @if($quote->sent_at)
                                <div>
                                    <dt class="text-gray-500">Envoyé le</dt>
                                    <dd class="text-gray-900 font-medium">{{ $quote->sent_at->format('d/m/Y à H:i') }}</dd>
                                </div>
                            @endif
                            @if($quote->updated_at && $quote->updated_at != $quote->created_at)
                                <div>
                                    <dt class="text-gray-500">Modifié le</dt>
                                    <dd class="text-gray-900 font-medium">{{ $quote->updated_at->format('d/m/Y à H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
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

        function togglePredefinedServices() {
            const list = document.getElementById('predefined-services-list');
            const toggleText = document.getElementById('toggle-text');
            
            list.classList.toggle('hidden');
            toggleText.textContent = list.classList.contains('hidden') ? 'Afficher' : 'Masquer';
        }

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

        document.getElementById('quoteForm').addEventListener('submit', function(e) {
            const itemsCount = document.querySelectorAll('.line-item').length;
            if (itemsCount === 0) {
                e.preventDefault();
                alert('Vous devez ajouter au moins une ligne au devis.');
                return false;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            @if($quote->items->count() > 0)
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
@endsection