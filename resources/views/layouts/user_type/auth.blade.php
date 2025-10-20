@extends('layouts.app')

@section('auth')
    @include('layouts.navbars.main-navbar')

    @if(\Request::is('static-sign-up') || \Request::is('static-sign-in')) 
        @include('layouts.navbars.guest.nav')
        @yield('content')
        @include('layouts.footers.guest.footer')

    @else
        @if (\Request::is('rtl'))  
            @include('layouts.navbars.auth.sidebar-rtl')
            <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg overflow-hidden">
                @include('layouts.navbars.auth.nav-rtl')
                <div class="container-fluid py-4">
                    @yield('content')
                    @include('layouts.footers.auth.footer')
                </div>
            </main>

        @elseif (\Request::is('profile'))  
            @include('layouts.navbars.auth.sidebar')
            <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
                @include('layouts.navbars.auth.nav')
                @yield('content')
            </div>

        @elseif (\Request::is('virtual-reality')) 
            @include('layouts.navbars.auth.nav')
            <div class="border-radius-xl mt-3 mx-3 position-relative" style="background-image: url('../assets/img/vr-bg.jpg'); background-size: cover;">
                @include('layouts.navbars.auth.sidebar')
                <main class="main-content mt-1 border-radius-lg">
                    @yield('content')
                </main>
            </div>
            @include('layouts.footers.auth.footer')

        @elseif (\Request::is('moderator/*') || \Request::is('dashboard/moderator'))
            @include('layouts.navbars.auth.sidebar_moderator')
            <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
                <div class="container-fluid py-4" style="margin-top: 80px;">
                    @yield('content')
                    @include('layouts.footers.auth.footer')
                </div>
            </main>

        @elseif (\Request::is('users/*/edit') || \Request::is('user-management') || \Request::is('users/create'))
            @if(auth()->check())
                @if(auth()->user()->role === 'admin')
                    @include('layouts.navbars.auth.sidebar')
                @elseif(auth()->user()->role === 'moderator')
                    @include('layouts.navbars.auth.sidebar_moderator')
                @elseif(auth()->user()->role === 'club_manager')
                    @include('layouts.navbars.auth.sidebar_clubmanager')
                @else
                    @include('layouts.navbars.auth.sidebar-user')
                @endif
            @endif
            <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
                <div class="container-fluid py-4" style="margin-top: 80px;">
                    @yield('content')
                    @include('layouts.footers.auth.footer')
                </div>
            </main>

        @else
            @if(auth()->check())
                @if(auth()->user()->role === 'admin')
                    @include('layouts.navbars.auth.sidebar')
                @elseif(auth()->user()->role === 'moderator')
                    @include('layouts.navbars.auth.sidebar_moderator')
                @elseif(auth()->user()->role === 'club_manager')
                    @include('layouts.navbars.auth.sidebar_clubmanager')
                @else
                    @include('layouts.navbars.auth.sidebar-user')
                @endif
            @endif
            <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
                <div class="container-fluid py-4" style="margin-top: 80px;">
                    @yield('content')
                    @include('layouts.footers.auth.footer')
                </div>
            </main>
        @endif

        @include('components.fixed-plugin')
    @endif
@endsection
