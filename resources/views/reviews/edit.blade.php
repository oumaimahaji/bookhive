@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

        <div class="row mb-3">
            <div class="col-12 text-start">
                <a href="{{ route('reviews.index') }}" class="btn btn-secondary">Retour à la liste</a>
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
                        <h6>Modifier l'Avis</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-3">
                        <form action="{{ route('reviews.update', $review->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Utilisateur</label>
                                <input type="text" class="form-control" value="{{ $review->user->name }} ({{ $review->user->email }})" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Livre</label>
                                <select name="book_id" class="form-control" required>
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}" {{ (old('book_id', $review->book_id) == $book->id) ? 'selected' : '' }}>
                                            {{ $book->titre }} - {{ $book->auteur }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(Auth::user()->role === 'user')
                                    <small class="text-muted">Seuls les livres que vous avez réservés et retournés sont disponibles</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <select name="note" class="form-control" required>
                                    <option value="1" {{ (old('note', $review->note) == '1') ? 'selected' : '' }}>⭐ (1) - Très mauvais</option>
                                    <option value="2" {{ (old('note', $review->note) == '2') ? 'selected' : '' }}>⭐⭐ (2) - Mauvais</option>
                                    <option value="3" {{ (old('note', $review->note) == '3') ? 'selected' : '' }}>⭐⭐⭐ (3) - Moyen</option>
                                    <option value="4" {{ (old('note', $review->note) == '4') ? 'selected' : '' }}>⭐⭐⭐⭐ (4) - Bon</option>
                                    <option value="5" {{ (old('note', $review->note) == '5') ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5) - Excellent</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Commentaire</label>
                                <textarea name="commentaire" class="form-control" rows="4" required>{{ old('commentaire', $review->commentaire) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" value="{{ old('date', $review->date) }}" required>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Modifier l'Avis</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection