@extends('layouts.user_type.auth')

@section('content')
<<<<<<< HEAD
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ route('books.create') }}" class="btn btn-primary">Add New Book</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Inline Edit Form --}}
        @if(isset($editBook))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Edit Book: {{ $editBook->titre }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('books.update', $editBook->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="titre" class="form-label">Title</label>
                                    <input type="text" name="titre" class="form-control" value="{{ old('titre', $editBook->titre) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="auteur" class="form-label">Author</label>
                                    <input type="text" name="auteur" class="form-control" value="{{ old('auteur', $editBook->auteur) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select name="category_id" class="form-control" required>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $editBook->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control">{{ old('description', $editBook->description) }}</textarea>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <input type="text" name="type" class="form-control" value="{{ old('type', $editBook->type) }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="is_valid" value="1" class="form-check-input" id="is_valid"
                                            {{ $editBook->is_valid ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_valid">Valid</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Update Book</button>
                            <a href="{{ route('books.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Your table code below --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Books Table</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Author</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-secondary opacity-7">Action</th>
=======
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style="margin-top: -15px !important;">
    <div class="container-fluid py-0">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Books Management</h6>
                        
                        {{-- Afficher le bouton "Add New Book" UNIQUEMENT si on n'est pas en mode édition --}}
                        @if(!request()->has('edit'))
                        <a href="{{ route('books.create') }}" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                            <i class="fas fa-plus me-1"></i>Add New Book
                        </a>
                        @endif
                    </div>

                    {{-- Message de succès --}}
                    @if(session('success'))
                    <div class="alert alert-success mx-2 my-0 py-1 alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                        <span class="alert-text">{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger mx-2 my-0 py-1 alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                        <span class="alert-text">{{ session('error') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <div class="card-body px-2 py-0">
                        {{-- Afficher la liste des livres UNIQUEMENT si on n'est pas en mode édition --}}
                        @if(!request()->has('edit'))

                        {{-- Formulaire Recherche Avancée --}}
                        <div class="row mb-4 mt-3">
                            <div class="col-12">
                                <form action="{{ route('books.index') }}" method="GET" id="searchForm">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        {{-- Barre de recherche globale --}}
                                        <div style="flex: 2; min-width: 300px;">
                                            <div class="mb-2">
                                                <input type="text" name="search" class="form-control form-control-sm"
                                                    placeholder="Search books by title, author, description, type or category..."
                                                    value="{{ $search_query ?? '' }}"
                                                    id="searchInput">
                                            </div>
                                        </div>

                                        {{-- Filtre par catégorie --}}
                                        <div style="flex: 1; min-width: 150px;">
                                            <div class="mb-2">
                                                <select name="category_id" class="form-control form-control-sm">
                                                    <option value="">All Categories</option>
                                                    @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->nom }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Tri par --}}
                                        <div style="flex: 1; min-width: 150px;">
                                            <div class="mb-2">
                                                <select name="sort_by" class="form-control form-control-sm">
                                                    <option value="titre" {{ $sort_by == 'titre' ? 'selected' : '' }}>Title</option>
                                                    <option value="auteur" {{ $sort_by == 'auteur' ? 'selected' : '' }}>Author</option>
                                                    <option value="created_at" {{ $sort_by == 'created_at' ? 'selected' : '' }}>Date Added</option>
                                                    <option value="is_valid" {{ $sort_by == 'is_valid' ? 'selected' : '' }}>Status</option>
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

                                        {{-- Boutons d'action --}}
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-search me-1"></i>Search
                                            </button>
                                            @if(request()->hasAny(['search', 'category_id', 'sort_by', 'sort_order']))
                                            <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-times me-1"></i>Reset
                                            </a>
                                            @endif
                                            <a href="{{ route('books.export', request()->query()) }}" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                                                <i class="fas fa-download me-1"></i>Export PDF
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Résultats de recherche --}}
                        @if($search_query || request()->has('category_id'))
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info py-2 mb-0">
                                    <small>
                                        <strong>{{ $books->total() }}</strong> result(s) found
                                        @if($search_query)
                                         for "<strong>{{ $search_query }}</strong>"
                                        @endif
                                        @if(request()->has('category_id') && $selectedCategory = $categories->where('id', request('category_id'))->first())
                                         in category "<strong>{{ $selectedCategory->nom }}</strong>"
                                        @endif
                                        @if($books->total() > 0)
                                         - Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }}
                                        @endif
                                        <a href="{{ route('books.index') }}" class="float-end text-decoration-none">
                                            <small>Clear filters</small>
                                        </a>
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Books Table --}}
                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-hover align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Cover</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Title</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2 border-0">Author</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Category</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Type</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Actions</th>
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($books as $book)
                                    <tr>
<<<<<<< HEAD
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $book->titre }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $book->description ?? 'No description' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $book->auteur }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">{{ $book->category->nom ?? 'No category' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">{{ $book->type ?? 'N/A' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
=======
                                        <td class="py-1">
                                            {{-- Photo miniature --}}
                                            @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}"
                                                alt="{{ $book->titre }}"
                                                class="rounded"
                                                style="width: 40px; height: 50px; object-fit: cover;">
                                            @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 50px;">
                                                <i class="fas fa-book text-muted"></i>
                                            </div>
                                            @endif
                                        </td>
                                        <td class="py-1">
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm">{{ $book->titre }}</h6>
                                                @if($book->description)
                                                <p class="text-xs text-secondary mb-0">{{ Str::limit($book->description, 30) }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-1">
                                            <p class="text-xs font-weight-bold mb-0">{{ $book->auteur }}</p>
                                        </td>
                                        <td class="align-middle text-center py-1">
                                            <span class="text-xs text-secondary">{{ $book->category->nom ?? 'No category' }}</span>
                                        </td>
                                        <td class="align-middle text-center py-1">
                                            <span class="text-xs text-secondary">{{ $book->type ?? 'N/A' }}</span>
                                        </td>
                                        <td class="align-middle text-center py-1">
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
                                            <span class="badge badge-sm {{ $book->is_valid ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $book->is_valid ? 'Valid' : 'Pending' }}
                                            </span>
                                        </td>
<<<<<<< HEAD
                                        <td class="align-middle">
                                            <a href="{{ route('books.index', ['edit' => $book->id]) }}"
                                               class="text-secondary font-weight-bold text-xs me-2">Edit</a>
                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-danger font-weight-bold text-xs border-0 bg-transparent" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-3">No books found.</td>
                                        </tr>
=======
                                        <td class="align-middle text-center py-1">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('books.index', array_merge(request()->query(), ['edit' => $book->id])) }}" 
                                                   class="btn btn-outline-info btn-xs px-2"
                                                   data-bs-toggle="tooltip" data-bs-title="Edit Book">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>

                                                {{-- Bouton PDF --}}
                                                @if($book->pdf_path)
                                                <a href="{{ route('books.download', $book->id) }}" 
                                                   class="btn btn-outline-primary btn-xs px-2"
                                                   data-bs-toggle="tooltip" data-bs-title="Download PDF" 
                                                   target="_blank">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </a>
                                                @endif

                                                <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline delete-form" data-book-title="{{ $book->titre }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-xs px-2"
                                                            data-bs-toggle="tooltip" data-bs-title="Delete Book">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-3">
                                            <p class="text-muted mb-0">
                                                @if($search_query || request()->has('category_id'))
                                                No books found matching your criteria. Try different search terms or filters.
                                                @else
                                                No books found. <a href="{{ route('books.create') }}">Create the first book</a>
                                                @endif
                                            </p>
                                        </td>
                                    </tr>
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
<<<<<<< HEAD
=======

                        {{-- Pagination --}}
                        @if($books->hasPages())
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    {{-- Informations de pagination --}}
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }} results
                                        </small>
                                    </div>

                                    {{-- Liens de pagination --}}
                                    <div class="mb-2">
                                        {{ $books->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
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

                        @else

                        {{-- Inline Edit Form - S'affiche SEUL quand on est en mode édition --}}
                        @if(request()->has('edit') && isset($editBook))
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white pb-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-white">Edit Book: {{ $editBook->titre }}</h6>
                                        <a href="{{ route('books.index') }}" class="btn btn-outline-light btn-sm">
                                            <i class="fas fa-times me-1"></i>Back to List
                                        </a>
                                    </div>
                                    <div class="card-body py-2">
                                        <form action="{{ route('books.update', $editBook->id) }}" method="POST" enctype="multipart/form-data" id="editBookForm">
                                            @csrf
                                            @method('PUT')

                                            {{-- Messages d'erreur --}}
                                            @if ($errors->any())
                                            <div class="alert alert-danger mb-3 py-2">
                                                <ul class="mb-0 mt-0 small">
                                                    @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif

                                            <div class="row g-3">
                                                {{-- Colonne gauche --}}
                                                <div class="col-md-6">
                                                    {{-- Titre --}}
                                                    <div class="mb-2">
                                                        <label for="titre" class="form-label fw-bold small mb-1">Title <span class="text-danger">*</span></label>
                                                        <input type="text" name="titre" class="form-control form-control-sm @error('titre') is-invalid @enderror"
                                                            value="{{ old('titre', $editBook->titre) }}"
                                                            maxlength="255"
                                                            required>
                                                        @error('titre')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Auteur --}}
                                                    <div class="mb-2">
                                                        <label for="auteur" class="form-label fw-bold small mb-1">Author <span class="text-danger">*</span></label>
                                                        <input type="text" name="auteur" class="form-control form-control-sm @error('auteur') is-invalid @enderror"
                                                            value="{{ old('auteur', $editBook->auteur) }}"
                                                            maxlength="255"
                                                            required>
                                                        @error('auteur')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Catégorie --}}
                                                    <div class="mb-2">
                                                        <label for="category_id" class="form-label fw-bold small mb-1">Category <span class="text-danger">*</span></label>
                                                        <select name="category_id" class="form-control form-control-sm @error('category_id') is-invalid @enderror" required>
                                                            <option value="">-- Select Category --</option>
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

                                                    {{-- Type --}}
                                                    <div class="mb-2">
                                                        <label for="type" class="form-label fw-bold small mb-1">Type</label>
                                                        <input type="text" name="type" class="form-control form-control-sm @error('type') is-invalid @enderror"
                                                            value="{{ old('type', $editBook->type) }}"
                                                            maxlength="100">
                                                        @error('type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Status --}}
                                                    <div class="mb-2">
                                                        <label class="form-label fw-bold small mb-1">Status</label>
                                                        <div class="form-check">
                                                            <input type="checkbox" name="is_valid" value="1" class="form-check-input" id="is_valid"
                                                                {{ old('is_valid', $editBook->is_valid) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="is_valid">Valid</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Colonne droite --}}
                                                <div class="col-md-6">
                                                    {{-- Description --}}
                                                    <div class="mb-2">
                                                        <label for="description" class="form-label fw-bold small mb-1">Description</label>
                                                        <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror"
                                                            rows="3"
                                                            maxlength="500"
                                                            placeholder="Enter book description">{{ old('description', $editBook->description) }}</textarea>
                                                        <small class="text-muted">Maximum 500 characters. Currently: <span id="charCount">0</span> characters</small>
                                                        @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Photo de couverture --}}
                                                    <div class="mb-2">
                                                        <label class="form-label fw-bold small mb-1">Cover Image</label>
                                                        <input type="file" name="cover_image" class="form-control form-control-sm @error('cover_image') is-invalid @enderror"
                                                            accept="image/*">
                                                        <small class="text-muted">Formats: JPG, PNG, WEBP - Max: 2MB</small>
                                                        @error('cover_image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        @if($editBook->cover_image)
                                                        <div class="mt-2">
                                                            <p class="mb-0 small">Current Cover:</p>
                                                            <img src="{{ asset('storage/' . $editBook->cover_image) }}"
                                                                alt="Current cover"
                                                                class="img-thumbnail mt-1"
                                                                style="max-width: 100px; max-height: 100px;">
                                                        </div>
                                                        @endif
                                                    </div>

                                                    {{-- Champ PDF admin --}}
                                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                                    <div class="mb-2">
                                                        <label class="form-label fw-bold small mb-1">Update PDF File (admin only)</label>
                                                        <input type="file" name="pdf" class="form-control form-control-sm @error('pdf') is-invalid @enderror"
                                                            accept="application/pdf">
                                                        <small class="text-muted">Maximum file size: 10MB. Accepted format: PDF only.</small>
                                                        @error('pdf')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        @if($editBook->pdf_path)
                                                        <div class="mt-2">
                                                            <p class="mb-0 small">Current PDF:</p>
                                                            <a href="{{ route('books.download', $editBook->id) }}" class="btn btn-outline-primary btn-xs" target="_blank">
                                                                <i class="fas fa-download me-1"></i>Download PDF
                                                            </a>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Boutons --}}
                                            <div class="text-center mt-3 pt-3 border-top">
                                                <button type="submit" class="btn btn-sm px-3" style="background-color: #d63384; color: white; border: none;">
                                                    <i class="fas fa-save me-1"></i>Update Book
                                                </button>
                                                <a href="{{ route('books.index') }}" class="btn btn-secondary btn-sm px-3 ms-1">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @endif
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<<<<<<< HEAD
=======
@endsection

