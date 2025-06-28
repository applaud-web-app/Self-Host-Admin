@extends('frontend.auth.layout.app')

@section('title', 'Checkout | Self Host Aplu')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/css/intlTelInput.css" />
    <style>
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
            height: 38px;
        }

        #apply-coupon:disabled {
            opacity: 0.7;
        }

        .price-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .price-summary hr {
            margin: 15px 0;
        }
    </style>
@endpush

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="section-padding" style="padding-top:4rem;padding-bottom:4rem;">
        <div class="container">
            <div class="checkout-card">
                <h1 class="checkout-heading">💸 Payment Checkout - {{ $product->name }}</h1>
                <p class="checkout-subtitle">
                    Fill in all required details, set a secure password, and complete payment to create your account.
                </p>

                <form id="checkout-form" action="{{ route('checkout.callback') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="name">Username <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" placeholder="Enter your name" required>
                            @error('name')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email') }}" placeholder="Enter your email" required>
                            @error('email')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="country_code" id="country_code"
                            value="{{ old('country_code', '+91') }}">
                        <input type="hidden" name="country" id="country" value="{{ old('country', 'India') }}">

                        <div class="form-group col-lg-6">
                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="phone" class="form-control"
                                value="{{ old('phone') }}" placeholder="Enter your phone number" required>
                            @error('phone')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

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
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                    <div class="billing-section mt-4">
                        <hr>
                        <h4>Billing Details</h4>
                        <hr>
                        <div class="row mt-2">
                            <div class="form-group col-lg-6">
                                <label for="billing_name">Billing Name <span class="text-danger">*</span></label>
                                <input type="text" name="billing_name" id="billing_name" class="form-control"
                                    value="{{ old('billing_name') }}" placeholder="Your Name" required>
                                @error('billing_name')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="state">State <span class="text-danger">*</span></label>
                                <select name="state" id="state" class="form-control form-select" required disabled>
                                    <option value="">Select State</option>
                                </select>
                                @error('state')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="city">City <span class="text-danger">*</span></label>
                                <select name="city" id="city" class="form-control form-select" required disabled>
                                    <option value="">Select City</option>
                                </select>
                                @error('city')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="pin_code">Pin Code <span class="text-danger">*</span></label>
                                <input type="text" name="pin_code" id="pin_code" class="form-control"
                                    value="{{ old('pin_code') }}" placeholder="2XXXXX" required>
                                @error('pin_code')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-lg-12">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                <textarea name="address" id="address" cols="30" rows="4" class="form-control"
                                    placeholder="Your complete address" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="pan_card">Pan Card (Optional)</label>
                                <input type="text" name="pan_card" id="pan_card" class="form-control"
                                    value="{{ old('pan_card') }}" placeholder="Enter Pan Card">
                                @error('pan_card')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

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

                    <div class="coupon-section mb-3">
                        <div class="coupon-container" id="coupon-container">
                            <div class="d-flex align-items-end gap-2">
                                <div class="flex-grow-1">
                                    <label for="coupon_code">Coupon Code</label>
                                    <input type="text" class="form-control" name="coupon_code" id="coupon-code"
                                        placeholder="Enter coupon code">
                                </div>
                                <button class="btn btn-secondary" id="apply-coupon" type="button">Apply</button>
                            </div>
                        </div>
                    </div>

                    <div class="price-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Unit Price:</span>
                            <span id="unit-price-display">₹{{ number_format($unit_price ?? $subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount:</span>
                            <span id="discount-display">-₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal-display">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>GST (18%):</span>
                            <span id="gst-display">₹{{ number_format($gstAmount, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span id="total-display">₹{{ number_format($totalAmount, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" id="pay-button" class="btn btn-primary w-100 py-3">
                        <span id="button-text">Pay ₹{{ number_format($totalAmount, 2) }}</span>
                        <span id="button-spinner" style="display:none;">
                            <i class="fas fa-spinner fa-spin ms-2"></i>
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
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/intlTelInput.min.js"></script>
    <script>
        // Replace the existing script section with this fixed version

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            let isPaymentInProgress = false; // Add flag to prevent multiple submissions

            // Initialize phone input
            const phoneInput = document.getElementById('phone');
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "in",
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/utils.js"
            });

            // Update hidden country fields when country changes
            phoneInput.addEventListener('countrychange', () => {
                const countryData = iti.getSelectedCountryData();
                $('#country_code').val('+' + countryData.dialCode);
                $('#country').val(countryData.name);
                loadStates(countryData.name);
            });

            // State and city dropdown management
            const stateSelect = document.getElementById('state');
            const citySelect = document.getElementById('city');

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
                    .then(data => {
                        data.data.forEach(c => addOption(citySelect, c));
                        citySelect.disabled = false;
                        if (preCity) citySelect.value = preCity;
                    });
            }

            function addOption(select, text) {
                const opt = document.createElement('option');
                opt.value = opt.textContent = text;
                select.appendChild(opt);
            }

            function resetSelect(select, placeholder) {
                select.innerHTML = `<option value="">${placeholder}</option>`;
                select.disabled = true;
            }

            // Initial load for India
            const initData = iti.getSelectedCountryData();
            loadStates(initData.name, @json(old('state')), @json(old('city')));

            // State change handler
            stateSelect.addEventListener('change', () => {
                loadCities($('#country').val(), stateSelect.value);
            });

            // Password toggle functionality
            document.querySelectorAll('.password-toggle').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const id = this.getAttribute('data-target');
                    const input = document.getElementById(id);
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            });

            // Price and coupon management
            const originalPrices = {
                unit: {{ $unit_price ?? $subtotal }},
                subtotal: {{ $subtotal }},
                gst: {{ $gstAmount }},
                total: {{ $totalAmount }},
                amount: Math.round({{ $totalAmount }} * 100) // Amount in paise for Razorpay
            };

            let currentPrices = {
                ...originalPrices
            };
            let razorpayOrderId = null;
            let isCouponApplied = false;

            // Update price displays
            function updatePriceDisplays() {
                $('#unit-price-display').text(`₹${originalPrices.unit.toFixed(2)}`);
                $('#discount-display').text(`-₹${(originalPrices.subtotal - currentPrices.subtotal).toFixed(2)}`);
                $('#subtotal-display').text(`₹${currentPrices.subtotal.toFixed(2)}`);
                $('#gst-display').text(`₹${currentPrices.gst.toFixed(2)}`);
                $('#total-display').text(`₹${currentPrices.total.toFixed(2)}`);
                $('#button-text').text(`Pay ₹${currentPrices.total.toFixed(2)}`);
            }

            $('#apply-coupon').click(async function() {
                const couponCode = $('#coupon-code').val().trim();
                if (!couponCode) {
                    showCouponMessage('Please enter a coupon code', false);
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Applying...');

                try {
                    const response = await fetch("{{ route('coupon.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            code: couponCode,
                            amount: originalPrices.unit
                        })
                    });

                    const data = await response.json();

                    if (data.status) {
                        // Update prices
                        currentPrices = {
                            unit: parseFloat(data.data.unit_price),
                            subtotal: parseFloat(data.data.subtotal),
                            gst: parseFloat(data.data.gst_amount),
                            total: parseFloat(data.data.final_amount),
                            amount: Math.round(parseFloat(data.data.final_amount) * 100)
                        };

                        updatePriceDisplays();
                        showCouponMessage(data.message, true);
                        isCouponApplied = true;

                        // Add hidden coupon field
                        if (!$('#applied_coupon').length) {
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'coupon_code',
                                id: 'applied_coupon',
                                value: couponCode
                            }).appendTo('#checkout-form');
                        } else {
                            $('#applied_coupon').val(couponCode);
                        }

                        // Change button text to "Remove" and handle removal
                        btn.text('Applied').off('click').on('click', function() {
                            removeCoupon();
                        });

                    } else {
                        showCouponMessage(data.message, false);
                        $('#coupon-code').val('');
                    }
                } catch (error) {
                    showCouponMessage('An error occurred. Please try again.', false);
                    console.error('Coupon error:', error);
                } finally {
                    if (!isCouponApplied) {
                        btn.prop('disabled', false).text('Apply');
                    }
                }
            });

            function removeCoupon() {
                // Reset to original prices
                currentPrices = {
                    ...originalPrices
                };
                updatePriceDisplays();
                isCouponApplied = false;

                // Remove coupon code and hidden field
                $('#coupon-code').val('');
                $('#applied_coupon').remove();

                // Remove coupon message
                $('#coupon-container .coupon-message').remove();

                // Reset button
                $('#apply-coupon').text('Apply').off('click').on('click', function() {
                    $('#apply-coupon').click();
                });
            }

            function showCouponMessage(message, isSuccess) {
                $('#coupon-container .coupon-message').remove();
                $('<div>')
                    .addClass(`coupon-message ${isSuccess ? 'text-success' : 'text-danger'}`)
                    .text(message)
                    .insertAfter($('#coupon-container .d-flex'));
            }

            $('#coupon-code').keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    if (!isCouponApplied) {
                        $('#apply-coupon').click();
                    }
                }
            });

            // Form validation - FIXED VERSION
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
                    phone: {
                        required: true,
                        minlength: 6,
                        normalizer: function(value) {
                            return value.replace(/[^\d]/g, '');
                        },
                        maxlength: 20,
                        digits: true,
                        remote: {
                            url: "{{ route('checkout.checkPhone') }}",
                            type: "post",
                            data: {
                                country_code: () => $('#country_code').val(),
                                phone: () => $('#phone').val().replace(/[^\d]/g, '')
                            }
                        }
                    },
                    password: {
                        required: true,
                        minlength: 8,
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
                    }
                    // REMOVED: razorpay validation rules here - they'll be validated only after payment
                },
                messages: {
                    email: {
                        remote: 'This email is already taken.'
                    },
                    phone: {
                        remote: 'This phone number is already registered.'
                    },
                    password_confirmation: {
                        equalTo: 'Passwords do not match.'
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                errorPlacement: function(error, element) {
                    if (element.parent('.password-wrapper').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();

                    // Prevent multiple submissions
                    if (isPaymentInProgress) {
                        console.log('Payment already in progress, ignoring submission');
                        return false;
                    }

                    // Check if this is after successful payment (has razorpay data)
                    const hasPaymentData = $('#razorpay_payment_id').val() &&
                        $('#razorpay_order_id').val() &&
                        $('#razorpay_signature').val();

                    if (hasPaymentData) {
                        // This is the final submission after successful payment
                        console.log('Submitting form with payment data');
                        isPaymentInProgress = true;

                        // Update button to show processing
                        $('#button-text').text('Processing Payment...');
                        $('#button-spinner').show();
                        $('#pay-button').prop('disabled', true);

                        // Submit the form directly to server
                        form.submit();
                        return;
                    }

                    // This is the initial submission - launch payment
                    console.log('Initial form submission - launching payment');
                    isPaymentInProgress = true;

                    $('#button-text').text('Preparing Payment...');
                    $('#button-spinner').show();
                    $('#pay-button').prop('disabled', true);

                    // Create Razorpay order and launch payment
                    createRazorpayOrder().then(() => {
                        launchRazorpay();
                    }).catch(error => {
                        console.error('Error creating Razorpay order:', error);
                        resetPaymentButton();
                        showCouponMessage('Failed to initiate payment. Please try again.',
                            false);
                    });
                },
                invalidHandler: function(event, validator) {
                    if (validator.errorList.length) {
                        $('html, body').animate({
                            scrollTop: $(validator.errorList[0].element).offset().top - 100
                        }, 400);
                    }
                }
            });

            // Helper function to reset payment button
            function resetPaymentButton() {
                isPaymentInProgress = false;
                $('#button-text').text(`Pay ₹${currentPrices.total.toFixed(2)}`);
                $('#button-spinner').hide();
                $('#pay-button').prop('disabled', false);
            }

            // Strong password validation method
            $.validator.addMethod('strongPassword', function(value) {
                    return /[A-Z]/.test(value) && /[a-z]/.test(value) && /[0-9]/.test(value) &&
                        /[!@#$%^&*()_+.,;:]/.test(value) && value.length >= 8;
                },
                'Password must contain uppercase, lowercase, number, special character, and be at least 8 characters long.'
                );

            // Create Razorpay order
            async function createRazorpayOrder() {
                try {
                    const response = await fetch("{{ route('razorpay.order.create') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            amount: currentPrices.total,
                            product_uuid: "{{ $product->uuid }}"
                        })
                    });

                    const data = await response.json();

                    if (!data.status || !data.order_id) {
                        throw new Error(data.message || 'Failed to create order');
                    }

                    razorpayOrderId = data.order_id;
                    return data;

                } catch (error) {
                    console.error('Order creation failed:', error);
                    isPaymentInProgress = false; // Reset flag on error
                    throw error;
                }
            }

            // Launch Razorpay payment - FIXED VERSION
            window.launchRazorpay = function() {
                if (!razorpayOrderId) {
                    console.error('Razorpay order ID is missing');
                    resetPaymentButton();
                    return;
                }

                const options = {
                    key: "{{ config('services.razorpay.key') }}",
                    amount: currentPrices.amount,
                    currency: "INR",
                    name: "Aplu",
                    description: "Payment for {{ $product->name }}",
                    order_id: razorpayOrderId,
                    handler: function(response) {
                        console.log('Payment successful:', response);

                        // Set the payment details
                        $('#razorpay_payment_id').val(response.razorpay_payment_id);
                        $('#razorpay_order_id').val(response.razorpay_order_id);
                        $('#razorpay_signature').val(response.razorpay_signature);

                        // Update button text
                        $('#button-text').text('Processing Payment...');

                        // Now submit the form with payment data
                        // Use setTimeout to ensure payment modal closes first
                        setTimeout(() => {
                            $('#checkout-form')[0]
                        .submit(); // Use native form submit to bypass validation
                        }, 500);
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
                            console.log('Payment modal dismissed');
                            resetPaymentButton();
                        }
                    }
                };

                const rzp = new Razorpay(options);

                // Handle payment failure
                rzp.on('payment.failed', function(response) {
                    console.error('Payment failed:', response.error);
                    resetPaymentButton();
                    showCouponMessage('Payment failed: ' + response.error.description, false);
                });

                rzp.open();
            };
        });
    </script>
@endpush
