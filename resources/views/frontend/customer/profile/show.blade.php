@extends('frontend.customer.layout.app')

@push('styles')
    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/css/intlTelInput.css"/>
    <style>
        /* Avatar wrapper */
        .avatar-wrapper { width: 140px; height: 140px; border: 4px solid #dee2e6; border-radius: 50%;}
        .avatar-img { width: 100%; height: 100%; object-fit: cover;  transition: .3s; }
        .avatar-edit-icon { position: absolute; bottom: 0; right: 0; width: 36px; height: 36px; line-height: 32px; text-align: center; background: #fff; border: 2px solid #dee2e6; border-radius: 50%; cursor: pointer; transition: .2s; }
        .avatar-edit-icon:hover { background-color: #f8f9fa; }
        .error-text { color: #dc3545; font-size: .875em; }
        label.error, input.error, select.error, textarea.error { color: #dc3545; }
        .password-toggle  {
    position:absolute; top:50%; right:12px; transform: translateY(-50%);
    cursor:pointer; color:#718096; font-size:1.1rem;
}
    </style>
@endpush

@section('content')
    <section class="content-body">
        <div class="container-fluid">
            <div class="row">
                <!-- Left Column: Profile Details + Address + Phone -->
                <div class="col-lg-8">
                    <div class="profile-card card h-auto">
                        <div class="card-header">
                            <h4 class="card-title fs-20 mb-0">Profile Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm" novalidate>
                                @csrf

                                <!-- Avatar Upload -->
                                <div class="row justify-content-center mb-4">
                                    <div class="col-auto position-relative avatar-wrapper">
                                        <img
                                            src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/user.png') }}"
                                            alt="User Avatar"
                                            id="avatarPreview"
                                            class="rounded-circle avatar-img">

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

                                <div class="row">
                                    <!-- Username -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="name"
                                            id="name"
                                            value="{{ old('name', $user->name) }}"
                                            readonly disabled>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            name="email"
                                            id="email"
                                            value="{{ old('email', $user->email) }}"
                                            readonly disabled>
                                    </div>

                                    <!-- Phone Number with intl-tel-input -->
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', $user->country_code) }}">
                                        <input type="hidden" name="country" id="country" value="{{ old('country', $user->country) }}">
                                        <input
                                            type="tel"
                                            class="form-control"
                                            name="phone"
                                            id="phone"
                                            value="{{ old('phone', $user->phone) }}"
                                            required>
                                        @error('phone')<div class="error-text">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <hr><h4>Billing Info</h4><hr>
                                <div class="row">
                                    <!-- Billing Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_name" class="form-label">Billing Name <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="billing_name"
                                            id="billing_name"
                                            value="{{ old('billing_name', optional($user->detail)->billing_name) }}"
                                            required>
                                        @error('billing_name')<div class="error-text">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                        <select
                                            name="state"
                                            id="state"
                                            class="form-control form-select"
                                            required
                                            disabled>
                                            <option value="">Select State</option>
                                        </select>
                                        @error('state')<div class="error-text">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- City -->
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                        <select
                                            name="city"
                                            id="city"
                                            class="form-control form-select"
                                            required
                                            disabled>
                                            <option value="">Select City</option>
                                        </select>
                                        @error('city')<div class="error-text">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Pin Code -->
                                    <div class="col-md-6 mb-3">
                                        <label for="pin_code" class="form-label">Pin Code <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="pin_code"
                                            id="pin_code"
                                            value="{{ old('pin_code', optional($user->detail)->pin_code) }}"
                                            required
                                            maxlength="10">
                                        @error('pin_code')<div class="error-text">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Address -->
                                    <div class="col-lg-12 mb-3">
                                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                        <textarea
                                            name="address"
                                            class="form-control"
                                            id="address"
                                            rows="4"
                                            required
                                            maxlength="500">{{ old('address', optional($user->detail)->address) }}</textarea>
                                        @error('address')<div class="error-text">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- PAN Card -->
                                    <div class="col-md-6 mb-3">
                                        <label for="pan_card" class="form-label">PAN Card (Optional)</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="pan_card"
                                            id="pan_card"
                                            value="{{ old('pan_card', optional($user->detail)->pan_card) }}"
                                            maxlength="20">
                                        @error('pan_card')<div class="error-text">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- GST Number -->
                                    <div class="col-md-6 mb-3">
                                        <label for="gst_number" class="form-label">GST Number (Optional)</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="gst_number"
                                            id="gst_number"
                                            value="{{ old('gst_number', optional($user->detail)->gst_number) }}"
                                            maxlength="20">
                                        @error('gst_number')<div class="error-text">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="profile-actions text-end mt-4">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="col-lg-4">
                    <div class="profile-card card h-auto">
                        <div class="card-header">
                            <h4 class="card-title fs-20 mb-0">Change Password</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('customer.password.update') }}" method="POST" id="passwordForm" novalidate>
                                @csrf

                                <!-- Current Password -->
                                <div class="mb-3 password-wrapper">
                                    <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input
                                            type="password"
                                            class="form-control"
                                            name="current_password"
                                            id="current_password"
                                            required>
                                        <span class="password-toggle" data-target="current_password"><i class="far fa-eye"></i></span>
                                    </div>
                                </div>

                                <!-- New Password -->
                                <div class="mb-3 password-wrapper">
                                    <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input
                                            type="password"
                                            class="form-control"
                                            name="new_password"
                                            id="new_password"
                                            required>
                                        <span class="password-toggle" data-target="new_password"><i class="far fa-eye"></i></span>
                                    </div>
                                </div>

                                <!-- Confirm New Password -->
                                <div class="mb-3 password-wrapper">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input
                                            type="password"
                                            class="form-control"
                                            name="new_password_confirmation"
                                            id="new_password_confirmation"
                                            required>
                                        <span class="password-toggle" data-target="new_password_confirmation"><i class="far fa-eye"></i></span>
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
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <script>
        // Toggle password visibility
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function(){
            // intl-tel-input init
            const phoneInput = document.getElementById('phone');
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: 'in',
                separateDialCode: true,
                utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/utils.js'
            });
            const initData = iti.getSelectedCountryData();
            $('#country_code').val('+' + initData.dialCode);
            $('#country').val(initData.name);

            // State/City selectors
            const stateSelect = document.getElementById('state');
            const citySelect = document.getElementById('city');
            function resetSelect(el, placeholder) { el.innerHTML = `<option value="">${placeholder}</option>`; el.disabled = true; }
            function addOption(el, text) { const opt = document.createElement('option'); opt.value = opt.textContent = text; el.appendChild(opt); }
            function loadStates(country, preState = null, preCity = null) {
                resetSelect(stateSelect, 'Select State');
                resetSelect(citySelect, 'Select City');
                fetch('https://countriesnow.space/api/v0.1/countries/states', {
                    method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ country })
                })
                .then(res => res.json())
                .then(data => {
                    data.data.states.forEach(s => addOption(stateSelect, s.name));
                    stateSelect.disabled = false;
                    if (preState) {
                        stateSelect.value = preState;
                        loadCities(country, preState, preCity);
                    }
                });
            }
            function loadCities(country, state, preCity = null) {
                resetSelect(citySelect, 'Select City');
                fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                    method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ country, state })
                })
                .then(res => res.json())
                .then(data => {
                    data.data.forEach(c => addOption(citySelect, c));
                    citySelect.disabled = false;
                    if (preCity) citySelect.value = preCity;
                });
            }
            // Prefill states and cities
            const oldState = `{{ old('state', optional($user->detail)->state) }}`;
            const oldCity = `{{ old('city', optional($user->detail)->city) }}`;
            loadStates(initData.name, oldState, oldCity);

            phoneInput.addEventListener('countrychange', () => {
                const d = iti.getSelectedCountryData();
                $('#country_code').val('+' + d.dialCode);
                $('#country').val(d.name);
                loadStates(d.name);
            });
            stateSelect.addEventListener('change', () => loadCities($('#country').val(), stateSelect.value));

            // jQuery Validation rules
            $.validator.addMethod('strongPassword', function(value, element) {
                return this.optional(element)
                    || /[A-Z]/.test(value)
                    && /[a-z]/.test(value)
                    && /[0-9]/.test(value)
                    && /[!@#$%^&*()_+.,;:]/.test(value)
                    && value.length >= 8;
            }, 'Password must be at least 8 characters long and contain uppercase, lowercase, number & special character.');

            $('#profileForm').validate({
                errorElement: 'div', errorClass: 'error-text',
                ignore: [],
                rules: {
                    billing_name: { required: true, maxlength: 255 },
                    state: { required: true },
                    city: { required: true },
                    pin_code: { required: true, digits: true, minlength:4, maxlength:10},
                    address: { required: true, maxlength:500 },
                    pan_card: { maxlength:20 },
                    gst_number:{ maxlength:20 }
                },
                submitHandler: function(form) {
                    $('#submitBtn').prop('disabled', true).text('Processing...');
                    form.submit();
                }
            });

            $('#passwordForm').validate({
                errorElement: 'div', errorClass: 'error-text',
                rules: {
                    current_password: { required: true },
                    new_password: { required: true, strongPassword: true },
                    new_password_confirmation: { required: true, equalTo: '#new_password' }
                },
                messages: {
                    new_password_confirmation: { equalTo: 'Passwords do not match.' }
                },
                submitHandler: function(form) {
                    $(form).find('button[type="submit"]').prop('disabled', true).text('Processing...');
                    form.submit();
                }
            });
        });
    </script>
@endpush
