@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style="margin-top: -15px !important;">
    <div class="container-fluid py-0">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Books Management</h6>
                        <a href="{{ route('books.create') }}" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">Add New Book</a>
                    </div>

                    {{-- Message de succès --}}
                    @if(session('success'))
                    <div class="alert alert-success mx-2 my-0 py-1">{{ session('success') }}</div>
                    @endif

                    <div class="card-body px-2 py-0">
                        {{-- Afficher le formulaire de recherche et la liste UNIQUEMENT si on n'est pas en mode édition --}}
                        @if(!isset($editBook))

                        {{-- Formulaire Recherche / Filtre --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <form action="{{ route('books.index') }}" method="GET">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        {{-- Search by Author --}}
                                        <div style="flex: 1; min-width: 200px;">
                                            <input type="text" name="author" class="form-control form-control-sm" placeholder="Search by author" value="{{ request('author') }}">
                                        </div>

                                        {{-- Category Filter --}}
                                        <div style="flex: 1; min-width: 180px;">
                                            <select name="category_id" class="form-control form-control-sm">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->nom }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Buttons Container --}}
                                        <div class="d-flex gap-2" style="min-width: 230px;">
                                            {{-- Filter Button --}}
                                            <div style="flex: 1;">
                                                <button type="submit" class="btn btn-sm w-100" style="background-color: #d63384; color: white; border: none; height: 31px; line-height: 1;">Filter</button>
                                            </div>

                                            {{-- Export PDF Button --}}
                                            <div style="flex: 1;">
                                                <a href="{{ route('books.export', request()->query()) }}" class="btn btn-sm w-100 d-flex align-items-center justify-content-center" style="background-color: #d63384; color: white; border: none; height: 31px; line-height: 1;">Export PDF</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Books Table --}}
                        <div class="table-responsive mt-0">
                            <table class="table table-sm table-hover align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Title</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2 border-0">Author</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Category</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Type</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 border-0">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($books as $book)
                                    <tr>
                                        <td class="py-0">
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm">{{ $book->titre }}</h6>
                                                @if($book->description)
                                                <p class="text-xs text-secondary mb-0">{{ Str::limit($book->description, 30) }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-0">
                                            <p class="text-xs font-weight-bold mb-0">{{ $book->auteur }}</p>
                                        </td>
                                        <td class="align-middle text-center py-0">
                                            <span class="text-xs text-secondary">{{ $book->category->nom ?? 'No category' }}</span>
                                        </td>
                                        <td class="align-middle text-center py-0">
                                            <span class="text-xs text-secondary">{{ $book->type ?? 'N/A' }}</span>
                                        </td>
                                        <td class="align-middle text-center py-0">
                                            <span class="badge badge-sm {{ $book->is_valid ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $book->is_valid ? 'Valid' : 'Pending' }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center py-0">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('books.index', ['edit' => $book->id]) }}" class="btn btn-outline-info btn-xs px-1">
                                                    Edit
                                                </a>

                                                {{-- Télécharger PDF --}}
                                                @if($book->pdf_path)
                                                <a href="{{ route('books.download', $book->id) }}" class="btn btn-outline-primary btn-xs px-1" target="_blank">
                                                    PDF
                                                </a>
                                                @endif

                                                <form action="{{ route('books.destroy', $book->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-xs px-1" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center p-0">
                                            <p class="text-muted mb-0">No books found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-0">
                            {{ $books->withQueryString()->links() }}
                        </div>

                        @endif

                        {{-- Inline Edit Form --}}
                        @if(isset($editBook))
                        <div class="row mt-0">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white pb-1 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-white">Edit Book: {{ $editBook->titre }}</h6>
                                        <a href="{{ route('books.index') }}" class="btn btn-outline-light btn-sm">
                                            <i class="fas fa-times me-1"></i>Close
                                        </a>
                                    </div>
                                    <div class="card-body py-1" style="max-height: 400px; overflow-y: auto;">
                                        <form action="{{ route('books.update', $editBook->id) }}" method="POST" enctype="multipart/form-data" id="editBookForm">
                                            @csrf
                                            @method('PUT')

                                            {{-- Messages d'erreur --}}
                                            @if ($errors->any())
                                            <div class="alert alert-danger mb-2 py-1">
                                                <ul class="mb-0 mt-0 small">
                                                    @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif

                                            <div class="row g-2">
                                                {{-- Colonne gauche --}}
                                                <div class="col-md-6">
                                                    {{-- Titre --}}
                                                    <div class="mb-1">
                                                        <label for="titre" class="form-label fw-bold small mb-0">Title <span class="text-danger">*</span></label>
                                                        <input type="text" name="titre" class="form-control form-control-sm @error('titre') is-invalid @enderror"
                                                            value="{{ old('titre', $editBook->titre) }}"
                                                            maxlength="255"
                                                            required>
                                                        @error('titre')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Auteur --}}
                                                    <div class="mb-1">
                                                        <label for="auteur" class="form-label fw-bold small mb-0">Author <span class="text-danger">*</span></label>
                                                        <input type="text" name="auteur" class="form-control form-control-sm @error('auteur') is-invalid @enderror"
                                                            value="{{ old('auteur', $editBook->auteur) }}"
                                                            maxlength="255"
                                                            required>
                                                        @error('auteur')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Catégorie --}}
                                                    <div class="mb-1">
                                                        <label for="category_id" class="form-label fw-bold small mb-0">Category <span class="text-danger">*</span></label>
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
                                                    <div class="mb-1">
                                                        <label for="type" class="form-label fw-bold small mb-0">Type</label>
                                                        <input type="text" name="type" class="form-control form-control-sm @error('type') is-invalid @enderror"
                                                            value="{{ old('type', $editBook->type) }}"
                                                            maxlength="100">
                                                        @error('type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Status --}}
                                                    <div class="mb-1">
                                                        <label class="form-label fw-bold small mb-0">Status</label>
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
                                                    <div class="mb-1">
                                                        <label for="description" class="form-label fw-bold small mb-0">Description</label>
                                                        <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror"
                                                            rows="3"
                                                            maxlength="500"
                                                            placeholder="Enter book description">{{ old('description', $editBook->description) }}</textarea>
                                                        <small class="text-muted">Maximum 500 characters. Currently: <span id="charCount">0</span> characters</small>
                                                        @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Champ PDF admin --}}
                                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                                    <div class="mb-1">
                                                        <label class="form-label fw-bold small mb-0">Update PDF File (admin only)</label>
                                                        <input type="file" name="pdf" class="form-control form-control-sm @error('pdf') is-invalid @enderror"
                                                            accept="application/pdf">
                                                        <small class="text-muted">Maximum file size: 10MB. Accepted format: PDF only.</small>
                                                        @error('pdf')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        @if($editBook->pdf_path)
                                                        <div class="mt-1">
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
                                            <div class="text-center mt-2 pt-2 border-top">
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

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@if(isset($editBook))
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
@endsection