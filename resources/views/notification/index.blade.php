@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12">
                <h4>Demandes de rejoindre les clubs</h4>
                <p class="text-muted">Gérez les demandes des utilisateurs souhaitant rejoindre vos clubs.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Filtres --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Filtrer les demandes</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">Statut</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="all">Tous les statuts</option>
                                    <option value="pending">En attente</option>
                                    <option value="accepted">Accepté</option>
                                    <option value="rejected">Refusé</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="clubFilter" class="form-label">Club</label>
                                <select class="form-select" id="clubFilter">
                                    <option value="all">Tous les clubs</option>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}">{{ $club->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dateFilter" class="form-label">Date</label>
                                <select class="form-select" id="dateFilter">
                                    <option value="all">Toutes les dates</option>
                                    <option value="today">Aujourd'hui</option>
                                    <option value="week">Cette semaine</option>
                                    <option value="month">Ce mois</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters">
                                    <i class="fas fa-redo me-2"></i>Réinitialiser
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
                        <div>
                            <h6>Demandes de rejoindre les clubs</h6>
                            <span class="text-muted text-sm" id="filterResultText">
                                {{ $notifications->count() }} demande(s) au total
                            </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2" id="pendingCount">{{ $notifications->where('status', 'pending')->count() }} en attente</span>
                            <span class="badge bg-success me-2" id="acceptedCount">{{ $notifications->where('status', 'accepted')->count() }} accepté(s)</span>
                            <span class="badge bg-danger" id="rejectedCount">{{ $notifications->where('status', 'rejected')->count() }} refusé(s)</span>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            @if($notifications->count() > 0)
                                <table class="table align-items-center mb-0" id="notificationsTable">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilisateur</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Club</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Message</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($notifications as $notification)
                                        <tr class="notification-row" 
                                            data-status="{{ $notification->status }}"
                                            data-club="{{ $notification->club->id ?? '' }}"
                                            data-date="{{ $notification->created_at->format('Y-m-d') }}">
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
                                                    <form action="{{ route('club_manager.notifications.accept', $notification->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-outline-success btn-sm mx-1"
                                                                data-bs-toggle="tooltip" title="Accepter">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('club_manager.notifications.reject', $notification->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm mx-1"
                                                                data-bs-toggle="tooltip" title="Refuser">
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
@endsection

@push('scripts')
<script>
    // Activer les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Fonctionnalité de filtrage
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('statusFilter');
        const clubFilter = document.getElementById('clubFilter');
        const dateFilter = document.getElementById('dateFilter');
        const resetFilters = document.getElementById('resetFilters');
        const notificationRows = document.querySelectorAll('.notification-row');
        const filterResultText = document.getElementById('filterResultText');

        function filterNotifications() {
            const statusValue = statusFilter.value;
            const clubValue = clubFilter.value;
            const dateValue = dateFilter.value;
            
            let visibleCount = 0;
            let pendingCount = 0;
            let acceptedCount = 0;
            let rejectedCount = 0;

            notificationRows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const rowClub = row.getAttribute('data-club');
                const rowDate = row.getAttribute('data-date');
                
                let statusMatch = statusValue === 'all' || rowStatus === statusValue;
                let clubMatch = clubValue === 'all' || rowClub === clubValue;
                let dateMatch = dateValue === 'all' || isDateInRange(rowDate, dateValue);
                
                if (statusMatch && clubMatch && dateMatch) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Compter les statuts pour les badges
                    if (rowStatus === 'pending') pendingCount++;
                    if (rowStatus === 'accepted') acceptedCount++;
                    if (rowStatus === 'rejected') rejectedCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Mettre à jour le texte des résultats
            filterResultText.textContent = `${visibleCount} demande(s) trouvée(s)`;
            
            // Mettre à jour les badges
            document.getElementById('pendingCount').textContent = `${pendingCount} en attente`;
            document.getElementById('acceptedCount').textContent = `${acceptedCount} accepté(s)`;
            document.getElementById('rejectedCount').textContent = `${rejectedCount} refusé(s)`;
        }

        function isDateInRange(dateString, range) {
            const date = new Date(dateString);
            const today = new Date();
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

            switch (range) {
                case 'today':
                    return date.toDateString() === today.toDateString();
                case 'week':
                    return date >= startOfWeek && date <= today;
                case 'month':
                    return date >= startOfMonth && date <= today;
                default:
                    return true;
            }
        }

        function resetAllFilters() {
            statusFilter.value = 'all';
            clubFilter.value = 'all';
            dateFilter.value = 'all';
            filterNotifications();
        }

        // Événements
        statusFilter.addEventListener('change', filterNotifications);
        clubFilter.addEventListener('change', filterNotifications);
        dateFilter.addEventListener('change', filterNotifications);
        resetFilters.addEventListener('click', resetAllFilters);

        // Initialiser le filtrage
        filterNotifications();
    });
</script>
@endpush