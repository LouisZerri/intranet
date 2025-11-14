// Calculateur dynamique pour les états des lieux
// À intégrer dans quotes/create.blade.php et quotes/edit.blade.php

function initEtatDesLieuxCalculator() {
    // Modal HTML
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
                    <!-- Type de bien -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de bien *</label>
                        <select id="edl-type-bien" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionnez...</option>
                            <option value="appartement">Appartement</option>
                            <option value="maison">Maison (+80€)</option>
                            <option value="local">Local professionnel (10€/m²)</option>
                        </select>
                    </div>

                    <!-- Surface (pour appartement/maison) -->
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

                    <!-- Surface en m² (pour local professionnel) -->
                    <div id="edl-surface-m2-section" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Surface en m² *</label>
                        <input type="number" id="edl-surface-m2" min="1" step="1" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ex: 80">
                        <p class="mt-1 text-xs text-gray-500">Tarif: 10€/m²</p>
                    </div>

                    <!-- Meublé -->
                    <div id="edl-meuble-section" class="hidden">
                        <label class="flex items-center">
                            <input type="checkbox" id="edl-meuble" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">Logement meublé (+55€)</span>
                        </label>
                    </div>

                    <!-- Récapitulatif du calcul -->
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
                    <button onclick="addEDLToQuote()" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                        Ajouter au devis
                    </button>
                </div>
            </div>
        </div>
    `;

    // Injecter le modal dans le DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Event listeners
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
    
    // Reset
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
    let description = 'État des lieux';
    
    if (typeBien === 'local') {
        const surfaceM2 = parseFloat(document.getElementById('edl-surface-m2').value) || 0;
        if (surfaceM2 > 0) {
            basePrice = surfaceM2 * 10;
            description = `État des lieux - Local professionnel (${surfaceM2}m²)`;
            
            document.getElementById('edl-base-price').textContent = formatPrice(basePrice);
            document.getElementById('edl-total-price').textContent = formatPrice(basePrice);
            document.getElementById('edl-maison-line').classList.add('hidden');
            document.getElementById('edl-meuble-line').classList.add('hidden');
            document.getElementById('edl-recap').classList.remove('hidden');
        } else {
            document.getElementById('edl-recap').classList.add('hidden');
        }
        return;
    }
    
    // Appartement ou Maison
    const surfaceSelect = document.getElementById('edl-surface');
    const selectedOption = surfaceSelect.options[surfaceSelect.selectedIndex];
    
    if (!surfaceSelect.value) {
        document.getElementById('edl-recap').classList.add('hidden');
        return;
    }
    
    basePrice = parseFloat(surfaceSelect.value);
    const surfaceLabel = selectedOption.getAttribute('data-label');
    description = `État des lieux - ${surfaceLabel}`;
    
    let totalPrice = basePrice;
    
    // Supplément maison
    if (typeBien === 'maison') {
        totalPrice += 80;
        description += ' (Maison)';
        document.getElementById('edl-maison-line').classList.remove('hidden');
    } else {
        document.getElementById('edl-maison-line').classList.add('hidden');
    }
    
    // Supplément meublé
    const meuble = document.getElementById('edl-meuble').checked;
    if (meuble) {
        totalPrice += 55;
        description += ' + Meublé';
        document.getElementById('edl-meuble-line').classList.remove('hidden');
    } else {
        document.getElementById('edl-meuble-line').classList.add('hidden');
    }
    
    document.getElementById('edl-base-price').textContent = formatPrice(basePrice);
    document.getElementById('edl-total-price').textContent = formatPrice(totalPrice);
    document.getElementById('edl-recap').classList.remove('hidden');
}

function addEDLToQuote() {
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
    
    // Ajouter la ligne au devis
    addLine(description, 1, price, 20);
    
    // Fermer le modal
    closeEDLCalculator();
    
    // Message de confirmation visuelle
    const message = document.createElement('div');
    message.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    message.textContent = '✅ État des lieux ajouté au devis';
    document.body.appendChild(message);
    
    setTimeout(() => {
        message.remove();
    }, 3000);
}

function formatPrice(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    initEtatDesLieuxCalculator();
});