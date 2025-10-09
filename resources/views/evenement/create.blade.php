@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Créer un Événement</h5>
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

                    <form action="{{ route('club_manager.events.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" name="titre" value="{{ old('titre') }}" class="form-control" placeholder="Entrez le titre de l'événement" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Entrez la description de l'événement">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Club</label>
                            <select name="club_id" class="form-control" required>
                                <option value="">-- Sélectionnez un club --</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                        {{ $club->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de l'événement</label>
                            <input type="date" name="date_event" value="{{ old('date_event') }}" class="form-control" required>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary">Créer l'Événement</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection