@extends('frontend.auth.layout.app')

@section('title', 'Checkout | Self Host Aplu')

@push('styles')
    <style>
        /* ------------------------------------------
           Existing styles you provided; do not change
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
            font-size: 0.9rem;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }

        /* Eye-toggle styling for password fields */
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

     
    </style>
@endpush

@section('content')
    {{-- Expose CSRF token so AJAX remote rules can include it --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="section-padding" style="padding-top: 4rem; padding-bottom: 4rem;">
        <div class="container">
            <div class="checkout-card">
                <h1 class="checkout-heading">💸 Payment Checkout - {{ $product->name }}</h1>
                <p class="checkout-subtitle">
                    Fill in all required details, set a secure password, and pay ₹{{$product->price}} to create your account.
                </p>

                {{-- ============================
                     Checkout + Registration Form
                ============================= --}}
             <form 
    id="checkout-form" 
    action="{{ route('checkout.callback') }}" 
    method="POST" 
    autocomplete="off"
>
    @csrf

    <div class="row">
        {{-- Username (Name) --}}
        <div class="form-group col-lg-6">
            <label for="name">Username <span class="text-danger">*</span></label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                class="form-control"
                value="{{ old('name') }}" 
                placeholder="Enter your name" 
                required
            >
            @error('name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email Address --}}
        <div class="form-group col-lg-6">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input 
                type="email"
                name="email"  
                id="email"  
                class="form-control"
                value="{{ old('email') }}"  
                placeholder="Enter your email"  
                required
            >
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Country Code dropdown --}}
        <div class="form-group col-lg-6">
            <label for="country_code">Country Code <span class="text-danger">*</span></label>
            <select 
                name="country_code" 
                id="country_code" 
                class="form-control form-select"
                required
            >
                <option value="">Select Country Code</option>
                <option value="+91" {{ old('country_code') == '+91' ? 'selected' : '' }}>+91 (India)</option>
                <option value="+1"  {{ old('country_code') == '+1'  ? 'selected' : '' }}>+1 (USA)</option>
                <option value="+44" {{ old('country_code') == '+44' ? 'selected' : '' }}>+44 (UK)</option>
                <option value="+61" {{ old('country_code') == '+61' ? 'selected' : '' }}>+61 (Australia)</option>
                {{-- Add more country codes as needed --}}
            </select>
            @error('country_code')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Phone Number --}}
        <div class="form-group col-lg-6">
            <label for="phone">Phone Number <span class="text-danger">*</span></label>
            <input 
                type="text"  
                name="phone"  
                id="phone"  
                class="form-control"
                value="{{ old('phone') }}"  
                placeholder="Enter your phone number"  
                required
            >
            @error('phone')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group col-lg-6 password-wrapper">
            <label for="password">Password <span class="text-danger">*</span></label>
            <div class="password-wrapper">
                <input 
                    class="form-control"
                    type="password"  
                    name="password"  
                    id="password"  
                    placeholder="Create a strong password"  
                    required
                >
                <span class="password-toggle" data-target="password"><i class="far fa-eye"></i></span>
            </div>
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group col-lg-6 password-wrapper">
            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
            <div class="password-wrapper">
                <input 
                    class="form-control"
                    type="password"  
                    name="password_confirmation"  
                    id="password_confirmation"  
                    placeholder="Re-enter your password"  
                    required
                >
                <span class="password-toggle" data-target="password_confirmation"><i class="far fa-eye"></i></span>
            </div>
            @error('password_confirmation')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <input type="hidden" name="product_uuid" value="{{ $product->uuid }}" />

    {{-- Hidden Razorpay fields --}}
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">

    {{-- Billing Details Section --}}
    <div class="billing-section">
         <hr>
        <h4>Billing Details</h4>
        <hr>
        <div class="row mt-2">
            {{-- Billing Name --}}
            <div class="form-group col-lg-6">
                <label for="billing_name">Billing Name <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    name="billing_name" 
                    id="billing_name" 
                    class="form-control"
                    value="{{ old('billing_name') }}" 
                    placeholder="Aplu" 
                    required
                >
                @error('billing_name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- State --}}
            <div class="form-group col-lg-6">
                <label for="state">State <span class="text-danger">*</span></label>
                <select 
                    name="state" 
                    id="state" 
                    class="form-control form-select"
                    required
                >
                    <option value="">Select State</option>
                    <option value="Uttarakhand" {{ old('state') == 'Uttarakhand' ? 'selected' : '' }}>Uttarakhand</option>
                    <option value="Rajasthan"   {{ old('state') == 'Rajasthan'   ? 'selected' : '' }}>Rajasthan</option>
                    <option value="Punjab"       {{ old('state') == 'Punjab'       ? 'selected' : '' }}>Punjab</option>
                </select>
                @error('state')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- City --}}
            <div class="form-group col-lg-6">
                <label for="city">City <span class="text-danger">*</span></label>
                <select 
                    name="city" 
                    id="city" 
                    class="form-control form-select"
                    required
                >
                    <option value="">Select City</option>
                    <option value="Dehradun" {{ old('city') == 'Dehradun' ? 'selected' : '' }}>Dehradun</option>
                    <option value="Nanital"   {{ old('city') == 'Nanital'   ? 'selected' : '' }}>Nanital</option>
                    <option value="Auli"      {{ old('city') == 'Auli'      ? 'selected' : '' }}>Auli</option>
                </select>
                @error('city')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Pin Code --}}
            <div class="form-group col-lg-6">
                <label for="pin_code">Pin Code <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    name="pin_code" 
                    id="pin_code" 
                    class="form-control"
                    value="{{ old('pin_code') }}" 
                    placeholder="2XXXXX" 
                    required
                >
                @error('pin_code')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Address --}}
            <div class="form-group col-lg-12">
                <label for="address">Address <span class="text-danger">*</span></label>
                <textarea 
                    name="address" 
                    class="form-control" 
                    id="address" 
                    cols="30" 
                    rows="4" 
                    placeholder="Indi Road" 
                    required
                >{{ old('address') }}</textarea>
                @error('address')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Pan Card (optional) --}}
            <div class="form-group col-lg-6">
                <label for="pan_card">Pan Card (Optional)</label>
                <input 
                    type="text" 
                    name="pan_card" 
                    id="pan_card" 
                    class="form-control"
                    value="{{ old('pan_card') }}" 
                    placeholder="Enter Pan Card"
                >
                @error('pan_card')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- GST Number (optional) --}}
            <div class="form-group col-lg-6">
                <label for="gst_number">GST Number (Optional)</label>
                <input 
                    type="text" 
                    name="gst_number" 
                    id="gst_number" 
                    class="form-control"
                    value="{{ old('gst_number') }}" 
                    placeholder="Enter GST Number"
                >
                @error('gst_number')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Submit Button --}}
    <button 
        type="submit" 
        id="pay-button" 
        class="btn btn-primary w-100">
        <span id="button-text">Pay ₹{{$product->price}}</span>
        <span id="button-spinner" style="display: none;">
            &nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="4" opacity="0.25"/>
                <path d="M22 12a10 10 0 0 1-10 10" stroke="#fff" stroke-width="4"/>
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

    <script>
        $(document).ready(function() {
            // Read the CSRF token from <meta> for AJAX calls:
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            ////////////////////////////////////////
            // 2) jQuery Validation Configuration
            ////////////////////////////////////////

            // Add custom method for strong password:
            $.validator.addMethod('strongPassword', function(value, element) {
                return this.optional(element) 
                    || /[A-Z]/.test(value)        // has uppercase letter
                    && /[a-z]/.test(value)       // has lowercase letter
                    && /[0-9]/.test(value)       // has digits
                    && /[\!\@\#\$\%\^\&\*\(\)\_\+\.\,\;\:]/.test(value) // has special char
                    && value.length >= 8;         // at least 8 characters
            }, 'Password must be at least 8 characters long and contain uppercase, lowercase, number & special character.');

            $('#checkout-form').validate({
                // Place error messages under each element
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
                                email: function() {
                                    return $("#email").val();
                                }
                            }
                        }
                    },
                    country_code: {
                        required: true
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 6,
                        maxlength: 20,
                        remote: {
                            url: "{{ route('checkout.checkPhone') }}",
                            type: "post",
                            data: {
                                country_code: function() {
                                    return $("#country_code").val();
                                },
                                phone: function() {
                                    return $("#phone").val();
                                }
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

                submitHandler: function(form) {
                    // When form is valid, show "Processing..." state on button
                    $('#button-text').text('Processing...');
                    $('#button-spinner').show();
                    $('#pay-button').prop('disabled', true);

                    // Trigger Razorpay checkout instead of normal submit
                    launchRazorpay();
                },

                invalidHandler: function(event, validator) {
                    // Scroll to the first error:
                    if (validator.errorList.length) {
                        $('html, body').animate({
                            scrollTop: $(validator.errorList[0].element).offset().top - 100
                        }, 400);
                    }
                }
            });

            //////////////////////////////////////////////////////////
            // 3) Razorpay integration (once form is valid)
            //////////////////////////////////////////////////////////
            function launchRazorpay() {
                let options = {
                    "key": "{{ $razorpayKey }}",        // Razorpay Key from controller
                    "amount": "{{ $amount }}", 
                    "currency": "INR",
                    "name": "Aplu",
                    "description": "Admin Signup Fee",
                    "order_id": "{{ $orderId }}",
                    "handler": function(response) {
                        // On successful payment, set hidden fields and submit real form
                        $('#razorpay_payment_id').val(response.razorpay_payment_id);
                        $('#razorpay_order_id').val(response.razorpay_order_id);
                        $('#razorpay_signature').val(response.razorpay_signature);

                        // Now submit the form for real:
                        formSubmitAfterRazorpay();
                    },
                    "prefill": {
                        "name":  $('#name').val(),
                        "email": $('#email').val(),
                        "contact": $('#phone').val()
                    },
                    "theme": {
                        "color": "#F37254"
                    },
                    "modal": {
                        ondismiss: function() {
                            // This function runs when the user closes (cancels) the Razorpay window
                            window.location.reload();
                        }
                    }
                };

                let rzp = new Razorpay(options);
                rzp.open();
            }

            ////////////////////////////////////////
            // 4) After Razorpay success, final submit
            ////////////////////////////////////////
            function formSubmitAfterRazorpay() {
                $('#checkout-form')[0].submit();
            }
        });
    </script>
@endpush