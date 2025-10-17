@extends('layouts.frontend')

@section('content')
<!-- Hero Section avec animation -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-indigo-900 via-purple-900 to-blue-900">
    <div class="absolute inset-0 bg-black opacity-40"></div>

    <!-- Background animé -->
    <div class="absolute inset-0">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
        <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-yellow-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-1/4 left-1/2 w-64 h-64 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-6 text-center">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="lg:w-1/2 mb-12 lg:mb-0 text-left">
                <!-- Badge animé -->
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full mb-6 border border-white/20">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-ping"></span>
                    <span class="text-white text-sm font-medium">Bibliothèque en ligne</span>
                </div>

                <h1 class="text-5xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                    Plongez dans l'
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                        Univers
                    </span>
                    <br>des Livres
                </h1>

                <p class="text-xl text-gray-200 mb-8 leading-relaxed">
                    BookHive réinvente votre expérience de lecture. Découvrez, partagez et vivez
                    des aventures littéraires uniques dans notre bibliothèque numérique communautaire.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#nouveautes" class="group bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center">
                        <span>Explorer la Collection</span>
                        <svg class="w-5 h-5 ml-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    @guest
                    <a href="{{ route('register') }}" class="group border-2 border-white text-white hover:bg-white hover:text-gray-900 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                        <span>Commencer Gratuitement</span>
                        <svg class="w-5 h-5 ml-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </a>
                    @endguest
                </div>

                <!-- Stats -->
                <div class="flex flex-wrap gap-8 mt-12">
                    <div class="text-left">
                        <div class="text-3xl font-bold text-white">{{ $stats['totalBooks'] ?? 0 }}+</div>
                        <div class="text-gray-300">Livres</div>
                    </div>
                    <div class="text-left">
                        <div class="text-3xl font-bold text-white">{{ $stats['totalCategories'] ?? 0 }}+</div>
                        <div class="text-gray-300">Catégories</div>
                    </div>
                    <div class="text-left">
                        <div class="text-3xl font-bold text-white">{{ $stats['totalAuthors'] ?? 0 }}+</div>
                        <div class="text-gray-300">Auteurs</div>
                    </div>
                </div>
            </div>

            <!-- Illustration Hero -->
            <div class="lg:w-1/2 relative">
                <div class="relative">
                    <!-- Carte livre flottante -->
                    <div class="absolute -top-10 -left-10 w-64 h-80 bg-white rounded-2xl shadow-2xl transform rotate-6 hover:rotate-0 transition-transform duration-500">
                        <div class="h-3/4 bg-gradient-to-br from-blue-400 to-purple-500 rounded-t-2xl"></div>
                        <div class="p-4">
                            <div class="h-2 bg-gray-200 rounded mb-2"></div>
                            <div class="h-2 bg-gray-200 rounded w-3/4"></div>
                        </div>
                    </div>

                    <!-- Carte livre principale -->
                    <div class="relative w-72 h-96 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl shadow-2xl transform hover:-translate-y-2 transition-transform duration-500">
                        <div class="h-3/4 bg-white/20 rounded-t-2xl flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="p-6 text-white">
                            <h3 class="font-bold text-lg mb-2">Aventure Littéraire</h3>
                            <p class="text-sm opacity-90">Découvrez des mondes infinis</p>
                        </div>
                    </div>

                    <!-- Carte livre flottante droite -->
                    <div class="absolute -bottom-10 -right-10 w-56 h-72 bg-white rounded-2xl shadow-2xl transform -rotate-12 hover:rotate-0 transition-transform duration-500">
                        <div class="h-3/4 bg-gradient-to-br from-green-400 to-blue-500 rounded-t-2xl"></div>
                        <div class="p-4">
                            <div class="h-2 bg-gray-200 rounded mb-2"></div>
                            <div class="h-2 bg-gray-200 rounded w-2/3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="w-6 h-10 border-2 border-white rounded-full flex justify-center">
            <div class="w-1 h-3 bg-white rounded-full mt-2"></div>
        </div>
    </div>
</section>

<!-- Section Features -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Pourquoi choisir
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500">
                    BookHive
                </span>
                ?
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Une expérience de lecture révolutionnaire conçue pour les passionnés de livres
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="group p-8 bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Recherche Intelligente</h3>
                <p class="text-gray-600 leading-relaxed">
                    Trouvez le livre parfait avec notre moteur de recherche avancé et nos recommandations personnalisées.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="group p-8 bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Communauté Active</h3>
                <p class="text-gray-600 leading-relaxed">
                    Rejoignez une communauté de lecteurs passionnés, partagez vos avis et découvrez de nouvelles perles.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="group p-8 bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Accès Instantané</h3>
                <p class="text-gray-600 leading-relaxed">
                    Lisez où vous voulez, quand vous voulez. Votre bibliothèque vous suit sur tous vos appareils.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section NOUVEAUTÉS (5 derniers livres) -->
