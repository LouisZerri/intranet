@extends('layouts.app')

@section('title', 'Commande réussie - Intranet')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- En-tête de succès -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Commande envoyée avec succès !</h1>
            <p class="text-green-100">Votre commande a bien été enregistrée et envoyée par email.</p>
        </div>

        <div class="p-8">
            <!-- Informations de la commande -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Numéro de commande</p>
                        <p class="text-lg font-bold text-gray-900">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Date</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->ordered_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Détail de la commande -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Détail de votre commande</h2>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                                @if($item->product->reference)
                                    <p class="text-xs text-gray-500">Réf: {{ $item->product->reference }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">{{ $item->quantity }} × {{ number_format($item->unit_price, 2, ',', ' ') }} €</p>
                                <p class="font-semibold text-gray-900">{{ number_format($item->subtotal, 2, ',', ' ') }} €</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-lg font-semibold text-gray-900">Total</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ number_format($order->total_amount, 2, ',', ' ') }} €</span>
                </div>
            </div>

            @if($order->notes)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Vos commentaires</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Informations importantes -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Prochaines étapes</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Un email récapitulatif a été envoyé à <strong>gestimmo.presta@gmail.com</strong>. 
                            Votre commande sera traitée dans les plus brefs délais. 
                            Vous pouvez suivre l'état de votre commande dans la section "Mes commandes".
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('communication.my-orders') }}" 
                   class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-base font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Voir mes commandes
                </a>
                <a href="{{ route('communication.index') }}" 
                   class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 text-base font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Retour au catalogue
                </a>
            </div>
        </div>
    </div>
</div>
@endsection