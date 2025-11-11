@extends('layouts.app')

@section('title', 'Gestion des devis')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📄 Gestion des devis</h1>
                <p class="mt-1 text-sm text-gray-500">Créez et gérez vos devis clients</p>
            </div>
            <a href="{{ route('quotes.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau devis
            </a>
        </div>

        {{-- KPI Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📊</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 overflow-hidden shadow rounded-lg border border-gray-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📝</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Brouillons</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['brouillon'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📤</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-600 truncate">Envoyés</dt>
                                <dd class="text-2xl font-bold text-blue-900">{{ $stats['envoye'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-600 truncate">Acceptés</dt>
                                <dd class="text-2xl font-bold text-green-900">{{ $stats['accepte'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 overflow-hidden shadow rounded-lg border border-purple-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">💰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-purple-600 truncate">Convertis</dt>
                                <dd class="text-2xl font-bold text-purple-900">{{ $stats['converti'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 overflow-hidden shadow rounded-lg border border-yellow-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📈</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-yellow-600 truncate">Taux conversion</dt>
                                <dd class="text-2xl font-bold text-yellow-900">{{ $conversionRate }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <form method="GET" action="{{ route('quotes.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Recherche --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher un client</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Nom du client, entreprise...">
                            </div>
                        </div>

                        {{-- Statut --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="status" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="brouillon" {{ request('status') === 'brouillon' ? 'selected' : '' }}>Brouillons</option>
                                <option value="envoye" {{ request('status') === 'envoye' ? 'selected' : '' }}>Envoyés</option>
                                <option value="accepte" {{ request('status') === 'accepte' ? 'selected' : '' }}>Acceptés</option>
                                <option value="refuse" {{ request('status') === 'refuse' ? 'selected' : '' }}>Refusés</option>
                                <option value="converti" {{ request('status') === 'converti' ? 'selected' : '' }}>Convertis</option>
                            </select>
                        </div>

                        {{-- Service --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de prestation</label>
                            <select name="service" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    onchange="this.form.submit()">
                                <option value="">Tous les services</option>
                                @foreach($services as $key => $label)
                                    <option value="{{ $key }}" {{ request('service') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            Filtrer
                        </button>
                        @if (request()->hasAny(['search', 'status', 'service']))
                            <a href="{{ route('quotes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Tableau des devis --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    Liste des devis
                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                        {{ $quotes->total() }} résultat(s)
                    </span>
                </h2>
            </div>

            @if ($quotes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Devis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date création</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant TTC</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($quotes as $quote)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('quotes.show', $quote) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ $quote->quote_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $quote->client->display_name }}</div>
                                        @if($quote->client->type === 'professionnel' && $quote->client->company_name)
                                            <div class="text-sm text-gray-500">{{ $quote->client->company_name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $quote->service_label }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $quote->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                        {{ $quote->formatted_total_ttc }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                            {{ $quote->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $quote->user->full_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="{{ route('quotes.show', $quote) }}" 
                                               class="text-indigo-600 hover:text-indigo-900" 
                                               title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            @if($quote->canBeEdited())
                                                <a href="{{ route('quotes.edit', $quote) }}" 
                                                   class="text-gray-600 hover:text-gray-900" 
                                                   title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $quotes->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <span class="text-6xl">📄</span>
                    <p class="mt-4 text-gray-500">Aucun devis trouvé.</p>
                    <a href="{{ route('quotes.create') }}"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                        Créer le premier devis
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection