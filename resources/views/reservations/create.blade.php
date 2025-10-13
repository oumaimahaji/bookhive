@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Créer une Réservation</h5>
                    <a href="{{ route('reservations.index') }}" class="btn bg-gradient-primary btn-sm mb-0">Retour à la liste</a>
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

                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf
                        
                        @if(Auth::user()->role === 'admin')
                        <div class="mb-3">
                            <label class="form-label">Utilisateur</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Sélectionnez --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="mb-3">
                            <label class="form-label">Utilisateur</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }} ({{ Auth::user()->email }})" readonly>
                            <small class="text-muted">Votre réservation sera liée à votre compte</small>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Livre</label>
                            <select name="book_id" class="form-control" required>
                                <option value="">-- Sélectionnez --</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ $book->titre }} - {{ $book->auteur }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de Réservation</label>
                            <input type="date" name="date_reservation" value="{{ old('date_reservation', date('Y-m-d')) }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de Retour Prévue</label>
                            <input type="date" name="date_retour_prev" value="{{ old('date_retour_prev', date('Y-m-d', strtotime('+7 days'))) }}" class="form-control" required>
                            <small class="text-muted">La réservation sera créée avec le statut "En attente"</small>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary">Créer la Réservation</button>
                    </form>

<<<<<<< HEAD
=======
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.querySelector('form[action="{{ route('reservations.store') }}"]');
                            const reservationDate = form.querySelector('input[name="date_reservation"]');
                            const returnDate = form.querySelector('input[name="date_retour_prev"]');
                            const submitButton = form.querySelector('button[type="submit"]');

                            function validateDates() {
                                const resDate = new Date(reservationDate.value);
                                const retDate = new Date(returnDate.value);
                                if (resDate >= retDate) {
                                    alert('La date de retour prévue doit être après la date de réservation.');
                                    return false;
                                }
                                return true;
                            }

                            form.addEventListener('submit', function(e) {
                                if (!validateDates()) {
                                    e.preventDefault();
                                }
                            });
                        });
                    </script>

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
                </div>
            </div>
        </div>
    </div>
</div>

@endsection