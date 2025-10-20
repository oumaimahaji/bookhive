<!DOCTYPE html>
<html lang="{{ \Request::is('rtl') ? 'ar' : 'en' }}" dir="{{ \Request::is('rtl') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @if(env('IS_DEMO'))
        <x-demo-metas></x-demo-metas>
    @endif

    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome removed - using alternative icon solution -->

    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.4" rel="stylesheet" />

    <!-- Bootstrap CSS pour navbar unifiée -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .navbar-unified {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background-color: #fff !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 70px;
            padding: 0.5rem 1rem;
        }
        .navbar-spacer { height: 70px; display: block; width: 100%; }
        .main-content-adjusted { min-height: calc(100vh - 70px); }
        .navbar-unified .navbar-brand { font-weight: 700; color: #344767 !important; }
        .navbar-unified .nav-link { color: #67748e !important; font-weight: 500; }
        .navbar-unified .nav-link.active { color: #cb0c9f !important; font-weight: 600; }
        .navbar-unified .nav-link:hover { color: #344767 !important; }
        .navbar-unified .dropdown-menu { border: none; box-shadow: 0 8px 26px -4px rgba(20,20,20,0.15); }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100 {{ \Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '') }}">

    <!-- Navbar unifiée -->
    @include('layouts.navbars.unified-navbar')
    <div class="navbar-spacer"></div>

    <!-- Contenu -->
    @auth
        @yield('auth')
    @endauth

    @guest
        @yield('guest')
    @endguest

    <!-- Alert success -->
    @if(session()->has('success'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 4000)"
             x-show="show"
             class="position-fixed bg-success rounded right-3 text-sm py-2 px-4"
             style="z-index: 9999; top: 80px; right: 20px;">
            <p class="m-0 text-white">{{ session('success')}}</p>
        </div>
    @endif

    <!-- Core JS -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/fullcalendar.min.js"></script>
    <script src="../assets/js/plugins/chartjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('rtl')
    @stack('dashboard')

    <script>
        // Scrollbar Windows
        if(navigator.platform.indexOf('Win') > -1 && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: '0.5' });
        }

        // Dropdown et menu mobile Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            var dropdowns = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            dropdowns.map(function(el){ return new bootstrap.Dropdown(el) });

            var toggler = document.querySelector('.navbar-toggler');
            var content = document.querySelector('#navbarContent');
            if(toggler && content){
                content.querySelectorAll('.nav-link').forEach(function(link){
                    link.addEventListener('click', function(){
                        if(content.classList.contains('show')) toggler.click();
                    });
                });
            }
        });
    </script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.4"></script>
</body>
</html>
