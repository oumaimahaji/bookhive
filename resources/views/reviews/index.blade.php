@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ route('reviews.create') }}" class="btn btn-primary">Nouvel Avis</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Formulaire d'édition inline --}}
        @if(isset($editReview))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Modifier l'Avis</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reviews.update', $editReview->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="book_id" class="form-label">Livre</label>
                                    <select name="book_id" class="form-control" required>
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}" {{ $editReview->book_id == $book->id ? 'selected' : '' }}>
                                                {{ $book->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="note" class="form-label">Note</label>
                                    <select name="note" class="form-control" required>
                                        <option value="1" {{ $editReview->note == 1 ? 'selected' : '' }}>⭐ (1)</option>
                                        <option value="2" {{ $editReview->note == 2 ? 'selected' : '' }}>⭐⭐ (2)</option>
                                        <option value="3" {{ $editReview->note == 3 ? 'selected' : '' }}>⭐⭐⭐ (3)</option>
                                        <option value="4" {{ $editReview->note == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (4)</option>
                                        <option value="5" {{ $editReview->note == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', $editReview->date) }}" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="commentaire" class="form-label">Commentaire</label>
                                    <textarea name="commentaire" class="form-control" rows="3">{{ old('commentaire', $editReview->commentaire) }}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Modifier</button>
                            <a href="{{ route('reviews.index') }}" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tableau des avis --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Liste des Avis</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilisateur</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Livre</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Note</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Commentaire</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                        <th class="text-secondary opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reviews as $review)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $review->user->name ?? 'N/A' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $review->user->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $review->book->titre ?? 'N/A' }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $review->book->auteur ?? '' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $review->note)
                                                        ⭐
                                                    @else
                                                        ☆
                                                    @endif
                                                @endfor
                                                ({{ $review->note }}/5)
                                            </span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($review->commentaire, 50) }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($review->date)->format('d/m/Y') }}
                                            </p>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('reviews.index', ['edit' => $review->id]) }}"
                                               class="text-secondary font-weight-bold text-xs me-2">Modifier</a>
                                            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-danger font-weight-bold text-xs border-0 bg-transparent" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-3">Aucun avis trouvé.</td>
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