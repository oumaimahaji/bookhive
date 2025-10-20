@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Create New User</h6>
                        <a href="{{ route('user-management') }}" class="btn btn-secondary btn-sm">Back to Users</a>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <form action="{{ route('users.store') }}" method="POST" role="form" id="createUserForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Enter full name"
                                       required 
                                       minlength="2"
                                       maxlength="255">
                                <div class="form-text">
                                    <span id="nameCount">0</span>/255 characters
                                    <span id="nameStatus" class="ms-2"></span>
                                </div>
                                @error('name') 
                                    <div class="text-danger text-xs">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Enter email address"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-envelope me-1"></i>Must be a valid email address
                                    <span id="emailStatus" class="ms-2"></span>
                                </div>
                                @error('email') 
                                    <div class="text-danger text-xs">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter password"
                                       required
                                       minlength="8">
                                <div class="form-text">
                                    <span id="passwordCount">0</span>/8 characters minimum
                                    <span id="passwordStatus" class="ms-2"></span>
                                </div>
                                @error('password') 
                                    <div class="text-danger text-xs">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm password"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-shield-alt me-1"></i>Re-enter password for confirmation
                                    <span id="passwordConfirmStatus" class="ms-2"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="moderator" {{ old('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                <option value="club_manager" {{ old('role') == 'club_manager' ? 'selected' : '' }}>Club Manager</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            <div class="form-text">
                                <i class="fas fa-user-tag me-1"></i>Select user role
                                <span id="roleStatus" class="ms-2"></span>
                            </div>
                            @error('role') 
                                <div class="text-danger text-xs">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Phone (Optional) -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}" 
                                   placeholder="Enter phone number (optional)"
                                   pattern="[0-9+\-\s()]{10,20}">
                            <div class="form-text">
                                <i class="fas fa-phone me-1"></i>Optional phone number (10-20 digits)
                                <span id="phoneStatus" class="ms-2"></span>
                            </div>
                            @error('phone') 
                                <div class="text-danger text-xs">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('user-management') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary" id="submitBtn">
                                <i class="fas fa-plus me-1"></i>Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control.is-valid { border-color: #28a745; }
.form-control.is-invalid { border-color: #dc3545; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-muted { color: #6c757d !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const roleSelect = document.getElementById('role');
    const phoneInput = document.getElementById('phone');
    const submitBtn = document.getElementById('submitBtn');

    const nameCount = document.getElementById('nameCount');
    const passwordCount = document.getElementById('passwordCount');
    const nameStatus = document.getElementById('nameStatus');
    const emailStatus = document.getElementById('emailStatus');
    const passwordStatus = document.getElementById('passwordStatus');
    const passwordConfirmStatus = document.getElementById('passwordConfirmStatus');
    const roleStatus = document.getElementById('roleStatus');
    const phoneStatus = document.getElementById('phoneStatus');

    function updateNameCount() {
        const length = nameInput.value.length;
        nameCount.textContent = length;
        if (length < 2) {
            nameCount.className = 'text-danger';
            nameStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Too short</span>';
        } else if (length > 200) {
            nameCount.className = 'text-warning';
            nameStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Long</span>';
        } else {
            nameCount.className = 'text-success';
            nameStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Good</span>';
        }
    }

    function updatePasswordCount() {
        const length = passwordInput.value.length;
        passwordCount.textContent = length;
        if (length < 8) {
            passwordCount.className = 'text-danger';
            passwordStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Too short</span>';
        } else if (length < 12) {
            passwordCount.className = 'text-warning';
            passwordStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Fair</span>';
        } else {
            passwordCount.className = 'text-success';
            passwordStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Strong</span>';
        }
    }

    function validateName() {
        const len = nameInput.value.length;
        const isValid = len >= 2 && len <= 255;
        nameInput.classList.toggle('is-valid', isValid);
        nameInput.classList.toggle('is-invalid', !isValid && nameInput.value);
        return isValid;
    }

    function validateEmail() {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = regex.test(emailInput.value);
        emailInput.classList.toggle('is-valid', isValid);
        emailInput.classList.toggle('is-invalid', !isValid && emailInput.value);
        emailStatus.innerHTML = isValid ? '<span class="text-success"><i class="fas fa-check"></i> Valid</span>' 
                                         : (emailInput.value ? '<span class="text-danger"><i class="fas fa-times"></i> Invalid</span>' : '');
        return isValid;
    }

    function validatePassword() {
        const isValid = passwordInput.value.length >= 8;
        passwordInput.classList.toggle('is-valid', isValid);
        passwordInput.classList.toggle('is-invalid', !isValid && passwordInput.value);
        return isValid;
    }

    function validatePasswordConfirm() {
        const isValid = passwordInput.value === passwordConfirmInput.value && passwordInput.value.length >= 8;
        passwordConfirmInput.classList.toggle('is-valid', isValid);
        passwordConfirmInput.classList.toggle('is-invalid', !isValid && passwordConfirmInput.value);
        passwordConfirmStatus.innerHTML = isValid ? '<span class="text-success"><i class="fas fa-check"></i> Matches</span>' 
                                                 : (passwordConfirmInput.value ? '<span class="text-danger"><i class="fas fa-times"></i> No match</span>' : '');
        return isValid;
    }

    function validateRole() {
        const isValid = roleSelect.value !== '';
        roleSelect.classList.toggle('is-valid', isValid);
        roleSelect.classList.toggle('is-invalid', !isValid);
        roleStatus.innerHTML = isValid ? '<span class="text-success"><i class="fas fa-check"></i> Selected</span>' 
                                       : '<span class="text-danger"><i class="fas fa-times"></i> Required</span>';
        return isValid;
    }

    function validatePhone() {
        const val = phoneInput.value;
        const isValid = val === '' || /^[0-9+\-\s()]{10,20}$/.test(val);
        phoneInput.classList.toggle('is-valid', isValid && val !== '');
        phoneInput.classList.toggle('is-invalid', !isValid && val !== '');
        phoneStatus.innerHTML = val === '' ? '' : (isValid ? '<span class="text-success"><i class="fas fa-check"></i> Valid</span>' 
                                                            : '<span class="text-danger"><i class="fas fa-times"></i> Invalid</span>');
        return isValid;
    }

    function updateSubmitButton() {
        const isFormValid = validateName() && validateEmail() && validatePassword() && validatePasswordConfirm() && validateRole();
        submitBtn.disabled = !isFormValid;
        submitBtn.className = isFormValid ? 'btn bg-gradient-primary' : 'btn btn-secondary';
        submitBtn.innerHTML = isFormValid ? '<i class="fas fa-plus me-1"></i>Create User ✓' : '<i class="fas fa-plus me-1"></i>Create User';
    }

    [nameInput, emailInput, passwordInput, passwordConfirmInput].forEach(el => el.addEventListener('input', updateSubmitButton));
    roleSelect.addEventListener('change', updateSubmitButton);
    phoneInput.addEventListener('input', validatePhone);

    form.addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            alert('Please correct the errors in the form before submitting.');
        }
    });

    nameInput.focus();
    updateNameCount();
    updatePasswordCount();
    updateSubmitButton();
});
</script>
@endsection
