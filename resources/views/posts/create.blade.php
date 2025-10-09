@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un Post</h5>
                    <a href="{{ route('posts.index') }}" class="btn bg-gradient-primary btn-sm mb-0">Retour à la liste</a>
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

                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Sélectionnez --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" name="titre" value="{{ old('titre') }}" class="form-control" placeholder="Entrez le titre du post" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contenu</label>
                            <textarea name="contenu" class="form-control" rows="6" placeholder="Entrez le contenu" required>{{ old('contenu') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" value="{{ old('date') }}" class="form-control" required>
                        </div>

                        <button type="submit" class="btn bg-gradient-primary">Ajouter</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection