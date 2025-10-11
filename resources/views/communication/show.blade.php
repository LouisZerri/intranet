@extends('layouts.app')

@section('title', $product->name . ' - Intranet')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Navigation de retour -->
    <div class="flex items-center justify-between">
        <a href="{{ route('communication.index') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au catalogue
        </a>
        
        <a href="{{ route('communication.cart') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Voir le panier
            @if(session('communication_cart') && count(session('communication_cart')) > 0)
                <span class="ml-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                    {{ array_sum(session('communication_cart')) }}
                </span>
            @endif
        </a>
    </div>

    <!-- Détails du produit -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <!-- Image du produit -->
            <div class="bg-gray-100 p-8 flex items-center justify-center">
                @if($product->image)
                    <img src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}" 
                         class="max-w-full max-h-96 object-contain rounded-lg shadow-md">
                @else
                    <div class="w-full h-96 flex items-center justify-center bg-gray-200 rounded-lg">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Informations et formulaire -->
            <div class="p-8">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    
                    @if($product->reference)
                        <p class="text-sm text-gray-500 mb-4">Référence: {{ $product->reference }}</p>
                    @endif

                    @if(!$product->isAvailable())
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Produit indisponible
                        </div>
                    @endif
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">📄 Description</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $product->description }}</p>
                </div>

                <div class="mb-6">
                    <div class="flex items-baseline space-x-4">
                        @if($product->price)
                            <span class="text-3xl font-bold text-indigo-600">{{ $product->formatted_price }}</span>
                        @else
                            <span class="text-lg text-gray-500 italic">Prix sur demande</span>
                        @endif
                        
                        @if($product->stock_quantity > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $product->stock_quantity }} en stock
                            </span>
                        @endif
                    </div>
                </div>

                @if($product->isAvailable())
                    <form method="POST" action="{{ route('communication.add-to-cart', $product) }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Quantité
                            </label>
                            <div class="flex items-center space-x-3">
                                <button type="button" 
                                        onclick="decreaseQuantity()" 
                                        class="inline-flex items-center justify-center w-10 h-10 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                
                                <input type="number" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $product->stock_quantity }}"
                                       required
                                       class="block w-20 text-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                
                                <button type="button" 
                                        onclick="increaseQuantity()" 
                                        class="inline-flex items-center justify-center w-10 h-10 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                                
                                <span class="text-sm text-gray-500">/ {{ $product->stock_quantity }} disponible(s)</span>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-base font-medium rounded-lg hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Ajouter au panier
                        </button>
                    </form>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <span class="text-yellow-800">
                                Ce produit n'est pas disponible actuellement.
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
const maxQuantity = {{ $product->stock_quantity }};

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue < maxQuantity) {
        input.value = currentValue + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}
</script>
@endsection