@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

        <div class="row mb-3">
            <div class="col-12 text-start">
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Retour à la liste</a>
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
                        <h6>Modifier la Réservation #{{ $reservation->id }}</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-3">
                        <form action="{{ route('reservations.update', $reservation->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @if(Auth::user()->role === 'admin')
                            <div class="mb-3">
                                <label class="form-label">Utilisateur</label>
                                <select name="user_id" class="form-control" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (old('user_id', $reservation->user_id) == $user->id) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                            <div class="mb-3">
                                <label class="form-label">Utilisateur</label>
                                <input type="text" class="form-control" value="{{ $reservation->user->name }} ({{ $reservation->user->email }})" readonly>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Livre</label>
                                <select name="book_id" class="form-control" required>
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}" {{ (old('book_id', $reservation->book_id) == $book->id) ? 'selected' : '' }}>
                                            {{ $book->titre }} - {{ $book->auteur }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de Réservation</label>
                                <input type="date" name="date_reservation" class="form-control" value="{{ old('date_reservation', $reservation->date_reservation) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de Retour Prévue</label>
                                <input type="date" name="date_retour_prev" class="form-control" value="{{ old('date_retour_prev', $reservation->date_retour_prev) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de Retour Effectif</label>
                                <input type="date" name="date_retour_effectif" class="form-control" value="{{ old('date_retour_effectif', $reservation->date_retour_effectif) }}">
                                <small class="text-muted">Laissez vide si le livre n'est pas encore retourné</small>
                            </div>

                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'moderator')
                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <select name="statut" class="form-control" required>
                                    <option value="en_attente" {{ (old('statut', $reservation->statut) == 'en_attente') ? 'selected' : '' }}>En attente</option>
                                    <option value="confirmee" {{ (old('statut', $reservation->statut) == 'confirmee') ? 'selected' : '' }}>Confirmée</option>
                                    <option value="retourne" {{ (old('statut', $reservation->statut) == 'retourne') ? 'selected' : '' }}>Retourné</option>
                                </select>
                            </div>
                            @else
                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <input type="text" class="form-control" value="{{ $reservation->statut }}" readonly>
                                <small class="text-muted">Seul l'administrateur ou le modérateur peut modifier le statut</small>
                            </div>
                            @endif

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Modifier la Réservation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection