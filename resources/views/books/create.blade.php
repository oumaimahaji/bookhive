@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-0">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card mb-0">
                <div class="card-header pb-0 text-center">
                    <h5 class="mb-0">Ajouter un Livre</h5>
                </div>
                <div class="card-body px-2 pt-1 pb-1">


                    @if ($errors->any())
                    <div class="alert alert-danger mb-1 py-1">
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul class="mb-0 mt-0">
                            @foreach ($errors->all() as $error)
                            <li class="small">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            {{-- Colonne gauche --}}
                            <div class="col-md-6">
                                {{-- Titre --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Titre <span class="text-danger">*</span></label>
                                    <input type="text" name="titre" value="{{ old('titre') }}" class="form-control form-control-sm" placeholder="Entrez le titre du livre" required>
                                </div>

                                {{-- Auteur --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Auteur <span class="text-danger">*</span></label>
                                    <input type="text" name="auteur" value="{{ old('auteur') }}" class="form-control form-control-sm" placeholder="Entrez le nom de l'auteur" required>
                                </div>

                                {{-- Catégorie --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Catégorie <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control form-control-sm" required>
                                        <option value="">-- Sélectionnez --</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Type --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Type</label>
                                    <input type="text" name="type" value="{{ old('type') }}" class="form-control form-control-sm" placeholder="Entrez le type du livre">
                                </div>
                            </div>

                            {{-- Colonne droite --}}
                            <div class="col-md-6">
                                {{-- Description --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Description</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Entrez la description du livre">{{ old('description') }}</textarea>
                                </div>

                                {{-- Champ PDF : visible uniquement pour l'admin --}}
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Fichier PDF du livre</label>
                                    <input type="file" name="pdf" class="form-control form-control-sm" accept="application/pdf">
                                    <small class="text-muted">Format accepté : PDF uniquement</small>
                                </div>

                                {{-- Case pour valider --}}
                                <div class="form-check mb-1">
                                    <input type="checkbox" name="is_valid" value="1" class="form-check-input" id="is_valid" {{ old('is_valid') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="is_valid">Valider le livre</label>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Boutons en bas --}}
                        <div class="text-center mt-2">
                            <button type="submit" class="btn bg-gradient-primary btn-sm px-3">
                                <i class="fas fa-plus me-1"></i>Ajouter le livre
                            </button>
                            <a href="{{ route('books.index') }}" class="btn bg-gradient-secondary btn-sm px-3 ms-1">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection