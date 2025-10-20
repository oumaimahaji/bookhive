<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>BookHive - User Dashboard</title>

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="../assets/css/soft-ui-dashboard.css" rel="stylesheet" />

    <style>
        /* NAVBAR FIXED STYLES */
        .main-navbar-fixed {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            height: 72px;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            height: 100%;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
        }

        .navbar-brand-custom {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #EA4C89 0%, #8A2387 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            gap: 16px;
            align-items: center;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link-custom {
            color: #6B7280;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
        }

        .nav-link-custom:hover {
            color: #1F2937;
            background: rgba(0, 0, 0, 0.02);
        }

        .nav-link-custom i {
            margin-right: 8px;
        }

        .logout-btn-custom {
            background: transparent;
            color: #EF4444;
            border: 1px solid #EF4444;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logout-btn-custom:hover {
            background: #EF4444;
            color: white;
        }

        .notification-badge-custom {
            position: absolute;
            top: 4px;
            right: 4px;
            background: #EA4C89;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .notification-container {
            position: relative;
        }

        /* Ajustements pour le contenu principal */
        .main-content-adjusted {
            margin-left: 270px;
            margin-top: 72px !important;
        }

        /* Dashboard styles */
        .dashboard-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .club-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .club-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .club-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #e91e63, #9c27b0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .members-count {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.8rem;
        }

        .quick-action-btn {
            height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .quick-action-btn:hover {
            transform: translateY(-3px);
        }

        /* Responsive adjustments */
        @media (max-width: 1199.98px) {
            .main-content-adjusted {
                margin-left: 0;
            }
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e91e63;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 10px;
        }

        .status-member {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-not-member {
            background: #f8d7da;
            color: #721c24;
        }

        /* Espace pour la navbar fixe */
        .navbar-spacer {
            height: 72px;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">

    
    <nav class="main-navbar-fixed">
        <div class="navbar-container">
            <div class="navbar-content">
               
                <a href="{{ url('/') }}" class="navbar-brand-custom">
                    BookHive
                </a>

                
                <div class="nav-menu">
                    <a href="{{ url('/') }}" class="nav-link-custom">
                        <i class="fas fa-home"></i>Home
                    </a>

                    @auth
                    @if(auth()->user()->role === 'user')
                    
                    <a href="{{ route('user.posts.my') }}" class="nav-link-custom">
                        <i class="fas fa-newspaper"></i>My Posts
                    </a>

                    
                    <a href="{{ route('user.posts.community') }}" class="nav-link-custom">
                        <i class="fas fa-users"></i>Community
                    </a>

                    
                    <div class="notification-container">
                        <a href="{{ route('user.notifications') }}" class="nav-link-custom">
                            <i class="fas fa-bell"></i>
                            @php
                            $unreadCount = auth()->user()->unreadNotificationsCount() ?? 0;
                            @endphp
                            @if($unreadCount > 0)
                            <span class="notification-badge-custom">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </div>

                    
                    <form method="POST" action="{{ url('/logout') }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn-custom">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </button>
                    </form>
                    @endif

                    
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ url('/dashboard') }}" class="nav-link-custom" style="
                                color: #1F2937;
                                background: rgba(234, 76, 137, 0.04);
                                border: 1px solid rgba(234, 76, 137, 0.1);
                            ">
                        <i class="fas fa-crown"></i>Admin Dashboard
                    </a>
                    <form method="POST" action="{{ url('/logout') }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn-custom">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </button>
                    </form>
                    @endif

                    @if(auth()->user()->role === 'moderator')
                    <a href="{{ url('/moderator/dashboard') }}" class="nav-link-custom" style="
                                color: #1F2937;
                                background: rgba(59, 130, 246, 0.04);
                                border: 1px solid rgba(59, 130, 246, 0.1);
                            ">
                        <i class="fas fa-shield-alt"></i>Moderator Dashboard
                    </a>
                    <form method="POST" action="{{ url('/logout') }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn-custom">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </button>
                    </form>
                    @endif

                    @if(auth()->user()->role === 'club_manager')
                    <a href="{{ url('/club-manager/dashboard') }}" class="nav-link-custom" style="
                                color: #1F2937;
                                background: rgba(16, 185, 129, 0.04);
                                border: 1px solid rgba(16, 185, 129, 0.1);
                            ">
                        <i class="fas fa-users"></i>Club Dashboard
                    </a>
                    <form method="POST" action="{{ url('/logout') }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn-custom">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </button>
                    </form>
                    @endif
                    @else
                   
                    <a href="{{ url('/about') }}" class="nav-link-custom">About</a>
                    <a href="{{ url('/contact') }}" class="nav-link-custom">Contact</a>
                    <a href="{{ url('/login') }}" class="nav-link-custom">Sign In</a>
                    <a href="{{ url('/register') }}" style="
                            background: #EA4C89;
                            color: white;
                            padding: 8px 20px;
                            border-radius: 6px;
                            text-decoration: none;
                            font-weight: 600;
                            border: 1px solid #EA4C89;
                        ">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    

   
    @include('layouts.navbars.auth.sidebar-user')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg main-content-adjusted">

        
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">


                    </ol>
                    <h6 class="font-weight-bolder mb-0">

                    </h6>
                </nav>
            </div>
        </nav>

        <div class="container-fluid py-4">

            {{-- Messages de statut --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Welcome Message (UNE SEULE FOIS) --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-gradient-primary shadow-primary border-radius-lg">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-8">
                                    <h4 class="text-white mb-1">Welcome back, {{ auth()->user()->name ?? 'User' }}! 👋</h4>
                                    <p class="text-white opacity-8 mb-0">Here's what's happening with your reading journey today.</p>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                        <i class="fas fa-book-reader text-primary text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card dashboard-card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">My Reservations</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ $activeReservations ?? 0 }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="fas fa-bookmark text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card dashboard-card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">My Reviews</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ $totalReviews ?? 0 }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                        <i class="fas fa-star text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card dashboard-card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Available Books</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ $availableBooks ?? 0 }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="fas fa-book text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card dashboard-card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Available Clubs</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ $totalClubs ?? 0 }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                        <i class="fas fa-users text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="row">
                <div class="col-md-3 mb-3">


                </div>
                <div class="col-md-3 mb-3">

                </div>
                <div class="col-md-3 mb-3">

                </div>
                <div class="col-md-3 mb-3">

                </div>
            </div>

            {{-- Recent Clubs --}}
            @if($recentClubs && $recentClubs->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card dashboard-card">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6><i class="fas fa-users me-2"></i>Recent Clubs</h6>
                            <a href="{{ route('user.clubs') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                @foreach($recentClubs as $club)
                                <div class="col-md-4 mb-3">
                                    <div class="card club-card h-100">
                                        <div class="card-body text-center">
                                            <div class="club-icon mx-auto">
                                                <i class="fas fa-users text-white"></i>
                                            </div>
                                            <h6 class="card-title">{{ $club->nom }}</h6>
                                            <p class="card-text text-muted small">{{ Str::limit($club->description, 80) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="members-count">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $club->createur->name ?? 'Owner' }}
                                                </span>
                                                <a href="{{ route('user.clubs') }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </main>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/soft-ui-dashboard.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Join Club functionality
            const joinClubButtons = document.querySelectorAll('.join-club-btn');
            joinClubButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const clubId = this.getAttribute('data-club-id');
                    const clubName = this.getAttribute('data-club-name');
                    joinClub(clubId, clubName, this);
                });
            });

            function joinClub(clubId, clubName, buttonElement) {
                if (!confirm(Are you sure you want to join "${clubName}"?)) {
                    return;
                }

                // Show loading state
                buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Requesting...';
                buttonElement.disabled = true;

                fetch(/user/clubs/${clubId}/join, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button to show pending state
                            buttonElement.innerHTML = '<i class="fas fa-clock me-1"></i> Request Pending';
                            buttonElement.className = 'btn btn-sm btn-warning w-100';
                            buttonElement.disabled = true;

                            // Show success message
                            showNotification(data.message, 'success');
                        } else {
                            // Show error message
                            showNotification(data.message, 'error');
                            // Reset button
                            buttonElement.innerHTML = '<i class="fas fa-sign-in-alt me-1"></i> Join Club';
                            buttonElement.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while processing your request.', 'error');
                        // Reset button
                        buttonElement.innerHTML = '<i class="fas fa-sign-in-alt me-1"></i> Join Club';
                        buttonElement.disabled = false;
                    });
            }

            function showNotification(message, type) {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = alert alert-${type} alert-dismissible fade show position-fixed;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                notification.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(notification);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 5000);
            }
        });
    </script>
</body>

</html>