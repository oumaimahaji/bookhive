@extends('layouts.app')

@section('guest')
    {{-- Afficher la navbar principale sauf sur les pages login et register --}}
    @if(!\Request::is('login') && !\Request::is('register') && !\Request::is('login/forgot-password'))
        @include('layouts.navbars.main-navbar')
    @elseif(\Request::is('login/forgot-password'))
        @include('layouts.navbars.guest.nav')
    @endif

    {{-- Contenu de la page --}}
    @yield('content')

    {{-- Footer sauf sur login et register --}}
    @if(!\Request::is('login') && !\Request::is('register'))
        @include('layouts.footers.guest.footer')
    @endif
@endsection
