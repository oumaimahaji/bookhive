@extends('layouts.user_type.auth')

@section('content')
<<<<<<< HEAD
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

        <div class="row mb-3">
            <div class="col-12 text-start">
                <a href="{{ route('club_manager.events.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Modifier l'Événement: {{ $event->titre }}</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-3">
                        <form action="{{ route('club_manager.events.update', $event->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Titre</label>
                                <input type="text" name="titre" class="form-control" value="{{ old('titre', $event->titre) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $event->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Club</label>
                                <select name="club_id" class="form-control" required>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}" {{ (old('club_id', $event->club_id) == $club->id) ? 'selected' : '' }}>
                                            {{ $club->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de l'événement</label>
                                <input type="date" name="date_event" class="form-control" value="{{ old('date_event', $event->date_event->format('Y-m-d')) }}" required>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Modifier l'Événement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
=======

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'Événement</h5>
                    <a href="{{ route('club_manager.events.index') }}" class="btn bg-gradient-primary btn-sm mb-0">Retour à la liste</a>
                </div>
                <div class="card-body px-4 pt-4 pb-2">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('club_manager.events.update', $event->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" name="titre" value="{{ old('titre', $event->titre) }}" class="form-control" placeholder="Entrez le titre de l'événement" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Entrez la description de l'événement">{{ old('description', $event->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Club</label>
                            <select name="club_id" class="form-control" required>
                                <option value="">-- Sélectionnez un club --</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" {{ old('club_id', $event->club_id) == $club->id ? 'selected' : '' }}>
                                        {{ $club->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de l'événement</label>
                            <input type="date" name="date_event" value="{{ old('date_event', $event->date_event->format('Y-m-d')) }}" class="form-control" required>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary">Modifier l'Événement</button>
                        <a href="{{ route('club_manager.events.index') }}" class="btn bg-gradient-secondary">Annuler</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
@endsection