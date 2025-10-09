<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>BookHive - My Notifications</title>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Autres styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="../assets/css/soft-ui-dashboard.css" rel="stylesheet" />

    <style>
        .notification-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .notification-unread {
            border-left: 4px solid #EA4C89;
            background: rgba(234, 76, 137, 0.02);
        }
        .event-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .badge-notification {
            background: #EA4C89;
        }
        .badge-event {
            background: #10B981;
        }
    </style>
</head>

<body class="bg-gray-100">
    
    @include('layouts.navbars.main-navbar')
    
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style="margin-top: 80px;">
        
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">My Notifications</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">
                        <i class="fas fa-bell me-2"></i>My Notifications
                    </h6>
                </nav>
            </div>
        </nav>

        <div class="container-fluid py-4">

            {{-- Header --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-gradient-dark shadow-dark border-radius-lg">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-8">
                                    <h4 class="text-white mb-1">My Notifications</h4>
                                    <p class="text-white opacity-8 mb-0">Stay updated with your club memberships and events.</p>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                        <i class="fas fa-bell text-dark text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Notifications Section --}}
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>
                                <i class="fas fa-envelope me-2"></i>Club Notifications
                                <span class="badge badge-notification ms-2">{{ $notifications->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            @if($notifications->count() > 0)
                                @foreach($notifications as $notification)
                                <div class="card notification-card {{ is_null($notification->read_at) ? 'notification-unread' : '' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    @if($notification->type == 'join_approved')
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                    @elseif($notification->type == 'join_rejected')
                                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                                    @else
                                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                                    @endif
                                                    {{ $notification->message }}
                                                </h6>
                                                <p class="text-muted mb-0 small">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                                @if($notification->club)
                                                <p class="text-muted mb-0 small">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $notification->club->nom }}
                                                </p>
                                                @endif
                                            </div>
                                            @if(is_null($notification->read_at))
                                            <span class="badge bg-warning">New</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center p-4">
                                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No notifications</h5>
                                    <p class="text-muted">You don't have any notifications yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Club Events Section --}}
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>
                                <i class="fas fa-calendar-alt me-2"></i>Upcoming Club Events
                                <span class="badge badge-event ms-2">{{ $clubEvents->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            @if($clubEvents->count() > 0)
                                @foreach($clubEvents as $event)
                                <div class="card event-card">
                                    <div class="card-body">
                                        <h6 class="mb-1">
                                            <i class="fas fa-calendar-check text-primary me-2"></i>
                                            {{ $event->titre }}
                                        </h6>
                                        <p class="text-muted mb-2 small">{{ Str::limit($event->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $event->club->nom }}
                                            </span>
                                            <span class="text-muted small">
                                                <i class="fas fa-clock me-1"></i>
                                                {{-- CORRECTION : Utiliser Carbon pour formater la date --}}
                                                @php
                                                    // Convertir la date en objet Carbon si ce n'est pas déjà le cas
                                                    $eventDate = $event->date_event instanceof \Carbon\Carbon 
                                                        ? $event->date_event 
                                                        : \Carbon\Carbon::parse($event->date_event);
                                                @endphp
                                                {{ $eventDate->format('M d, Y H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center p-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No upcoming events</h5>
                                    <p class="text-muted">There are no upcoming events in your clubs.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/soft-ui-dashboard.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Marquer les notifications comme lues lorsqu'elles sont visualisées
            const unreadNotifications = document.querySelectorAll('.notification-unread');
            
            unreadNotifications.forEach(notification => {
                // Optionnel: Ajouter une logique pour marquer comme lu via AJAX
                notification.addEventListener('click', function() {
                    const notificationId = this.dataset.notificationId;
                    if (notificationId) {
                        fetch(`/user/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>