@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ route('reservations.create') }}" class="btn btn-primary">Nouvelle Réservation</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Formulaire d'édition inline --}}
        @if(isset($editReservation))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Modifier la Réservation: {{ $editReservation->id }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reservations.update', $editReservation->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                @if(Auth::user()->role === 'admin')
                                <div class="col-md-4 mb-3">
                                    <label for="user_id" class="form-label">Utilisateur</label>
                                    <select name="user_id" class="form-control" required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $editReservation->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <div class="col-md-4 mb-3">
                                    <label for="user_id" class="form-label">Utilisateur</label>
                                    <input type="text" class="form-control" value="{{ $editReservation->user->name }}" readonly>
                                </div>
                                @endif
                                
                                <div class="col-md-4 mb-3">
                                    <label for="book_id" class="form-label">Livre</label>
                                    <select name="book_id" class="form-control" required>
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}" {{ $editReservation->book_id == $book->id ? 'selected' : '' }}>
                                                {{ $book->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'moderator')
                                <div class="col-md-4 mb-3">
                                    <label for="statut" class="form-label">Statut</label>
                                    <select name="statut" class="form-control" required>
                                        <option value="en_attente" {{ $editReservation->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                        <option value="confirmee" {{ $editReservation->statut == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                                        <option value="retourne" {{ $editReservation->statut == 'retourne' ? 'selected' : '' }}>Retourné</option>
                                    </select>
                                </div>
                                @else
                                <div class="col-md-4 mb-3">
                                    <label for="statut" class="form-label">Statut</label>
                                    <input type="text" class="form-control" value="{{ $editReservation->statut }}" readonly>
                                </div>
                                @endif
                                
                                <div class="col-md-3 mb-3">
                                    <label for="date_reservation" class="form-label">Date Réservation</label>
                                    <input type="date" name="date_reservation" class="form-control" value="{{ old('date_reservation', $editReservation->date_reservation) }}" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="date_retour_prev" class="form-label">Retour Prévu</label>
                                    <input type="date" name="date_retour_prev" class="form-control" value="{{ old('date_retour_prev', $editReservation->date_retour_prev) }}" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="date_retour_effectif" class="form-label">Retour Effectif</label>
                                    <input type="date" name="date_retour_effectif" class="form-control" value="{{ old('date_retour_effectif', $editReservation->date_retour_effectif) }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Modifier</button>
                            <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tableau des réservations --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Liste des Réservations</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilisateur</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Livre</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date Réservation</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Retour Prévu</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                        <th class="text-secondary opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reservations as $reservation)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $reservation->user->name ?? 'N/A' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $reservation->user->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $reservation->book->titre ?? 'N/A' }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $reservation->book->auteur ?? '' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($reservation->date_reservation)->format('d/m/Y') }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($reservation->date_retour_prev)->format('d/m/Y') }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @php
                                                $statusColors = [
                                                    'en_attente' => 'bg-gradient-warning',
                                                    'confirmee' => 'bg-gradient-info',
                                                    'retourne' => 'bg-gradient-success'
                                                ];
                                            @endphp
                                            <span class="badge badge-sm {{ $statusColors[$reservation->statut] ?? 'bg-gradient-secondary' }}">
                                                {{ $reservation->statut }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('reservations.index', ['edit' => $reservation->id]) }}"
                                               class="text-secondary font-weight-bold text-xs me-2">Modifier</a>
                                            @if(Auth::user()->role === 'admin')
                                            <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-danger font-weight-bold text-xs border-0 bg-transparent" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-3">Aucune réservation trouvée.</td>
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