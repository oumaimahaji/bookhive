@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-6">
                <h4>Mes Clubs de Lecture</h4>
            </div>
            <div class="col-6 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createClubForm">
                    Ajouter un Club
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Formulaire de création --}}
        <div class="row mb-4 collapse" id="createClubForm">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Créer un Nouveau Club</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('club_manager.clubs.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom du Club <span class="text-danger">*</span></label>
                                    <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
                                    @error('nom')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="createur_id" class="form-label">Créateur <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                    <input type="hidden" name="createur_id" value="{{ Auth::id() }}">
                                    <small class="text-muted">Le club sera automatiquement associé à votre compte</small>
                                    @error('createur_id')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Créer le Club</button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#createClubForm">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulaire d'édition inline --}}
        @if(isset($editClub))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Modifier le Club: {{ $editClub->nom }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('club_manager.clubs.update', $editClub->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom du Club</label>
                                    <input type="text" name="nom" class="form-control" value="{{ old('nom', $editClub->nom) }}" required>
                                    @error('nom')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="createur_id" class="form-label">Créateur</label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                    <input type="hidden" name="createur_id" value="{{ Auth::id() }}">
                                    <small class="text-muted">Le club reste associé à votre compte</small>
                                    @error('createur_id')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required>{{ old('description', $editClub->description) }}</textarea>
                                    @error('description')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Modifier le Club</button>
                            <a href="{{ route('club_manager.clubs.index') }}" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tableau des clubs --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Liste des Clubs de Lecture</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Créateur</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Événements</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clubs as $club)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $club->nom }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ Str::limit($club->description, 50) }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">{{ $club->createur->name ?? 'N/A' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">
                                                {{ $club->evenements_count ?? $club->evenements->count() }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('club_manager.clubs.index', ['edit' => $club->id]) }}"
                                                   class="btn btn-outline-primary btn-sm mx-1"
                                                   data-bs-toggle="tooltip" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('club_manager.events.index') }}?club_id={{ $club->id }}"
                                                   class="btn btn-outline-info btn-sm mx-1"
                                                   data-bs-toggle="tooltip" title="Voir les Événements">
                                                    <i class="fas fa-calendar"></i>
                                                </a>
                                                <form action="{{ route('club_manager.clubs.destroy', $club->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm mx-1"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce club?')"
                                                            data-bs-toggle="tooltip" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-3">Aucun club trouvé.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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

    // Ouvrir automatiquement le formulaire de création s'il y a des erreurs de validation
    @if($errors->any() && !isset($editClub))
        document.addEventListener('DOMContentLoaded', function() {
            var createForm = new bootstrap.Collapse(document.getElementById('createClubForm'));
            createForm.show();
        });
    @endif
</script>
@endpush