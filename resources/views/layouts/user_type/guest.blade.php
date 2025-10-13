@extends('layouts.app')

@section('guest')
<<<<<<< HEAD
    @if(\Request::is('login/forgot-password')) 
        @include('layouts.navbars.guest.nav')
        @yield('content') 
    @else
        <div class="container position-sticky z-index-sticky top-0">
            <div class="row">
                <div class="col-12">
                    @include('layouts.navbars.guest.nav')
                </div>
            </div>
        </div>
        @yield('content')        
=======
    @if(!\Request::is('login') && !\Request::is('register'))
        @include('layouts.navbars.main-navbar')
    @endif
    
    @yield('content')
    
    @if(!\Request::is('login') && !\Request::is('register'))
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        @include('layouts.footers.guest.footer')
    @endif
@endsection