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

        @if(isset($editCategory))
        {{-- Formulaire d'édition --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Modifier la catégorie : {{ $editCategory->nom }}</h6>
                        @php
                        $cancelParams = request()->query();
                        unset($cancelParams['edit']);
                        @endphp
                        <a href="{{ route('categories.index', $cancelParams) }}" class="btn btn-outline-secondary btn-sm">
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

                        <form action="{{ route('categories.update', $editCategory->id) }}" method="POST" id="editCategoryForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                        value="{{ old('nom', $editCategory->nom) }}"
                                        required
                                        maxlength="255"
                                        id="editCategoryName"
                                        placeholder="Entrez le nom de la catégorie">
                                    @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 255 caractères</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Entrez la description de la catégorie (optionnel)"
                                        maxlength="500"
                                        id="editCategoryDescription">{{ old('description', $editCategory->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 500 caractères</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                                    <i class="fas fa-save me-1"></i>Mettre à jour
                                </button>
                                @php
                                $cancelParams = request()->query();
                                unset($cancelParams['edit']);
                                @endphp
                                <a href="{{ route('categories.index', $cancelParams) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @else

        {{-- Liste des catégories avec recherche et tri --}}
        <div class="row mb-4 mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Gestion des Catégories</h6>
                    <a href="{{ route('categories.create') }}" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                        <i class="fas fa-plus me-1"></i>Nouvelle Catégorie
                    </a>
                </div>
            </div>
        </div>

        {{-- Formulaire Recherche Avancée --}}
        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('categories.index') }}" method="GET" id="searchForm">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        {{-- Barre de recherche par nom --}}
                        <div style="flex: 2; min-width: 300px;">
                            <div class="mb-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Rechercher par nom de catégorie..."
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
                                    <option value="nom" {{ $sort_by == 'nom' ? 'selected' : '' }}>Nom</option>
                                    <option value="books_count" {{ $sort_by == 'books_count' ? 'selected' : '' }}>Nombre de Livres</option>
                                    <option value="created_at" {{ $sort_by == 'created_at' ? 'selected' : '' }}>Date d'ajout</option>
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

                        {{-- Boutons d'action --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-search me-1"></i>Rechercher
                            </button>
                            @if(request()->hasAny(['search', 'sort_by', 'sort_order']))
                            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-refresh me-1"></i>Réinitialiser
                            </a>
                            @endif
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
                        <strong>{{ $categories->total() }}</strong> résultat(s) trouvé(s)
                        @if($search_query)
                        pour "<strong>{{ $search_query }}</strong>"
                        @endif

                        <a href="{{ route('categories.index') }}" class="float-end text-decoration-none">
                            <small>Effacer les filtres</small>
                        </a>
                    </small>
                </div>
            </div>
        </div>
        @endif

        {{-- Liste des catégories --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Liste des Catégories</h6>
                            <small class="text-muted">{{ $categories->total() }} catégorie(s) au total</small>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre de Livres</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date d'ajout</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $category->nom }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $category->description ? Str::limit($category->description, 50) : 'Aucune description' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">{{ $category->books_count }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs text-secondary">
                                                {{ $category->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- Bouton Edit --}}
                                                <a href="{{ route('categories.index', array_merge(request()->query(), ['edit' => $category->id])) }}"
                                                    class="btn btn-outline-info btn-xs px-2"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- Bouton PDF --}}
                                                <a href="{{ route('categories.pdf', $category->id) }}"
                                                    class="btn btn-outline-primary btn-xs px-2"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    title="Télécharger PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>

                                                {{-- Bouton Delete --}}
                                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-xs px-2" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')"
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

                            @if($categories->isEmpty())
                            <div class="text-center p-4">
                                <p class="text-muted mb-0">
                                    @if($search_query)
                                    Aucune catégorie trouvée pour "{{ $search_query }}". Essayez d'autres mots-clés.
                                    @else
                                    Aucune catégorie trouvée. <a href="{{ route('categories.create') }}">Créez la première catégorie</a>
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAGINATION CENTRÉE --}}
        @if($categories->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex flex-column align-items-center">
                    


                    {{-- Liens de pagination --}}
                    <nav aria-label="Pagination des catégories">
                        <ul class="pagination pagination-sm mb-0 justify-content-center">
                            {{-- Premier lien --}}
                            @if($categories->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-angle-double-left"></i>
                                </span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->url(1) . '&' . http_build_query(request()->except('page')) }}" title="Première page">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            @endif

                            {{-- Page précédente --}}
                            @if($categories->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-angle-left"></i>
                                </span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" title="Page précédente">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            </li>
                            @endif

                            {{-- Pages autour de la page courante --}}
                            @php
                            $current = $categories->currentPage();
                            $last = $categories->lastPage();
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
                                    <a class="page-link" href="{{ $categories->url($page) . '&' . http_build_query(request()->except('page')) }}">{{ $page }}</a>
                                </li>
                                @endif
                            @endfor

                            @if ($end < $last)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif

                            {{-- Page suivante --}}
                            @if($categories->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" title="Page suivante">
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
                            @if($categories->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->url($categories->lastPage()) . '&' . http_build_query(request()->except('page')) }}" title="Dernière page">
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

        @if(isset($editCategory))
        // Script pour l'édition
        const editForm = document.getElementById('editCategoryForm');
        const nameInput = document.getElementById('editCategoryName');
        const descriptionInput = document.getElementById('editCategoryDescription');

        function validateName() {
            const value = nameInput.value.trim();
            if (value.length === 0) {
                nameInput.classList.add('is-invalid');
                nameInput.classList.remove('is-valid');
                return false;
            } else if (value.length > 255) {
                nameInput.classList.add('is-invalid');
                nameInput.classList.remove('is-valid');
                return false;
            } else {
                nameInput.classList.remove('is-invalid');
                nameInput.classList.add('is-valid');
                return true;
            }
        }

        function validateDescription() {
            const value = descriptionInput.value;
            if (value.length > 500) {
                descriptionInput.classList.add('is-invalid');
                descriptionInput.classList.remove('is-valid');
                return false;
            } else {
                descriptionInput.classList.remove('is-invalid');
                if (value.length > 0) {
                    descriptionInput.classList.add('is-valid');
                }
                return true;
            }
        }

        nameInput.addEventListener('input', validateName);
        descriptionInput.addEventListener('input', validateDescription);

        editForm.addEventListener('submit', function(e) {
            const isNameValid = validateName();
            const isDescriptionValid = validateDescription();

            if (!isNameValid || !isDescriptionValid) {
                e.preventDefault();
                let errorMessage = 'Veuillez corriger les erreurs suivantes :\n';
                if (!isNameValid) {
                    if (nameInput.value.trim().length === 0) {
                        errorMessage += '- Le nom de la catégorie est requis\n';
                    } else if (nameInput.value.length > 255) {
                        errorMessage += '- Le nom ne doit pas dépasser 255 caractères\n';
                    }
                }
                if (!isDescriptionValid) {
                    errorMessage += '- La description ne doit pas dépasser 500 caractères\n';
                }
                alert(errorMessage);
            }
        });

        validateName();
        validateDescription();
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
    .pagination {
        margin-bottom: 0;
    }
    .justify-content-center {
        justify-content: center !important;
    }
    .text-center {
        text-align: center !important;
    }
</style>
@endsection