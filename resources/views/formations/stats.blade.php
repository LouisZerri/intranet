@extends('layouts.app')

@section('title', 'Statistiques formations')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">📊 Statistiques formations</h1>
                <p class="mt-2 text-gray-600">Vue d'ensemble de l'activité formation</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('formations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    ← Catalogue
                </a>
                <a href="{{ route('formations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    + Nouvelle formation
                </a>
            </div>
        </div>
    </div>

    <!-- KPIs globaux -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Formations actives -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <span class="text-4xl mr-4">📚</span>
                <div>
                    <p class="text-sm text-gray-600">Formations actives</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_formations'] }}</p>
                </div>
            </div>
        </div>

        <!-- Demandes totales -->
        <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-200">
            <div class="flex items-center">
                <span class="text-4xl mr-4">📝</span>
                <div>
                    <p class="text-sm text-blue-700">Demandes totales</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $stats['total_requests'] }}</p>
                </div>
            </div>
        </div>

        <!-- Demandes cette année -->
        <div class="bg-purple-50 p-6 rounded-lg shadow border border-purple-200">
            <div class="flex items-center">
                <span class="text-4xl mr-4">📅</span>
                <div>
                    <p class="text-sm text-purple-700">Cette année</p>
                    <p class="text-3xl font-bold text-purple-900">{{ $stats['this_year_requests'] }}</p>
                </div>
            </div>
        </div>

        <!-- Taux de complétion -->
        <div class="bg-green-50 p-6 rounded-lg shadow border border-green-200">
            <div class="flex items-center">
                <span class="text-4xl mr-4">✅</span>
                <div>
                    <p class="text-sm text-green-700">Taux de complétion</p>
                    <p class="text-3xl font-bold text-green-900">{{ $stats['completion_rate'] }}%</p>
                </div>
            </div>
        </div>

        <!-- Note moyenne -->
        <div class="bg-yellow-50 p-6 rounded-lg shadow border border-yellow-200">
            <div class="flex items-center">
                <span class="text-4xl mr-4">⭐</span>
                <div>
                    <p class="text-sm text-yellow-700">Note moyenne</p>
                    <p class="text-3xl font-bold text-yellow-900">
                        @if($stats['average_rating'])
                            {{ number_format($stats['average_rating'], 1) }}/5
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Heures délivrées -->
        <div class="bg-indigo-50 p-6 rounded-lg shadow border border-indigo-200">
            <div class="flex items-center">
                <span class="text-4xl mr-4">⏱️</span>
                <div>
                    <p class="text-sm text-indigo-700">Heures délivrées</p>
                    <p class="text-3xl font-bold text-indigo-900">{{ number_format($stats['total_hours_delivered'], 0, ',', ' ') }}h</p>
                </div>
            </div>
        </div>

        <!-- Fichiers -->
        <div class="bg-orange-50 p-6 rounded-lg shadow border border-orange-200">
            <div class="flex items-center">
                <span class="text-4xl mr-4">📄</span>
                <div>
                    <p class="text-sm text-orange-700">Fichiers</p>
                    <p class="text-3xl font-bold text-orange-900">{{ $stats['total_files'] }}</p>
                </div>
            </div>
        </div>

        <!-- Stockage -->
        <div class="bg-pink-50 p-6 rounded-lg shadow border border-pink-200">
            <div class="flex items-center">
                <span class="text-4xl mr-4">💾</span>
                <div>
                    <p class="text-sm text-pink-700">Stockage</p>
                    <p class="text-2xl font-bold text-pink-900">
                        {{ number_format($stats['total_files_size'] / 1048576, 1) }} MB
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formations populaires -->
    @if(isset($popularFormations) && $popularFormations->count() > 0)
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                🏆 Formations les plus populaires
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($popularFormations as $formation)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="font-semibold text-gray-900">{{ $formation->title }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                        {{ $formation->category ?? 'Non classé' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ Str::limit($formation->description, 150) }}</p>
                                <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                                    <span>⏱️ {{ $formation->duration_hours }}h</span>
                                    <span>🎯 {{ ucfirst($formation->level) }}</span>
                                    <span>{{ $formation->format_label }}</span>
                                    @if($formation->cost)
                                        <span>💰 {{ number_format($formation->cost, 0, ',', ' ') }}€</span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-6 text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ $formation->participants_count }}</div>
                                <div class="text-xs text-gray-500">participants</div>
                                <a href="{{ route('formations.show', $formation) }}" class="mt-2 inline-block text-xs text-indigo-600 hover:text-indigo-800">
                                    Voir →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Statistiques par catégorie -->
    @if(isset($categoriesStats) && count($categoriesStats) > 0)
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                📂 Statistiques par catégorie
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categoriesStats as $stat)
                    <div class="border border-gray-200 rounded-lg p-5 hover:bg-gray-50 transition-colors">
                        <h3 class="font-semibold text-gray-900 mb-4 text-lg">
                            {{ $stat['category'] ?? 'Non classée' }}
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Formations</span>
                                <span class="font-medium text-gray-900">{{ $stat['formations_count'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Participants</span>
                                <span class="font-medium text-purple-600">{{ $stat['participants_count'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Heures totales</span>
                                <span class="font-medium text-indigo-600">{{ number_format($stat['total_hours'], 0) }}h</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Résumé visuel -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold mb-6 flex items-center text-gray-900">
            <span class="mr-3">📈</span>
            Évolution de l'activité formation
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center bg-white rounded-lg p-6 shadow-md">
                <div class="text-5xl font-bold text-indigo-600">{{ $stats['this_year_requests'] }}</div>
                <div class="text-gray-600 mt-2 font-medium">Demandes cette année</div>
            </div>
            <div class="text-center bg-white rounded-lg p-6 shadow-md">
                <div class="text-5xl font-bold text-purple-600">{{ number_format($stats['total_hours_delivered'], 0) }}h</div>
                <div class="text-gray-600 mt-2 font-medium">Heures de formation</div>
            </div>
            <div class="text-center bg-white rounded-lg p-6 shadow-md">
                <div class="text-5xl font-bold text-green-600">{{ $stats['completion_rate'] }}%</div>
                <div class="text-gray-600 mt-2 font-medium">Taux de complétion</div>
            </div>
            <div class="text-center bg-white rounded-lg p-6 shadow-md">
                <div class="text-5xl font-bold text-yellow-600">
                    @if($stats['average_rating'])
                        {{ number_format($stats['average_rating'], 1) }}
                    @else
                        N/A
                    @endif
                </div>
                <div class="text-gray-600 mt-2 font-medium">Note moyenne /5</div>
            </div>
        </div>
    </div>
</div>
@endsection