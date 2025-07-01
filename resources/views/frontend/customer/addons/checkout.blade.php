@extends('frontend.customer.layout.app')

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .checkout-summary {
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        .product-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        .support-text {
            background-color: #fff8e1;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #f93a0b;
        }
        .coupon-input {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .coupon-message {
            margin-top: 5px;
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .coupon-success {
            color: #198754;
            background-color: #d1e7dd;
        }
        .coupon-error {
            color: #dc3545;
            background-color: #f8d7da;
        }
        .steps-container {
            margin-bottom: 30px;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }
        .step {
            text-align: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active .step-number {
            background: #f93a0b;
            color: white;
        }
        .step.completed .step-number {
            background: #198754;
            color: white;
        }
        .step-title {
            font-weight: 500;
            color: #6c757d;
        }
        .step.active .step-title {
            color: #f93a0b;
        }
        .step.completed .step-title {
            color: #198754;
        }
        .steps:before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }
        .steps-progress {
            position: absolute;
            top: 20px;
            left: 0;
            height: 2px;
            background: #f93a0b;
            z-index: 1;
            transition: width 0.3s ease;
        }
        .step-icon {
            font-size: 1.2rem;
            display: none;
        }
        .step.completed .step-icon {
            display: block;
        }
        .step.completed .step-number {
            display: none;
        }
        .license-info {
            background: #fff8e1;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid #f93a0b;
        }
        .feature-list {
            list-style-type: none;
            padding-left: 0;
        }
        .feature-list li {
            padding: 8px 0;
            border-bottom: none;
            display: flex;
            align-items: center;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list li i {
            margin-right: 10px;
            color: #f93a0b;
        }
        .payment-methods img {
            max-height: 30px;
            margin-right: 10px;
        }
    </style>
@endpush

@section('content')
    <section class="content-body">
        <div class="container-fluid position-relative">
            <div class="d-flex flex-wrap align-items-center justify-content-between text-head mb-3">
                <h2 class="me-auto mb-0">Complete Your Purchase</h2>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-shopping-cart me-2"></i>Order Summary</h4>
                        </div>
                        <div class="card-body">
                            @if(isset($product))
                            <div class="product-info">
                                <img src="{{ asset('storage/icons/'.$product->icon ?? 'images/placeholder-product.png') }}" alt="{{ $product->name }}" class="product-image">
                                <div>
                                    <h5 class="mb-1">{{ $product->name }}</h5>
                                    <p class="text-muted mb-0">{{ $product->description ?? 'Premium Add-on Service' }}</p>
                                    <p class="mb-0 text-primary fw-bold">{{ $pricing['currency'] }}{{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                            @endif
                            
                            <div class="license-info">
                                <h5><i class="fas fa-key me-2"></i>What happens after payment?</h5>
                                <ul class="feature-list mt-3">
                                    <li><i class="fas fa-check-circle"></i> Instant license key delivery to your email</li>
                                    <li><i class="fas fa-check-circle"></i> Detailed installation instructions</li>
                                    <li><i class="fas fa-check-circle"></i> 24/7 support for setup assistance</li>
                                    <li><i class="fas fa-check-circle"></i> Access to all future updates</li>
                                </ul>
                            </div>
                            
                            <div class="support-text">
                                <h5><i class="fas fa-info-circle me-2"></i> Need Help?</h5>
                                <p class="mb-0">Our team is ready to assist you with any questions. Contact us at <a href="mailto:{{ $support_contact['email'] }}">{{ $support_contact['email'] }}</a> or call {{ $support_contact['phone'] }}.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card checkout-summary">
                        <div class="card-header">
                            <h4><i class="fas fa-receipt me-2"></i>Payment Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="coupon-section mb-3">
                                <div class="coupon-input">
                                    <input type="text" class="form-control" id="coupon-input" placeholder="Enter coupon code">
                                    <button class="btn btn-outline-primary apply-coupon-btn">Apply</button>
                                </div>
                                <div id="coupon-message-container"></div>
                            </div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Unit Price</td>
                                        <td class="text-end">{{ $pricing['currency'] }} <span id="unit_price-amount">{{ number_format($pricing['subtotal'], 2) }}</span></td>
                                    </tr>
                                    <tr class="discount-row">
                                        <td>Discount</td>
                                        <td class="text-end text-success">-{{ $pricing['currency'] }}<span id="discount-amount">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td class="text-end">{{ $pricing['currency'] }}<span id="subtotal-amount">{{ number_format($pricing['subtotal'], 2) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>Tax ({{ $pricing['gst_rate'] }}%)</td>
                                        <td class="text-end">{{ $pricing['currency'] }}<span id="tax-amount">{{ number_format($pricing['gst_amount'], 2) }}</span></td>
                                    </tr>
                                    <tr class="fw-bold" style="font-size: 1.1rem;">
                                        <td>Total</td>
                                        <td class="text-end">{{ $pricing['currency'] }}<span id="total-amount">{{ number_format($pricing['total_amount'], 2) }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-primary btn-lg py-3 checkout-btn">
                                    <i class="fas fa-lock me-2"></i> Pay {{ $pricing['currency'] }}{{ number_format($pricing['total_amount'], 2) }}
                                </button>
                            </div>
                            
                            <div class="payment-methods mt-4 text-center">
                                <img src="{{ asset('images/payment-cards.png') }}" alt="Visa">
                                <p class="small text-muted mt-2 mb-0"><i class="fas fa-lock"></i> Secured payment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize prices from your PHP variables
            const originalPrices = {
                subtotal: {{ $pricing['subtotal'] }},
                gst: {{ $pricing['gst_amount'] }},
                total: {{ $pricing['total_amount'] }},
                amount: Math.round({{ $pricing['total_amount'] }} * 100) // Amount in paise for Razorpay
            };

            let currentPrices = {...originalPrices};
            let razorpayOrderId = null;
            let isCouponApplied = false;
            let isPaymentInProgress = false;
            let appliedCouponCode = '';

            // Update price displays
            function updatePriceDisplays(prices) {
                $('#subtotal-amount').text(prices.subtotal.toFixed(2));
                $('#tax-amount').text(prices.gst.toFixed(2));
                $('#total-amount').text(prices.total.toFixed(2));
                $('.checkout-btn').html(`<i class="fas fa-lock me-2"></i> Pay {{ $pricing['currency'] }}${prices.total.toFixed(2)}`);
            }

            // Show coupon message
            function showCouponMessage(message, isSuccess) {
                const messageContainer = $('#coupon-message-container');
                messageContainer.empty();
                
                const messageDiv = $(`<div class="coupon-message ${isSuccess ? 'coupon-success' : 'coupon-error'}">`)
                    .text(message);
                
                messageContainer.append(messageDiv);
                
                // Auto-hide success messages after 5 seconds
                if (isSuccess) {
                    setTimeout(() => {
                        messageDiv.fadeOut();
                    }, 5000);
                }
            }

            // Coupon application
            $('.apply-coupon-btn').click(async function() {
                const couponCode = $('#coupon-input').val().trim();
                if (!couponCode) {
                    showCouponMessage('Please enter a coupon code', false);
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>');

                try {
                    const response = await fetch("{{ route('coupon.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            code: couponCode,
                            amount: originalPrices.subtotal
                        })
                    });

                    const data = await response.json();

                    if (data.status) {
                        // Update prices based on response
                        const newPrices = {
                            subtotal: parseFloat(data.data.subtotal),
                            gst: parseFloat(data.data.gst_amount),
                            total: parseFloat(data.data.final_amount),
                            amount: Math.round(parseFloat(data.data.final_amount) * 100)
                        };

                        // Update UI
                        $('.discount-row').show();
                        $('#discount-amount').text(data.data.discount_amount.toFixed(2));
                        $('#subtotal-amount').text(data.data.subtotal.toFixed(2));
                        $('#unit_price-amount').text(data.data.unit_price.toFixed(2));
                        updatePriceDisplays(newPrices);
                        
                        // Store current prices and coupon code
                        currentPrices = newPrices;
                        appliedCouponCode = couponCode;
                        isCouponApplied = true;

                        // Show success message
                        showCouponMessage(data.message, true);

                        // Change button to "Remove"
                        btn.html('<i class="fas fa-check"></i>').removeClass('btn-outline-primary').addClass('btn-primary')
                            .off('click').on('click', function() {
                                removeCoupon();
                            });

                    } else {
                        showCouponMessage(data.message, false);
                        $('#coupon-input').val('');
                    }
                } catch (error) {
                    console.error('Coupon error:', error);
                    showCouponMessage('An error occurred. Please try again.', false);
                } finally {
                    if (!isCouponApplied) {
                        btn.prop('disabled', false).text('Apply');
                    }
                }
            });

            function removeCoupon() {
                // Reset to original prices
                currentPrices = {...originalPrices};
                updatePriceDisplays(currentPrices);
                
                // Hide discount row
                $('.discount-row').hide();
                
                // Clear coupon code
                $('#coupon-input').val('');
                appliedCouponCode = '';
                isCouponApplied = false;
                
                // Clear message
                $('#coupon-message-container').empty();
                
                // Reset button
                $('.apply-coupon-btn').text('Apply').removeClass('btn-outline-danger').addClass('btn-outline-primary')
                    .off('click').on('click', function() {
                        $('.apply-coupon-btn').click();
                    });
            }

            // Payment button click handler
            $('.checkout-btn').click(function() {
                if (isPaymentInProgress) return;
                
                isPaymentInProgress = true;
                const btn = $(this);
                btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...').prop('disabled', true);
                
                createRazorpayOrder().then(() => {
                    launchRazorpay();
                }).catch(error => {
                    console.error('Error:', error);
                    resetPaymentButton();
                    showCouponMessage('Payment initiation failed. Please try again.', false);
                });
            });

            function resetPaymentButton() {
                isPaymentInProgress = false;
                $('.checkout-btn').html(`<i class="fas fa-lock me-2"></i> Pay {{ $pricing['currency'] }}${currentPrices.total.toFixed(2)}`).prop('disabled', false);
            }

            // Create Razorpay order
            async function createRazorpayOrder() {
                try {
                    const requestData = {
                        amount: currentPrices.total,
                        product_uuid: "{{ $product->uuid ?? '' }}"
                    };

                    if (isCouponApplied) {
                        requestData.coupon_code = appliedCouponCode;
                    }

                    const response = await fetch("{{ route('razorpay.order.create') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(requestData)
                    });

                    const data = await response.json();

                    if (!data.status || !data.order_id) {
                        throw new Error(data.message || 'Failed to create order');
                    }

                    razorpayOrderId = data.order_id;
                    return data;

                } catch (error) {
                    console.error('Order creation failed:', error);
                    throw error;
                }
            }

            // Launch Razorpay payment
            function launchRazorpay() {
                if (!razorpayOrderId) {
                    console.error('Razorpay order ID is missing');
                    resetPaymentButton();
                    return;
                }

                const options = {
                    key: "{{ config('services.razorpay.key') }}",
                    amount: currentPrices.amount,
                    currency: "INR",
                    name: "Your Company Name",
                    description: "Payment for {{ $product->name ?? 'Product' }}",
                    order_id: razorpayOrderId,
                    handler: function(response) {
                        // Create a form and submit it
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('customer.addons.callback') }}";
                        
                        // Add CSRF token
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = "{{ csrf_token() }}";
                        form.appendChild(csrf);
                        
                        // Add payment details
                        const paymentId = document.createElement('input');
                        paymentId.type = 'hidden';
                        paymentId.name = 'razorpay_payment_id';
                        paymentId.value = response.razorpay_payment_id;
                        form.appendChild(paymentId);
                        
                        const orderId = document.createElement('input');
                        orderId.type = 'hidden';
                        orderId.name = 'razorpay_order_id';
                        orderId.value = response.razorpay_order_id;
                        form.appendChild(orderId);
                        
                        const signature = document.createElement('input');
                        signature.type = 'hidden';
                        signature.name = 'razorpay_signature';
                        signature.value = response.razorpay_signature;
                        form.appendChild(signature);
                        
                        // Add product details
                        const productId = document.createElement('input');
                        productId.type = 'hidden';
                        productId.name = 'product_uuid';
                        productId.value = "{{ $product->uuid ?? '' }}";
                        form.appendChild(productId);
                        
                        // Add coupon if applied
                        if (isCouponApplied) {
                            const coupon = document.createElement('input');
                            coupon.type = 'hidden';
                            coupon.name = 'coupon_code';
                            coupon.value = appliedCouponCode;
                            form.appendChild(coupon);
                        }
                        
                        document.body.appendChild(form);
                        form.submit();
                    },
                    prefill: {
                        name: "{{ auth()->user()->name ?? '' }}",
                        email: "{{ auth()->user()->email ?? '' }}",
                        contact: "{{ auth()->user()->phone ?? '' }}"
                    },
                    theme: {
                        color: "#F37254"
                    },
                    modal: {
                        ondismiss: function() {
                            resetPaymentButton();
                        }
                    }
                };

                const rzp = new Razorpay(options);

                rzp.on('payment.failed', function(response) {
                    console.error('Payment failed:', response.error);
                    resetPaymentButton();
                    showCouponMessage('Payment failed: ' + response.error.description, false);
                });

                rzp.open();
            }
        });
    </script>
@endpush