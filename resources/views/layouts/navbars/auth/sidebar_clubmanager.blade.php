<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('club_manager.dashboard') }}">
            <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="...">
            <span class="ms-3 font-weight-bold">BOOKHIVE</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('club-manager/dashboard') ? 'active' : '' }}" href="{{ route('club_manager.dashboard') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-tachometer-alt text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <!-- Gestion des Clubs -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Clubs Management</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('club-manager/clubs*') ? 'active' : '' }}" href="{{ route('club_manager.clubs.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Clubs</span>
                </a>
            </li>
            
            <!-- Gestion des Événements -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Events Management</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('club-manager/events*') ? 'active' : '' }}" href="{{ route('club_manager.events.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-calendar-alt text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Events</span>
                </a>
            </li>

            <!-- Gestion des Membres -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Members Management</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('club-manager/members*') ? 'active' : '' }}" href="{{ route('club_manager.members.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-friends text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Club Members</span>
                </a>
            </li>

            <!-- Notifications -->
            <li class="nav-item">
                <a class="nav-link {{ Route::is('club_manager.notifications.*') ? 'active' : '' }}" href="{{ route('club_manager.notifications.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-bell text-dark text-lg opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1"> Notifications</span>
                </a>
            </li>

            <!-- Compte / Profil -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('club-manager/profile') ? 'active' : '' }}" href="{{ route('club_manager.profile') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-cog text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profil</span>
                </a>
            </li>

            

        </ul>
    </div>
</aside>