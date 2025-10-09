@extends('layouts.app')

@section('guest')
    @if(!\Request::is('login') && !\Request::is('register'))
        @include('layouts.navbars.main-navbar')
    @endif
    
    @yield('content')
    
    @if(!\Request::is('login') && !\Request::is('register'))
        @include('layouts.footers.guest.footer')
    @endif
@endsection