@extends('layouts.app')

@section('title', 'Gestion des clients')

@section('content')
    <div class="space-y-6">
        {{-- En-tête --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💼 Gestion des clients</h1>
                <p class="mt-1 text-sm text-gray-500">Liste complète des clients particuliers et professionnels</p>
            </div>
            <a href="{{ route('clients.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau client
            </a>
        </div>

        {{-- KPI - CORRIGÉ : utilise $stats du contrôleur --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">👥</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total clients</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">👤</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-600 truncate">Particuliers</dt>
                                <dd class="text-2xl font-bold text-blue-900">{{ $stats['particuliers'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 overflow-hidden shadow rounded-lg border border-green-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">🏢</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-600 truncate">Professionnels</dt>
                                <dd class="text-2xl font-bold text-green-900">{{ $stats['professionnels'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <form method="GET" action="{{ route('clients.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Recherche --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Nom, entreprise, email...">
                            </div>
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select name="type"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="this.form.submit()">
                                <option value="">Tous les types</option>
                                <option value="particulier" {{ request('type') === 'particulier' ? 'selected' : '' }}>
                                    Particuliers</option>
                                <option value="professionnel" {{ request('type') === 'professionnel' ? 'selected' : '' }}>
                                    Professionnels</option>
                            </select>
                        </div>

                        {{-- Statut --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="status"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="actif" {{ request('status') === 'actif' ? 'selected' : '' }}>Actifs</option>
                                <option value="inactif" {{ request('status') === 'inactif' ? 'selected' : '' }}>Inactifs
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            Filtrer
                        </button>
                        @if (request()->hasAny(['search', 'type', 'status']))
                            <a href="{{ route('clients.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    Liste des clients
                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                        {{ $clients->total() }} résultat(s)
                    </span>
                </h2>
            </div>

            @if ($clients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-32">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase min-w-[200px]">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase min-w-[180px]">Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-32">Ville</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-20">Devis</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">Factures
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-32">CA Total</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($clients as $client)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">
                                        @if ($client->type === 'particulier')
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap">
                                                👤 Particulier
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                                                🏢 Pro
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900 text-sm">{{ $client->display_name }}</div>
                                        @if ($client->type === 'professionnel' && $client->company_name)
                                            <div class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ $client->company_name }}">{{ $client->company_name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($client->email)
                                            <div class="text-xs text-gray-900 truncate max-w-[180px]" title="{{ $client->email }}">{{ $client->email }}</div>
                                        @endif
                                        @if ($client->phone || $client->mobile)
                                            <div class="text-xs text-gray-500 whitespace-nowrap">{{ $client->phone ?? $client->mobile }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900">{{ $client->city ?? '-' }}</div>
                                        @if ($client->postal_code)
                                            <div class="text-xs text-gray-500">{{ $client->postal_code }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 font-medium">
                                            {{ $client->quotes_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 font-medium">
                                            {{ $client->invoices_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900 text-sm whitespace-nowrap">
                                        {{ number_format($client->getTotalRevenue(), 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($client->is_active)
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                                                Actif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 whitespace-nowrap">
                                                Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="{{ route('clients.show', $client) }}"
                                                class="text-indigo-600 hover:text-indigo-900 transition" title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('clients.edit', $client) }}"
                                                class="text-gray-600 hover:text-gray-900 transition" title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $clients->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <span class="text-6xl">👥</span>
                    <p class="mt-4 text-gray-500">Aucun client trouvé.</p>
                    <a href="{{ route('clients.create') }}"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                        Créer le premier client
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection