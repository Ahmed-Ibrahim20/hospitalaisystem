@extends('layouts.app')

@section('title', 'Manage Users ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 text-primary fw-bold">
                            Add User
                        </h3>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-lg rounded-pill">
                            <i class="fas fa-users me-2"></i> View Users
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form id="userForm" action="{{ route('users.store') }}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label text-secondary">Name</label>
                            <input type="text" class="form-control rounded-pill" id="name" name="name" placeholder="Enter name" required>
                            <div class="invalid-feedback" id="nameError">The name field is required.</div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label text-secondary">Email</label>
                            <input type="email" class="form-control rounded-pill" id="email" name="email" placeholder="Enter email" required>
                            <div class="invalid-feedback" id="emailError">The email field is required.</div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label text-secondary">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control rounded-end-pill" id="password" name="password" placeholder="Enter a strong password" required>
                                <button type="button" class="btn btn-light rounded-start-pill border-end-0" id="togglePassword">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="passwordError">The password field is required.</div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label text-secondary">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control rounded-end-pill" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
                                <button type="button" class="btn btn-light rounded-start-pill border-end-0" id="togglePasswordConfirmation">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="passwordConfirmationError">Password confirmation does not match.</div>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label text-secondary">Role</label>
                            <select class="form-control rounded-pill" id="role" name="role" required>
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select>
                            <div class="invalid-feedback" id="roleError">The role field is required.</div>
                        </div>

                        <!-- Upload Image -->
                        <div class="mb-3">
                            <label class="form-label text-secondary">Profile Picture</label>
                            <div class="image-upload-wrapper text-center p-4 border border-2 border-dashed rounded-4">
                                <input type="file" id="image" name="image" accept="image/*" class="d-none">
                                <label for="image" class="btn btn-outline-primary rounded-pill px-4">
                                    <i class="fas fa-upload me-2"></i> Choose Image
                                </label>

                                <div id="imagePreview" class="mt-3 d-none">
                                    <img src="" alt="Preview" class="img-fluid rounded shadow-sm mb-2" style="max-height: 200px;">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-danger rounded-pill" id="removeImage">
                                        <i class="fas fa-trash-alt me-1"></i> Remove Image
                                    </button>
                                </div>
                            </div>
                            <div class="invalid-feedback d-block" id="imageError" style="display:none;">Image upload is required.</div>
                        </div>

                        <!-- Save Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                <i class="fas fa-save me-2"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Image Preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = imagePreview.querySelector('img');
    const removeBtn = document.getElementById('removeImage');

    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    removeBtn.addEventListener('click', function() {
        imageInput.value = "";
        previewImg.src = "";
        imagePreview.classList.add('d-none');
    });

    // Show/Hide Password
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    });

    document.getElementById('togglePasswordConfirmation').addEventListener('click', function() {
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const icon = this.querySelector('i');
        if (passwordConfirmationInput.type === 'password') {
            passwordConfirmationInput.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            passwordConfirmationInput.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    });
</script>
@endsection
