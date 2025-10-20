@extends('layouts.frontend')

@section('content')
<!-- Hero Section with animation -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-indigo-900 via-purple-900 to-blue-900">
    <div class="absolute inset-0 bg-black opacity-40"></div>

    <!-- Animated background -->
    <div class="absolute inset-0">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
        <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-yellow-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-1/4 left-1/2 w-64 h-64 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-6 text-center">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="lg:w-1/2 mb-12 lg:mb-0 text-left">
                <!-- Animated badge -->
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full mb-6 border border-white/20">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-ping"></span>
                    <span class="text-white text-sm font-medium">Online Library</span>
                </div>

                <h1 class="text-5xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                    Dive into the
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                        Universe
                    </span>
                    <br>of Books
                </h1>

                <p class="text-xl text-gray-200 mb-8 leading-relaxed">
                    BookHive reinvents your reading experience. Discover, share and live
                    unique literary adventures in our digital community library.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#collection" class="group bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center">
                        <span>Explore Collection</span>
                        <svg class="w-5 h-5 ml-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    @guest
                    <a href="{{ route('register') }}" class="group border-2 border-white text-white hover:bg-white hover:text-gray-900 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                        <span>Start Free</span>
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
                        <div class="text-gray-300">Books</div>
                    </div>
                    <div class="text-left">
                        <div class="text-3xl font-bold text-white">{{ $stats['totalAuthors'] ?? 0 }}+</div>
                        <div class="text-gray-300">Authors</div>
                    </div>
                </div>
            </div>

            <!-- Hero Illustration -->
            <div class="lg:w-1/2 relative">
                <div class="relative">
                    <!-- Floating book card -->
                    <div class="absolute -top-10 -left-10 w-64 h-80 bg-white rounded-2xl shadow-2xl transform rotate-6 hover:rotate-0 transition-transform duration-500">
                        <div class="h-3/4 bg-gradient-to-br from-blue-400 to-purple-500 rounded-t-2xl"></div>
                        <div class="p-4">
                            <div class="h-2 bg-gray-200 rounded mb-2"></div>
                            <div class="h-2 bg-gray-200 rounded w-3/4"></div>
                        </div>
                    </div>

                    <!-- Main book card -->
                    <div class="relative w-72 h-96 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl shadow-2xl transform hover:-translate-y-2 transition-transform duration-500">
                        <div class="h-3/4 bg-white/20 rounded-t-2xl flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="p-6 text-white">
                            <h3 class="font-bold text-lg mb-2">Literary Adventure</h3>
                            <p class="text-sm opacity-90">Discover infinite worlds</p>
                        </div>
                    </div>

                    <!-- Right floating book card -->
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

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Why Choose
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500">
                    BookHive
                </span>
                ?
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                A revolutionary reading experience designed for book lovers
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
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Smart Search</h3>
                <p class="text-gray-600 leading-relaxed">
                    Find the perfect book with our advanced search engine and personalized recommendations.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="group p-8 bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Active Community</h3>
                <p class="text-gray-600 leading-relaxed">
                    Join a community of passionate readers, share your reviews and discover new gems.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="group p-8 bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Instant Access</h3>
                <p class="text-gray-600 leading-relaxed">
                    Read anywhere, anytime. Your library follows you on all your devices.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- NEW ARRIVALS Section (5 latest books) -->
<section id="nouveautes" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                New
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500">
                    Arrivals
                </span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Discover the 5 latest books added to our collection.
            </p>
        </div>

        @if($latestBooks->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($latestBooks as $book)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-gray-100">
                <!-- Cover image -->
                <div class="relative h-64 overflow-hidden">
                    @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                        alt="Cover of {{ $book->titre }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    @endif

                    <!-- New Badge -->
                    <div class="absolute top-3 left-3">
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                            NEW
                        </span>
                    </div>

                    <!-- Overlay hover -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        @auth
                        <a href="{{ route('frontend.book', $book->id) }}" class="bg-white text-gray-900 font-bold py-3 px-6 rounded-full transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            View Details
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="bg-white text-gray-900 font-bold py-3 px-6 rounded-full transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            Login to View
                        </a>
                        @endauth
                    </div>
                </div>

                <!-- Card content -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $book->titre }}</h3>
                    <p class="text-gray-600 mb-3">By {{ $book->auteur }}</p>
                    <p class="text-gray-700 text-sm leading-relaxed line-clamp-3">{{ Str::limit($book->description, 120) }}</p>
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
            <h3 class="text-xl font-bold text-gray-700 mb-2">No New Arrivals</h3>
            <p class="text-gray-500">Check back soon to discover our new books</p>
        </div>
        @endif
    </div>
