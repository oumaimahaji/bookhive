@extends('layouts.user_type.auth')

@section('content')
<<<<<<< HEAD

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
=======
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

                    {{-- Alerte doublons IA --}}
                    @if(isset($duplicates) && !empty($duplicates))
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-robot me-2 fs-5"></i>
                            <h6 class="mb-0 fw-bold">IA - Doublons Potentiels Détectés</h6>
                        </div>
                        <p class="small mb-2 mt-1">Notre intelligence artificielle a détecté des livres similaires :</p>

                        @foreach($duplicates as $duplicate)
                        <div class="card mb-2 border-warning">
                            <div class="card-body py-2">
                                <div class="row">
                                    <div class="col-8">
                                        <h6 class="mb-1">{{ $duplicate['book']->titre }}</h6>
                                        <p class="small mb-1 text-muted">
                                            <strong>Auteur:</strong> {{ $duplicate['book']->auteur }}
                                            @if($duplicate['book']->category)
                                            | <strong>Catégorie:</strong> {{ $duplicate['book']->category->nom }}
                                            @endif
                                        </p>
                                        <div class="small">
                                            @foreach($duplicate['reasons'] as $reason)
                                            <span class="badge bg-warning text-dark me-1">{{ $reason }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="fs-5 fw-bold text-danger">
                                            {{ $duplicate['score'] }}%
                                        </div>
                                        <small class="text-muted">Score de similarité</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-2">
                            <p class="small mb-1">
                                <strong>Voulez-vous quand même continuer ?</strong><br>
                                Si vous êtes sûr qu'il s'agit d'un livre différent, vous pouvez ignorer cet avertissement.
                            </p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="force_create" id="force_create" value="1">
                                <label class="form-check-label small fw-bold" for="force_create">
                                    Forcer la création malgré les doublons détectés
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" id="bookForm">
                        @csrf

                        <div class="row">
                            {{-- Colonne gauche --}}
                            <div class="col-md-6">
                                {{-- Titre --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Titre <span class="text-danger">*</span></label>
                                    <input type="text" name="titre" value="{{ old('titre', $input['titre'] ?? '') }}" class="form-control form-control-sm" placeholder="Entrez le titre du livre" required>
                                </div>

                                {{-- Auteur --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Auteur <span class="text-danger">*</span></label>
                                    <input type="text" name="auteur" value="{{ old('auteur', $input['auteur'] ?? '') }}" class="form-control form-control-sm" placeholder="Entrez le nom de l'auteur" required>
                                </div>

                                {{-- Catégorie --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Catégorie <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control form-control-sm" required>
                                        <option value="">-- Sélectionnez --</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id', $input['category_id'] ?? '') == $category->id) ? 'selected' : '' }}>
                                            {{ $category->nom }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Type --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Type</label>
                                    <input type="text" name="type" value="{{ old('type', $input['type'] ?? '') }}" class="form-control form-control-sm" placeholder="Entrez le type du livre">
                                </div>
                            </div>

                            {{-- Colonne droite --}}
                            <div class="col-md-6">
                                {{-- Photo de couverture --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Photo de couverture <span class="text-danger">*</span></label>
                                    <input type="file" name="cover_image" class="form-control form-control-sm" accept="image/*" required>
                                    <small class="text-muted">Formats: JPG, PNG, WEBP - Max: 2MB</small>
                                </div>

                                {{-- Description --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Description</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Entrez la description du livre">{{ old('description', $input['description'] ?? '') }}</textarea>
                                </div>

                                {{-- Champ PDF : visible uniquement pour l'admin --}}
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                <div class="mb-1">
                                    <label class="form-label fw-bold small">Fichier PDF du livre</label>
                                    <input type="file" name="pdf" class="form-control form-control-sm" accept="application/pdf">
                                    <small class="text-muted">Format accepté : PDF uniquement - Max: 10MB</small>
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
                            <button type="submit" class="btn bg-gradient-primary btn-sm px-3" id="submitBtn">
                                <i class="fas fa-plus me-1"></i>
                                @if(isset($duplicates) && !empty($duplicates))
                                Confirmer la création
                                @else
                                Ajouter le livre
                                @endif
                            </button>
                            <a href="{{ route('books.index') }}" class="btn bg-gradient-secondary btn-sm px-3 ms-1">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                        </div>
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<<<<<<< HEAD

@endsection
=======
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forceCheckbox = document.getElementById('force_create');
        const submitBtn = document.getElementById('submitBtn');

        if (forceCheckbox && submitBtn) {
            forceCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Forcer l\'ajout';
                    submitBtn.classList.add('bg-gradient-warning');
                    submitBtn.classList.remove('bg-gradient-primary');
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i>Confirmer la création';
                    submitBtn.classList.add('bg-gradient-primary');
                    submitBtn.classList.remove('bg-gradient-warning');
                }
            });
        }
    });
</script>
@endpush
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
