<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>BookHive - Browse Clubs</title>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- PerfectScrollbar CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/perfect-scrollbar.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/css/perfect-scrollbar.min.css">
    
    <!-- Autres styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="../assets/css/soft-ui-dashboard.css" rel="stylesheet" />

    <style>
        .club-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            height: 100%;
        }
        .club-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .club-icon {
            width: 70px;
            height: 70px;
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
        .search-box {
            border-radius: 25px;
            border: 1px solid #e2e8f0;
            padding: 10px 20px;
        }
        .filter-btn {
            border-radius: 25px;
        }
        .btn-member {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-pending {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
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
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Browse Clubs</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">
                        <i class="fas fa-users me-2"></i>Browse Clubs
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
                                    <h4 class="text-white mb-1">Discover Reading Clubs</h4>
                                    <p class="text-white opacity-8 mb-0">Join communities of book lovers and share your reading experiences.</p>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                        <i class="fas fa-users text-dark text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search and Filter --}}
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control search-box border-start-0" placeholder="Search clubs by name or description...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select filter-btn">
                        <option value="">All Clubs</option>
                        <option value="popular">Most Popular</option>
                        <option value="recent">Recently Created</option>
                        <option value="active">Most Active</option>
                    </select>
                </div>
            </div>

            {{-- Clubs Count --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Found <strong>{{ $clubs->count() }}</strong> reading clubs
                    </div>
                </div>
            </div>

            {{-- Clubs Grid --}}
            <div class="row">
                @forelse($clubs as $club)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card club-card">
                        <div class="card-body text-center p-4">
                            <div class="club-icon mx-auto">
                                <i class="fas fa-users text-white fa-2x"></i>
                            </div>
                            <h5 class="card-title mb-2">{{ $club->nom }}</h5>
                            <p class="card-text text-muted mb-3">{{ Str::limit($club->description, 120) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="text-start">
                                    <small class="text-muted">Created by</small>
                                    <br>
                                    <strong>{{ $club->createur->name ?? 'Unknown' }}</strong>
                                </div>
                                <span class="members-count">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $club->created_at->format('M d, Y') }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-book me-1"></i>
                                    {{ $club->evenements_count ?? 0 }} events
                                </small>
                                
                                {{-- Afficher le bon bouton selon le statut --}}
                                @if($club->user_status == 'member')
                                    <button class="btn btn-member" disabled>
                                        <i class="fas fa-check me-1"></i>Membre
                                    </button>
                                @elseif($club->user_status == 'pending')
                                    <button class="btn btn-pending" disabled>
                                        <i class="fas fa-clock me-1"></i>En attente
                                    </button>
                                @else
                                    <button class="btn btn-primary join-club-btn" data-club-id="{{ $club->id }}">
                                        <i class="fas fa-plus me-1"></i>Join Club
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No Clubs Available</h4>
                            <p class="text-muted">There are no reading clubs available at the moment.</p>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/soft-ui-dashboard.js"></script>

   <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Join club functionality
        const joinButtons = document.querySelectorAll('.join-club-btn');
        joinButtons.forEach(button => {
            button.addEventListener('click', function() {
                const clubId = this.getAttribute('data-club-id');
                const button = this;
                const card = button.closest('.card');
                
                // Show loading state
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>En attente...';
                button.disabled = true;
                
                fetch(`/user/clubs/${clubId}/join`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mettre à jour le bouton
                        button.innerHTML = '<i class="fas fa-clock me-1"></i>En attente';
                        button.classList.remove('btn-primary');
                        button.classList.add('btn-pending');
                        button.disabled = true;
                        showNotification(data.message, 'success');
                    } else {
                        button.innerHTML = originalText;
                        button.disabled = false;
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.innerHTML = originalText;
                    button.disabled = false;
                    showNotification('Une erreur est survenue lors de l\'envoi de la demande.', 'error');
                });
            });
        });

        // Search functionality
        const searchInput = document.querySelector('.search-box');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const clubCards = document.querySelectorAll('.col-xl-4.col-md-6.mb-4');
            
            clubCards.forEach(card => {
                const clubName = card.querySelector('.card-title').textContent.toLowerCase();
                const clubDescription = card.querySelector('.card-text').textContent.toLowerCase();
                
                if (clubName.includes(searchTerm) || clubDescription.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        function showNotification(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const notification = document.createElement('div');
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
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