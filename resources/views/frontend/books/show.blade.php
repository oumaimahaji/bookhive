@extends('layouts.frontend')

@section('content')
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumb -->
          

            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="md:flex">
                    <!-- Image du livre -->
                    <div class="md:w-2/5 p-8">
                        @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->titre }}"
                             class="w-full h-auto rounded-2xl shadow-lg">
                        @else
                        <div class="w-full h-64 bg-gradient-to-br from-gray-200 to-gray-300 rounded-2xl flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        @endif
                    </div>

                    <!-- Détails du livre -->
                    <div class="md:w-3/5 p-8">
                        <div class="flex items-center mb-4">
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                Validé
                            </span>
                            <span class="ml-4 bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                                {{ $book->category->nom ?? 'Non catégorisé' }}
                            </span>
                        </div>

                        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $book->titre }}</h1>
                        <p class="text-xl text-gray-600 mb-6">Par <span class="font-semibold">{{ $book->auteur }}</span></p>

                        <div class="prose max-w-none mb-8">
                            <p class="text-gray-700 leading-relaxed">{{ $book->description }}</p>
                        </div>

                        <!-- Informations techniques -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span class="text-gray-600">ISBN: <strong>{{ $book->isbn ?? 'Non spécifié' }}</strong></span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-gray-600">Publié le: <strong>{{ $book->publication_date ? $book->publication_date->format('d/m/Y') : 'Non spécifié' }}</strong></span>
                            </div>
                        </div>

                        {{-- Thèmes du livre --}}
                        @php
                            $themes = [];
                            if ($book->description) {
                                $themeService = app(\App\Services\TwinwordThemeService::class);
                                $themes = $themeService->getThemes($book->description);
                            }
                        @endphp
                        @if($themes && count($themes) > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Thèmes du Livre</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($themes as $theme)
                                <span class="inline-block bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                    {{ $theme }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Bouton de réservation -->
                        @auth
                        <div class="flex space-x-4">
                            <a href="{{ route('reservations.create', ['book_id' => $book->id]) }}"
                               class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                Réserver ce livre
                            </a>
                            <a href="{{ route('home') }}"
                               class="flex-1 border-2 border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                                Retour à l'accueil
                            </a>
                        </div>
                        @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center">
                            <p class="text-yellow-800 mb-4">Vous devez être connecté pour réserver ce livre</p>
                            <div class="flex space-x-4 justify-center">
                                <a href="{{ route('login') }}"
                                   class="bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold py-3 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105">
                                    Se connecter
                                </a>
                                <a href="{{ route('register.create') }}"
                                   class="border-2 border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50 font-bold py-3 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105">
                                    S'inscrire
                                </a>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection