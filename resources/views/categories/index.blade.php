@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid" style="padding-top: 0;">

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(isset($editCategory))
        {{-- Formulaire d'édition --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Edit Category: {{ $editCategory->nom }}</h6>
                        @php
                        $cancelParams = request()->query();
                        unset($cancelParams['edit']);
                        @endphp
                        <a href="{{ route('categories.index', $cancelParams) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Close
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
                                    <label for="nom" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                        value="{{ old('nom', $editCategory->nom) }}"
                                        required
                                        maxlength="255"
                                        id="editCategoryName"
                                        placeholder="Enter category name">
                                    @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 255 characters</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Enter category description (optional)"
                                        maxlength="500">{{ old('description', $editCategory->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 500 characters</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                                <i class="fas fa-save me-1"></i>Update Category
                            </button>
                            @php
                            $cancelParams = request()->query();
                            unset($cancelParams['edit']);
                            @endphp
                            <a href="{{ route('categories.index', $cancelParams) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
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
                    <h6 class="mb-0">Categories Management</h6>
                    <a href="{{ route('categories.create') }}" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                        <i class="fas fa-plus me-1"></i>Add New Category
                    </a>
                </div>
            </div>
        </div>

        {{-- Formulaire Recherche Avancée --}}
        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('categories.index') }}" method="GET" id="searchForm">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        {{-- Barre de recherche globale --}}
                        <div style="flex: 2; min-width: 300px;">
                            <div class="mb-2">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Search categories by name or description..."
                                    value="{{ $search_query ?? '' }}"
                                    id="searchInput">
                            </div>
                        </div>

                        {{-- Tri par --}}
                        <div style="flex: 1; min-width: 150px;">
                            <div class="mb-2">
                                <select name="sort_by" class="form-control form-control-sm">
                                    <option value="nom" {{ $sort_by == 'nom' ? 'selected' : '' }}>Name</option>
                                    <option value="books_count" {{ $sort_by == 'books_count' ? 'selected' : '' }}>Number of Books</option>
                                    <option value="created_at" {{ $sort_by == 'created_at' ? 'selected' : '' }}>Date Added</option>
                                </select>
                            </div>
                        </div>

                        {{-- Ordre de tri --}}
                        <div style="flex: 1; min-width: 120px;">
                            <div class="mb-2">
                                <select name="sort_order" class="form-control form-control-sm">
                                    <option value="asc" {{ $sort_order == 'asc' ? 'selected' : '' }}>Ascending (A-Z)</option>
                                    <option value="desc" {{ $sort_order == 'desc' ? 'selected' : '' }}>Descending (Z-A)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Items par page --}}
                        <div style="flex: 1; min-width: 100px;">
                            <div class="mb-2">
                                <select name="per_page" class="form-control form-control-sm">
                                    <option value="5" {{ $per_page == 5 ? 'selected' : '' }}>5 per page</option>
                                    <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 per page</option>
                                    <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 per page</option>
                                    <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 per page</option>
                                    <option value="100" {{ $per_page == 100 ? 'selected' : '' }}>100 per page</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Résultats de recherche --}}
        @if($search_query)
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info py-2 mb-0">
                    <small>
                        <strong>{{ $categories->total() }}</strong> result(s) found for "<strong>{{ $search_query }}</strong>"
                        @if($categories->total() > 0)
                        - Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }}
                        @endif
                        <a href="{{ route('categories.index') }}" class="float-end text-decoration-none">
                            <small>Clear search</small>
                        </a>
                    </small>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Categories Table</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Number of Books</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date Added</th>
                                        <th class="text-secondary opacity-7 text-center">Actions</th>
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
                                                {{ $category->description ? Str::limit($category->description, 50) : 'No description' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">{{ $category->books_count }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs text-secondary">
                                                {{ $category->created_at->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('categories.index', array_merge(request()->query(), ['edit' => $category->id])) }}"
                                                    class="btn btn-outline-info btn-xs px-2">
                                                    Edit
                                                </a>
                                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-xs px-2" onclick="return confirm('Are you sure?')">Delete</button>
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
                                    No categories found for "{{ $search_query }}". Try different keywords.
                                    @else
                                    No categories found.
                                    @endif
                                </p>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pagination Avancée --}}
        @if($categories->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    {{-- Informations de pagination --}}
                    <div class="mb-2">
                        <small class="text-muted">
                            Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} results
                        </small>
                    </div>

                    {{-- Liens de pagination --}}
                    <div class="mb-2">
                        {{ $categories->appends(request()->query())->links() }}
                    </div>

                    {{-- Sélecteur d'items par page --}}
                    <div class="mb-2">
                        <small class="text-muted me-2">Items per page:</small>
                        <select name="per_page" class="form-select form-select-sm d-inline-block w-auto" onchange="updatePerPage(this.value)">
                            <option value="5" {{ $per_page == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $per_page == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>
</main>

@if(isset($editCategory))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editCategoryForm');
        const nameInput = document.getElementById('editCategoryName');
        const descriptionInput = document.querySelector('textarea[name="description"]');

        // Fonction pour valider le nom
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

        // Fonction pour valider la description
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

        // Validation en temps réel pour le nom
        nameInput.addEventListener('input', function() {
            validateName();
        });

        // Validation en temps réel pour la description
        descriptionInput.addEventListener('input', function() {
            validateDescription();
        });

        // Validation avant soumission
        editForm.addEventListener('submit', function(e) {
            const isNameValid = validateName();
            const isDescriptionValid = validateDescription();

            if (!isNameValid || !isDescriptionValid) {
                e.preventDefault();

                let errorMessage = 'Please correct the following errors:\n';
                if (!isNameValid) {
                    if (nameInput.value.trim().length === 0) {
                        errorMessage += '- Category name is required\n';
                    } else if (nameInput.value.length > 255) {
                        errorMessage += '- Category name must not exceed 255 characters\n';
                    }
                }
                if (!isDescriptionValid) {
                    errorMessage += '- Description must not exceed 500 characters\n';
                }

                alert(errorMessage);
            }
        });

        // Validation initiale au chargement
        validateName();
        validateDescription();
    });
</script>
@else
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        // Recherche en temps réel (après 500ms de pause)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length === 0 || this.value.length >= 2) {
                    searchForm.submit();
                }
            }, 500);
        });

        // Changer le nombre d'items par page
        window.updatePerPage = function(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Retour à la première page
            window.location.href = url.toString();
        };

        // Soumission automatique des selects (tri)
        const selects = document.querySelectorAll('select[name="sort_by"], select[name="sort_order"], select[name="per_page"]');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                searchForm.submit();
            });
        });
    });
</script>
@endif
@endsection