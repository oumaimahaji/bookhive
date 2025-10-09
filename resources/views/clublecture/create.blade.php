@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Créer un Club de Lecture</h5>
                    <a href="{{ route('club_manager.clubs.index') }}" class="btn bg-gradient-primary btn-sm mb-0">Retour à la liste</a>
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

                    <form action="{{ route('club_manager.clubs.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nom du Club</label>
                            <input type="text" name="nom" value="{{ old('nom') }}" class="form-control" placeholder="Entrez le nom du club" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Entrez la description du club" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Créateur</label>
                            <select name="createur_id" class="form-control" required>
                                <option value="">-- Sélectionnez un créateur --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('createur_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary">Créer le Club</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection