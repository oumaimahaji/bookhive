@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

        <div class="row mb-3">
            <div class="col-12 text-start">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Back to Books</a>
            </div>
        </div>

        {{-- Affichage des erreurs --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Message de succès --}}
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Edit Book</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-3">
                        {{-- Formulaire de modification --}}
                        <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Titre</label>
                                <input type="text" name="titre" class="form-control" value="{{ old('titre', $book->titre) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Auteur</label>
                                <input type="text" name="auteur" class="form-control" value="{{ old('auteur', $book->auteur) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $book->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catégorie</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- Sélectionnez --</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $book->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->nom }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <input type="text" name="type" class="form-control" value="{{ old('type', $book->type) }}">
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_valid" class="form-check-input" value="1" {{ old('is_valid', $book->is_valid) ? 'checked' : '' }}>
                                <label class="form-check-label">Valider le livre</label>
                            </div>

                            {{-- Champ PDF pour admin --}}
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <div class="mb-3">
                                <label class="form-label">PDF du livre (admin only)</label>
                                <input type="file" name="pdf" class="form-control" accept="application/pdf">
                                @if($book->pdf_path)
                                <p class="mt-2">PDF actuel :
                                    <a href="{{ route('books.download', $book->id) }}" target="_blank">Voir / Télécharger</a>
                                </p>
                                @endif
                            </div>
                            @endif

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Update Book</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection