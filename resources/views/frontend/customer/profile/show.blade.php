@extends('frontend.customer.layout.app')

@push('styles')
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

        .error-text {
            color: #dc3545;
            font-size: 0.875em;
        }

        /* Highlight invalid fields */
        label.error {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        input.error, select.error, textarea.error {
            border-color: #dc3545;
        }
    </style>
@endpush

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
                            <form
                                action="{{ route('customer.profile.update') }}"
                                method="POST"
                                enctype="multipart/form-data"
                                id="profileForm"
                                novalidate>
                                @csrf

                                {{-- Avatar Upload --}}
                                <div class="row justify-content-center mb-4">
                                    <div class="col-auto">
                                        <div class="position-relative avatar-wrapper">
                                            <!-- Avatar Image -->
                                            <img
                                                src="{{ $user->avatar
                                                    ? asset('storage/' . $user->avatar)
                                                    : 'https://img.freepik.com/premium-vector/vector-flat-illustration-grayscale-avatar-user-profile-person-icon-gender-neutral-silhouette-profile-picture-suitable-social-media-profiles-icons-screensavers-as-templatex9xa_719432-875.jpg?semt=ais_hybrid&w=740' }}"
                                                alt="User Avatar"
                                                id="avatarPreview"
                                                class="rounded-circle avatar-img">

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
                                                onchange="previewAvatar(this)">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Full Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            Username <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="name"
                                            id="name"
                                            value="{{ old('name', $user->name) }}"
                                            readonly disabled>
                                        <div class="invalid-feedback">Please enter your username.</div>
                                    </div>

                                    <!-- Email Address -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            name="email"
                                            id="email"
                                            value="{{ old('email', $user->email) }}"
                                            readonly disabled>
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">
                                            Phone Number
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ old('country_code', $user->country_code) }}</span>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="phone"
                                                id="phone"
                                                value="{{ old('phone', $user->phone) }}"
                                                readonly disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <h3 class="my-3"><u>Billing Info</u></h3>

                                    {{-- Billing Name --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_name" class="form-label">
                                            Billing Name <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="billing_name"
                                            id="billing_name"
                                            value="{{ old('billing_name', optional($user->detail)->billing_name) }}"
                                            required>
                                    </div>

                                    {{-- State --}}
                                    <div class="col-lg-6 mb-3">
                                        <label for="state">State <span class="text-danger">*</span></label>
                                        <select
                                            name="state"
                                            class="form-control"
                                            id="state"
                                            required>
                                            <option value="">Select State</option>
                                            @php
                                                $selectedState = old('state', optional($user->detail)->state);
                                            @endphp
                                            <option
                                                value="Uttarakhand"
                                                {{ $selectedState === 'Uttarakhand' ? 'selected' : '' }}>
                                                Uttarakhand
                                            </option>
                                            <option
                                                value="Rajasthan"
                                                {{ $selectedState === 'Rajasthan' ? 'selected' : '' }}>
                                                Rajasthan
                                            </option>
                                            <option
                                                value="Punjab"
                                                {{ $selectedState === 'Punjab' ? 'selected' : '' }}>
                                                Punjab
                                            </option>
                                        </select>
                                    </div>

                                    {{-- City --}}
                                    <div class="col-lg-6 mb-3">
                                        <label for="city">City <span class="text-danger">*</span></label>
                                        <select
                                            name="city"
                                            class="form-control"
                                            id="city"
                                            required>
                                            <option value="">Select City</option>
                                            @php
                                                $selectedCity = old('city', optional($user->detail)->city);
                                            @endphp
                                            <option
                                                value="Dehradun"
                                                {{ $selectedCity === 'Dehradun' ? 'selected' : '' }}>
                                                Dehradun
                                            </option>
                                            <option
                                                value="Nanital"
                                                {{ $selectedCity === 'Nanital' ? 'selected' : '' }}>
                                                Nanital
                                            </option>
                                            <option
                                                value="Auli"
                                                {{ $selectedCity === 'Auli' ? 'selected' : '' }}>
                                                Auli
                                            </option>
                                        </select>
                                    </div>

                                    {{-- Pin Code --}}
                                    <div class="col-lg-6 mb-3">
                                        <label for="pin_code" class="form-label">
                                            Pin Code <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="pin_code"
                                            id="pin_code"
                                            value="{{ old('pin_code', optional($user->detail)->pin_code) }}"
                                            placeholder="2XXXXX"
                                            required
                                            maxlength="10">
                                    </div>

                                    {{-- Address --}}
                                    <div class="col-lg-12 mb-3">
                                        <label for="address" class="form-label">
                                            Address <span class="text-danger">*</span>
                                        </label>
                                        <textarea
                                            name="address"
                                            class="form-control"
                                            id="address"
                                            cols="30"
                                            rows="4"
                                            placeholder="Indi Road"
                                            required
                                            maxlength="500">{{ old('address', optional($user->detail)->address) }}</textarea>
                                    </div>

                                    {{-- PAN Card --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="pan_card" class="form-label">
                                            PAN Card
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="pan_card"
                                            id="pan_card"
                                            value="{{ old('pan_card', optional($user->detail)->pan_card) }}"
                                            maxlength="20">
                                    </div>

                                    {{-- GST Number --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="gst_number" class="form-label">
                                            GST Number
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="gst_number"
                                            id="gst_number"
                                            value="{{ old('gst_number', optional($user->detail)->gst_number) }}"
                                            maxlength="20">
                                    </div>
                                </div>

                                <div class="profile-actions text-end mt-4">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Changes</button>
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
                            <form
                                action="{{ route('customer.password.update') }}"
                                method="POST"
                                id="passwordForm"
                                novalidate>
                                @csrf

                                <!-- Current Password -->
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">
                                        Current Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input
                                            type="password"
                                            class="form-control"
                                            name="current_password"
                                            id="current_password"
                                            required>
                                        <span
                                            class="input-group-text toggle-password"
                                            data-target="current_password"
                                            style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- New Password -->
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">
                                        New Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input
                                            type="password"
                                            class="form-control"
                                            name="new_password"
                                            id="new_password"
                                            required>
                                        <span
                                            class="input-group-text toggle-password"
                                            data-target="new_password"
                                            style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Confirm New Password -->
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">
                                        Confirm New Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input
                                            type="password"
                                            class="form-control"
                                            name="new_password_confirmation"
                                            id="new_password_confirmation"
                                            required>
                                        <span
                                            class="input-group-text toggle-password"
                                            data-target="new_password_confirmation"
                                            style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
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
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script>
        // -----------------------------
        // 1) Toggle password visibility
        // -----------------------------
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

        // -----------------------------
        // 2) Preview selected avatar image
        // -----------------------------
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // -----------------------------
        // 3) jQuery Validation rules
        // -----------------------------
        $(document).ready(function() {
            // --- custom strongPassword rule ---
            $.validator.addMethod('strongPassword', function(value, element) {
                return this.optional(element)
                    || /[A-Z]/.test(value)         // has uppercase letter
                    && /[a-z]/.test(value)        // has lowercase letter
                    && /[0-9]/.test(value)        // has digit
                    && /[\!\@\#\$\%\^\&\*\(\)\_\+\.\,\;\:]/.test(value) // special char
                    && value.length >= 8;         // at least 8 characters
            }, 'Password must be at least 8 characters long and contain uppercase, lowercase, number & special character.');

            // --- Validate Profile Form ---
            $('#profileForm').validate({
                ignore: [], // do not ignore hidden fields (for file upload)
                rules: {
                    billing_name: {
                        required: true,
                        maxlength: 255
                    },
                    state: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    pin_code: {
                        required: true,
                        maxlength: 10
                    },
                    address: {
                        required: true,
                        maxlength: 500
                    },
                    pan_card: {
                        maxlength: 20
                    },
                    gst_number: {
                        maxlength: 20
                    }
                },
                messages: {
                    billing_name: {
                        required: "Billing Name is required.",
                        maxlength: "Billing Name cannot exceed 255 characters."
                    },
                    state: {
                        required: "Please select a state."
                    },
                    city: {
                        required: "Please select a city."
                    },
                    pin_code: {
                        required: "Pin Code is required.",
                        maxlength: "Pin Code cannot exceed 10 characters."
                    },
                    address: {
                        required: "Address is required.",
                        maxlength: "Address cannot exceed 500 characters."
                    },
                    pan_card: {
                        maxlength: "PAN Card cannot exceed 20 characters."
                    },
                    gst_number: {
                        maxlength: "GST Number cannot exceed 20 characters."
                    }
                },
                errorClass: 'error',
                errorElement: 'label',
                highlight: function(element) {
                    $(element).addClass('error');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error');
                },
                submitHandler: function(form) {
                    // Disable and change the submit button to “Processing…”:
                    var $btn = $('#submitBtn').prop('disabled', true).text('Processing...');
                    form.submit();
                }
            });

            // --- Validate Password Form ---
            $('#passwordForm').validate({
                rules: {
                    current_password: {
                        required: true
                    },
                    new_password: {
                        required: true,
                        strongPassword: true
                    },
                    new_password_confirmation: {
                        required: true,
                        equalTo: '#new_password'
                    }
                },
                messages: {
                    current_password: {
                        required: "Please enter your current password."
                    },
                    new_password: {
                        required: "Please enter a new password."
                    },
                    new_password_confirmation: {
                        required: "Please confirm your new password.",
                        equalTo: "Passwords do not match."
                    }
                },
                errorClass: 'error',
                errorElement: 'label',
                highlight: function(element) {
                    $(element).addClass('error');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error');
                },
                submitHandler: function(form) {
                    // Disable and change the submit button to “Processing…”:
                    var $btn = $(form).find('button[type="submit"]');
                    $btn.prop('disabled', true).text('Processing...');
                    form.submit();
                }
            });
        });
    </script>
@endpush