@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Edit User</h6>
                        <a href="{{ route('user-management') }}" class="btn btn-secondary btn-sm">Back to Users</a>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" role="form">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name') 
                                <div class="text-danger text-xs">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email') 
                                <div class="text-danger text-xs">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone', $user->phone) }}">
                            @error('phone') 
                                <div class="text-danger text-xs">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                                <option value="club_manager" {{ old('role', $user->role) == 'club_manager' ? 'selected' : '' }}>Club Manager</option>
                                <option value="moderator" {{ old('role', $user->role) == 'moderator' ? 'selected' : '' }}>Moderator</option>
                            </select>
                            @error('role') 
                                <div class="text-danger text-xs">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('user-management') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
