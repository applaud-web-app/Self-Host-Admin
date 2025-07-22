@extends('frontend.auth.layout.app')

@section('title', 'Checkout | Self Host Aplu')


@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/css/intlTelInput.css" />
    <style>
        .checkout-card {
            padding: 40px;
            background: #fff;
            border-radius: 12px;
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
            margin-top: 4px;
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

        .coupon-section {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #e0e0e0;
        }

        h5 {
            font-size: 1.1rem;
            color: #2d3748;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .coupon-message {
            font-size: 0.9rem;
            margin-top: 0.5rem;
            padding: 6px 10px;
            border-radius: 4px;
        }

        #apply-coupon {
            transition: all 0.3s ease;
            height: 42px;
            line-height: 0px;
        }

        #apply-coupon:disabled {
            opacity: 0.7;
        }

        .price-summary {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #e0e0e0;
        }

        .price-summary hr {
            margin: 16px 0;
            border-color: #e2e8f0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
        }

        .price-row.total {
            font-weight: 600;
            font-size: 1.1rem;
            color: #2d3748;
        }

        .form-control, .form-select {
            border-radius: 6px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            /* box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2); */
        }

        #pay-button {
            border-radius: 8px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        #pay-button:hover {
            transform: translateY(-2px);
            /* box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); */
        }

        /* Layout adjustments */
        .checkout-container {
            display: grid;
            grid-template-columns: 8fr 4fr;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        .billing-section h4 {
            font-size: 1.3rem;
            color: #2d3748;
            margin-bottom: 16px;
            font-weight: 600;
        }

        /* Add these new styles */
        .addon-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            /* box-shadow: 0 2px 4px rgba(0,0,0,0.05); */
        }
        
        .addon-card h5 {
            font-size: 1.1rem;
            color: #2d3748;
            margin-bottom: 12px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .addon-card .price {
            color: #10b981;
            font-weight: 600;
        }
        
        .addon-card p {
            color: #4a5568;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f3f4f6;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .quantity-btn:hover {
            background: #e5e7eb;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 5px;
        }

        .addons-section h4 {
            font-size: 1.3rem;
            color: #2d3748;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .addon-card .form-check {
            display: flex;
            align-items: flex-start;
        }

        .addon-card .form-check-input {
            margin-top: 0.3em;
            margin-right: 10px;
        }

        .addon-card .form-check-label {
            flex: 1;
        }

.price-table {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 12px;
    font-size: 0.95rem;
}

.price-header {
    font-weight: 600;
    color: #4a5568;
    padding-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 8px;
    grid-column: 1 / span 4;
    display: flex;
}

.price-row {
    display: flex;
    grid-column: 1 / span 4;
}

.price-row.subtotal {
    border-top: 1px solid #e2e8f0;
    padding-top: 12px;
    margin-top: 8px;
    font-weight: 600;
}

.price-row.total {
    font-weight: 700;
    color: #000000;
    font-size: 1.1rem;
    background: #ffebe6;
    padding: 12px;
    border-radius: 8px;
    margin-top: 8px;
}

.price-row.discount {
    color: #ef4444;
}

.price-header span,
.price-row span {
    flex: 1;
    text-align: right;
}

.price-header span:first-child,
.price-row span:first-child {
    text-align: left;
    flex: 1;
}

.price-divider {
    grid-column: 1 / span 4;
    border-top: 1px dashed #e2e8f0;
    margin: 8px 0;
}

h5 {
    font-size: 1.25rem;
    color: #2d3748;
    margin-bottom: 16px;
    font-weight: 600;
    position: relative;
    padding-bottom: 8px;
}

h5::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: #f95549;
    border-radius: 3px;
}

.addons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

.addon-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
}

.addon-card:hover {
    border-color: #c7d2fe;
    /* box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1); */
}

.addon-card-header {
    display: flex;
    align-items: center;
    justify-content:space-between;
}

.addon-card-header .addon-box{
    display: flex;
    align-items: flex-start;
}

.addon-icon {
    margin-right: 12px;
    flex-shrink: 0;
}

.addon-icon img {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border-radius: 8px;
}

.addon-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 4px;
}

.addon-price {
    color: #10b981;
    font-weight: 600;
    font-size: 1.05rem;
}

.addon-description {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 16px;
    flex-grow: 1;
}

.addon-checkbox {
    width: 18px;
    height: 18px;
    margin-right: 8px;
    cursor: pointer;
}

.form-check-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

/* Support card styling */
.support-card {
    background: #f0f9ff;
    border: 1px solid #e0f2fe;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
}

.support-card h5 {
    display: flex;
    justify-content: space-between;
    font-size: 1.1rem;
    margin-bottom: 12px;
}

.support-price {
    color: #0284c7;
    font-weight: 600;
}

.support-description {
    color: #64748b;
    margin-bottom: 16px;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.quantity-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.quantity-input {
    width: 60px;
    text-align: center;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 6px;
    font-weight: 500;
}
    </style>
@endpush


@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="section-padding" style="padding-top:4rem;padding-bottom:4rem;">
        <div class="container">
            <div class="">
                <div class="coupon-section py-5">
                    <h1 class="checkout-heading">💸 Payment Checkout - {{ $product->name }}</h1>
                    <p class="checkout-subtitle mb-0">
                        Fill in all required details, set a secure password, and complete payment to create your account.
                    </p>
                </div>
                <form id="checkout-form" action="{{ route('checkout.callback') }}" method="POST" autocomplete="off">
                    <div class="checkout-container mt-4">
                        @csrf
                        <div class="checkout-form-section">
                            <div class="customer-details coupon-section">
                                <h5>Customer Details</h5>
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

                                    <div class="form-group col-lg-6">
                                        <label for="billing_name">Billing Name <span class="text-danger">*</span></label>
                                        <input type="text" name="billing_name" id="billing_name" class="form-control"
                                            value="{{ old('billing_name') }}" placeholder="Your Name" required>
                                        @error('billing_name')
                                            <div class="error-text">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-5">
                                        <label for="state">State <span class="text-danger">*</span></label>
                                        <select name="state" id="state" class="form-control form-select" required disabled>
                                            <option value="">Select State</option>
                                        </select>
                                        @error('state')
                                            <div class="error-text">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-5">
                                        <label for="city">City <span class="text-danger">*</span></label>
                                        <select name="city" id="city" class="form-control form-select" required disabled>
                                            <option value="">Select City</option>
                                        </select>
                                        @error('city')
                                            <div class="error-text">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-lg-2">
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

                                <input type="hidden" name="product_uuid" value="{{ $product->uuid }}">
                                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                                <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                            </div> 
                            <div class="addon-list coupon-section">
                                @if(count($addons) > 0)
                                    <div class="addons-section">
                                        <h5>Additional Services</h5>
                                        <div class="addons-grid">
                                            @foreach($addons as $addon)
                                            <div class="addon-card">
                                                <div class="addon-card-header">
                                                    <div class="addon-box">
                                                        <div class="addon-icon">
                                                            @if($addon->icon)
                                                                <img src="{{ asset('storage/icons/' . $addon->icon) }}" alt="{{ $addon->name }}">
                                                            @else
                                                                <img src="/assets/images/default-addon.png" alt="Default">
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="addon-title">{{ $addon->name }}</div>
                                                            <div class="addon-price">₹{{ number_format($addon->price, 2) }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="addon-checkbox" type="checkbox" 
                                                        id="addon-{{ $addon->uuid }}" 
                                                        name="addons[]" 
                                                        value="{{ $addon->uuid }}"
                                                        data-price="{{ $addon->price }}">
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="checkout-summary-section">
                            <div class="coupon-section">
                                <h5>Apply Coupon</h5>
                                <div class="coupon-container" id="coupon-container">
                                    <div class="d-flex align-items-end gap-2">
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" name="coupon_code" id="coupon-code"
                                                placeholder="Enter coupon code">
                                        </div>
                                        <button class="btn btn-primary" id="apply-coupon" type="button">Apply</button>
                                    </div>
                                </div>
                            </div>

                            <div class="coupon-section">
                                <h5>Personal Support</h5><span class="text-primary">₹{{$support}}/year (1st year free)</span>
                                <p class="support-description">
                                    Your purchase includes one year of free personal support. For subsequent years, support can be extended at ₹{{$support}}/year.
                                </p>
                                <div class="quantity-selector">
                                    <button type="button" class="quantity-btn" id="decrease-support">-</button>
                                    <input type="number" id="support-years" class="quantity-input" name="support_years" value="1" min="1" max="10">
                                    <button type="button" class="quantity-btn" id="increase-support">+</button>
                                    <span>years (1st year free)</span>
                                </div>
                            </div>
                            
                            <div class="price-summary">
                                <h5>Order Summary</h5>
                                <div class="price-table">
                                    <div class="price-header">
                                        <span class="item">Item</span>
                                        <span class="price-with-gst">Price</span>
                                    </div>

                                    <!-- Core Product -->
                                    <div class="price-row">
                                        <span class="item">Core Product</span>
                                        <span class="price-with-gst" id="core-product-price">₹0.00</span>
                                    </div>

                                    <!-- Addons -->
                                    <div id="addons-price-rows" class="flex-column w-100 price-row gap-3"></div>

                                    <!-- Personal Support -->
                                    <div class="price-row">
                                        <span class="item">Personal Support <br><span class="text-primary">(1st year free)</span></span>
                                        <span class="price-with-gst" id="support-price"><s>₹5000.00</s> ₹0.00</span>
                                    </div>

                                    <div class="price-divider"></div>

                                    <div class="price-row">
                                        <span class="item">Subtotal</span>
                                        <span class="sub-total-amount" id="sub-total-amount">₹0.00</span>
                                    </div>

                                    <div class="price-row">
                                        <span class="item">Discount</span>
                                        <span class="discount-amount" id="discount-amount" data-discount="0">-₹0.00</span>
                                    </div>

                                    <div class="price-row">
                                        <span class="item">GST (18%)</span>
                                        <span class="total-gst-amount" id="total-gst-amount">₹0.00</span>
                                    </div>

                                    <!-- Total -->
                                    <div class="price-row total">
                                        <span class="item">Total</span>
                                        <span class="price-with-gst" id="total-display">₹0.00</span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="pay-button" class="btn btn-primary w-100 py-3">
                                <span id="button-text">Click to Pay</span>
                                <span id="button-spinner" style="display:none;">
                                    <i class="fas fa-spinner fa-spin ms-2"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

{{-- @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script>
    // CSRF Token setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize phone input with intl-tel-input
    document.addEventListener('DOMContentLoaded', function() {
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


        // Password visibility toggle
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
    });

    // Fetch product pricing data and setup calculations
    document.addEventListener('DOMContentLoaded', function() {
        const gstRate = 0.18;
        const supportPricePerYear = 5000; // ₹5000 per year (excluding GST)

        let currentPrices = {
            coreProduct: parseFloat('{{ $product->price }}'), // Already excluding GST
            addons: {},
            supportYears: 1,
            discount: 0
        };

        // Addons data from backend (prices are now GST excluded)
        const addonsData = [
            @foreach($addons as $addon)
            {
                uuid: '{{ $addon->uuid }}',
                name: '{{ $addon->name }}',
                price: {{ $addon->price }}, // GST excluded
            },
            @endforeach
        ];

        // Calculate and update all prices
        function updatePriceDisplays() {
            // Calculate addons total
            let addonsTotal = 0;
            Object.values(currentPrices.addons).forEach(price => {
                addonsTotal += price;
            });

            // Calculate support total (first year free)
            const supportYears = Math.max(currentPrices.supportYears - 1, 0);
            const supportTotal = supportYears * supportPricePerYear;

            // Calculate subtotal (before discount and GST)
            const subtotal = currentPrices.coreProduct + addonsTotal + supportTotal;
            
            // Apply discount
            const discountedSubtotal = Math.max(subtotal - currentPrices.discount, 0);
            
            // Calculate GST (18% of discounted subtotal)
            const gstAmount = discountedSubtotal * gstRate;
            
            // Calculate grand total
            const grandTotal = discountedSubtotal + gstAmount;

            // Update UI
            $('#core-product-price').text(`₹${currentPrices.coreProduct.toFixed(2)}`);
            updateAddonsPriceRows();
            $('#support-price').text(`₹${supportTotal.toFixed(2)}`);
            $('#sub-total-amount').text(`₹${subtotal.toFixed(2)}`);
            $('#discount-amount').text(`-₹${currentPrices.discount.toFixed(2)}`);
            $('#total-gst-amount').text(`₹${gstAmount.toFixed(2)}`);
            $('#total-display').text(`₹${grandTotal.toFixed(2)}`);
            $('#button-text').text(`Pay ₹${grandTotal.toFixed(2)}`);

            // Update razorpay payload
            razorpayPayload = {
                product_uuid: '{{ $product->uuid }}',
                addons: Object.keys(currentPrices.addons),
                support_years: currentPrices.supportYears,
                coupon_code: $('#coupon-code').val(),
                frontend_total: grandTotal
            };
        }

        // Update Addons price rows
        function updateAddonsPriceRows() {
            const container = $('#addons-price-rows');
            container.empty();

            Object.entries(currentPrices.addons).forEach(([uuid, price]) => {
                const addon = addonsData.find(a => a.uuid === uuid);
                if (!addon) return;
                
                const row = $(`
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="item">${addon.name}</span>
                        <span class="price-with-gst">₹${price.toFixed(2)}</span>
                    </div>
                `);
                container.append(row);
            });
        }

        // Handle addon selection
        $(document).on('change', '.addon-checkbox', function() {
            const addonId = $(this).val();
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                const price = addonsData.find(a => a.uuid === addonId).price;
                currentPrices.addons[addonId] = price;
            } else {
                delete currentPrices.addons[addonId];
            }

            updatePriceDisplays();
        });

        // Handle support year changes
        $('#increase-support').click(function() {
            let value = parseInt($('#support-years').val()) || 1;
            if (value < 10) {
                $('#support-years').val(value + 1).trigger('change');
            }
        });

        $('#decrease-support').click(function() {
            let value = parseInt($('#support-years').val()) || 1;
            if (value > 1) {
                $('#support-years').val(value - 1).trigger('change');
            }
        });

        $('#support-years').change(function() {
            currentPrices.supportYears = parseInt($(this).val()) || 1;
            updatePriceDisplays();
        });

        // Apply coupon logic
        $('#apply-coupon').click(function() { 
            const couponCode = $('#coupon-code').val();
            const subtotal = parseFloat($('#sub-total-amount').text().replace('₹', '').replace(',', ''));

            if (couponCode) {
                $.ajax({
                    url: '{{route("coupon.verify")}}',
                    method: 'POST',
                    data: { 
                        coupon_code: couponCode, 
                        amount: subtotal
                    },
                    success: function(response) {
                        if (response.status) {
                            currentPrices.discount = response.data.discount_amount;
                            updatePriceDisplays();
                            
                            $('#coupon-container').append(`<div class="coupon-message">${response.message}</div>`);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        });

         // Helper function to reset payment button
        function resetPaymentButton() {
            isPaymentInProgress = false;
            $('#button-text').text(`Pay ₹${currentPrices.total.toFixed(2)}`);
            $('#button-spinner').hide();
            $('#pay-button').prop('disabled', false);
        }

        // ALL YOUR EXISTING FORM VALIDATION STAYS THE SAME
        $("#checkout-form").validate({
            rules: {
                name: { required: true },
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
                password: { required: true, minlength: 6 },
                password_confirmation: { required: true, equalTo: "#password" },
                billing_name: { required: true },
                state: { required: true },
                city: { required: true },
                pin_code: { required: true, digits: true, minlength: 6, maxlength: 6 },
                address: { required: true }
            },
            messages: {
                name: { required: "Please enter your name." },
                email: { required: "Please enter your email.", email: "Please enter a valid email." , remote: 'This email is already taken.'},
                phone: { required: "Please enter your phone number.", remote: 'This phone number is already registered.' },
                password: { required: "Please create a password.", minlength: "Password must be at least 6 characters." },
                password_confirmation: { required: "Please confirm your password.", equalTo: "Passwords do not match." },
                billing_name: { required: "Please enter your billing name." },
                state: { required: "Please select your state." },
                city: { required: "Please select your city." },
                pin_code: { required: "Pin code required.", digits: "Only numbers.", minlength: "6 digits only." },
                address: { required: "Please enter your address." }
            },
            submitHandler: function(form, event) {
                // Handle form submission (e.g., Razorpay integration)
                // form.submit();
                event.preventDefault();
                createRazorpayOrder().then(() => {
                    launchRazorpay();
                }).catch(error => {
                    console.error('Error creating Razorpay order:', error);
                    resetPaymentButton();
                    showCouponMessage('Failed to initiate payment. Please try again.',
                        false);
                });
            }
        });

        // Create Razorpay order
        async function createRazorpayOrder() {
            try {
                const response = await fetch("{{ route('razorpay.order.create') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(razorpayPayload)
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

        // Initialize prices
        updatePriceDisplays();
    });
    </script>
@endpush --}}
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.3.2/build/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script>
    // CSRF Token setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize phone input with intl-tel-input
    document.addEventListener('DOMContentLoaded', function() {
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

        // Password visibility toggle
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
    });

    // Fetch product pricing data and setup calculations
    document.addEventListener('DOMContentLoaded', function() {
        const gstRate = 0.18;
        const supportPricePerYear = {{$support}}; // per year (excluding GST)

        let currentPrices = {
            coreProduct: parseFloat('{{ $product->price }}'), // Already excluding GST
            addons: {},
            supportYears: 1,
            discount: 0
        };

        // Addons data from backend (prices are now GST excluded)
        const addonsData = [
            @foreach($addons as $addon)
            {
                uuid: '{{ $addon->uuid }}',
                name: '{{ $addon->name }}',
                price: {{ $addon->price }}, // GST excluded
            },
            @endforeach
        ];

        // Razorpay variables
        let razorpayOrderId = null;
        let razorpayPayload = {};
        let isPaymentInProgress = false;

        // Calculate and update all prices
        function updatePriceDisplays() {
            // Calculate addons total
            let addonsTotal = 0;
            Object.values(currentPrices.addons).forEach(price => {
                addonsTotal += price;
            });

            // Calculate support total (first year free)
            const supportYears = Math.max(currentPrices.supportYears - 1, 0);
            const supportTotal = supportYears * supportPricePerYear;

            // Calculate subtotal (before discount and GST)
            const subtotal = currentPrices.coreProduct + addonsTotal + supportTotal;
            
            // Apply discount
            const discountedSubtotal = Math.max(subtotal - currentPrices.discount, 0);
            
            // Calculate GST (18% of discounted subtotal)
            const gstAmount = discountedSubtotal * gstRate;
            
            // Calculate grand total
            const grandTotal = discountedSubtotal + gstAmount;

            // Update UI
            $('#core-product-price').text(`₹${currentPrices.coreProduct.toFixed(2)}`);
            updateAddonsPriceRows();
            if (supportTotal === 0) {
                $('#support-price').html('<s class="text-primary me-1">₹5000.00</s> ₹0.00');
            } else {
                $('#support-price').text(`₹${supportTotal.toFixed(2)}`);
            }

            // $('#support-price').text(`₹${supportTotal.toFixed(2)}`);
            $('#sub-total-amount').text(`₹${subtotal.toFixed(2)}`);
            $('#discount-amount').text(`-₹${currentPrices.discount.toFixed(2)}`);
            $('#total-gst-amount').text(`₹${gstAmount.toFixed(2)}`);
            $('#total-display').text(`₹${grandTotal.toFixed(2)}`);
            $('#button-text').text(`Pay ₹${grandTotal.toFixed(2)}`);

            // Update razorpay payload
            razorpayPayload = {
                product_uuid: '{{ $product->uuid }}',
                addons: Object.keys(currentPrices.addons),
                support_years: currentPrices.supportYears,
                coupon_code: $('#coupon-code').val(),
                frontend_total: grandTotal
            };
        }

        // Update Addons price rows
        function updateAddonsPriceRows() {
            const container = $('#addons-price-rows');
            container.empty();

            Object.entries(currentPrices.addons).forEach(([uuid, price]) => {
                const addon = addonsData.find(a => a.uuid === uuid);
                if (!addon) return;
                
                const row = $(`
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="item">${addon.name}</span>
                        <span class="price-with-gst">₹${price.toFixed(2)}</span>
                    </div>
                `);
                container.append(row);
            });
        }

        // Handle addon selection
        $(document).on('change', '.addon-checkbox', function() {
            const addonId = $(this).val();
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                const price = addonsData.find(a => a.uuid === addonId).price;
                currentPrices.addons[addonId] = price;
            } else {
                delete currentPrices.addons[addonId];
            }

            updatePriceDisplays();
        });

        // Handle support year changes
        $('#increase-support').click(function() {
            let value = parseInt($('#support-years').val()) || 1;
            if (value < 10) {
                $('#support-years').val(value + 1).trigger('change');
            }
        });

        $('#decrease-support').click(function() {
            let value = parseInt($('#support-years').val()) || 1;
            if (value > 1) {
                $('#support-years').val(value - 1).trigger('change');
            }
        });

        $('#support-years').change(function() {
            currentPrices.supportYears = parseInt($(this).val()) || 1;
            updatePriceDisplays();
        });

        // Apply coupon logic
        $('#apply-coupon').click(function() { 
            const couponCode = $('#coupon-code').val();
            const subtotal = parseFloat($('#sub-total-amount').text().replace('₹', '').replace(',', ''));

            if (couponCode) {
                $.ajax({
                    url: '{{route("coupon.verify")}}',
                    method: 'POST',
                    data: { 
                        coupon_code: couponCode, 
                        amount: subtotal
                    },
                    beforeSend: function() {
                        $('#apply-coupon').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                        // Remove any existing messages
                        $('.coupon-message').remove();
                    },
                    complete: function() {
                        $('#apply-coupon').prop('disabled', false).text('Apply');
                    },
                    success: function(response) {
                        if (response.status) {
                            // Valid coupon - apply discount
                            currentPrices.discount = response.data.discount_amount;
                            
                            // Add success message
                            $('#coupon-container').append(`
                                <div class="coupon-message alert alert-success">
                                    ${response.message}
                                </div>
                            `);
                        } else {
                            // Invalid coupon - reset discount
                            currentPrices.discount = 0;
                            
                            // Add error message
                            $('#coupon-container').append(`
                                <div class="coupon-message alert alert-danger">
                                    ${response.message}
                                </div>
                            `);
                        }
                        
                        // Update prices in both cases
                        updatePriceDisplays();
                    },
                    error: function() {
                        // Reset discount on error
                        currentPrices.discount = 0;
                        updatePriceDisplays();
                        
                        // Add error message
                        $('#coupon-container').append(`
                            <div class="coupon-message alert alert-danger">
                                Failed to verify coupon. Please try again.
                            </div>
                        `);
                    }
                });
            } else {
                // If no coupon code entered, reset discount
                currentPrices.discount = 0;
                updatePriceDisplays();
                
                // Remove any existing messages
                $('.coupon-message').remove();
            }
        });

        // Helper function to reset payment button
        function resetPaymentButton() {
            isPaymentInProgress = false;
            $('#button-spinner').hide();
            $('#button-text').text(`Pay ₹${$('#total-display').text().replace('₹', '')}`);
            $('#pay-button').prop('disabled', false);
        }

        // Show error message
        function showErrorMessage(message) {
            // Remove any existing error messages
            $('.payment-error-message').remove();
            
            // Add error message above the pay button
            $('.price-summary').append(`
                <div class="payment-error-message alert alert-danger mt-3">
                    ${message}
                </div>
            `);
        }

        // Form validation
        $("#checkout-form").validate({
            rules: {
                name: { required: true },
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
                password: { required: true, minlength: 6 },
                password_confirmation: { required: true, equalTo: "#password" },
                billing_name: { required: true },
                state: { required: true },
                city: { required: true },
                pin_code: { required: true, digits: true, minlength: 6, maxlength: 6 },
                address: { required: true }
            },
            messages: {
                name: { required: "Please enter your name." },
                email: { required: "Please enter your email.", email: "Please enter a valid email." , remote: 'This email is already taken.'},
                phone: { required: "Please enter your phone number.", remote: 'This phone number is already registered.' },
                password: { required: "Please create a password.", minlength: "Password must be at least 6 characters." },
                password_confirmation: { required: "Please confirm your password.", equalTo: "Passwords do not match." },
                billing_name: { required: "Please enter your billing name." },
                state: { required: "Please select your state." },
                city: { required: "Please select your city." },
                pin_code: { required: "Pin code required.", digits: "Only numbers.", minlength: "6 digits only." },
                address: { required: "Please enter your address." }
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                
                // If payment is already in progress, do nothing
                if (isPaymentInProgress) return;
                
                // Set payment in progress
                isPaymentInProgress = true;
                $('#pay-button').prop('disabled', true);
                $('#button-text').text('Processing...');
                $('#button-spinner').show();
                
                // Remove any existing error messages
                $('.payment-error-message').remove();
                
                // Create Razorpay order
                createRazorpayOrder()
                    .then(() => {
                        // Launch Razorpay payment
                        launchRazorpay();
                    })
                    .catch(error => {
                        console.error('Payment error:', error);
                        showErrorMessage(error.message || 'Failed to initiate payment. Please try again.');
                        resetPaymentButton();
                    });
            }
        });

        // Create Razorpay order
        async function createRazorpayOrder() {
            try {
                const response = await fetch("{{ route('razorpay.order.create') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(razorpayPayload)
                });

                const data = await response.json();

                if (!response.ok || !data.status || !data.order_id) {
                    throw new Error(data.message || 'Failed to create payment order');
                }

                razorpayOrderId = data.order_id;
                return data;

            } catch (error) {
                console.error('Order creation failed:', error);
                throw error;
            }
        }

        // Launch Razorpay payment
        window.launchRazorpay = function() {
            if (!razorpayOrderId) {
                console.error('Razorpay order ID is missing');
                showErrorMessage('Payment initialization failed. Please try again.');
                resetPaymentButton();
                return;
            }

            const options = {
                key: "{{ config('services.razorpay.key') }}",
                amount: Math.round(parseFloat($('#total-display').text().replace('₹', '')) * 100),
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
                    $('#button-text').text('Completing Payment...');
                    $('#button-spinner').show();

                    // Submit the form with payment data
                    $('#checkout-form')[0].submit();
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
                        showErrorMessage('Payment was cancelled. Please try again if you wish to proceed.');
                        resetPaymentButton();
                        // Optional: Reload page after 3 seconds
                        setTimeout(() => location.reload(), 3000);
                    }
                }
            };

            const rzp = new Razorpay(options);

            // Handle payment failure
            rzp.on('payment.failed', function(response) {
                console.error('Payment failed:', response.error);
                showErrorMessage('Payment failed: ' + (response.error.description || 'Unknown error'));
                resetPaymentButton();
                // Optional: Reload page after 3 seconds
                setTimeout(() => location.reload(), 3000);
            });

            rzp.open();
        };

        // Initialize prices
        updatePriceDisplays();
    });
    </script>
@endpush