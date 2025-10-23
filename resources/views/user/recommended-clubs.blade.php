@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Clubs Recommandés</h4>
                    <div>
                        <a href="{{ route('user.clubs') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i> Voir tous les clubs
                        </a>
                    </div>
                </div>
                <p class="text-muted">Découvrez les clubs les plus actifs avec le plus d'événements</p>
            </div>
        </div>

        {{-- Section Clubs Recommandés --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-star text-warning me-2"></i>
                            Clubs populaires recommandés pour vous
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($recommendedClubs->count() > 0)
                            <div class="row">
                                @foreach($recommendedClubs as $club)
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0">{{ $club->nom }}</h6>
                                                    @if($club->evenements_count >= 5)
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-fire me-1"></i> Populaire
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <p class="card-text text-muted small mb-2">
                                                    {{ Str::limit($club->description, 100) }}
                                                </p>
                                                
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $club->evenements_count }} événement(s)
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>
                                                        {{ $club->createur->name }}
                                                    </small>
                                                </div>

                                                {{-- Bouton d'action --}}
                                                @if($club->user_status == 'member')
                                                    <button class="btn btn-success btn-sm w-100" disabled>
                                                        <i class="fas fa-check me-1"></i> Membre
                                                    </button>
                                                @elseif($club->user_status == 'pending')
                                                    <button class="btn btn-warning btn-sm w-100" disabled>
                                                        <i class="fas fa-clock me-1"></i> En attente
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary btn-sm w-100 join-club-btn" 
                                                            data-club-id="{{ $club->id }}">
                                                        <i class="fas fa-plus me-1"></i> Rejoindre
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun club recommandé pour le moment</h5>
                                <p class="text-muted">Revenez plus tard pour découvrir de nouveaux clubs.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Clubs les plus actifs --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line text-success me-2"></i>
                            Clubs les plus actifs
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($activeClubs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Club</th>
                                            <th>Événements (30 jours)</th>
                                            <th>Créateur</th>
                                            <th>Statut</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeClubs as $club)
                                            <tr>
                                                <td>
                                                    <strong>{{ $club->nom }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($club->description, 50) }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $club->evenements_count }}</span>
                                                </td>
                                                <td>
                                                    <small>{{ $club->createur->name }}</small>
                                                </td>
                                                <td>
                                                    @if($club->user_status == 'member')
                                                        <span class="badge bg-success">Membre</span>
                                                    @elseif($club->user_status == 'pending')
                                                        <span class="badge bg-warning">En attente</span>
                                                    @else
                                                        <span class="badge bg-secondary">Non membre</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($club->user_status == 'not_member')
                                                        <button class="btn btn-outline-primary btn-sm join-club-btn" 
                                                                data-club-id="{{ $club->id }}">
                                                            <i class="fas fa-plus me-1"></i> Rejoindre
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-secondary btn-sm" disabled>
                                                            <i class="fas fa-check me-1"></i> 
                                                            {{ $club->user_status == 'member' ? 'Membre' : 'En attente' }}
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun club actif pour le moment</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gestion du clic sur le bouton "Rejoindre"
    $('.join-club-btn').on('click', function() {
        const button = $(this);
        const clubId = button.data('club-id');
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Envoi...');

        // CORRECTION : Utiliser la route correcte avec le paramètre clubId
        $.ajax({
            url: '{{ route("user.clubs.join", ["clubId" => ":clubId"]) }}'.replace(':clubId', clubId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    button.removeClass('btn-primary').addClass('btn-warning')
                         .html('<i class="fas fa-clock me-1"></i> En attente')
                         .prop('disabled', true);
                    
                    showToast('success', response.message);
                } else {
                    button.prop('disabled', false).html('<i class="fas fa-plus me-1"></i> Rejoindre');
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                button.prop('disabled', false).html('<i class="fas fa-plus me-1"></i> Rejoindre');
                showToast('error', 'Une erreur est survenue');
            }
        });
    });

    function showToast(type, message) {
        // Implémentation simple de toast - vous pouvez utiliser votre système de notification existant
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
});
</script>
@endpush