@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container py-4">
        <h4>Résumé des avis pour : <span class="text-primary">{{ $book->titre }}</span></h4>
        <div class="card mt-3">
            <div class="card-body">
                <p>{{ $summary }}</p>
            </div>
        </div>
        <a href="{{ route('reviews.index') }}" class="btn btn-secondary mt-3">⬅ Retour</a>
    </div>
</main>
@endsection



