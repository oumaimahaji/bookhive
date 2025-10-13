@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-6">
                @if(isset($club))
                    <h4>Événements du Club: {{ $club->nom }}</h4>
                @else
                    <h4>Tous les Événements</h4>
                @endif
            </div>
            <div class="col-6 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createEventForm">
                    Ajouter un Événement
                </button>
                @if(isset($club))
                    <a href="{{ route('club_manager.clubs.index') }}" class="btn btn-secondary">Retour aux Clubs</a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Formulaire de création --}}
        <div class="row mb-4 collapse" id="createEventForm">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Créer un Nouvel Événement</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('club_manager.events.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                                    <input type="text" name="titre" class="form-control" value="{{ old('titre') }}" required>
                                    @error('titre')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="club_id" class="form-label">Club <span class="text-danger">*</span></label>
                                    <select name="club_id" class="form-control" required>
                                        <option value="">-- Sélectionnez un club --</option>
                                        @foreach($clubs as $clubItem)
                                            <option value="{{ $clubItem->id }}" {{ old('club_id') == $clubItem->id ? 'selected' : '' }}>
                                                {{ $clubItem->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('club_id')
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
                                <div class="col-md-6 mb-3">
                                    <label for="date_event" class="form-label">Date de l'événement <span class="text-danger">*</span></label>
                                    <input type="date" name="date_event" class="form-control" value="{{ old('date_event') }}" required>
                                    @error('date_event')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Créer l'Événement</button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#createEventForm">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulaire d'édition inline --}}
        @if(isset($editEvent))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Modifier l'Événement: {{ $editEvent->titre }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('club_manager.events.update', $editEvent->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="titre" class="form-label">Titre</label>
                                    <input type="text" name="titre" class="form-control" value="{{ old('titre', $editEvent->titre) }}" required>
                                    @error('titre')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="club_id" class="form-label">Club</label>
                                    <select name="club_id" class="form-control" required>
                                        <option value="">-- Sélectionnez un club --</option>
                                        @foreach($clubs as $clubItem)
                                            <option value="{{ $clubItem->id }}" {{ (old('club_id', $editEvent->club_id) == $clubItem->id) ? 'selected' : '' }}>
                                                {{ $clubItem->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('club_id')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required>{{ old('description', $editEvent->description) }}</textarea>
                                    @error('description')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_event" class="form-label">Date de l'événement</label>
                                    <input type="date" name="date_event" class="form-control" value="{{ old('date_event', $editEvent->date_event ? \Carbon\Carbon::parse($editEvent->date_event)->format('Y-m-d') : '') }}" required>
                                    @error('date_event')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Modifier l'Événement</button>
                            <a href="{{ route('club_manager.events.index') }}" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tableau des événements --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Liste des Événements</h6>
                        @if($clubs->count() > 0)
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" onchange="if(this.value) window.location.href='{{ route('club_manager.events.index') }}?club_id=' + this.value">
                                <option value="">-- Tous les clubs --</option>
                                @foreach($clubs as $clubItem)
                                    <option value="{{ $clubItem->id }}" {{ isset($club) && $club->id == $clubItem->id ? 'selected' : '' }}>
                                        {{ $clubItem->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Titre</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Club</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($evenements as $event)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $event->titre }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ Str::limit($event->description, 50) }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">{{ $event->club->nom ?? 'N/A' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $event->date_event ? \Carbon\Carbon::parse($event->date_event)->format('d/m/Y') : 'N/A' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('club_manager.events.index', ['edit' => $event->id]) }}"
                                                   class="btn btn-outline-primary btn-sm mx-1"
                                                   data-bs-toggle="tooltip" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('club_manager.events.destroy', $event->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm mx-1"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement?')"
                                                            data-bs-toggle="tooltip" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-3">Aucun événement trouvé.</td>
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
    @if($errors->any() && !isset($editEvent))
        document.addEventListener('DOMContentLoaded', function() {
            var createForm = new bootstrap.Collapse(document.getElementById('createEventForm'));
            createForm.show();
        });
    @endif

    // Ouvrir automatiquement le formulaire d'édition si on arrive avec le paramètre edit
    @if(isset($editEvent))
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll vers le formulaire d'édition
            const editForm = document.querySelector('.row.mb-4');
            if (editForm) {
                editForm.scrollIntoView({ behavior: 'smooth' });
            }
        });
    @endif
</script>
@endpush