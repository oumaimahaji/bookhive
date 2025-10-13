<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHive</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Main Navbar - Used on ALL pages -->
    <nav style="
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        padding: 0;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    ">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; height: 72px; min-height: 72px;">

                <!-- Logo -->
                <div style="flex-shrink: 0;">
                    <a href="{{ url('/') }}" style="
                        color: #000;
                        text-decoration: none;
                        font-size: 24px;
                        font-weight: 700;
                        display: flex;
                        align-items: center;
                    ">
                        <span style="
                            background: linear-gradient(135deg, #EA4C89 0%, #8A2387 100%);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            font-weight: 800;
                        ">BookHive</span>
                    </a>
                </div>

                <!-- Menu -->
                <div style="display: flex; gap: 16px; align-items: center; font-size: 14px; font-weight: 500; flex-wrap: nowrap; overflow: visible;">

                    <a href="{{ url('/') }}" style="
                        color: #6B7280;
                        text-decoration: none;
                        padding: 8px 12px;
                        border-radius: 6px;
                        transition: all 0.2s ease;
                        white-space: nowrap;
                    " onmouseover="this.style.color='#1F2937'; this.style.background='rgba(0,0,0,0.02)'"
                        onmouseout="this.style.color='#6B7280'; this.style.background='transparent'">
                        <i class="fas fa-home me-1"></i>Home
                    </a>

                    @auth
                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: nowrap;">

                        @if(auth()->user()->role === 'user')
                        <!-- Dashboard Link -->
                        <a href="{{ route('user.dashboard') }}" style="
                                    color: #1F2937;
                                    text-decoration: none;
                                    padding: 8px 16px;
                                    border-radius: 6px;
                                    background: rgba(59, 130, 246, 0.04);
                                    border: 1px solid rgba(59, 130, 246, 0.1);
                                    font-weight: 500;
                                    transition: all 0.2s ease;
                                    white-space: nowrap;
                                " onmouseover="this.style.background='rgba(59, 130, 246, 0.08)'; this.style.borderColor='rgba(59, 130, 246, 0.2)'"
                            onmouseout="this.style.background='rgba(59, 130, 246, 0.04)'; this.style.borderColor='rgba(59, 130, 246, 0.1)'">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>

                        <!-- My Posts Link -->
                        <a href="{{ route('user.posts.my') }}" style="
                                    color: #6B7280;
                                    text-decoration: none;
                                    padding: 8px 12px;
                                    border-radius: 6px;
                                    transition: all 0.2s ease;
                                    white-space: nowrap;
                                " onmouseover="this.style.color='#1F2937'; this.style.background='rgba(0,0,0,0.02)'"
                            onmouseout="this.style.color='#6B7280'; this.style.background='transparent'">
                            <i class="fas fa-newspaper me-1"></i>My Posts
                        </a>

                        <!-- Community Posts Link -->
                        <a href="{{ route('user.posts.community') }}" style="
                                    color: #6B7280;
                                    text-decoration: none;
                                    padding: 8px 12px;
                                    border-radius: 6px;
                                    transition: all 0.2s ease;
                                    white-space: nowrap;
                                " onmouseover="this.style.color='#1F2937'; this.style.background='rgba(0,0,0,0.02)'"
                            onmouseout="this.style.color='#6B7280'; this.style.background='transparent'">
                            <i class="fas fa-users me-1"></i>Community
                        </a>

                        <!-- NOTIFICATIONS ICON -->
                        <div style="position: relative; flex-shrink: 0;">
                            <a href="{{ route('user.notifications') }}" style="
                                        color: #6B7280;
                                        text-decoration: none;
                                        padding: 8px 12px;
                                        border-radius: 6px;
                                        transition: all 0.2s ease;
                                        position: relative;
                                        display: flex;
                                        align-items: center;
                                    " onmouseover="this.style.color='#1F2937'; this.style.background='rgba(0,0,0,0.02)'"
                                onmouseout="this.style.color='#6B7280'; this.style.background='transparent'">
                                <i class="fas fa-bell me-1"></i>
                                @php
                                $unreadCount = auth()->user()->unreadNotificationsCount() ?? 0;
                                @endphp
                                @if($unreadCount > 0)
                                <span style="
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
                                            ">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </div>

                        <!-- LOGOUT BUTTON - BIEN VISIBLE -->
                        <form method="POST" action="{{ url('/logout') }}" style="display: inline; margin: 0; flex-shrink: 0;">
                            @csrf
                            <button type="submit" style="
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
                                    " onmouseover="this.style.background='#EF4444'; this.style.color='white'"
                                onmouseout="this.style.background='transparent'; this.style.color='#EF4444'">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </button>
                        </form>
                        @endif

                        <!-- Other roles (admin, moderator, club_manager) -->
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ url('/dashboard') }}" style="
                                    color: #1F2937;
                                    text-decoration: none;
                                    padding: 8px 16px;
                                    border-radius: 6px;
                                    background: rgba(234, 76, 137, 0.04);
                                    border: 1px solid rgba(234, 76, 137, 0.1);
                                    font-weight: 500;
                                    transition: all 0.2s ease;
                                    white-space: nowrap;
                                " onmouseover="this.style.background='rgba(234, 76, 137, 0.08)'; this.style.borderColor='rgba(234, 76, 137, 0.2)'"
                            onmouseout="this.style.background='rgba(234, 76, 137, 0.04)'; this.style.borderColor='rgba(234, 76, 137, 0.1)'">
                            <i class="fas fa-crown me-1"></i>Admin Dashboard
                        </a>

                        <!-- Logout for Admin -->
                        <form method="POST" action="{{ url('/logout') }}" style="display: inline; margin: 0; flex-shrink: 0;">
                            @csrf
                            <button type="submit" style="
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
                                    " onmouseover="this.style.background='#EF4444'; this.style.color='white'"
                                onmouseout="this.style.background='transparent'; this.style.color='#EF4444'">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </button>
                        </form>
                        @endif

                        @if(auth()->user()->role === 'moderator')
                        <a href="{{ url('/moderator/dashboard') }}" style="
                                    color: #1F2937;
                                    text-decoration: none;
                                    padding: 8px 16px;
                                    border-radius: 6px;
                                    background: rgba(59, 130, 246, 0.04);
                                    border: 1px solid rgba(59, 130, 246, 0.1);
                                    font-weight: 500;
                                    transition: all 0.2s ease;
                                    white-space: nowrap;
                                " onmouseover="this.style.background='rgba(59, 130, 246, 0.08)'; this.style.borderColor='rgba(59, 130, 246, 0.2)'"
                            onmouseout="this.style.background='rgba(59, 130, 246, 0.04)'; this.style.borderColor='rgba(59, 130, 246, 0.1)'">
                            <i class="fas fa-shield-alt me-1"></i>Moderator Dashboard
                        </a>

                        <!-- Logout for Moderator -->
                        <form method="POST" action="{{ url('/logout') }}" style="display: inline; margin: 0; flex-shrink: 0;">
                            @csrf
                            <button type="submit" style="
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
                                    " onmouseover="this.style.background='#EF4444'; this.style.color='white'"
                                onmouseout="this.style.background='transparent'; this.style.color='#EF4444'">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </button>
                        </form>
                        @endif

                        @if(auth()->user()->role === 'club_manager')
                        <a href="{{ url('/club-manager/dashboard') }}" style="
                                    color: #1F2937;
                                    text-decoration: none;
                                    padding: 8px 16px;
                                    border-radius: 6px;
                                    background: rgba(16, 185, 129, 0.04);
                                    border: 1px solid rgba(16, 185, 129, 0.1);
                                    font-weight: 500;
                                    transition: all 0.2s ease;
                                    white-space: nowrap;
                                " onmouseover="this.style.background='rgba(16, 185, 129, 0.08)'; this.style.borderColor='rgba(16, 185, 129, 0.2)'"
                            onmouseout="this.style.background='rgba(16, 185, 129, 0.04)'; this.style.borderColor='rgba(16, 185, 129, 0.1)'">
                            <i class="fas fa-users me-1"></i>Club Dashboard
                        </a>

                        <!-- Logout for Club Manager -->
                        <form method="POST" action="{{ url('/logout') }}" style="display: inline; margin: 0; flex-shrink: 0;">
                            @csrf
                            <button type="submit" style="
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
                                    " onmouseover="this.style.background='#EF4444'; this.style.color='white'"
                                onmouseout="this.style.background='transparent'; this.style.color='#EF4444'">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </button>
                        </form>
                        @endif

                    </div>

                    @else
                    <!-- Visitor Menu -->
                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: nowrap;">
                        <a href="{{ url('/about') }}" style="
                                color: #6B7280;
                                text-decoration: none;
                                padding: 8px 12px;
                                border-radius: 6px;
                                transition: all 0.2s ease;
                                white-space: nowrap;
                            " onmouseover="this.style.color='#1F2937'; this.style.background='rgba(0,0,0,0.02)'"
                            onmouseout="this.style.color='#6B7280'; this.style.background='transparent'">
                            About
                        </a>

                        <a href="{{ url('/contact') }}" style="
                                color: #6B7280;
                                text-decoration: none;
                                padding: 8px 12px;
                                border-radius: 6px;
                                transition: all 0.2s ease;
                                white-space: nowrap;
                            " onmouseover="this.style.color='#1F2937'; this.style.background='rgba(0,0,0,0.02)'"
                            onmouseout="this.style.color='#6B7280'; this.style.background='transparent'">
                            Contact
                        </a>

                        <a href="{{ url('/login') }}" style="
                                color: #6B7280;
                                text-decoration: none;
                                padding: 8px 16px;
                                border-radius: 6px;
                                transition: all 0.2s ease;
                                border: 1px solid transparent;
                                white-space: nowrap;
                            " onmouseover="this.style.color='#1F2937'; this.style.background='rgba(0,0,0,0.02)'; this.style.borderColor='#D1D5DB'"
                            onmouseout="this.style.color='#6B7280'; this.style.background='transparent'; this.style.borderColor='transparent'">
                            Sign In
                        </a>

                        <a href="{{ url('/register') }}" style="
                                background: #EA4C89;
                                color: white;
                                padding: 8px 20px;
                                border-radius: 6px;
                                text-decoration: none;
                                font-weight: 600;
                                transition: all 0.2s ease;
                                border: 1px solid #EA4C89;
                                white-space: nowrap;
                            " onmouseover="this.style.background='#D8417A'; this.style.borderColor='#D8417A'; this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.background='#EA4C89'; this.style.borderColor='#EA4C89'; this.style.transform='translateY(0)'">
                            Sign Up
                        </a>
                    </div>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <!-- Space for fixed navbar -->
    <div style="height: 0px;"></div>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>