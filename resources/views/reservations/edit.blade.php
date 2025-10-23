@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

        <div class="row mb-3">
            <div class="col-12 text-start">
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back to list</a>
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
                        <h6>Edit Reservation #{{ $reservation->id }}</h6>
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
                                <small class="text-muted">Leave empty if the book is not yet returned</small>
                            </div>

                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'moderator')
                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <select name="statut" class="form-control" required>
                                    <option value="en_attente" {{ (old('statut', $reservation->statut) == 'en_attente') ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmee" {{ (old('statut', $reservation->statut) == 'confirmee') ? 'selected' : '' }}>Confirmed</option>
                                    <option value="retourne" {{ (old('statut', $reservation->statut) == 'retourne') ? 'selected' : '' }}>Returned</option>
                                </select>
                            </div>
                            @else
                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <input type="text" class="form-control" value="{{ $reservation->statut }}" readonly>
                                <small class="text-muted">Only the administrator or moderator can modify the status</small>
                            </div>
                            @endif

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Edit Reservation</button>
                            </div>
                        </form>

                        @if($reservation->statut === 'confirmee' && $reservation->user_id === Auth::id())
                        <!-- Section pour ajouter un avis -->
                        <div class="card border-success mb-4 mt-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-star me-2"></i>Leave a review</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Your reservation has been confirmed! You can now leave a review for this book.
                                </div>

                                @php
                                    $existingReview = \App\Models\Review::where('user_id', Auth::id())->where('book_id', $reservation->book_id)->first();
                                @endphp

                                @if($existingReview)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        You have already left a review for this book.
                                    </div>
                                @else
                                    <form action="{{ route('reviews.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $reservation->book_id }}">

                                        <div class="mb-3">
                                            <label class="form-label">Note globale <span class="text-danger">*</span></label>
                                            <select name="note" class="form-control" required>
                                                <option value="">Choose your rating</option>
                                                <option value="1">⭐ - Very bad</option>
                                                <option value="2">⭐⭐ - Bad</option>
                                                <option value="3">⭐⭐⭐ - Average</option>
                                                <option value="4">⭐⭐⭐⭐ - Good</option>
                                                <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Votre commentaire</label>
                                            <textarea name="commentaire" class="form-control" rows="5" placeholder="Share your reading experience..."></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-paper-plane me-2"></i>Publish the review
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection