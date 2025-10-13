<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHive - Votre Bibliothèque en Ligne</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        .line-clamp-2 { 
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <!-- Main Navbar -->
    @include('layouts.navbars.main-navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">BookHive</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Votre bibliothèque numérique où chaque livre trouve son lecteur.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Navigation</h4>
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Accueil</a>
                        <a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition-colors">À propos</a>
                        <a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors">Contact</a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <p class="text-gray-400 mb-2">contact@bookhive.com</p>
                    <p class="text-gray-400">+33 1 23 45 67 89</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 BookHive. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>