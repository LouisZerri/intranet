@extends('layouts.app')

@section('title', 'Modifier une actualité - Intranet')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation de retour -->
    <div class="flex items-center justify-between">
        <a href="{{ route('news.show', $news) }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à l'actualité
        </a>
        
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <span>Créé par {{ $news->author->full_name }}</span>
            <span>•</span>
            <span>{{ $news->created_at->format('d/m/Y à H:i') }}</span>
        </div>
    </div>

    <!-- Formulaire d'édition -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">✏️ Modifier l'actualité</h1>
            <p class="text-gray-600 mt-1">
                Apportez vos modifications à cette actualité
            </p>
        </div>

        <form method="POST" action="{{ route('news.update', $news) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Titre -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Titre de l'actualité <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $news->title) }}"
                           required
                           maxlength="255"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                </div>
                @error('title')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Contenu -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    📄 Contenu de l'actualité <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <textarea id="content" 
                              name="content" 
                              rows="8" 
                              required
                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 resize-y @error('content') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('content', $news->content) }}</textarea>
                    <div class="absolute bottom-3 right-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </div>
                </div>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Priorité -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        🏷️ Priorité <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="priority" 
                                name="priority" 
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('priority') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            @foreach($priorities as $value => $label)
                                <option value="{{ $value }}" {{ old('priority', $news->priority) === $value ? 'selected' : '' }}>
                                    @if($value === 'urgent')🚨 {{ $label }} - Diffusion immédiate
                                    @elseif($value === 'important')⚠️ {{ $label }} - À lire rapidement
                                    @else ℹ️ {{ $label }} - Information générale
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        📊 Statut <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="status" 
                                name="status" 
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 appearance-none bg-white cursor-pointer @error('status') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            <option value="draft" {{ old('status', $news->status) === 'draft' ? 'selected' : '' }}>📝 Brouillon</option>
                            <option value="published" {{ old('status', $news->status) === 'published' ? 'selected' : '' }}>📤 Publié</option>
                            <option value="archived" {{ old('status', $news->status) === 'archived' ? 'selected' : '' }}>📦 Archivé</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Date d'expiration -->
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                        ⏰ Date d'expiration
                    </label>
                    <div class="relative">
                        <input type="date" 
                               id="expires_at" 
                               name="expires_at" 
                               value="{{ old('expires_at', $news->expires_at?->format('Y-m-d')) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-gray-400 @error('expires_at') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Ciblage - Seulement pour les administrateurs -->
            @if(auth()->user()->isAdministrateur() && count($roles) > 1)
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-gray-50 -m-6 p-6 rounded-t-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                            🎯 Ciblage de l'actualité
                        </h3>
                        <p class="text-sm text-gray-600">
                            Modifiez qui peut voir cette actualité. Si aucune sélection n'est faite, elle sera visible par tous les collaborateurs.
                        </p>
                    </div>

                    <div class="mt-6">
                        <!-- Rôles ciblés -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                👥 Rôles concernés
                            </label>
                            <div class="space-y-3">
                                @foreach($roles as $value => $label)
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer">
                                        <input type="checkbox" 
                                               name="target_roles[]" 
                                               value="{{ $value }}"
                                               {{ in_array($value, old('target_roles', $news->target_roles ?? [])) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                                        <div class="ml-3 flex-1">
                                            <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                            <p class="text-xs text-gray-500">
                                                @if($value === 'collaborateur')
                                                    Tous les collaborateurs de base
                                                @elseif($value === 'manager')
                                                    Responsables d'équipe et managers
                                                @else
                                                    Administrateurs du système
                                                @endif
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->isManager())
                <!-- Message explicatif pour les managers -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-blue-800 mb-1">🎯 Destinataires de votre actualité</h3>
                                <p class="text-sm text-blue-700">
                                    Cette actualité sera automatiquement visible par <strong>tous les collaborateurs de votre équipe</strong>. 
                                    En tant que manager, vous ne pouvez pas cibler d'autres rôles.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informations de publication -->
            @if($news->published_at)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-blue-800">
                            <strong>Publié le {{ $news->published_at->format('d/m/Y à H:i') }}</strong>
                            @if($news->expires_at && $news->expires_at->isPast())
                                • <span class="text-orange-600">Expiré le {{ $news->expires_at->format('d/m/Y') }}</span>
                            @elseif($news->expires_at)
                                • Expire le {{ $news->expires_at->format('d/m/Y') }}
                            @endif
                        </span>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Dernière modification : {{ $news->updated_at->format('d/m/Y à H:i') }}
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('news.show', $news) }}" 
                           class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </a>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Sauvegarder les modifications
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-resize du textarea
document.getElementById('content').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Indication visuelle de la priorité
document.getElementById('priority').addEventListener('change', function() {
    const priorityColors = {
        'urgent': 'border-red-300 bg-red-50',
        'important': 'border-orange-300 bg-orange-50', 
        'normal': 'border-blue-300 bg-blue-50'
    };
    
    // Reset classes
    this.className = this.className.replace(/border-\w+-300|bg-\w+-50/g, '');
    
    if (priorityColors[this.value]) {
        this.className += ' ' + priorityColors[this.value];
    }
});

// Indication visuelle du statut
document.getElementById('status').addEventListener('change', function() {
    const statusColors = {
        'draft': 'border-yellow-300 bg-yellow-50',
        'published': 'border-green-300 bg-green-50',
        'archived': 'border-gray-300 bg-gray-50'
    };
    
    // Reset classes
    this.className = this.className.replace(/border-\w+-300|bg-\w+-50/g, '');
    
    if (statusColors[this.value]) {
        this.className += ' ' + statusColors[this.value];
    }
});
</script>
@endsection