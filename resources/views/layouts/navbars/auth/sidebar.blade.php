<!-- Sidebar Admin -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
            <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="...">
            <span class="ms-3 font-weight-bold">BOOKHIVE</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <svg width="12px" height="12px" viewBox="0 0 45 40">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                    <g transform="translate(1716.000000, 291.000000)">
                                        <g transform="translate(0.000000, 148.000000)">
                                            <path class="color-background opacity-6" d="M46.72,10.74 L40.84,0.95 ..."></path>
                                            <path class="color-background" d="M39.198,22.49 ..."></path>
                                        </g>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <!-- User Profile -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('user-profile') ? 'active' : '' }}" href="{{ url('user-profile') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Profile</span>
                </a>
            </li>

            <!-- User Management -->
            <li class="nav-item pb-2">
                <a class="nav-link {{ Request::is('user-management') ? 'active' : '' }}" href="{{ url('user-management') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-list-ul text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Management</span>
                </a>
            </li>

            <!-- Manage Books and Categories -->
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manage Books And Categories</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('categories') ? 'active' : '' }}" href="{{ url('categories') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-folder text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('books') ? 'active' : '' }}" href="{{ url('books') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-book text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Books</span>
                </a>
            </li>
            <!-- Manage Clubs and Events -->
<li class="nav-item mt-3">
    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manage Clubs & Events</h6>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::is('admin/clubs*') ? 'active' : '' }}" href="{{ route('admin.clubs.index') }}">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-users text-dark"></i>
        </div>
        <span class="nav-link-text ms-1">Clubs Management</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::is('admin/events*') ? 'active' : '' }}" href="{{ route('admin.events.index') }}">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-calendar-alt text-dark"></i>
        </div>
        <span class="nav-link-text ms-1">Events Management</span>
    </a>
</li>
<!-- Gestion des Membres des Clubs -->
<li class="nav-item">
    <a class="nav-link {{ Request::is('admin/members*') ? 'active' : '' }}" href="{{ route('admin.members.index') }}">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-user-friends text-dark"></i>
        </div>
        <span class="nav-link-text ms-1">Membres des Clubs</span>
    </a>
</li>
            <!-- Reservations and Reviews -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manage Library</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('reservations') ? 'active' : '' }}" href="{{ route('reservations.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-calendar-check text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Reservations</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('reviews') ? 'active' : '' }}" href="{{ route('reviews.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-star text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Reviews</span>
                </a>
            </li>
            <!-- Manage Notifications -->
<li class="nav-item mt-3">
    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manage Notifications</h6>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::is('admin/notifications*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-bell text-dark"></i>
        </div>
        <span class="nav-link-text ms-1">Join Requests</span>
    </a>
</li>
            <!-- Manage Community -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manage Community</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('posts*') ? 'active' : '' }}" href="{{ route('posts.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-newspaper text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Posts Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('comments*') ? 'active' : '' }}" href="{{ route('comments.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-comments text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Comments Management</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
