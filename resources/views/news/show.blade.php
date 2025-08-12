@extends('layouts.app')

@section('title', $news->title . ' - Actualités')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation de retour -->
    <div class="flex items-center">
        <a href="{{ route('news.index') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-500 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux actualités
        </a>
    </div>

    <!-- Article principal -->
    <article class="bg-white shadow rounded-lg overflow-hidden">
        <!-- En-tête de l'article -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <!-- Badges et métadonnées -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $news->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($news->priority === 'important' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }}">
                            @if($news->priority === 'urgent')
                                🚨 {{ $news->priority_label }}
                            @elseif($news->priority === 'important')
                                ⚠️ {{ $news->priority_label }}
                            @else
                                ℹ️ {{ $news->priority_label }}
                            @endif
                        </span>
                        
                        <span class="text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Publié le {{ $news->published_at->format('d/m/Y à H:i') }}
                        </span>
                        
                        @if($news->expires_at && $news->expires_at->isFuture())
                            <span class="text-sm text-orange-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Expire le {{ $news->expires_at->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Titre -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $news->title }}</h1>
                    
                    <!-- Informations sur l'auteur et le ciblage -->
                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-medium mr-3">
                                {{ substr($news->author->first_name, 0, 1) }}{{ substr($news->author->last_name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $news->author->full_name }}</div>
                                <div class="text-gray-500">{{ $news->author->position }}</div>
                            </div>
                        </div>
                        
                        @if($news->target_roles)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Destiné aux : {{ implode(', ', $news->target_roles) }}</span>
                            </div>
                        @endif
                        
                        @if($news->target_departments)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Départements : {{ implode(', ', $news->target_departments) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Actions (pour les auteurs/admin) -->
                @if((auth()->user()->isAdministrateur()) || (auth()->user()->id === $news->author_id))
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('news.edit', $news) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Modifier
                        </a>
                        
                        <form method="POST" action="{{ route('news.destroy', $news) }}" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Contenu de l'actualité -->
        <div class="p-6">
            <div class="prose prose-lg max-w-none">
                {!! nl2br(e($news->content)) !!}
            </div>
        </div>
    </article>

    <!-- Actualités similaires -->
    @if($relatedNews->count() > 0)
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Actualités similaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($relatedNews as $related)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $related->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($related->priority === 'important' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $related->priority_label }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $related->published_at->format('d/m/Y') }}
                            </span>
                        </div>
                        
                        <h3 class="font-medium text-gray-900 mb-2">
                            <a href="{{ route('news.show', $related) }}" 
                               class="hover:text-indigo-600 transition-colors">
                                {{ Str::limit($related->title, 60) }}
                            </a>
                        </h3>
                        
                        <p class="text-sm text-gray-600 mb-3">
                            {{ $related->getExcerpt(80) }}
                        </p>
                        
                        <a href="{{ route('news.show', $related) }}" 
                           class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Lire →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection