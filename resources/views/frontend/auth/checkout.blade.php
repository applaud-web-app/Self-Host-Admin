@extends('frontend.auth.layout.app')

@section('title', 'Checkout | Self Host Aplu')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/css/intlTelInput.css" />

    <style>
        /* ------------------------------------------
       Existing styles you provided; unchanged
    ------------------------------------------- */
        .checkout-card {
            padding: 40px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            max-width: 100%;
            margin: auto;
        }

        .checkout-heading {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #2d3748;
            text-align: center;
        }

        .checkout-subtitle {
            font-size: 1.1rem;
            color: #4a5568;
            margin-bottom: 2rem;
            line-height: 1.5;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .error-text {
            color: #e53e3e;
            font-size: .9rem;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #718096;
            font-size: 1.1rem;
        }

        .coupon-message {
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        #apply-coupon {
            transition: all 0.3s ease;
            height: 38px; /* Match input height */
        }

        #apply-coupon:disabled {
            opacity: 0.7;
        }
    </style>
@endpush

@section('content')
    {{-- Expose CSRF token so AJAX remote rules can include it --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="section-padding" style="padding-top:4rem;padding-bottom:4rem;">
        <div class="container">
            <div class="checkout-card">
                <h1 class="checkout-heading">💸 Payment Checkout - {{ $product->name }}</h1>
                <p class="checkout-subtitle">
                    Fill in all required details, set a secure password, and pay ₹{{ $product->price }} to create your
                    account.
                </p>

                {{-- ============================
                     Checkout + Registration Form
                ============================= --}}
                <form id="checkout-form" action="{{ route('checkout.callback') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="row">
                        {{-- Username --}}
                        <div class="form-group col-lg-6">
                            <label for="name">Username <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" placeholder="Enter your name" required>
                            @error('name')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-group col-lg-6">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email') }}" placeholder="Enter your email" required>
                            @error('email')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hidden Country Dial Code & Full Country --}}
                        <input type="hidden" name="country_code" id="country_code"
                            value="{{ old('country_code', '+91') }}">
                        <input type="hidden" name="country" id="country" value="{{ old('country', 'India') }}">

                        {{-- Phone --}}
                        <div class="form-group col-lg-6">
                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="phone" class="form-control"
                                value="{{ old('phone') }}" placeholder="Enter your phone number" required>
                            @error('phone')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="form-group col-lg-6 password-wrapper">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Create a strong password" required>
                                <span class="password-toggle" data-target="password"><i class="far fa-eye"></i></span>
                            </div>

                            @error('password')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="form-group col-lg-6 password-wrapper">
                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Re-enter your password" required>
                                <span class="password-toggle" data-target="password_confirmation"><i
                                        class="far fa-eye"></i></span>
                            </div>

                            @error('password_confirmation')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" name="product_uuid" value="{{ $product->uuid }}">

                    {{-- Razorpay hidden fields --}}
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                    {{-- Billing Details --}}
                    <div class="billing-section">
                        <hr>
                        <h4>Billing Details</h4>
                        <hr>
                        <div class="row mt-2">
                            {{-- Billing Name --}}
                            <div class="form-group col-lg-6">
                                <label for="billing_name">Billing Name <span class="text-danger">*</span></label>
                                <input type="text" name="billing_name" id="billing_name" class="form-control"
                                    value="{{ old('billing_name') }}" placeholder="Aplu" required>
                                @error('billing_name')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- State (dynamically populated) --}}
                            <div class="form-group col-lg-6">
                                <label for="state">State <span class="text-danger">*</span></label>
                                <select name="state" id="state" class="form-control form-select " required
                                    disabled>
                                    <option value="">Select State</option>
                                </select>
                                @error('state')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- City (dynamically populated) --}}
                            <div class="form-group col-lg-6">
                                <label for="city">City <span class="text-danger">*</span></label>
                                <select name="city" id="city" class="form-control form-select" required disabled>
                                    <option value="">Select City</option>
                                </select>
                                @error('city')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pin Code --}}
                            <div class="form-group col-lg-6">
                                <label for="pin_code">Pin Code <span class="text-danger">*</span></label>
                                <input type="text" name="pin_code" id="pin_code" class="form-control"
                                    value="{{ old('pin_code') }}" placeholder="2XXXXX" required>
                                @error('pin_code')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="form-group col-lg-12">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                <textarea name="address" id="address" cols="30" rows="4" class="form-control" placeholder="Indi Road"
                                    required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pan Card (opt) --}}
                            <div class="form-group col-lg-6">
                                <label for="pan_card">Pan Card (Optional)</label>
                                <input type="text" name="pan_card" id="pan_card" class="form-control"
                                    value="{{ old('pan_card') }}" placeholder="Enter Pan Card">
                                @error('pan_card')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- GST (opt) --}}
                            <div class="form-group col-lg-6">
                                <label for="gst_number">GST Number (Optional)</label>
                                <input type="text" name="gst_number" id="gst_number" class="form-control"
                                    value="{{ old('gst_number') }}" placeholder="Enter GST Number">
                                @error('gst_number')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Coupon Input Section -->
                    <div class="coupon-section mb-3">
                        <div class="coupon-container" id="coupon-container">
                            <div class="d-flex align-items-end gap-2">
                                <div>
                                    <label for="coupon_code">Coupon Code </label>
                                    <input type="text" class="coupon-code form-control" name="coupon_code"
                                        id="coupon-code" placeholder="Coupon Code">
                                </div>
                                <button class="btn btn-secondary" id="apply-coupon" type="button">Apply</button>
                            </div>
                        </div>
                    </div>


                    {{-- Pay button --}}
                    <button type="submit" id="pay-button" class="btn btn-primary w-100">
                        <span id="button-text">Pay ₹{{ $product->price }}</span>
                        <span id="button-spinner" style="display:none;">
                            &nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="4"
                                    opacity="0.25" />
                                <path d="M22 12a10 10 0 0 1-10 10" stroke="#fff" stroke-width="4" />
                            </svg>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <!-- intl-tel-input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/intlTelInput.min.js"></script>

    <script>
        /* ========= Helpers ========= */
        function addOption(select, text) {
            const opt = document.createElement('option');
            opt.value = opt.textContent = text;
            select.appendChild(opt);
        }

        function resetSelect(select, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            select.disabled = true;
        }

        $(document).ready(function() {

            // ------------------------------------------------------------------
            // 0) CSRF for every Ajax request
            // ------------------------------------------------------------------
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ------------------------------------------------------------------
            // 1) intl-tel-input initialisation
            // ------------------------------------------------------------------
            const phoneInput = document.getElementById('phone');
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "in",
                separateDialCode: true,
                formatOnDisplay: false,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/utils.js"
            })



            /* Elements we re-use */
            const initData = iti.getSelectedCountryData();
            $('#country_code').val('+' + initData.dialCode);
            $('#country').val(initData.name);

            /* Elements we re-use */
            const stateSelect = document.getElementById('state');
            const citySelect = document.getElementById('city');

            /* 2) Helpers that load states/cities */
            function loadStates(country, preState = null, preCity = null) {
                resetSelect(stateSelect, 'Select State');
                resetSelect(citySelect, 'Select City');

                fetch('https://countriesnow.space/api/v0.1/countries/states', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            country
                        })
                    })
                    .then(r => r.json())
                    .then(js => {
                        js.data.states.forEach(s => addOption(stateSelect, s.name));
                        stateSelect.disabled = false;
                        stateSelect.removeAttribute('disabled');

                        if (preState) {
                            stateSelect.value = preState;
                            loadCities(country, preState, preCity);
                        }
                    });
            }

            function loadCities(country, state, preCity = null) {
                resetSelect(citySelect, 'Select City');

                fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            country,
                            state
                        })
                    })
                    .then(r => r.json())
                    .then(js => {
                        js.data.forEach(c => addOption(citySelect, c));
                        citySelect.disabled = false;
                        citySelect.removeAttribute('disabled');
                        if (preCity) citySelect.value = preCity;
                    });
            }

            /* 3) First page-load → India states immediately */
            const oldState = @json(old('state'));
            const oldCity = @json(old('city'));
            loadStates(initData.name, oldState, oldCity);

            /* 4) If user changes the phone flag (country) */
            phoneInput.addEventListener('countrychange', () => {
                const d = iti.getSelectedCountryData();
                $('#country_code').val('+' + d.dialCode);
                $('#country').val(d.name);

                loadStates(d.name); // resets + loads fresh
            });

            /* 5) When user picks a state, load its cities */
            stateSelect.addEventListener('change', () => {
                loadCities($('#country').val(), stateSelect.value);
            });

            // ------------------------------------------------------------------
            // 4) jQuery Validation rules
            // ------------------------------------------------------------------
            $.validator.addMethod('strongPassword', function(value, element) {
                    return this.optional(element) ||
                        (/[A-Z]/.test(value) // uppercase
                            &&
                            /[a-z]/.test(value) // lowercase
                            &&
                            /[0-9]/.test(value) // digits
                            &&
                            /[!@#$%^&*()_+.,;:]/.test(value) // special
                            &&
                            value.length >= 8);
                },
                'Password must be at least 8 characters long and contain uppercase, lowercase, number & special character.'
                );

            $('#checkout-form').validate({
                errorElement: 'div',
                errorClass: 'error-text',
                rules: {
                    name: {
                        required: true,
                        maxlength: 255
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255,
                        remote: {
                            url: "{{ route('checkout.checkEmail') }}",
                            type: "post",
                            data: {
                                email: () => $('#email').val()
                            }
                        }
                    },
                    country: {
                        required: true
                    },
                    country_code: {
                        required: true
                    },
                    phone: {
                        required: true,
                        minlength: 6,
                        maxlength: 20,

                        // <-- this runs before 'digits' and 'remote'
                        normalizer: function(value) {
                            return value.replace(/[^\d]/g, ''); // knock out spaces, dashes, etc.
                        },

                        digits: true, // now passes
                        remote: {
                            url: "{{ route('checkout.checkPhone') }}",
                            type: "post",
                            data: {
                                country_code: () => $('#country_code').val(),
                                // strip before sending to server as well
                                phone: () => $('#phone').val().replace(/[^\d]/g, '')
                            }
                        }
                    },
                    password: {
                        required: true,
                        strongPassword: true
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: '#password'
                    },
                    billing_name: {
                        required: true,
                        maxlength: 255
                    },
                    address: {
                        required: true,
                        maxlength: 500
                    },
                    state: {
                        required: true,
                        maxlength: 100
                    },
                    city: {
                        required: true,
                        maxlength: 100
                    },
                    pin_code: {
                        required: true,
                        digits: true,
                        minlength: 4,
                        maxlength: 10
                    },
                    pan_card: {
                        maxlength: 20
                    },
                    gst_number: {
                        maxlength: 20
                    },
                    razorpay_payment_id: {
                        required: true
                    },
                    razorpay_order_id: {
                        required: true
                    },
                    razorpay_signature: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        remote: 'This email is already taken.'
                    },
                    phone: {
                        remote: 'This phone is already taken.'
                    },
                    password_confirmation: {
                        equalTo: 'Passwords do not match.'
                    }
                },
                highlight: function(element /*, errorClass, validClass */ ) {
                    // add Bootstrap’s feedback class – nothing else
                    $(element)
                        .addClass('is-invalid')
                        .removeClass('is-valid');
                },
                unhighlight: function(element /*, errorClass, validClass */ ) {
                    // field is now valid
                    $(element)
                        .removeClass('is-invalid')
                        .addClass('is-valid');
                },
                errorPlacement: function(error, element) {
                    /* Put the message in the right place for BS */
                    if (element.parent('.password-wrapper').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    $('#button-text').text('Processing...');
                    $('#button-spinner').show();
                    $('#pay-button').prop('disabled', true);
                    launchRazorpay(); // call Razorpay instead of default submit
                },
                invalidHandler: function(evt, validator) {
                    if (validator.errorList.length) {
                        $('html,body').animate({
                            scrollTop: $(validator.errorList[0].element).offset().top - 100
                        }, 400);
                    }
                }
            });

            // ------------------------------------------------------------------
            // 5) Razorpay checkout
            // ------------------------------------------------------------------
            function launchRazorpay() {
                const options = {
                    key: "{{ $razorpayKey }}",
                    amount: "{{ $amount }}",
                    currency: "INR",
                    name: "Aplu",
                    description: "Admin Signup Fee",
                    order_id: "{{ $orderId }}",
                    handler: function(response) {
                        $('#razorpay_payment_id').val(response.razorpay_payment_id);
                        $('#razorpay_order_id').val(response.razorpay_order_id);
                        $('#razorpay_signature').val(response.razorpay_signature);
                        formSubmitAfterRazorpay();
                    },
                    prefill: {
                        name: $('#name').val(),
                        email: $('#email').val(),
                        contact: $('#phone').val()
                    },
                    theme: {
                        color: "#F37254"
                    },
                    modal: {
                        ondismiss: function() {
                            window.location.reload();
                        }
                    }
                };
                new Razorpay(options).open();
            }

            function formSubmitAfterRazorpay() {
                $('#checkout-form')[0].submit();
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Define the amount variable based on the product price
            let originalAmount = {{ $product->price }}; // Original product price
            let currentAmount = originalAmount; // Tracks current amount (may be discounted)
            const payButton = document.getElementById('pay-button');
            const buttonText = document.getElementById('button-text');
            const applyCouponBtn = document.getElementById('apply-coupon');
            const couponCodeInput = document.getElementById('coupon-code');
            const couponContainer = document.getElementById('coupon-container');

            // Function to update the payment button text
            function updatePaymentButton(amount) {
                buttonText.textContent = `Pay ₹${amount}`;
                currentAmount = amount;
            }

            // Function to show coupon status message
            function showCouponMessage(message, isSuccess) {
                // Remove any existing message
                const existingMessage = couponContainer.querySelector('.coupon-message');
                if (existingMessage) {
                    existingMessage.remove();
                }

                // Create new message element
                const messageElement = document.createElement('div');
                messageElement.className = `coupon-message mt-2 ${isSuccess ? 'text-success' : 'text-danger'}`;
                messageElement.textContent = message;
                
                // Insert after the coupon input group
                const inputGroup = couponContainer.querySelector('.d-flex');
                inputGroup.parentNode.insertBefore(messageElement, inputGroup.nextSibling);
            }

            // Coupon application handler
            applyCouponBtn.addEventListener('click', function() {
                let couponCode = couponCodeInput.value.trim();
                
                // Validate if coupon code is empty
                if (!couponCode) {
                    showCouponMessage('Please enter a coupon code', false);
                    return;
                }

                // Disable button during processing
                applyCouponBtn.disabled = true;
                applyCouponBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';

                // Make an API call to verify the coupon
                fetch("{{ route('coupon.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            code: couponCode,
                            amount: originalAmount
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            // Success - update UI
                            showCouponMessage(data.message, true);
                            
                            // Update payment button with discounted amount
                            if (data.data.discount_type === 'percentage') {
                                const discountAmount = (originalAmount * data.data.discount_amount) / 100;
                                const discountedAmount = originalAmount - discountAmount;
                                updatePaymentButton(discountedAmount);
                            } else {
                                // Fixed amount discount
                                const discountedAmount = originalAmount - data.data.discount_amount;
                                updatePaymentButton(discountedAmount);
                            }
                            
                            // Add hidden input to submit coupon code with form
                            if (!document.getElementById('applied_coupon')) {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'applied_coupon';
                                hiddenInput.id = 'applied_coupon';
                                hiddenInput.value = couponCode;
                                document.getElementById('checkout-form').appendChild(hiddenInput);
                            }
                        } else {
                            // Error case
                            showCouponMessage(data.message, false);
                            couponCodeInput.value = "";
                            updatePaymentButton(originalAmount); // Reset to original price
                        }
                    })
                    .catch(error => {
                        showCouponMessage('An error occurred. Please try again.', false);
                        couponCodeInput.value = "";
                        updatePaymentButton(originalAmount); // Reset to original price
                    })
                    .finally(() => {
                        // Re-enable button regardless of outcome
                        applyCouponBtn.disabled = false;
                        applyCouponBtn.textContent = 'Apply';
                    });
            });

            // Optional: Allow pressing Enter in coupon field to apply
            couponCodeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyCouponBtn.click();
                }
            });
        });
    </script>
@endpush