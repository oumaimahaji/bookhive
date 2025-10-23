@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12 text-end">
                @if(Auth::user()->role !== 'admin')
                <a href="{{ route('reservations.create') }}" class="btn btn-primary">Nouvelle Réservation</a>
                @endif
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

        {{-- Filtres et Tri --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Filtres et Tri</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('reservations.index') }}">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="statut" class="form-label">Statut</label>
                                    <select name="statut" class="form-control">
                                        <option value="">Tous</option>
                                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                        <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                                        <option value="retourne" {{ request('statut') == 'retourne' ? 'selected' : '' }}>Retourné</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="date_from" class="form-label">Date de début</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="date_to" class="form-label">Date de fin</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="sort" class="form-label">Trier par</label>
                                    <select name="sort" class="form-control">
                                        <option value="">Par défaut</option>
                                        <option value="date_reservation_asc" {{ request('sort') == 'date_reservation_asc' ? 'selected' : '' }}>Date de réservation (croissant)</option>
                                        <option value="date_reservation_desc" {{ request('sort') == 'date_reservation_desc' ? 'selected' : '' }}>Date de réservation (décroissant)</option>
                                        <option value="date_retour_prev_asc" {{ request('sort') == 'date_retour_prev_asc' ? 'selected' : '' }}>Date de retour (croissant)</option>
                                        <option value="date_retour_prev_desc" {{ request('sort') == 'date_retour_prev_desc' ? 'selected' : '' }}>Date de retour (décroissant)</option>
                                        <option value="date_retour_effectif_asc" {{ request('sort') == 'date_retour_effectif_asc' ? 'selected' : '' }}>Date de retour effectif (croissant)</option>
                                        <option value="date_retour_effectif_desc" {{ request('sort') == 'date_retour_effectif_desc' ? 'selected' : '' }}>Date de retour effectif (décroissant)</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Appliquer</button>
                            <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Réinitialiser</a>
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

        {{-- Chatbot Assistant --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Assistant BookHive 🤖</h6>
                                <p class="text-sm text-secondary mb-0">Posez vos questions sur les livres et réservations</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="chatbotForm" class="mb-3">
                            @csrf
                            <div class="input-group">
                                <input type="text" id="chatbotMessage" name="message" class="form-control"
                                       placeholder="Ex: Quels livres avez-vous ? Comment réserver ?"
                                       required maxlength="500" style="border-radius: 20px 0 0 20px;">
                                <button class="btn btn-primary" type="submit" style="border-radius: 0 20px 20px 0;">
                                    <i class="fas fa-paper-plane"></i> Envoyer
                                </button>
                            </div>
                        </form>

                        <div id="chatbotResponse" class="alert alert-light" style="display: none; border-radius: 15px; min-height: 60px; align-items: center;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-robot text-primary me-2"></i>
                                <span id="responseText">Bonjour ! Je suis votre assistant BookHive. Posez-moi une question sur les livres, réservations ou notre bibliothèque !</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const editForm = document.querySelector('form[action*="reservations.update"]');
                if (editForm) {
                    const reservationDate = editForm.querySelector('input[name="date_reservation"]');
                    const returnDate = editForm.querySelector('input[name="date_retour_prev"]');
                    const returnDateEffectif = editForm.querySelector('input[name="date_retour_effectif"]');

                    function validateDates() {
                        const resDate = new Date(reservationDate.value);
                        const retDate = new Date(returnDate.value);
                        const retDateEffectif = new Date(returnDateEffectif.value);
                        if (resDate >= retDate) {
                            alert('La date de retour prévue doit être après la date de réservation.');
                            return false;
                        }
                        if (retDateEffectif && retDateEffectif < retDate) {
                            alert('La date de retour effectif doit être après la date de retour prévue.');
                            return false;
                        }
                        return true;
                    }

                    editForm.addEventListener('submit', function(e) {
                        if (!validateDates()) {
                            e.preventDefault();
                        }
                    });
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterForm = document.querySelector('form[action="{{ route('reservations.index') }}"]');
                const tableBody = document.querySelector('tbody');
                const rows = Array.from(tableBody.querySelectorAll('tr'));

                function applyFilters() {
                    const statut = filterForm.querySelector('select[name="statut"]').value;
                    const dateFrom = filterForm.querySelector('input[name="date_from"]').value;
                    const dateTo = filterForm.querySelector('input[name="date_to"]').value;
                    const sort = filterForm.querySelector('select[name="sort"]').value;

                    // Filter rows
                    const filteredRows = rows.filter(row => {
                        if (row.querySelector('td[colspan]')) return false; // Skip "no results" row

                        const statutCell = row.querySelector('td:nth-child(5) span');
                        const dateCell = row.querySelector('td:nth-child(3)');

                        // Status filter (case-insensitive)
                        if (statut && statutCell) {
                            const cellText = statutCell.textContent.toLowerCase();
                            if (!cellText.includes(statut.toLowerCase())) {
                                return false;
                            }
                        }

                        // Date filters (assuming date format is dd/mm/yyyy)
                        if (dateFrom) {
                            const resDate = new Date(dateCell.textContent.split('/').reverse().join('-'));
                            const fromDate = new Date(dateFrom);
                            if (resDate < fromDate) return false;
                        }
                        if (dateTo) {
                            const resDate = new Date(dateCell.textContent.split('/').reverse().join('-'));
                            const toDate = new Date(dateTo);
                            if (resDate > toDate) return false;
                        }

                        return true;
                    });

                    // Sort rows
                    filteredRows.sort((a, b) => {
                        if (sort === 'date_reservation_asc') {
                            const aDate = new Date(a.querySelector('td:nth-child(3)').textContent.split('/').reverse().join('-'));
                            const bDate = new Date(b.querySelector('td:nth-child(3)').textContent.split('/').reverse().join('-'));
                            return aDate - bDate;
                        } else if (sort === 'date_reservation_desc') {
                            const aDate = new Date(a.querySelector('td:nth-child(3)').textContent.split('/').reverse().join('-'));
                            const bDate = new Date(b.querySelector('td:nth-child(3)').textContent.split('/').reverse().join('-'));
                            return bDate - aDate;
                        } else if (sort === 'date_retour_prev_asc') {
                            const aDate = new Date(a.querySelector('td:nth-child(4)').textContent.split('/').reverse().join('-'));
                            const bDate = new Date(b.querySelector('td:nth-child(4)').textContent.split('/').reverse().join('-'));
                            return aDate - bDate;
                        } else if (sort === 'date_retour_prev_desc') {
                            const aDate = new Date(a.querySelector('td:nth-child(4)').textContent.split('/').reverse().join('-'));
                            const bDate = new Date(b.querySelector('td:nth-child(4)').textContent.split('/').reverse().join('-'));
                            return bDate - aDate;
                        }
                        return 0;
                    });

                    // Update table
                    tableBody.innerHTML = '';
                    if (filteredRows.length > 0) {
                        filteredRows.forEach(row => tableBody.appendChild(row));
                    } else {
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.innerHTML = '<td colspan="6" class="text-center p-3">Aucune réservation trouvée.</td>';
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

        {{-- Chatbot JavaScript --}}
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatbotForm = document.getElementById('chatbotForm');
            const chatbotResponse = document.getElementById('chatbotResponse');
            const responseText = document.getElementById('responseText');

            if (chatbotForm) {
                chatbotForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const messageInput = document.getElementById('chatbotMessage');
                    const message = messageInput.value.trim();

                    // Client-side validation
                    if (!message) {
                        responseText.textContent = "Veuillez saisir un message.";
                        chatbotResponse.style.display = 'flex';
                        chatbotResponse.className = 'alert alert-warning';
                        messageInput.focus();
                        return;
                    }

                    if (message.length > 500) {
                        responseText.textContent = "Le message ne peut pas dépasser 500 caractères.";
                        chatbotResponse.style.display = 'flex';
                        chatbotResponse.className = 'alert alert-warning';
                        return;
                    }

                    // Show loading state
                    responseText.textContent = "Envoi en cours...";
                    chatbotResponse.style.display = 'flex';
                    chatbotResponse.className = 'alert alert-info';

                    const formData = new FormData(e.target);

                    try {
                        const res = await fetch("/chatbot", {
                            method: "POST",
                            body: formData,
                            headers: {
                                "Accept": "application/json"
                            }
                        });

                        if (!res.ok) {
                            let errorMessage = `Erreur HTTP ${res.status}`;
                            if (res.status === 422) {
                                errorMessage = "Message invalide. Vérifiez que votre message n'est pas vide et ne dépasse pas 500 caractères.";
                            }
                            throw new Error(errorMessage);
                        }

                        const data = await res.json();

                        // Show success response
                        responseText.textContent = data.response;
                        chatbotResponse.className = 'alert alert-light';

                    } catch (error) {
                        console.error('Chatbot error:', error);
                        responseText.textContent = "Désolé, une erreur est survenue. Veuillez réessayer.";
                        chatbotResponse.className = 'alert alert-danger';
                    }

                    // Clear input
                    messageInput.value = "";
                });
            }

            // Show initial welcome message
            if (chatbotResponse && responseText) {
                responseText.textContent = "Bonjour ! Je suis votre assistant BookHive. Posez-moi une question sur les livres, réservations ou notre bibliothèque !";
                chatbotResponse.style.display = 'flex';
                chatbotResponse.className = 'alert alert-light';
            }
        });
        </script>
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
                                            <div class="d-flex flex-column gap-1">
                                                <a href="{{ route('reservations.index', ['edit' => $reservation->id]) }}"
                                                   class="text-secondary font-weight-bold text-xs">Modifier</a>

                                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'moderator')
                                                    @if($reservation->statut === 'en_attente')
                                                        <form action="{{ route('reservations.update', $reservation->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="statut" value="confirmee">
                                                            <button type="submit" class="text-success font-weight-bold text-xs border-0 bg-transparent">
                                                                <i class="fas fa-check me-1"></i>Confirmer
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($reservation->statut === 'confirmee')
                                                        <form action="{{ route('reservations.markReturned', $reservation->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="text-warning font-weight-bold text-xs border-0 bg-transparent">
                                                                <i class="fas fa-undo me-1"></i>Marquer retourné
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                @if($reservation->statut === 'confirmee' && $reservation->book->pdf_path && $reservation->user_id === Auth::id())
                                                    <a href="{{ route('books.download', $reservation->book->id) }}"
                                                       class="text-primary font-weight-bold text-xs" target="_blank">
                                                        <i class="fas fa-download me-1"></i>Télécharger PDF
                                                    </a>
                                                @endif

                                                @if(Auth::user()->role === 'admin')
                                                    <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="text-danger font-weight-bold text-xs border-0 bg-transparent" onclick="return confirm('Êtes-vous sûr?')">
                                                            <i class="fas fa-trash me-1"></i>Supprimer
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
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