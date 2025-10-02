@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ route('posts.create') }}" class="btn btn-primary">Add New Post</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Inline Edit Form --}}
        @if(isset($editPost))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Edit Post: {{ $editPost->titre }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('posts.update', $editPost->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="user_id" class="form-label">User</label>
                                    <select name="user_id" class="form-control" required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $editPost->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', $editPost->date) }}" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="titre" class="form-label">Title</label>
                                    <input type="text" name="titre" class="form-control" value="{{ old('titre', $editPost->titre) }}" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="contenu" class="form-label">Content</label>
                                    <textarea name="contenu" class="form-control" rows="4" required>{{ old('contenu', $editPost->contenu) }}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Update Post</button>
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Posts Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Posts Table</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">User</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comments</th>
                                        <th class="text-secondary opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($posts as $post)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $post->titre }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ Str::limit($post->contenu, 50) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $post->user->name ?? 'N/A' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">{{ $post->date }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">{{ $post->comments->count() }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('posts.index', ['edit' => $post->id]) }}"
                                               class="text-secondary font-weight-bold text-xs me-2">Edit</a>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-danger font-weight-bold text-xs border-0 bg-transparent" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-3">No posts found.</td>
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