@extends('layouts.app')

@section('title', 'Catalogue Communication - Intranet')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Produits de Communication
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Commandez vos supports de communication
                    </p>
                </div>
                <div class="flex space-x-3">
                    <!-- Boutons administrateur -->
                    @if(auth()->user()->isAdministrateur())
                        <a href="{{ route('admin.communication.products') }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 border border-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Gérer les produits
                        </a>
                        <a href="{{ route('admin.communication.orders') }}" 
                           class="inline-flex items-center px-4 py-2 bg-orange-600 border border-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Gérer les commandes
                        </a>
                    @endif

                    <!-- Boutons utilisateurs -->
                    <a href="{{ route('communication.my-orders') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Mes commandes
                    </a>
                    <a href="{{ route('communication.cart') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-blue-700 shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Panier
                        @if(session('communication_cart') && count(session('communication_cart')) > 0)
                            <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                {{ array_sum(session('communication_cart')) }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille de produits -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Image du produit -->
                    <div class="relative h-48 bg-gray-200">
                        @if($product->image)
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        
                        @if(!$product->isAvailable())
                            <div class="absolute top-0 right-0 bg-red-500 text-white px-3 py-1 text-xs font-semibold">
                                Indisponible
                            </div>
                        @endif

                        <!-- Badge admin pour édition rapide -->
                        @if(auth()->user()->isAdministrateur())
                            <div class="absolute top-2 left-2">
                                <a href="{{ route('admin.communication.products.edit', $product) }}" 
                                   class="inline-flex items-center px-2 py-1 bg-white bg-opacity-90 text-xs font-medium text-gray-700 rounded hover:bg-opacity-100">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Éditer
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Informations produit -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                        
                        @if($product->reference)
                            <p class="text-xs text-gray-500 mb-2">Réf: {{ $product->reference }}</p>
                        @endif

                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ Str::limit($product->description, 80) }}
                        </p>

                        <div class="flex items-center justify-between mb-4">
                            @if($product->price)
                                <span class="text-xl font-bold text-indigo-600">{{ $product->formatted_price }}</span>
                            @else
                                <span class="text-sm text-gray-500 italic">Prix sur demande</span>
                            @endif
                            
                            @if($product->stock_quantity > 0)
                                <span class="text-xs text-green-600">
                                    {{ $product->stock_quantity }} en stock
                                </span>
                            @else
                                <span class="text-xs text-red-600">
                                    Rupture de stock
                                </span>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('communication.show', $product) }}" 
                               class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Détails
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun produit disponible</h3>
            <p class="mt-2 text-sm text-gray-500">
                Le catalogue est actuellement vide. Revenez plus tard.
            </p>
            
            @if(auth()->user()->isAdministrateur())
                <a href="{{ route('admin.communication.products.create') }}" 
                   class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Créer le premier produit
                </a>
            @endif
        </div>
    @endif
</div>
@endsection