@extends('layouts.master')

@section('content')
<section class="content-body">
    <div class="container-fluid">
        <div class="row">
            <!-- Left Column: Profile Details + Address -->
            <div class="col-lg-8">
                <div class="profile-card card h-auto">
                    <div class="card-header">
                        <h4 class="card-title fs-20 mb-0">Profile Details</h4>
                    </div>
                    <div class="card-body">
                        {{-- Avatar Upload --}}
                        <div class="row justify-content-center mb-4">
                            <div class="col-auto">
                                <div class="position-relative avatar-wrapper">
                                    <!-- Avatar Image -->
                                    <img
                                        src="https://img.freepik.com/premium-vector/vector-flat-illustration-grayscale-avatar-user-profile-person-icon-gender-neutral-silhouette-profile-picture-suitable-social-media-profiles-icons-screensavers-as-templatex9xa_719432-875.jpg?semt=ais_hybrid&w=740"
                                        alt="User Avatar"
                                        id="avatarPreview"
                                        class="rounded-circle avatar-img"
                                    >
                                    <!-- Camera Icon Overlay -->
                                    <label for="avatar" class="avatar-edit-icon">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                    <input
                                        type="file"
                                        class="d-none"
                                        name="avatar"
                                        id="avatar"
                                        accept="image/*"
                                        onchange="previewAvatar(this)"
                                    >
                                </div>
                            </div>
                        </div>

                        <form action="#" method="POST" enctype="multipart/form-data" id="profileForm" novalidate>
                            @csrf

                            <div class="row">
                                <!-- Full Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        name="fname" 
                                        id="fname" 
                                        value="John "
                                        required
                                    >
                                    <div class="invalid-feedback">Please enter your full name.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        name="lname" 
                                        id="lname" 
                                        value=" Doe"
                                        required
                                    >
                                    <div class="invalid-feedback">Please enter your full name.</div>
                                </div>
                                <!-- Email Address -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input 
                                        type="email" 
                                        class="form-control" 
                                        name="email" 
                                        id="email" 
                                        value="john.doe@example.com"
                                        required
                                    >
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                           
                                <!-- Phone Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        name="phone" 
                                        id="phone" 
                                        value="+1 555 123 4567"
                                    >
                                </div>
                                <!-- Address Line 1 -->
                                <div class="col-md-6 mb-3">
                                    <label for="address_line1" class="form-label">Address Line 1</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        name="address_line1" 
                                        id="address_line1" 
                                        placeholder="123 Main St"
                                    >
                                </div>
                           
                                <!-- Address Line 2 -->
                                <div class="col-md-6 mb-3">
                                    <label for="address_line2" class="form-label">Address Line 2</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        name="address_line2" 
                                        id="address_line2" 
                                        placeholder="Apt, Suite, etc. (optional)"
                                    >
                                </div>
                                <!-- City -->
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        name="city" 
                                        id="city" 
                                        placeholder="e.g. New York"
                                    >
                                </div>
                            
                                <!-- State/Province -->
                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">State/Province</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        name="state" 
                                        id="state" 
                                        placeholder="e.g. NY"
                                    >
                                </div>
                                <!-- ZIP/Postal Code -->
                                <div class="col-md-6 mb-3">
                                    <label for="zip" class="form-label">ZIP/Postal Code</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        name="zip" 
                                        id="zip" 
                                        placeholder="e.g. 10001"
                                    >
                                </div>
                            </div>

                            <div class="profile-actions text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <button type="reset" class="btn btn-light ms-2">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Change Password -->
            <div class="col-lg-4">
                <div class="profile-card card h-auto">
                    <div class="card-header">
                        <h4 class="card-title fs-20 mb-0">Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST" id="passwordForm" novalidate>
                            @csrf

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        name="current_password" 
                                        id="current_password" 
                                        required
                                    >
                                    <span class="input-group-text toggle-password" data-target="current_password" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                    <div class="invalid-feedback">Please enter your current password.</div>
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        name="new_password" 
                                        id="new_password" 
                                        required
                                    >
                                    <span class="input-group-text toggle-password" data-target="new_password" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                    <div class="invalid-feedback">Please enter a new password.</div>
                                </div>
                            </div>

                            <!-- Confirm New Password -->
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        name="new_password_confirmation" 
                                        id="new_password_confirmation" 
                                        required
                                    >
                                    <span class="input-group-text toggle-password" data-target="new_password_confirmation" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                    <div class="invalid-feedback">Passwords do not match.</div>
                                </div>
                            </div>

                            <div class="profile-actions">
                                <button type="submit" class="btn w-100 btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Custom Styles --}}
<style>
    /* Avatar wrapper size */
    .avatar-wrapper {
        width: 140px;
        height: 140px;
    }

    /* Avatar image styling */
    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 4px solid #dee2e6;
        transition: 0.3s ease-in-out;
    }

    /* Camera icon overlay styling */
    .avatar-edit-icon {
        position: absolute;
        bottom: 0;
        width: 36px;
        height: 36px;
        line-height: 32px;
        text-align: center;        
        right: 0;
        background-color: #ffffff;
        border: 2px solid #dee2e6;
        border-radius: 50%;
       
        cursor: pointer;
       
        transition: background-color 0.2s;
    }
    .avatar-edit-icon:hover {
        background-color: #f8f9fa;
    }
    .avatar-edit-icon i {
        color: #495057;
        font-size: 16px;
    }
</style>

{{-- Custom Scripts --}}
<script>
    // Bootstrap form validation
    (function () {
        'use strict';
        const profileForm = document.getElementById('profileForm');
        const passwordForm = document.getElementById('passwordForm');

        [profileForm, passwordForm].forEach((form) => {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Toggle password visibility using input group
    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Preview selected avatar image
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