@section('scripts')
@if(!request()->has('edit'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

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
        const selects = document.querySelectorAll('select[name="sort_by"], select[name="sort_order"], select[name="per_page"], select[name="category_id"]');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                searchForm.submit();
            });
        });

        // Confirmation de suppression
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const bookTitle = this.getAttribute('data-book-title');
                
                if (confirm(Are you sure you want to delete the book "${bookTitle}"? This action cannot be undone.)) {
                    this.submit();
                }
            });
        });
    });
</script>
@endif

@if(request()->has('edit') && isset($editBook))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const descriptionField = document.querySelector('textarea[name="description"]');
        const charCount = document.getElementById('charCount');

        // Initial count
        if (descriptionField && charCount) {
            charCount.textContent = descriptionField.value.length;

            // Update count on input
            descriptionField.addEventListener('input', function() {
                charCount.textContent = this.value.length;

                // Optional: Add warning when approaching limit
                if (this.value.length > 450) {
                    charCount.style.color = 'red';
                    charCount.style.fontWeight = 'bold';
                } else {
                    charCount.style.color = '';
                    charCount.style.fontWeight = '';
                }
            });
        }

        // Form validation
        const form = document.getElementById('editBookForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                let valid = true;

                // Check required fields
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        valid = false;
                        field.classList.add('is-invalid');
                    }
                });

                // Check cover image type
                const coverImage = form.querySelector('input[name="cover_image"]');
                if (coverImage && coverImage.files.length > 0) {
                    const file = coverImage.files[0];
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];

                    if (!allowedTypes.includes(file.type)) {
                        valid = false;
                        coverImage.classList.add('is-invalid');
                        alert('Please select a valid image file (JPG, PNG, GIF, WEBP).');
                    }

                    // Check file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        valid = false;
                        coverImage.classList.add('is-invalid');
                        alert('Cover image size must be less than 2MB.');
                    }
                }

                // Check file type if PDF is selected
                const pdfFile = form.querySelector('input[name="pdf"]');
                if (pdfFile && pdfFile.files.length > 0) {
                    const file = pdfFile.files[0];
                    if (file.type !== 'application/pdf') {
                        valid = false;
                        pdfFile.classList.add('is-invalid');
                        alert('Please select a PDF file only.');
                    }

                    // Check file size (10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        valid = false;
                        pdfFile.classList.add('is-invalid');
                        alert('File size must be less than 10MB.');
                    }
                }

                if (!valid) {
                    e.preventDefault();
                    alert('Please correct the errors in the form.');
                }
            });
        }

        // Auto-scroll to the edit form when page loads
        const editForm = document.querySelector('.card.border-primary');
        if (editForm) {
            editForm.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
</script>
@endif
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
@endsection