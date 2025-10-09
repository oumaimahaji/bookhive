<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>

@if (\Request::is('rtl'))
<html dir="rtl" lang="ar">
@else
<html lang="en">
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if (env('IS_DEMO'))
  <x-demo-metas></x-demo-metas>
  @endif

  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">

  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />

  <!-- Bootstrap pour la navbar unifiée -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Styles pour la navbar unifiée -->
  <style>
    /* Reset des styles de navbar pour éviter les conflits */
    .navbar-unified {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1030;
      background-color: #ffffff !important;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      height: 70px;
      padding: 0.5rem 1rem;
    }

    /* Espace pour la navbar fixe */
    .navbar-spacer {
      height: 10px;
      /* Au lieu de 70px */
      width: 100%;
      display: block;
    }

    /* Ajustement du contenu principal */
    .main-content-adjusted {
      min-height: calc(100vh - 70px);
    }

    /* Correction pour les pages avec sidebar */
    .g-sidenav-show .navbar-unified {
      margin-left: 0;
    }

    /* Assurer que le contenu n'est pas caché derrière la navbar */
    body {
      padding-top: 0;
    }

    /* Styles pour la navbar Bootstrap override */
    .navbar-unified .navbar-brand {
      font-weight: 700;
      color: #344767 !important;
    }

    .navbar-unified .nav-link {
      color: #67748e !important;
      font-weight: 500;
    }

    .navbar-unified .nav-link.active {
      color: #cb0c9f !important;
      font-weight: 600;
    }

    .navbar-unified .nav-link:hover {
      color: #344767 !important;
    }

    /* Correction pour le dropdown */
    .navbar-unified .dropdown-menu {
      border: none;
      box-shadow: 0 8px 26px -4px rgba(20, 20, 20, 0.15);
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }}">

  <!-- Navbar Unifiée -->
  @include('layouts.navbars.unified-navbar')

  <!-- Espace pour la navbar fixe -->
  <div class="navbar-spacer"></div>

  @auth
  @yield('auth')
  @endauth
  @guest
  @yield('guest')
  @endguest

  @if(session()->has('success'))
  <div x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    class="position-fixed bg-success rounded right-3 text-sm py-2 px-4"
    style="z-index: 9999; top: 80px; right: 20px;">
    <p class="m-0 text-white">{{ session('success')}}</p>
  </div>
  @endif

  <!-- Core JS Files -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/fullcalendar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>

  <!-- Bootstrap JS pour la navbar -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  @stack('rtl')
  @stack('dashboard')

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    // Initialisation Bootstrap pour la navbar
    document.addEventListener('DOMContentLoaded', function() {
      // Activer les dropdowns Bootstrap
      var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
      var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
      });

      // Fermer le menu mobile après clic sur un lien
      var navbarToggler = document.querySelector('.navbar-toggler');
      var navbarContent = document.querySelector('#navbarContent');
      if (navbarToggler && navbarContent) {
        var navLinks = navbarContent.querySelectorAll('.nav-link');
        navLinks.forEach(function(link) {
          link.addEventListener('click', function() {
            if (navbarContent.classList.contains('show')) {
              navbarToggler.click();
            }
          });
        });
      }
    });
  </script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script>
</body>

</html>