<section id="nouveautes" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Nos
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500">
                    Nouveautés
                </span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Découvrez les 5 derniers livres ajoutés à notre collection
            </p>
        </div>

        @if($latestBooks->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($latestBooks as $book)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-gray-100">
                <!-- Image de couverture -->
                <div class="relative h-64 overflow-hidden">
                    @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                        alt="Couverture de {{ $book->titre }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    @endif

                    <!-- Badge Nouveau -->
                    <div class="absolute top-3 left-3">
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                            NOUVEAU
                        </span>
                    </div>

                    <!-- Overlay hover -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        @auth
                        <a href="{{ route('frontend.book', $book->id) }}" class="bg-white text-gray-900 font-bold py-3 px-6 rounded-full transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            Voir détails
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="bg-white text-gray-900 font-bold py-3 px-6 rounded-full transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            Se connecter pour voir
                        </a>
                        @endauth
                    </div>
                </div>

                <!-- Contenu de la carte -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $book->titre }}</h3>
                    <p class="text-gray-600 mb-3">Par {{ $book->auteur }}</p>
                    <p class="text-gray-700 text-sm leading-relaxed line-clamp-3">{{ Str::limit($book->description, 120) }}</p>

                    <!-- Catégorie -->
                    @if($book->category)
                    <div class="mt-4">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                            {{ $book->category->nom }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Aucune nouveauté</h3>
            <p class="text-gray-500">Revenez bientôt pour découvrir nos nouveaux livres</p>
        </div>
        @endif
    </div>
</section>

<!-- Section TOUTE LA COLLECTION -->
<section id="collection" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Toute notre
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500">
                    Collection
                </span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Découvrez l'ensemble de nos livres disponibles
            </p>
        </div>

        @if($allBooks->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-12">
            @foreach($allBooks as $book)
            <div class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-gray-100">
                <!-- Image de couverture -->
                <div class="relative h-48 overflow-hidden">
                    @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                        alt="Couverture de {{ $book->titre }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    @endif
                </div>

                <!-- Contenu de la carte -->
                <div class="p-3">
                    <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2 text-xs">{{ $book->titre }}</h3>
                    <p class="text-gray-600 text-xs mb-2">Par {{ $book->auteur }}</p>

                    <!-- Catégorie -->
                    @if($book->category)
                    <div class="mb-2">
                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                            {{ $book->category->nom }}
                        </span>
                    </div>
                    @endif

                    <!-- Bouton Voir détails -->
                    @auth
                    <a href="{{ route('frontend.book', $book->id) }}"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-3 rounded-lg transition-colors duration-200 block text-center">
                        📖 Détails
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold py-2 px-3 rounded-lg transition-colors duration-200 block text-center">
                        🔒 Connexion
                    </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($allBooks->hasPages())
        <div class="flex justify-center">
            {{ $allBooks->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-20">
            <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 mb-4">Aucun livre disponible</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                Notre bibliothèque se remplit de trésors littéraires. Revenez bientôt pour découvrir notre collection.
            </p>
        </div>
        @endif

        
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 relative overflow-hidden">
    <!-- Background animé -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-72 h-72 bg-white/10 rounded-full mix-blend-overlay filter blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full mix-blend-overlay filter blur-3xl animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-6 text-center">
        <h2 class="text-4xl lg:text-6xl font-bold text-white mb-6">
            Prêt à commencer votre
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-300">
                aventure
            </span>
            ?
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Rejoignez des milliers de lecteurs passionnés et transformez votre façon de lire
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @guest
            <a href="{{ route('register') }}" class="group bg-white text-gray-900 hover:bg-gray-100 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center">
                <span>Créer un compte gratuit</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
            @else
            <a href="{{ route('books.index') }}" class="group bg-white text-gray-900 hover:bg-gray-100 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center">
                <span>Explorer tous les livres</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
            @endguest

            <a href="#nouveautes" class="group border-2 border-white text-white hover:bg-white hover:text-gray-900 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                <span>Découvrir les nouveautés</span>
                <svg class="w-5 h-5 ml-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </a>
        </div>

        <!-- Trust indicators -->
        <div class="flex flex-wrap justify-center gap-8 mt-12">
            <div class="text-center">
                <div class="text-2xl font-bold text-white">{{ $stats['totalBooks'] ?? 0 }}+</div>
                <div class="text-white/70 text-sm">Livres</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">{{ $stats['totalCategories'] ?? 0 }}+</div>
                <div class="text-white/70 text-sm">Catégories</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">24/7</div>
                <div class="text-white/70 text-sm">Disponible</div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush