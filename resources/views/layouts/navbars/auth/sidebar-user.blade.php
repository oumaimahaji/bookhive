<<<<<<< HEAD
<!-- Sidebar pour User -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('user.dashboard') }}">
            <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="...">
            <span class="ms-3 font-weight-bold">BOOKHIVE - USER</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ (Request::is('user/dashboard') ? 'active' : '') }}" href="{{ route('user.dashboard') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-home text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            
=======
<!-- resources/views/layouts/navbars/auth/sidebar-user.blade.php -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('user.dashboard') }}">

        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">


            </li>

            <!-- Profile -->
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
            <li class="nav-item">
                <a class="nav-link {{ (Request::is('user/profile') ? 'active' : '') }}" href="{{ route('user.profile') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Profile</span>
                </a>
            </li>

<<<<<<< HEAD
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Library</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ (Request::is('user/books') ? 'active' : '') }}" href="{{ route('user.books') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-book text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Browse Books</span>
=======
            <!-- Books Section -->


            <li class="nav-item">
                <a class="nav-link {{ (Request::is('user/posts/community') ? 'active' : '') }}" href="{{ route('user.posts.community') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-comments text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Community Posts</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Community</h6>
            </li>



            <li class="nav-item">
                <a class="nav-link {{ (Request::is('user/posts/community') ? 'active' : '') }}" href="{{ route('user.posts.community') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-comments text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Community Posts</span>
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
                </a>
            </li>

            <li class="nav-item">
<<<<<<< HEAD
                <a class="nav-link {{ (Request::is('reservations') ? 'active' : '') }}" href="{{ route('reservations.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-calendar-check text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Reservations</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ (Request::is('reviews') ? 'active' : '') }}" href="{{ route('reviews.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-star text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Reviews</span>
                </a>
            </li>

=======
                <a class="nav-link {{ (Request::is('user/posts/my') ? 'active' : '') }}" href="{{ route('user.posts.my') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-file-alt text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Posts</span>
                </a>
            </li>

            <!-- Notifications -->
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account</h6>
            </li>

            <li class="nav-item">
<<<<<<< HEAD
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
=======
                <a class="nav-link {{ (Request::is('user/notifications') ? 'active' : '') }}" href="{{ route('user.notifications') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center position-relative">
                        <i class="fas fa-bell text-dark"></i>
                        @php
                        $unreadCount = auth()->user()->unread_notifications_count ?? 0;
                        @endphp
                        @if($unreadCount > 0)
                        <span class="notification-badge">{{ $unreadCount }}</span>
                        @endif
                    </div>
                    <span class="nav-link-text ms-1">Notifications</span>
                </a>
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
            </li>
        </ul>
    </div>
</aside>