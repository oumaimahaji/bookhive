@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12">
                <h4>Gestion des Membres des Clubs</h4>
                <p class="text-muted">Gérez tous les membres de tous les clubs de lecture.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Membres</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['totalMembers'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="fas fa-users text-primary text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Clubs Actifs</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['totalClubs'] }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="fas fa-book text-success text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Membres Moyens</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ $stats['totalClubs'] > 0 ? round($stats['totalMembers'] / $stats['totalClubs'], 1) : 0 }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="fas fa-chart-bar text-info text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card bg-gradient-warning text-white">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Clubs avec Membres</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{ count($stats['membersPerClub']) }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="fas fa-user-check text-warning text-lg opacity-10"></i>
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
                            <div class="col-md-8">
                                <label class="form-label">Filtrer par Club</label>
                                <select class="form-control" name="club_id" id="clubFilter">
                                    <option value="">Tous les clubs</option>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}" 
                                            {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                            {{ $club->nom }} ({{ $club->createur->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Liste des Membres --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Liste des Membres</h6>
                        <div>
                            <span class="badge bg-primary me-2" id="visibleCount">{{ $members->count() }}</span>
                            <span class="text-muted">sur</span>
                            <span class="badge bg-secondary ms-2" id="totalCount">{{ $members->count() }}</span>
                            <span class="text-muted">membres</span>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            @if($members->count() > 0)
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Membre</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Club</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Manager du Club</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date d'adhésion</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="membersTable">
                                        @foreach ($members as $member)
                                        <tr class="member-row" data-club="{{ $member->club_id }}">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <i class="fas fa-user-circle text-primary me-2"></i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $member->user->name ?? 'Utilisateur' }}</h6>
                                                        <p class="text-xs text-secondary mb-0">Membre</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $member->club->nom ?? 'N/A' }}</p>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $member->club->evenements_count ?? 0 }} événements
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $member->club->createur->name ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $member->club->createur->email ?? '' }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs text-secondary mb-0">{{ $member->user->email ?? 'N/A' }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $member->created_at->format('d/m/Y') }}
                                                </p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $member->created_at->diffForHumans() }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <form action="{{ route('admin.members.remove', $member->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            data-bs-toggle="tooltip" 
                                                            title="Retirer du club"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir retirer {{ $member->user->name }} du club {{ $member->club->nom }} ?')">
                                                        <i class="fas fa-user-times"></i> Retirer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center p-4">
                                    <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun membre trouvé</h5>
                                    <p class="text-muted">
                                        @if(request('club_id'))
                                            Aucun membre dans le club sélectionné.
                                        @else
                                            Aucun membre dans les clubs pour le moment.
                                        @endif
                                    </p>
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé - initialisation des fonctionnalités');
    
    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Références aux éléments
    const clubFilter = document.getElementById('clubFilter');
    const visibleCountElement = document.getElementById('visibleCount');
    const totalCountElement = document.getElementById('totalCount');

    // Fonction pour appliquer les filtres côté client (pour l'affichage en temps réel)
    function applyFilters() {
        const currentClub = clubFilter ? clubFilter.value : '';
        
        const rows = document.querySelectorAll('.member-row');
        let visibleCount = 0;

        console.log('Application des filtres:', {
            club: currentClub,
            totalRows: rows.length
        });

        rows.forEach(row => {
            const rowClub = row.getAttribute('data-club');
            
            let showRow = true;

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

    // Fonction pour rediriger avec le filtre (filtrage côté serveur)
    function redirectWithFilter() {
        const selectedClub = clubFilter.value;
        let url = '{{ route("admin.members.index") }}';
        
        if (selectedClub) {
            url += '?club_id=' + selectedClub;
        }
        
        window.location.href = url;
    }

    // Ajouter les écouteurs d'événements
    if (clubFilter) {
        // Filtrage automatique côté client pour l'affichage en temps réel
        clubFilter.addEventListener('change', applyFilters);
        
        // Optionnel: Si vous voulez aussi le filtrage côté serveur, décommentez la ligne suivante
        // clubFilter.addEventListener('change', redirectWithFilter);
    }

    // Appliquer les filtres initiaux
    applyFilters();

    // Rendre les fonctions globales pour le débogage
    window.applyFilters = applyFilters;
    window.redirectWithFilter = redirectWithFilter;
    
    console.log('Fonctionnalités initialisées avec succès');
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
.member-row {
    transition: all 0.3s ease;
}

.member-row:hover {
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
.bg-gradient-info { background: linear-gradient(87deg, #11cdef 0, #1171ef 100%) !important; }
.bg-gradient-warning { background: linear-gradient(87deg, #fb6340 0, #fbb140 100%) !important; }

.btn-outline-danger:hover {
    background-color: #f5365c;
    border-color: #f5365c;
    color: white;
}
</style>
