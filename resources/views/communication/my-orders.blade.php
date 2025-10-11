@extends('layouts.app')

@section('title', 'Mes commandes - Intranet')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Mes Commandes
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Historique de vos commandes de produits de communication
                    </p>
                </div>
                <div>
                    <a href="{{ route('communication.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Catalogue produits
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <!-- En-tête de la commande -->
                            <div class="bg-gray-50 px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Commande</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->order_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date</p>
                                        <p class="text-base font-medium text-gray-900">{{ $order->ordered_at->format('d/m/Y à H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Statut</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Total</p>
                                    <p class="text-xl font-bold text-indigo-600">{{ number_format($order->total_amount, 2, ',', ' ') }} €</p>
                                </div>
                            </div>

                            <!-- Contenu de la commande -->
                            <div class="px-6 py-4">
                                <div class="space-y-2">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-100 rounded text-xs font-medium text-gray-600">
                                                    {{ $item->quantity }}
                                                </span>
                                                <span class="text-gray-900">{{ $item->product->name }}</span>
                                            </div>
                                            <span class="text-gray-600">{{ number_format($item->subtotal, 2, ',', ' ') }} €</span>
                                        </div>
                                    @endforeach

                                    @if($order->items->count() > 3)
                                        <p class="text-sm text-gray-500 italic">
                                            et {{ $order->items->count() - 3 }} autre(s) produit(s)...
                                        </p>
                                    @endif
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                    <a href="{{ route('communication.order-details', $order) }}" 
                                       class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Voir les détails
                                    </a>

                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        {{ $order->items->count() }} article(s)
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune commande</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Vous n'avez pas encore passé de commande de produits de communication.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('communication.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Découvrir le catalogue
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection