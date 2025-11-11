@extends('layouts.app')

@section('title', 'Devis ' . $quote->quote_number)

@section('content')
    <div class="space-y-6">
        {{-- En-tête avec actions --}}
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">Devis {{ $quote->quote_number }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                        {{ $quote->status_label }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">Créé le {{ $quote->created_at->format('d/m/Y à H:i') }} par {{ $quote->user->full_name }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('quotes.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    ← Retour
                </a>
                
                {{-- Actions selon le statut --}}
                @if($quote->canBeEdited())
                    <a href="{{ route('quotes.edit', $quote) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        ✏️ Modifier
                    </a>
                @endif

                @if($quote->status === 'brouillon')
                    <form method="POST" action="{{ route('quotes.send', $quote) }}" class="inline">
                        @csrf
                        <button type="submit" 
                            onclick="return confirm('Envoyer ce devis au client ?')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            📤 Envoyer au client
                        </button>
                    </form>
                @endif

                @if($quote->status === 'envoye')
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('quotes.accept', $quote) }}" class="inline">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Accepter ce devis ? Une mission sera créée automatiquement.')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                ✅ Accepter
                            </button>
                        </form>
                        <form method="POST" action="{{ route('quotes.refuse', $quote) }}" class="inline">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Refuser ce devis ?')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                ❌ Refuser
                            </button>
                        </form>
                    </div>
                @endif

                @if($quote->canBeConverted())
                    <form method="POST" action="{{ route('quotes.convert', $quote) }}" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Convertir ce devis en facture ?')"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                            💰 Convertir en facture
                        </button>
                    </form>
                @endif

                {{-- Dropdown avec styles inline pour forcer la visibilité --}}
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
                            <a href="{{ route('quotes.pdf', $quote) }}" target="_blank"
                                style="display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;"
                                onmouseover="this.style.backgroundColor='#f3f4f6'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <svg style="width: 1rem; height: 1rem; margin-right: 0.75rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Télécharger PDF
                            </a>
                            <a href="{{ route('clients.show', $quote->client) }}"
                                style="display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;"
                                onmouseover="this.style.backgroundColor='#f3f4f6'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <svg style="width: 1rem; height: 1rem; margin-right: 0.75rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Voir le client
                            </a>
                            
                            @if($quote->status === 'brouillon')
                                <div style="border-top: 1px solid #e5e7eb; margin: 0.25rem 0;"></div>
                                <form method="POST" action="{{ route('quotes.destroy', $quote) }}"
                                    onsubmit="return confirm('⚠️ Supprimer définitivement ce devis ?')" style="display: block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        style="width: 100%; text-align: left; display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; color: #dc2626; background: transparent; border: none; cursor: pointer;"
                                        onmouseover="this.style.backgroundColor='#fef2f2'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                        <svg style="width: 1rem; height: 1rem; margin-right: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Supprimer le devis
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
                            <p class="text-lg font-medium text-gray-900">{{ $quote->client->display_name }}</p>
                            @if($quote->client->type === 'professionnel' && $quote->client->company_name)
                                <p class="text-sm text-gray-600">{{ $quote->client->company_name }}</p>
                            @endif
                            @if($quote->client->email)
                                <p class="text-sm text-gray-600 mt-2">📧 {{ $quote->client->email }}</p>
                            @endif
                            @if($quote->client->phone)
                                <p class="text-sm text-gray-600">📞 {{ $quote->client->phone }}</p>
                            @endif
                        </div>
                        <a href="{{ route('clients.show', $quote->client) }}"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            Voir la fiche →
                        </a>
                    </div>
                </div>

                {{-- Détails du devis --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Détails du devis</h2>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Type de prestation</p>
                            <p class="text-base font-medium text-gray-900">{{ $quote->service_label }}</p>
                        </div>
                        @if($quote->validity_date)
                            <div>
                                <p class="text-sm text-gray-500">Valide jusqu'au</p>
                                <p class="text-base font-medium text-gray-900">{{ $quote->validity_date->format('d/m/Y') }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Lignes du devis --}}
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
                                @foreach($quote->items as $item)
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
                                    <td class="px-4 py-3 text-sm font-medium text-right">{{ $quote->formatted_total_ht }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Total TVA</td>
                                    <td class="px-4 py-3 text-sm font-medium text-right">{{ $quote->formatted_total_tva }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-base font-bold text-gray-900 text-right">Total TTC</td>
                                    <td class="px-4 py-3 text-lg font-bold text-indigo-600 text-right">{{ $quote->formatted_total_ttc }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Notes --}}
                @if($quote->client_notes || $quote->payment_terms)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Notes et conditions</h2>
                        
                        @if($quote->client_notes)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Notes client</p>
                                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $quote->client_notes }}</p>
                            </div>
                        @endif

                        @if($quote->payment_terms)
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-2">Conditions de paiement</p>
                                <p class="text-sm text-gray-600">{{ $quote->payment_terms }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Statut et informations --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">ℹ️ Informations</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                    {{ $quote->status_label }}
                                </span>
                            </dd>
                        </div>
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
                        @if($quote->accepted_at)
                            <div>
                                <dt class="text-gray-500">Accepté le</dt>
                                <dd class="text-gray-900 font-medium">{{ $quote->accepted_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                        @if($quote->validity_date)
                            <div>
                                <dt class="text-gray-500">Validité</dt>
                                <dd class="text-gray-900 font-medium">Jusqu'au {{ $quote->validity_date->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Montants --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-indigo-900 mb-4">💰 Montants</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-indigo-700">Total HT</dt>
                            <dd class="font-medium text-indigo-900">{{ $quote->formatted_total_ht }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-indigo-700">TVA</dt>
                            <dd class="font-medium text-indigo-900">{{ $quote->formatted_total_tva }}</dd>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-indigo-200">
                            <dt class="font-semibold text-indigo-900">Total TTC</dt>
                            <dd class="text-lg font-bold text-indigo-900">{{ $quote->formatted_total_ttc }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Relations --}}
                @if($quote->mission || $quote->invoice)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">🔗 Documents liés</h3>
                        <div class="space-y-2">
                            @if($quote->mission)
                                <a href="{{ route('missions.show', $quote->mission) }}"
                                    class="flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Mission créée →
                                </a>
                            @endif
                            @if($quote->invoice)
                                <a href="{{ route('invoices.show', $quote->invoice) }}"
                                    class="flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Facture {{ $quote->invoice->invoice_number }} →
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Notes internes --}}
                @if($quote->internal_notes)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-yellow-900 mb-2">📝 Notes internes</h3>
                        <p class="text-sm text-yellow-800 whitespace-pre-line">{{ $quote->internal_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- JavaScript pour le dropdown --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownButton = document.getElementById('dropdownButton');
            const dropdownMenu = document.getElementById('dropdownMenu');

            if (dropdownButton && dropdownMenu) {
                // Toggle dropdown au clic
                dropdownButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '') {
                        dropdownMenu.style.display = 'block';
                    } else {
                        dropdownMenu.style.display = 'none';
                    }
                });

                // Fermer si on clique ailleurs
                document.addEventListener('click', function(e) {
                    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection