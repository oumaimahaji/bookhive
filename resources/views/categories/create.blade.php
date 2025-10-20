@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid" style="padding-top: 0.1rem;">
    <div class="row">
        <div class="col-12">
            <div class="card mb-0" style="border: 1px solid #dee2e6;">
                <div class="card-header pb-0" style="padding: 0.4rem 1rem;">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <h5 class="mb-0" style="font-size: 1.1rem;">Add New Category</h5>
                    </div>
                </div>
                <div class="card-body px-3 pt-1 pb-2" style="padding: 0.6rem 1rem;">
                    {{-- Error display --}}
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-2 py-1" role="alert" style="font-size: 0.8rem;">
                        <strong class="small">Please correct the following errors:</strong>
                        <ul class="mb-0 mt-1 small" style="margin-left: 1rem;">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.7rem;"></button>
                    </div>
                    @endif

                    <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label fw-bold small mb-1">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="categoryName" class="form-control form-control-sm @error('nom') is-invalid @enderror"
                                placeholder="Enter category name"
                                value="{{ old('nom') }}"
                                maxlength="100"
                                pattern="[A-Za-zÀ-ÿ0-9\s\-_]+"
                                title="Only letters, numbers, spaces, hyphens and underscores are allowed"
                                required>

                            {{-- Real-time validation message --}}
                            <div id="validationMessage" class="mt-1 small"></div>

                            @error('nom')
                            <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted small">
                                Maximum 100 characters. Only letters, numbers, spaces, hyphens and underscores allowed.
                            </small>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small mb-1">Description</label>
                            <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror"
                                rows="2"
                                placeholder="Enter description"
                                maxlength="500">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted small">
                                Maximum 500 characters. Currently: <span id="charCount">0</span> characters
                            </small>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" id="submitBtn" class="btn bg-gradient-primary btn-sm py-1">Add Category</button>
                            <a href="{{ route('categories.index') }}" class="btn bg-gradient-secondary btn-sm py-1">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('categoryForm');
        const descriptionField = document.querySelector('textarea[name="description"]');
        const charCount = document.getElementById('charCount');
        const categoryNameField = document.getElementById('categoryName');
        const validationMessage = document.getElementById('validationMessage');
        const submitBtn = document.getElementById('submitBtn');
        let validationTimeout;

        // ✅ NEW: Real-time category name validation
        categoryNameField.addEventListener('input', function() {
            const categoryName = this.value.trim();

            // Clear previous message
            validationMessage.textContent = '';
            validationMessage.className = 'mt-1 small';

            // Disable button during validation
            submitBtn.disabled = true;

            // Cancel previous validation
            clearTimeout(validationTimeout);

            // Wait for user to stop typing (500ms)
            validationTimeout = setTimeout(() => {
                if (categoryName.length < 2) {
                    validationMessage.textContent = 'Name must be at least 2 characters long';
                    validationMessage.className = 'mt-1 small text-warning';
                    submitBtn.disabled = true;
                    return;
                }

                // Call validation API
                fetch('{{ route("categories.validate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            nom: categoryName
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.valid) {
                            validationMessage.textContent = '✓ Name available';
                            validationMessage.className = 'mt-1 small text-success';
                            submitBtn.disabled = false;
                        } else {
                            validationMessage.textContent = '❌ ' + (data.errors.nom ? data.errors.nom[0] : 'Validation error');
                            validationMessage.className = 'mt-1 small text-danger';
                            submitBtn.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        validationMessage.textContent = 'Validation error';
                        validationMessage.className = 'mt-1 small text-danger';
                        submitBtn.disabled = false;
                    });
            }, 500); // 500ms delay after typing
        });

        // Character counter for description
        if (descriptionField && charCount) {
            // Initial count
            charCount.textContent = descriptionField.value.length;

            // Update count on input
            descriptionField.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length;

                // Warning when approaching limit
                if (length > 450) {
                    charCount.style.color = 'red';
                    charCount.style.fontWeight = 'bold';
                } else {
                    charCount.style.color = '';
                    charCount.style.fontWeight = '';
                }

                // Trim if exceeds max length
                if (length > 500) {
                    this.value = this.value.substring(0, 500);
                    charCount.textContent = 500;
                }
            });
        }

        // Category name validation (cleaning)
        if (categoryNameField) {
            categoryNameField.addEventListener('input', function() {
                // Remove special characters except allowed ones
                this.value = this.value.replace(/[^A-Za-zÀ-ÿ0-9\s\-_]/g, '');

                // Trim if exceeds max length
                if (this.value.length > 100) {
                    this.value = this.value.substring(0, 100);
                }
            });
        }

        // Form validation before submission
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const errors = [];

            // Name validation
            const nomValue = categoryNameField.value.trim();
            if (!nomValue) {
                isValid = false;
                categoryNameField.classList.add('is-invalid');
                errors.push('Category name is required');
            } else if (nomValue.length < 2) {
                isValid = false;
                categoryNameField.classList.add('is-invalid');
                errors.push('Category name must be at least 2 characters long');
            } else if (!/^[A-Za-zÀ-ÿ0-9\s\-_]+$/.test(nomValue)) {
                isValid = false;
                categoryNameField.classList.add('is-invalid');
                errors.push('Category name contains invalid characters');
            } else {
                categoryNameField.classList.remove('is-invalid');
            }

            // Description validation
            const descValue = descriptionField.value.trim();
            if (descValue.length > 500) {
                isValid = false;
                descriptionField.classList.add('is-invalid');
                errors.push('Description cannot exceed 500 characters');
            } else {
                descriptionField.classList.remove('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();

                // Display errors
                let errorMessage = 'Please correct the following errors:\n';
                errors.forEach(error => {
                    errorMessage += `• ${error}\n`;
                });
                alert(errorMessage);
            }
        });

        function showFieldError(field, message) {
            // Remove existing error feedback
            const existingFeedback = field.parentNode.querySelector('.field-error-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }

            // Add new error feedback
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error-feedback text-danger small mt-1';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }

        // Auto-remove error feedback when user starts typing
        categoryNameField.addEventListener('input', function() {
            const errorFeedback = this.parentNode.querySelector('.field-error-feedback');
            if (errorFeedback) {
                errorFeedback.remove();
            }
            this.classList.remove('is-invalid');
        });

        descriptionField.addEventListener('input', function() {
            const errorFeedback = this.parentNode.querySelector('.field-error-feedback');
            if (errorFeedback) {
                errorFeedback.remove();
            }
            this.classList.remove('is-invalid');
        });
    });
</script>

<style>
    .field-error-feedback {
        font-size: 0.7em;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    /* Maximum spacing reduction */
    .card {
        margin-top: 0;
        margin-bottom: 0;
    }

    .card-body {
        padding: 0.5rem 1rem !important;
    }

    /* Remove extra spacing */
    .container-fluid {
        min-height: auto;
        padding-top: 0.1rem !important;
    }

    /* Reduce card header spacing */
    .card-header {
        padding: 0.4rem 1rem !important;
    }

    /* Reduce label margins */
    .form-label {
        margin-bottom: 0.3rem !important;
    }

    /* Reduce alert size */
    .alert {
        margin-bottom: 0.8rem !important;
        padding: 0.4rem 0.8rem !important;
    }
</style>

@endsection