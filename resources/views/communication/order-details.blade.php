@extends('layouts.app')

@section('title', 'Commande ' . $order->order_number . ' - Intranet')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('communication.my-orders') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à mes commandes
        </a>
    </div>

    <!-- En-tête de la commande -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Commande {{ $order->order_number }}</h1>
                    <p class="text-gray-600 mt-1">
                        Passée le {{ $order->ordered_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>

        <!-- Informations client -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">👤 Informations du demandeur</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Nom complet</p>
                    <p class="text-base font-medium text-gray-900">{{ $order->user->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="text-base font-medium text-gray-900">{{ $order->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Département</p>
                    <p class="text-base font-medium text-gray-900">{{ $order->user->department }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Téléphone</p>
                    <p class="text-base font-medium text-gray-900">{{ $order->user->phone ?? 'Non renseigné' }}</p>
                </div>
            </div>
        </div>

        <!-- Produits commandés -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📦 Produits commandés</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produit
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantité
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix unitaire
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sous-total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gray-100 rounded overflow-hidden">
                                            @if($item->product->image)
                                                <img src="{{ $item->product->image_url }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            @if($item->product->reference)
                                                <div class="text-xs text-gray-500">Réf: {{ $item->product->reference }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">
                                    {{ number_format($item->unit_price, 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    {{ number_format($item->subtotal, 2, ',', ' ') }} €
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-base font-semibold text-gray-900">
                                Total
                            </td>
                            <td class="px-6 py-4 text-right text-xl font-bold text-indigo-600">
                                {{ number_format($order->total_amount, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Commentaires -->
        @if($order->notes)
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">💬 Commentaires</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 whitespace-pre-line">{{ $order->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Informations de suivi -->
        <div class="p-6 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Suivi de la commande</h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Commande passée</p>
                        <p class="text-sm text-gray-500">{{ $order->ordered_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                @if(in_array($order->status, ['validee', 'en_preparation', 'expediee', 'livree']))
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Commande validée</p>
                            <p class="text-sm text-gray-500">En cours de traitement</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center opacity-50">
                        <div class="flex-shrink-0 h-10 w-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">En attente de validation</p>
                        </div>
                    </div>
                @endif

                @if(in_array($order->status, ['en_preparation', 'expediee', 'livree']))
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">En préparation</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center opacity-50">
                        <div class="flex-shrink-0 h-10 w-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">En préparation</p>
                        </div>
                    </div>
                @endif

                @if($order->status === 'livree')
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Commande livrée</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex space-x-3">
        <a href="{{ route('communication.my-orders') }}" 
           class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 text-base font-medium rounded-lg hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux commandes
        </a>
        <a href="{{ route('communication.index') }}" 
           class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-base font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Nouvelle commande
        </a>
    </div>
</div>
@endsection