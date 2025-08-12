@extends('layouts.app')

@section('title', 'Documentation & Support - Intranet')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'contacts' }">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📚 Documentation & Support</h1>
                <p class="text-gray-600 mt-1">Trouvez rapidement les informations et ressources dont vous avez besoin</p>
            </div>
            @if(auth()->user()->isAdministrateur())
                <div class="flex space-x-3">
                    <a href="{{ route('documentation.contacts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        ➕ Ajouter contact
                    </a>
                    <a href="{{ route('documentation.faq.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        ➕ Ajouter FAQ
                    </a>
                    <a href="{{ route('documentation.resources.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        ➕ Ajouter ressource
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation par onglets -->
    <div class="bg-white shadow rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <button @click="activeTab = 'contacts'" 
                        :class="activeTab === 'contacts' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    📞 Contacts par secteur
                </button>
                <button @click="activeTab = 'faq'" 
                        :class="activeTab === 'faq' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    ❓ Questions fréquentes
                </button>
                <button @click="activeTab = 'resources'" 
                        :class="activeTab === 'resources' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    📁 Ressources & Téléchargements
                </button>
            </nav>
        </div>

        <!-- Contenu des onglets -->
        <div class="p-6">
            <!-- Onglet Contacts -->
            <div x-show="activeTab === 'contacts'" x-transition>
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">📞 Contacts par secteur</h2>
                    <p class="text-gray-600">Trouvez rapidement les contacts professionnels par secteur d'activité</p>
                </div>

                <!-- Barre de recherche contacts -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               id="contactSearch"
                               placeholder="Rechercher un contact par nom, entreprise ou secteur..."
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Contacts groupés par secteur -->
                @php
                    $contactsBySector = $contacts->groupBy('sector');
                @endphp

                <div class="space-y-6">
                    @foreach($contactsBySector as $sector => $sectorContacts)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 capitalize">
                                @switch($sector)
                                    @case('commercial') 🏢 Commercial @break
                                    @case('technique') 🔧 Technique @break
                                    @case('juridique') ⚖️ Juridique @break
                                    @case('rh') 👥 Ressources Humaines @break
                                    @case('finance') 💰 Finance @break
                                    @case('it') 💻 Informatique @break
                                    @default {{ ucfirst($sector) }}
                                @endswitch
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($sectorContacts as $contact)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border contact-card">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">{{ $contact->name }}</h4>
                                                @if($contact->position)
                                                    <p class="text-sm text-gray-600">{{ $contact->position }}</p>
                                                @endif
                                                @if($contact->company)
                                                    <p class="text-sm font-medium text-blue-600">{{ $contact->company }}</p>
                                                @endif
                                            </div>
                                            @if(auth()->user()->isAdministrateur())
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('documentation.contacts.edit', $contact) }}" 
                                                       class="text-blue-600 hover:text-blue-800">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-3 space-y-1">
                                            @if($contact->email)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    <a href="mailto:{{ $contact->email }}" class="hover:text-blue-600">{{ $contact->email }}</a>
                                                </div>
                                            @endif
                                            @if($contact->phone)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    <a href="tel:{{ $contact->phone }}" class="hover:text-blue-600">{{ $contact->phone }}</a>
                                                </div>
                                            @endif
                                            @if($contact->mobile)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z"/>
                                                    </svg>
                                                    <a href="tel:{{ $contact->mobile }}" class="hover:text-blue-600">{{ $contact->mobile }}</a>
                                                </div>
                                            @endif
                                        </div>
                                        @if($contact->notes)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <p class="text-xs text-gray-500">{{ $contact->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Onglet FAQ -->
            <div x-show="activeTab === 'faq'" x-transition>
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">❓ Questions fréquentes</h2>
                    <p class="text-gray-600">Trouvez rapidement les réponses aux questions les plus courantes</p>
                </div>

                <!-- Barre de recherche FAQ -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               id="faqSearch"
                               placeholder="Rechercher dans les questions..."
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- FAQ groupées par catégorie -->
                @php
                    $faqsByCategory = $faqs->groupBy('category');
                @endphp

                <div class="space-y-6">
                    @foreach($faqsByCategory as $category => $categoryFaqs)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 capitalize">
                                @switch($category)
                                    @case('général') 🏠 Questions générales @break
                                    @case('missions') 📁 Missions @break
                                    @case('demandes') 📋 Demandes internes @break
                                    @case('formations') 📚 Formations @break
                                    @case('technique') 🔧 Support technique @break
                                    @default {{ ucfirst($category) }}
                                @endswitch
                            </h3>
                            <div class="space-y-3">
                                @foreach($categoryFaqs as $faq)
                                    <div class="bg-white border border-gray-200 rounded-lg faq-item" x-data="{ open: false }">
                                        <button @click="open = !open" 
                                                class="w-full text-left p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-inset">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium text-gray-900">{{ $faq->question }}</span>
                                                <svg :class="open ? 'transform rotate-180' : ''" 
                                                     class="h-5 w-5 text-gray-400 transition-transform duration-200">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </button>
                                        <div x-show="open" x-transition class="px-4 pb-4">
                                            <div class="text-gray-600 whitespace-pre-line">{{ $faq->answer }}</div>
                                            @if(auth()->user()->isAdministrateur())
                                                <div class="mt-3 pt-3 border-t border-gray-100 flex space-x-3">
                                                    <a href="{{ route('documentation.faq.edit', $faq) }}" 
                                                       class="text-sm text-blue-600 hover:text-blue-800">Modifier</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Onglet Ressources -->
            <div x-show="activeTab === 'resources'" x-transition>
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">📁 Ressources & Téléchargements</h2>
                    <p class="text-gray-600">Téléchargez les modèles, guides et documents utiles</p>
                </div>

                <!-- Barre de recherche ressources -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               id="resourceSearch"
                               placeholder="Rechercher une ressource..."
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ressources groupées par catégorie -->
                @php
                    $resourcesByCategory = $resources->groupBy('category');
                @endphp

                <div class="space-y-6">
                    @foreach($resourcesByCategory as $category => $categoryResources)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 capitalize">
                                @switch($category)
                                    @case('modèles') 📄 Modèles @break
                                    @case('procédures') 📋 Procédures @break
                                    @case('formulaires') 📝 Formulaires @break
                                    @case('guides') 📖 Guides @break
                                    @case('règlements') ⚖️ Règlements @break
                                    @default {{ ucfirst($category) }}
                                @endswitch
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($categoryResources as $resource)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border hover:shadow-md transition-shadow resource-card">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center">
                                                <span class="text-2xl mr-3">{{ $resource->file_icon }}</span>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900 text-sm">{{ $resource->name }}</h4>
                                                    <p class="text-xs text-gray-500">{{ $resource->file_type }} - {{ $resource->formatted_file_size }}</p>
                                                </div>
                                            </div>
                                            @if(auth()->user()->isAdministrateur())
                                                <a href="{{ route('documentation.resources.edit', $resource) }}" 
                                                   class="text-blue-600 hover:text-blue-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                        @if($resource->description)
                                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($resource->description, 100) }}</p>
                                        @endif
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">{{ $resource->download_count }} téléchargement{{ $resource->download_count > 1 ? 's' : '' }}</span>
                                            <a href="{{ route('documentation.resources.download', $resource) }}" 
                                               class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Télécharger
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche contacts
    const contactSearch = document.getElementById('contactSearch');
    if (contactSearch) {
        contactSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const contactCards = document.querySelectorAll('.contact-card');
            
            contactCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    // Recherche FAQ
    const faqSearch = document.getElementById('faqSearch');
    if (faqSearch) {
        faqSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    // Recherche ressources
    const resourceSearch = document.getElementById('resourceSearch');
    if (resourceSearch) {
        resourceSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const resourceCards = document.querySelectorAll('.resource-card');
            
            resourceCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }
});
</script>
@endsection