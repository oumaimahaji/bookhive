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

<<<<<<< HEAD
=======
        {{-- Filtres et Tri --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Filtres et Tri</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('reviews.index') }}">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="note" class="form-label">Note minimale</label>
                                    <select name="note" class="form-control">
                                        <option value="">Toutes</option>
                                        <option value="4" {{ request('note') == '4' ? 'selected' : '' }}>4 étoiles et plus</option>
                                        <option value="5" {{ request('note') == '5' ? 'selected' : '' }}>5 étoiles seulement</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="book_id" class="form-label">Livre spécifique</label>
                                    <select name="book_id" class="form-control">
                                        <option value="">Tous les livres</option>
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}" {{ request('book_id') == $book->id ? 'selected' : '' }}>
                                                {{ $book->titre }} - {{ $book->auteur }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="date_from" class="form-label">Date de début</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="sort" class="form-label">Trier par</label>
                                    <select name="sort" class="form-control">
                                        <option value="">Par défaut</option>
                                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (croissant)</option>
                                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (décroissant)</option>
                                        <option value="note_asc" {{ request('sort') == 'note_asc' ? 'selected' : '' }}>Note (croissant)</option>
                                        <option value="note_desc" {{ request('sort') == 'note_desc' ? 'selected' : '' }}>Note (décroissant)</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Appliquer</button>
                            <a href="{{ route('reviews.index') }}" class="btn btn-secondary">Réinitialiser</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Statistiques</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $reservationsPerBook = \App\Models\Reservation::selectRaw('book_id, COUNT(*) as count')->groupBy('book_id')->with('book')->get();
                                $averageRatingPerBook = \App\Models\Review::selectRaw('book_id, AVG(note) as avg_note')->groupBy('book_id')->with('book')->get();
                                $reviewsPerUser = \App\Models\Review::selectRaw('user_id, COUNT(*) as count')->groupBy('user_id')->with('user')->get();
                            @endphp
                            <div class="col-md-4 mb-3">
                                <div class="card bg-gradient-primary shadow-primary border-radius-lg h-100">
                                    <div class="card-body p-3">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="numbers">
                                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Réservations par Livre</p>
                                                    <h5 class="font-weight-bolder mb-0">
                                                        {{ $reservationsPerBook->count() }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-4 text-end">
                                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                    <i class="fas fa-book text-primary text-lg opacity-10"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @foreach($reservationsPerBook->take(3) as $stat)
                                                <small class="text-white">{{ $stat->book->titre ?? 'N/A' }}: {{ $stat->count }}</small><br>
                                            @endforeach
                                            @if($reservationsPerBook->count() > 3)
                                                <small class="text-white">...</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-gradient-success shadow-success border-radius-lg h-100">
                                    <div class="card-body p-3">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="numbers">
                                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Note Moyenne par Livre</p>
                                                    <h5 class="font-weight-bolder mb-0">
                                                        {{ $averageRatingPerBook->avg('avg_note') ? number_format($averageRatingPerBook->avg('avg_note'), 1) : 'N/A' }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-4 text-end">
                                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                    <i class="fas fa-star text-success text-lg opacity-10"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @foreach($averageRatingPerBook->take(3) as $stat)
                                                <small class="text-white">{{ $stat->book->titre ?? 'N/A' }}: {{ number_format($stat->avg_note, 1) }}</small><br>
                                            @endforeach
                                            @if($averageRatingPerBook->count() > 3)
                                                <small class="text-white">...</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-gradient-info shadow-info border-radius-lg h-100">
                                    <div class="card-body p-3">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="numbers">
                                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Avis par Utilisateur</p>
                                                    <h5 class="font-weight-bolder mb-0">
                                                        {{ $reviewsPerUser->count() }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-4 text-end">
                                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                    <i class="fas fa-users text-info text-lg opacity-10"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @foreach($reviewsPerUser->take(3) as $stat)
                                                <small class="text-white">{{ $stat->user->name ?? 'N/A' }}: {{ $stat->count }}</small><br>
                                            @endforeach
                                            @if($reviewsPerUser->count() > 3)
                                                <small class="text-white">...</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
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
<<<<<<< HEAD
=======

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form[action="{{ route('reviews.index') }}"]');
        const tableBody = document.querySelector('tbody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        function applyFilters() {
            const note = filterForm.querySelector('select[name="note"]').value;
            const bookId = filterForm.querySelector('select[name="book_id"]').value;
            const dateFrom = filterForm.querySelector('input[name="date_from"]').value;
            const sort = filterForm.querySelector('select[name="sort"]').value;

            // Filter rows
            const filteredRows = rows.filter(row => {
                if (row.querySelector('td[colspan]')) return false; // Skip "no results" row

                const noteCell = row.querySelector('td:nth-child(3) span');
                const bookCell = row.querySelector('td:nth-child(2)');
                const dateCell = row.querySelector('td:nth-child(5)');

                // Note filter
                if (note) {
                    const rowNote = noteCell.textContent.match(/\((\d+)\//);
                    if (rowNote) {
                        const numNote = parseInt(rowNote[1]);
                        if (note === '4' && numNote < 4) return false;
                        if (note === '5' && numNote !== 5) return false;
                    }
                }

                // Book filter
                if (bookId && bookCell && !bookCell.textContent.includes(bookId)) {
                    return false;
                }

                // Date filter (assuming date format is dd/mm/yyyy)
                if (dateFrom) {
                    const reviewDate = new Date(dateCell.textContent.split('/').reverse().join('-'));
                    const fromDate = new Date(dateFrom);
                    if (reviewDate < fromDate) return false;
                }

                return true;
            });

            // Sort rows
            filteredRows.sort((a, b) => {
                if (sort === 'date_asc') {
                    const aDate = new Date(a.querySelector('td:nth-child(5)').textContent.split('/').reverse().join('-'));
                    const bDate = new Date(b.querySelector('td:nth-child(5)').textContent.split('/').reverse().join('-'));
                    return aDate - bDate;
                } else if (sort === 'date_desc') {
                    const aDate = new Date(a.querySelector('td:nth-child(5)').textContent.split('/').reverse().join('-'));
                    const bDate = new Date(b.querySelector('td:nth-child(5)').textContent.split('/').reverse().join('-'));
                    return bDate - aDate;
                } else if (sort === 'note_asc') {
                    const aNote = parseInt(a.querySelector('td:nth-child(3) span').textContent.match(/\((\d+)\//)[1]);
                    const bNote = parseInt(b.querySelector('td:nth-child(3) span').textContent.match(/\((\d+)\//)[1]);
                    return aNote - bNote;
                } else if (sort === 'note_desc') {
                    const aNote = parseInt(a.querySelector('td:nth-child(3) span').textContent.match(/\((\d+)\//)[1]);
                    const bNote = parseInt(b.querySelector('td:nth-child(3) span').textContent.match(/\((\d+)\//)[1]);
                    return bNote - aNote;
                }
                return 0;
            });

            // Update table
            tableBody.innerHTML = '';
            if (filteredRows.length > 0) {
                filteredRows.forEach(row => tableBody.appendChild(row));
            } else {
                const noResultsRow = document.createElement('tr');
                noResultsRow.innerHTML = '<td colspan="6" class="text-center p-3">Aucun avis trouvé.</td>';
                tableBody.appendChild(noResultsRow);
            }
        }

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });

        // Initial load
        applyFilters();
    });
</script>

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
@endsection