</section>

<!-- ENTIRE COLLECTION Section -->
<section id="collection" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Our Entire
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500">
                    Collection
                </span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Discover all of our available books
            </p>
        </div>

        <!-- Search Form -->
        <div class="mb-12 max-w-2xl mx-auto">
            <form action="{{ route('home') }}" method="GET" class="relative">
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <input
                            type="text"
                            name="author"
                            value="{{ request('author') }}"
                            placeholder="Search books by author name..."
                            class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300 text-lg">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                    @if(request('author'))
                    <a
                        href="{{ route('home') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Search Results Info -->
        @if(request('author'))
        <div class="text-center mb-8">
            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-6 max-w-md mx-auto">
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-lg text-purple-800">
                        Showing results for author:
                        <span class="font-semibold">"{{ request('author') }}"</span>
                        <span class="text-purple-600">({{ $allBooks->total() }} books found)</span>
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($allBooks->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-12">
            @foreach($allBooks as $book)
            <div class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-gray-100">
                <!-- Cover image -->
                <div class="relative h-48 overflow-hidden">
                    @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                        alt="Cover of {{ $book->titre }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    @endif

                    <!-- Updated Badge -->
                    <div class="absolute top-2 right-2">
                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-semibold">

                        </span>
                    </div>
                </div>

                <!-- Card content -->
                <div class="p-3">
                    <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2 text-xs">{{ $book->titre }}</h3>
                    <p class="text-gray-600 text-xs mb-2">By {{ $book->auteur }}</p>

                    <!-- Last Updated -->
                    <div class="text-xs text-gray-500 mb-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $book->updated_at->format('M d, Y') }}
                    </div>

                    <!-- View Details Button -->
                    @auth
                    <a href="{{ route('frontend.book', $book->id) }}"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-3 rounded-lg transition-colors duration-200 block text-center">
                        📖 Details
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold py-2 px-3 rounded-lg transition-colors duration-200 block text-center">
                        🔒 Login
                    </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($allBooks->hasPages())
        <div class="flex justify-center">
            <div class="bg-white px-6 py-4 rounded-2xl shadow-lg border border-gray-100">
                {{ $allBooks->links() }}
            </div>
        </div>
        @endif

        @else
        <div class="text-center py-20">
            <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 mb-4">
                @if(request('author'))
                No books found for "{{ request('author') }}"
                @else
                No books available
                @endif
            </h3>
            <p class="text-gray-500 max-w-md mx-auto mb-6">
                @if(request('author'))
                Try searching with a different author name or browse our entire collection.
                @else
                Our library is being filled with literary treasures. Check back soon to discover our collection.
                @endif
            </p>
            @if(request('author'))
            <a href="{{ route('home') }}" class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-3 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                View All Books
            </a>
            @endif
        </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 relative overflow-hidden">
    <!-- Animated background -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-72 h-72 bg-white/10 rounded-full mix-blend-overlay filter blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full mix-blend-overlay filter blur-3xl animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-6 text-center">
        <h2 class="text-4xl lg:text-6xl font-bold text-white mb-6">
            Ready to start your
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-300">
                adventure
            </span>
            ?
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Join thousands of passionate readers and transform your reading experience
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @guest
            <a href="{{ route('register') }}" class="group bg-white text-gray-900 hover:bg-gray-100 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center">
                <span>Create Free Account</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
            @else
            <a href="{{ route('books.index') }}" class="group bg-white text-gray-900 hover:bg-gray-100 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center">
                <span>Explore All Books</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
            @endguest

            <a href="#nouveautes" class="group border-2 border-white text-white hover:bg-white hover:text-gray-900 font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                <span>Discover New Arrivals</span>
                <svg class="w-5 h-5 ml-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </a>
        </div>

        <!-- Trust indicators -->
        <div class="flex flex-wrap justify-center gap-8 mt-12">
            <div class="text-center">
                <div class="text-2xl font-bold text-white">{{ $stats['totalBooks'] ?? 0 }}+</div>
                <div class="text-white/70 text-sm">Books</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">{{ $stats['totalAuthors'] ?? 0 }}+</div>
                <div class="text-white/70 text-sm">Authors</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">24/7</div>
                <div class="text-white/70 text-sm">Available</div>
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