@extends('layouts.app')

@section('title', 'Gestion des commandes - Communication')

@section('content')
    <div class="space-y-6">
        <!-- En-tête -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📦 Gestion des commandes</h1>
            <p class="text-gray-600 mt-1">Suivez et gérez toutes les commandes de produits</p>
        </div>

        <!-- Messages de succès -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <span class="text-2xl">📋</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total commandes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <span class="text-2xl">⏳</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">En attente</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <span class="text-2xl">📦</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Ce mois</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['orders_this_month'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">CA ce mois</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ number_format($stats['total_amount_month'], 0, ',', ' ') }}€</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white shadow rounded-lg p-4">
            <form method="GET" action="{{ route('admin.communication.orders') }}" class="flex flex-wrap gap-4">
                <!-- Filtre par statut -->
                <div class="flex-1 min-w-[200px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" id="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente
                        </option>
                        <option value="validee" {{ request('status') == 'validee' ? 'selected' : '' }}>Validée</option>
                        <option value="en_preparation" {{ request('status') == 'en_preparation' ? 'selected' : '' }}>En
                            préparation</option>
                        <option value="expediee" {{ request('status') == 'expediee' ? 'selected' : '' }}>Expédiée</option>
                        <option value="livree" {{ request('status') == 'livree' ? 'selected' : '' }}>Livrée</option>
                        <option value="annulee" {{ request('status') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                <!-- Filtre par période -->
                <div class="flex-1 min-w-[200px]">
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                    <select name="period" id="period"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Toutes les périodes</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Ce mois</option>
                    </select>
                </div>

                <!-- Boutons -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                        Filtrer
                    </button>
                    <a href="{{ route('admin.communication.orders') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des commandes -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°
                                Commande</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Articles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 font-mono">{{ $order->order_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->user->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->user->department ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->ordered_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->ordered_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $order->items->count() }} article(s)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ number_format($order->total_amount, 2, ',', ' ') }}€</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        onclick="openOrderModal({{ $order->id }}, '{{ $order->order_number }}', '{{ $order->status }}', '{{ addslashes($order->notes ?? '') }}')"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        Gérer
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <span class="text-4xl">📦</span>
                                    <p class="text-gray-500 mt-2">Aucune commande trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($orders->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de gestion de commande -->
    <div id="orderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Gérer la commande <span id="modalOrderNumber"
                        class="font-mono"></span></h3>

                <form id="updateStatusForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="modal_status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select name="status" id="modal_status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="en_attente">En attente</option>
                            <option value="validee">Validée</option>
                            <option value="en_preparation">En préparation</option>
                            <option value="expediee">Expédiée</option>
                            <option value="livree">Livrée</option>
                            <option value="annulee">Annulée</option>
                        </select>
                    </div>

                    <div>
                        <label for="modal_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes
                            (optionnel)</label>
                        <textarea name="notes" id="modal_notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="Ajoutez des notes sur cette commande..."></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeOrderModal()"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900">
                            Annuler
                        </button>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openOrderModal(orderId, orderNumber, currentStatus, currentNotes) {
            document.getElementById('modalOrderNumber').textContent = orderNumber;
            document.getElementById('modal_status').value = currentStatus;
            document.getElementById('modal_notes').value = currentNotes;
            document.getElementById('updateStatusForm').action = `/admin/communication/commandes/${orderId}/status`;
            document.getElementById('orderModal').classList.remove('hidden');
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
            document.getElementById('updateStatusForm').reset();
        }

        // Fermer le modal en cliquant en dehors
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });
    </script>
@endsection
