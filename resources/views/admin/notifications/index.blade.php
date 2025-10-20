@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12">
                <h4>Gestion des Demandes de Rejoindre les Clubs</h4>
                <p class="text-muted">Gérez toutes les demandes des utilisateurs souhaitant rejoindre les clubs.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Statistiques --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Demandes en Attente</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $notifications->where('status', 'pending')->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="fas fa-clock text-primary text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card bg-gradient-success text-white">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Demandes Acceptées</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $notifications->where('status', 'accepted')->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="fas fa-check text-success text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card bg-gradient-danger text-white">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Demandes Refusées</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $notifications->where('status', 'rejected')->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="fas fa-times text-danger text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card bg-gradient-info text-white">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Demandes</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $notifications->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="fas fa-bell text-info text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Filtrer par Statut</label>
                                <select class="form-control" id="statusFilter">
                                    <option value="">Tous les statuts</option>
                                    <option value="pending">En attente</option>
                                    <option value="accepted">Acceptées</option>
                                    <option value="rejected">Refusées</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Filtrer par Club</label>
                                <select class="form-control" id="clubFilter">
                                    <option value="">Tous les clubs</option>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}">{{ $club->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary" id="resetFiltersBtn">
                                    <i class="fas fa-refresh me-1"></i>Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Liste des Demandes</h6>
                        <div>
                            <span class="badge bg-primary me-2" id="visibleCount">{{ $notifications->count() }}</span>
                            <span class="text-muted">sur</span>
                            <span class="badge bg-secondary ms-2" id="totalCount">{{ $notifications->count() }}</span>
                            <span class="text-muted">demandes</span>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            @if($notifications->count() > 0)
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilisateur</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Club</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Manager</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Message</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="notificationsTable">
                                        @foreach ($notifications as $notification)
                                        <tr class="notification-row" data-status="{{ $notification->status }}" data-club="{{ $notification->club_id }}">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <i class="fas fa-user-circle text-primary me-2"></i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $notification->applicant->name ?? 'Utilisateur' }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $notification->applicant->email ?? '' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $notification->club->nom ?? 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $notification->club->createur->name ?? 'N/A' }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs text-secondary mb-0">{{ $notification->message }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $notification->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if($notification->status == 'pending')
                                                    <span class="badge bg-warning">En attente</span>
                                                @elseif($notification->status == 'accepted')
                                                    <span class="badge bg-success">Accepté</span>
                                                @else
                                                    <span class="badge bg-danger">Refusé</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($notification->status == 'pending')
                                                <div class="btn-group" role="group">
                                                    <form action="{{ route('admin.notifications.accept', $notification->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-outline-success btn-sm mx-1"
                                                                data-bs-toggle="tooltip" title="Accepter"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir accepter cette demande?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.notifications.reject', $notification->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm mx-1"
                                                                data-bs-toggle="tooltip" title="Refuser"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir refuser cette demande?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                @else
                                                    <span class="text-muted text-xs">Traité</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center p-4">
                                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucune demande pour le moment</h5>
                                    <p class="text-muted">Les demandes des utilisateurs apparaîtront ici.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Code JavaScript directement dans la page pour éviter les problèmes de chargement
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé - initialisation des filtres');
    
    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Références aux éléments
    const statusFilter = document.getElementById('statusFilter');
    const clubFilter = document.getElementById('clubFilter');
    const resetBtn = document.getElementById('resetFiltersBtn');
    const visibleCountElement = document.getElementById('visibleCount');
    const totalCountElement = document.getElementById('totalCount');

    // Fonction pour appliquer les filtres
    function applyFilters() {
        const currentStatus = statusFilter ? statusFilter.value : '';
        const currentClub = clubFilter ? clubFilter.value : '';
        
        const rows = document.querySelectorAll('.notification-row');
        let visibleCount = 0;

        console.log('Application des filtres:', {
            status: currentStatus,
            club: currentClub,
            totalRows: rows.length
        });

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowClub = row.getAttribute('data-club');
            
            let showRow = true;

            // Filtre par statut
            if (currentStatus && rowStatus !== currentStatus) {
                showRow = false;
            }

            // Filtre par club - convertir en string pour la comparaison
            if (currentClub && rowClub !== currentClub.toString()) {
                showRow = false;
            }

            if (showRow) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Mettre à jour les compteurs
        updateCounters(visibleCount, rows.length);
        
        console.log('Filtres appliqués - visibles:', visibleCount);
    }

    // Fonction pour mettre à jour les compteurs
    function updateCounters(visible, total) {
        if (visibleCountElement) {
            visibleCountElement.textContent = visible;
            // Changer la couleur si aucun résultat
            if (visible === 0) {
                visibleCountElement.className = 'badge bg-danger me-2';
            } else {
                visibleCountElement.className = 'badge bg-primary me-2';
            }
        }
        
        if (totalCountElement) {
            totalCountElement.textContent = total;
        }
    }

    // Fonction pour réinitialiser les filtres
    function resetFilters() {
        if (statusFilter) statusFilter.value = '';
        if (clubFilter) clubFilter.value = '';
        applyFilters();
        console.log('Filtres réinitialisés');
    }

    // Ajouter les écouteurs d'événements
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }

    if (clubFilter) {
        clubFilter.addEventListener('change', applyFilters);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }

    // Appliquer les filtres initiaux
    applyFilters();

    // Rendre les fonctions globales pour le débogage
    window.applyFilters = applyFilters;
    window.resetFilters = resetFilters;
    
    console.log('Filtres initialisés avec succès');
});

// Gestion d'erreur pour l'SVG (problème cosmétique)
window.addEventListener('error', function(e) {
    if (e.target.tagName === 'path' && e.message.includes('attribute d')) {
        e.preventDefault();
        console.log('Erreur SVG ignorée - problème cosmétique');
    }
});
</script>
@endsection

<style>
.notification-row {
    transition: all 0.3s ease;
}

.notification-row:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75em;
}

.table-responsive {
    border-radius: 0.5rem;
}

.card {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.bg-gradient-primary { background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important; }
.bg-gradient-success { background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important; }
.bg-gradient-danger { background: linear-gradient(87deg, #f5365c 0, #f56036 100%) !important; }
.bg-gradient-info { background: linear-gradient(87deg, #11cdef 0, #1171ef 100%) !important; }
</style>