@extends('layouts.app')

@section('title', 'Panier - Intranet')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('communication.index') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Continuer mes achats
        </a>
    </div>

    <!-- Panier -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Mon Panier
            </h1>
        </div>

        @if(count($products) > 0)
            <form method="POST" action="{{ route('communication.update-cart') }}">
                @csrf
                @method('PUT')
                
                <div class="p-6 space-y-4">
                    @foreach($products as $item)
                        <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <!-- Image -->
                            <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded overflow-hidden">
                                @if($item['product']->image)
                                    <img src="{{ $item['product']->image_url }}" 
                                         alt="{{ $item['product']->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Informations produit -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-medium text-gray-900">{{ $item['product']->name }}</h3>
                                @if($item['product']->reference)
                                    <p class="text-xs text-gray-500 mt-1">Réf: {{ $item['product']->reference }}</p>
                                @endif
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ number_format($item['product']->price, 2, ',', ' ') }} € l'unité
                                </p>
                            </div>

                            <!-- Quantité -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm text-gray-600">Qté:</label>
                                <input type="number" 
                                       name="quantities[{{ $item['product']->id }}]" 
                                       value="{{ $item['quantity'] }}" 
                                       min="0" 
                                       max="{{ $item['product']->stock_quantity }}"
                                       class="w-20 px-2 py-1 text-center border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <!-- Sous-total -->
                            <div class="text-right">
                                <p class="text-base font-semibold text-gray-900">
                                    {{ number_format($item['subtotal'], 2, ',', ' ') }} €
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 pb-6 flex items-center justify-between">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Mettre à jour le panier
                    </button>

                    <form method="POST" action="{{ route('communication.clear-cart') }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Êtes-vous sûr de vouloir vider le panier ?')"
                                class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Vider le panier
                        </button>
                    </form>
                </div>
            </form>

            <!-- Récapitulatif et commande -->
            <div class="border-t border-gray-200 bg-gray-50 p-6">
                <div class="max-w-md ml-auto space-y-4">
                    <div class="flex items-center justify-between text-base">
                        <span class="text-gray-600">Sous-total</span>
                        <span class="font-medium text-gray-900">{{ number_format($total, 2, ',', ' ') }} €</span>
                    </div>
                    
                    <div class="flex items-center justify-between text-lg font-semibold border-t border-gray-300 pt-4">
                        <span class="text-gray-900">Total</span>
                        <span class="text-indigo-600">{{ number_format($total, 2, ',', ' ') }} €</span>
                    </div>

                    <form method="POST" action="{{ route('communication.place-order') }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Commentaires (optionnel)
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Instructions de livraison, remarques..."
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-base font-medium rounded-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Passer la commande
                        </button>
                    </form>

                    <p class="text-xs text-gray-500 text-center">
                        Un email de confirmation sera envoyé à gestimmo.presta@gmail.com
                    </p>
                </div>
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Votre panier est vide</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Commencez par ajouter des produits depuis notre catalogue
                </p>
                <div class="mt-6">
                    <a href="{{ route('communication.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Voir le catalogue
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection