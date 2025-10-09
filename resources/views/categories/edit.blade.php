@extends('layouts.user_type.auth')

@section('content')

<form action="{{ route('categories.update', $category) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Nom:</label>
    <input type="text" name="nom" value="{{ old('nom', $category->nom) }}"><br>

    <label>Description:</label>
    <textarea name="description">{{ old('description', $category->description) }}</textarea><br><br>

    <button type="submit">Modifier</button>
</form>
@endsection
