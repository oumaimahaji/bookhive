@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

        <div class="row mb-3">
            <div class="col-12 text-start">
                <a href="{{ route('club_manager.clubs.index') }}" class="btn btn-secondary">Retour à la liste</a>
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
                        <h6>Modifier le Club: {{ $club->nom }}</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-3">
                        <form action="{{ route('club_manager.clubs.update', $club->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Nom du Club</label>
                                <input type="text" name="nom" class="form-control" value="{{ old('nom', $club->nom) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required>{{ old('description', $club->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Créateur</label>
                                <select name="createur_id" class="form-control" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (old('createur_id', $club->createur_id) == $user->id) ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Modifier le Club</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection