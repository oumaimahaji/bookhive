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
                <a href="{{ route('club_manager.events.create') }}" class="btn btn-primary">Ajouter un Événement</a>
                @if(isset($club))
                    <a href="{{ route('club_manager.clubs.index') }}" class="btn btn-secondary">Retour aux Clubs</a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="club_id" class="form-label">Club</label>
                                    <select name="club_id" class="form-control" required>
                                        @foreach($clubs as $clubItem)
                                            <option value="{{ $clubItem->id }}" {{ $editEvent->club_id == $clubItem->id ? 'selected' : '' }}>
                                                {{ $clubItem->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4">{{ old('description', $editEvent->description) }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_event" class="form-label">Date de l'événement</label>
                                    <input type="date" name="date_event" class="form-control" value="{{ old('date_event', $editEvent->date_event) }}" required>
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
                    <div class="card-header pb-0">
                        <h6>Liste des Événements</h6>
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
                                        <th class="text-secondary opacity-7">Actions</th>
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
                                                {{ \Carbon\Carbon::parse($event->date_event)->format('d/m/Y') }}
                                            </p>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('club_manager.events.index', ['edit' => $event->id]) }}"
                                               class="text-secondary font-weight-bold text-xs me-2">Modifier</a>
                                            <form action="{{ route('club_manager.events.destroy', $event->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-danger font-weight-bold text-xs border-0 bg-transparent" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                                            </form>
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