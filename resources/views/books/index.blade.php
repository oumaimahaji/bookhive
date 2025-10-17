@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid" style="padding-top: 0;">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
            <span class="alert-text">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <span class="alert-text">{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(isset($editBook))
        {{-- Formulaire d'édition --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Modifier le livre : {{ $editBook->titre }}</h6>
                        @php
                        $cancelParams = request()->query();
                        unset($cancelParams['edit']);
                        @endphp
                        <a href="{{ route('books.index', $cancelParams) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Fermer
                        </a>
                    </div>
                    <div class="card-body">
                        {{-- Messages d'erreur --}}
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('books.update', $editBook->id) }}" method="POST" enctype="multipart/form-data" id="editBookForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                                    <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror"
                                        value="{{ old('titre', $editBook->titre) }}"
                                        required
                                        maxlength="255"
                                        placeholder="Entrez le titre du livre">
                                    @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="auteur" class="form-label">Auteur <span class="text-danger">*</span></label>
                                    <input type="text" name="auteur" class="form-control @error('auteur') is-invalid @enderror"
                                        value="{{ old('auteur', $editBook->auteur) }}"
                                        required
                                        maxlength="255"
                                        placeholder="Entrez le nom de l'auteur">
                                    @error('auteur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- Sélectionnez une catégorie --</option>
                                        @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $editBook->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nom }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <input type="text" name="type" class="form-control @error('type') is-invalid @enderror"
                                        value="{{ old('type', $editBook->type) }}"
                                        maxlength="100"
                                        placeholder="Entrez le type de livre">
                                    @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="3"
                                        maxlength="500"
                                        placeholder="Entrez la description du livre">{{ old('description', $editBook->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 500 caractères</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image de couverture</label>
                                    <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror"
                                        accept="image/*">
                                    <small class="form-text text-muted">Formats: JPG, PNG, WEBP - Max: 2MB</small>
                                    @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if($editBook->cover_image)
                                    @php
                                    $imageUrl = asset('storage/' . $editBook->cover_image);
                                    @endphp
                                    <div class="mt-2">
                                        <p class="mb-0 small">Couverture actuelle :</p>
                                        <img src="{{ $imageUrl }}"
                                            alt="Couverture actuelle"
                                            class="img-thumbnail mt-1"
                                            style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="alert alert-warning p-2 small mt-1 d-none">
                                            ⚠️ Image non chargée : {{ $editBook->cover_image }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Statut</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_valid" value="1" class="form-check-input" id="is_valid"
                                            {{ old('is_valid', $editBook->is_valid) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_valid">Valide</label>
                                    </div>
                                </div>
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Mettre à jour le fichier PDF (admin uniquement)</label>
                                    <input type="file" name="pdf" class="form-control @error('pdf') is-invalid @enderror"
                                        accept="application/pdf">
                                    <small class="form-text text-muted">Taille maximale : 10MB. Format accepté : PDF uniquement.</small>
                                    @error('pdf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if($editBook->pdf_path)
                                    <div class="mt-2">
                                        <p class="mb-0 small">PDF actuel :</p>
                                        <a href="{{ route('books.download', $editBook->id) }}" class="btn btn-outline-primary btn-xs" target="_blank">
                                            <i class="fas fa-download me-1"></i>Télécharger PDF
                                        </a>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                                    <i class="fas fa-save me-1"></i>Mettre à jour
                                </button>
                                @php
                                $cancelParams = request()->query();
                                unset($cancelParams['edit']);
                                @endphp
                                <a href="{{ route('books.index', $cancelParams) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @else

        {{-- Liste des livres avec recherche et tri --}}
        <div class="row mb-4 mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Gestion des Livres</h6>
                    <a href="{{ route('books.create') }}" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                        <i class="fas fa-plus me-1"></i>Nouveau Livre
                    </a>
                </div>
            </div>
        </div>

        {{-- Formulaire Recherche Simplifiée --}}
        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('books.index') }}" method="GET" id="searchForm">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        {{-- Barre de recherche par titre et auteur uniquement --}}
                        <div style="flex: 2; min-width: 300px;">
                            <div class="mb-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Rechercher par titre ou auteur..."
                                        value="{{ $search_query ?? '' }}"
                                        id="searchInput">
                                    @if($search_query)
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Tri par --}}
                        <div style="flex: 1; min-width: 150px;">
                            <div class="mb-2">
                                <select name="sort_by" class="form-control form-control-sm">
                                    <option value="titre" {{ $sort_by == 'titre' ? 'selected' : '' }}>Titre</option>
                                    <option value="auteur" {{ $sort_by == 'auteur' ? 'selected' : '' }}>Auteur</option>
                                    <option value="created_at" {{ $sort_by == 'created_at' ? 'selected' : '' }}>Date d'ajout</option>
                                    <option value="is_valid" {{ $sort_by == 'is_valid' ? 'selected' : '' }}>Statut</option>
                                </select>
                            </div>
                        </div>

                        {{-- Ordre de tri --}}
                        <div style="flex: 1; min-width: 120px;">
                            <div class="mb-2">
                                <select name="sort_order" class="form-control form-control-sm">
                                    <option value="asc" {{ $sort_order == 'asc' ? 'selected' : '' }}>Croissant (A-Z)</option>
                                    <option value="desc" {{ $sort_order == 'desc' ? 'selected' : '' }}>Décroissant (Z-A)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- Résultats de recherche --}}
        @if($search_query || request()->has('sort_by') || request()->has('sort_order'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info py-2 mb-0">
                    <small>
                        <strong>{{ $books->total() }}</strong> résultat(s) trouvé(s)
                        @if($search_query)
                        pour "<strong>{{ $search_query }}</strong>"
                        @endif
                        @if($books->total() > 0)
                        - Affichage de {{ $books->firstItem() }} à {{ $books->lastItem() }} sur {{ $books->total() }}
                        @endif
                        <a href="{{ route('books.index') }}" class="float-end text-decoration-none">
                            <small>Effacer les filtres</small>
                        </a>
                    </small>
                </div>
            </div>
        </div>
        @endif

        {{-- Liste des livres --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Liste des Livres</h6>
                            <small class="text-muted">{{ $books->total() }} livre(s) au total</small>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Couverture</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Titre</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Auteur</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catégorie</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($books as $book)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                @if($book->cover_image)
                                                @php
                                                $imageUrl = asset('storage/' . $book->cover_image);
                                                @endphp

                                                {{-- Image avec fallback --}}
                                                <div class="position-relative">
                                                    <img src="{{ $imageUrl }}"
                                                        alt="{{ $book->titre }}"
                                                        class="rounded"
                                                        style="width: 50px; height: 65px; object-fit: cover;"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                                    {{-- Fallback si image ne charge pas --}}
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center d-none position-relative"
                                                        style="width: 50px; height: 65px;">
                                                        <i class="fas fa-book text-muted"></i>
                                                        <small class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 6px;">
                                                            ⚠️
                                                        </small>
                                                    </div>
                                                </div>
                                                @else
                                                {{-- Si pas d'image --}}
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 65px;">
                                                    <i class="fas fa-book text-muted"></i>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $book->titre }}</h6>
                                                @if($book->description)
                                                <p class="text-xs text-secondary mb-0">{{ Str::limit($book->description, 30) }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $book->auteur }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs text-secondary">{{ $book->category->nom ?? 'Aucune catégorie' }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs text-secondary">{{ $book->type ?? 'N/A' }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm {{ $book->is_valid ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $book->is_valid ? 'Valide' : 'En attente' }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- Bouton Edit --}}
                                                <a href="{{ route('books.index', array_merge(request()->query(), ['edit' => $book->id])) }}"
                                                    class="btn btn-outline-info btn-xs px-2"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- Bouton PDF --}}
                                                @if($book->pdf_path)
                                                <a href="{{ route('books.download', $book->id) }}"
                                                    class="btn btn-outline-primary btn-xs px-2"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    title="Télécharger PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @endif

                                                {{-- Bouton Delete --}}
                                                <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-xs px-2" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if($books->isEmpty())
                            <div class="text-center p-4">
                                <p class="text-muted mb-0">
                                    @if($search_query)
                                    Aucun livre trouvé pour "{{ $search_query }}". Essayez d'autres mots-clés.
                                    @else
                                    Aucun livre trouvé. <a href="{{ route('books.create') }}">Créez le premier livre</a>
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAGINATION AVANCÉE --}}
        @if($books->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex flex-column align-items-center">


                    {{-- Liens de pagination --}}
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Premier lien --}}
                            @if($books->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-angle-double-left"></i>
                                </span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $books->url(1) . '&' . http_build_query(request()->except('page')) }}" title="Première page">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            @endif

                            {{-- Page précédente --}}
                            @if($books->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-angle-left"></i>
                                </span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $books->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" title="Page précédente">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            </li>
                            @endif

                            {{-- Pages autour de la page courante --}}
                            @php
                            $current = $books->currentPage();
                            $last = $books->lastPage();
                            $start = max($current - 2, 1);
                            $end = min($current + 2, $last);
                            
                            if ($start > 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            @endphp

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $current)
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                                @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $books->url($page) . '&' . http_build_query(request()->except('page')) }}">{{ $page }}</a>
                                </li>
                                @endif
                            @endfor

                            @if ($end < $last)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif

                            {{-- Page suivante --}}
                            @if($books->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $books->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" title="Page suivante">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                            @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-angle-right"></i>
                                </span>
                            </li>
                            @endif

                            {{-- Dernier lien --}}
                            @if($books->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $books->url($books->lastPage()) . '&' . http_build_query(request()->except('page')) }}" title="Dernière page">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                            @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-angle-double-right"></i>
                                </span>
                            </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>
</main>

<script>
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('searchForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        // Recherche en temps réel (après 800ms de pause)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length === 0 || this.value.length >= 2) {
                    searchForm.submit();
                }
            }, 800);
        });

        // Soumission automatique des selects (tri)
        const selects = document.querySelectorAll('select[name="sort_by"], select[name="sort_order"]');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                searchForm.submit();
            });
        });

        @if(isset($editBook))
        const descriptionField = document.querySelector('textarea[name="description"]');
        const charCount = document.createElement('small');
        charCount.className = 'form-text text-muted float-end';
        charCount.textContent = '0/500 caractères';

        if (descriptionField) {
            descriptionField.parentNode.appendChild(charCount);

            // Initial count
            charCount.textContent = descriptionField.value.length + '/500 caractères';

            // Update count on input
            descriptionField.addEventListener('input', function() {
                charCount.textContent = this.value.length + '/500 caractères';

                if (this.value.length > 450) {
                    charCount.style.color = 'red';
                    charCount.style.fontWeight = 'bold';
                } else {
                    charCount.style.color = '';
                    charCount.style.fontWeight = '';
                }
            });
        }

        // Scroll to top when edit form is loaded
        window.scrollTo(0, 0);
        @endif
    });
</script>

<style>
    .page-link {
        color: #d63384;
        border-color: #dee2e6;
    }
    .page-item.active .page-link {
        background-color: #d63384;
        border-color: #d63384;
    }
    .page-link:hover {
        color: #a52766;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endsection