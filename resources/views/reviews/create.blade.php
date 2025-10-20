@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Créer un Avis</h5>
                    <a href="{{ route('reviews.index') }}" class="btn bg-gradient-primary btn-sm mb-0">Retour à la liste</a>
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

                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Utilisateur</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }} ({{ Auth::user()->email }})" readonly>
                            <small class="text-muted">Votre avis sera lié à votre compte</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Livre</label>
                            <select name="book_id" class="form-control" required>
                                <option value="">-- Sélectionnez un livre --</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" {{ (request('book_id') == $book->id || old('book_id') == $book->id) ? 'selected' : '' }}>
                                        {{ $book->titre }} - {{ $book->auteur }}
                                    </option>
                                @endforeach
                            </select>
                            @if($books->isEmpty())
                                <small class="text-danger">Vous devez avoir réservé et retourné un livre pour pouvoir laisser un avis.</small>
                            @else
                                <small class="text-muted">Seuls les livres que vous avez réservés et retournés sont disponibles</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <select name="note" class="form-control" required>
                                <option value="">-- Sélectionnez une note --</option>
                                <option value="1" {{ old('note') == '1' ? 'selected' : '' }}>⭐ (1) - Très mauvais</option>
                                <option value="2" {{ old('note') == '2' ? 'selected' : '' }}>⭐⭐ (2) - Mauvais</option>
                                <option value="3" {{ old('note') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3) - Moyen</option>
                                <option value="4" {{ old('note') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4) - Bon</option>
                                <option value="5" {{ old('note') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5) - Excellent</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Commentaire</label>
                            <textarea name="commentaire" class="form-control" rows="4" placeholder="Partagez votre expérience avec ce livre..." required>{{ old('commentaire') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control" required>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary" {{ $books->isEmpty() ? 'disabled' : '' }}>Créer l'Avis</button>
                        
                        @if($books->isEmpty())
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Vous ne pouvez pas créer d'avis car vous n'avez aucun livre réservé et retourné.
                                <a href="{{ route('reservations.index') }}" class="alert-link">Voir mes réservations</a>
                            </div>
                        @endif
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.querySelector('form[action="{{ route('reviews.store') }}"]');
                            const bookSelect = form.querySelector('select[name="book_id"]');
                            const noteSelect = form.querySelector('select[name="note"]');
                            const commentaireTextarea = form.querySelector('textarea[name="commentaire"]');

                            function validateForm() {
                                if (!bookSelect.value) {
                                    alert('Veuillez sélectionner un livre.');
                                    return false;
                                }
                                if (!noteSelect.value) {
                                    alert('Veuillez sélectionner une note.');
                                    return false;
                                }
                                if (!commentaireTextarea.value.trim()) {
                                    alert('Veuillez entrer un commentaire.');
                                    return false;
                                }
                                return true;
                            }

                            form.addEventListener('submit', function(e) {
                                if (!validateForm()) {
                                    e.preventDefault();
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection