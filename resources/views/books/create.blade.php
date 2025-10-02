@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un Livre</h5>
                    <a href="{{ route('books.index') }}" class="btn bg-gradient-primary btn-sm mb-0">Retour à la liste</a>
                </div>
                <div class="card-body px-4 pt-4 pb-2">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('books.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" name="titre" value="{{ old('titre') }}" class="form-control" placeholder="Entrez le titre du livre" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Auteur</label>
                            <input type="text" name="auteur" value="{{ old('auteur') }}" class="form-control" placeholder="Entrez le nom de l'auteur" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Entrez la description">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catégorie</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">-- Sélectionnez --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <input type="text" name="type" value="{{ old('type') }}" class="form-control" placeholder="Entrez le type du livre">
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" name="is_valid" value="1" class="form-check-input" id="is_valid" {{ old('is_valid') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_valid">Valider le livre</label>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary">Ajouter</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
