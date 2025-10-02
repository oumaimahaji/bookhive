@extends('layouts.user_type.auth')

@section('content')
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($books as $book)
                                    <tr>
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
                                            <span class="badge badge-sm {{ $book->is_valid ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $book->is_valid ? 'Valid' : 'Pending' }}
                                            </span>
                                        </td>
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