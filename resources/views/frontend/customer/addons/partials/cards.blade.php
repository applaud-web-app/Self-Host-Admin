@php
    // A small palette of gradient pairs. We'll pick one randomly per card.
$gradients = [
    ['#63e', '#42a5f5'],
    ['#ff7043', '#fbc02d'],
    ['#42a5f5', '#3b5998'],
    ['#388e3c', '#43a047'],
    ['#8e24aa', '#5e35b1'],
    ['#e53935', '#ffca28'],
    ['#0097a7', '#00bcd4'],
    ];
@endphp

<div class="row">
    @forelse ($products as $product)
        @php
            // Pick a random gradient for this card's icon background:
            $pair = $gradients[array_rand($gradients)];
            $gradientBg = "linear-gradient(135deg, {$pair[0]}, {$pair[1]})";

            // Check if this product has a payment record for the current user:
            $isPurchased = $product->payment !== null;

            // If purchased, this is the license key (or null if license row doesn't exist yet)
            $licenseKey = optional($product->license)->raw_key;

            $activatedDomain = optional($product->license)->activated_domain;
        @endphp

        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card addon-card position-relative h-100">
                {{-- Badge: “Purchased” (green) or “Available to Purchase” (blue) --}}
                @if ($isPurchased)
                    <span class="addon-badge badge bg-success">Purchased</span>
                @else
                    <span class="addon-badge badge bg-primary">Available to Purchase</span>
                @endif

                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div class="card-content">
                        <img src="{{ asset('storage/icons/' . $product->icon) }}" class="mb-3" width="60"
                            height="60" alt="{{ $product->name }}">

                        <div class="addon-title">{{ $product->name }}</div>

                        <div class="addon-desc mb-2">
                            {{ \Illuminate\Support\Str::limit($product->description, 60) }}
                        </div>

                        @if (isset($product->price))
                            <div class="mb-0">
                                <span class="fw-bold fs-4 text-success">
                                    ₹{{ number_format($product->price) }}
                                </span>
                                <span class="text-muted fs-7"> one time</span>
                            </div>
                        @endif
                    </div>

                    <div class="addon-actions">
                        @if (isset($activatedDomain))
                            <button type="button" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-check me-1"></i> Activated
                            </button>
                        @else
                            @if ($isPurchased)
                                <div class="d-flex gap-2">
                                    {{-- Download & Copy License Key --}}
                                    <a href="{{ route('customer.addons.download', ['uuid' => $product->uuid]) }}"
                                        class="btn btn-outline-success btn-sm w-50" download>
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>

                                    @if ($licenseKey)
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm w-50 btn-copy-license"
                                            data-key="{{ $licenseKey }}">
                                            <i class="fas fa-copy me-1"></i> Copy License Key
                                        </button>
                                    @endif
                                </div>
                            @else
                                {{-- Preview & Purchase --}}
                                <div class="d-flex gap-2">
                                    @php
                                        $purchaseUrl = route('customer.addons.purchase');
                                        $param = ['uuid' => $product->uuid];
                                        $encryptedUrl = encryptUrl($purchaseUrl, $param);
                                    @endphp
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100 py-2 btn-purchase"
                                        data-url="{{$encryptedUrl}}">
                                        <i class="fas fa-shopping-cart me-1"></i> Purchase
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-secondary text-center mb-4">
                No addons found.
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination links --}}
@if ($products->hasPages())
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {!! $products->links() !!}
        </div>
    </div>
@endif

@push('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        $(document).on('click', '.btn-purchase', function() {
            let productUrl = $(this).data('url'); // Get the purchase URL from the button

            // Change the button to "Processing" state
            let $btn = $(this);
            $btn.prop('disabled', true).text('Processing...');

            $.ajax({
                url: productUrl, // The URL is dynamically generated using the UUID
                method: 'GET', // Ensure it's a GET request to trigger the correct route
                success: function(response) {
                    if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                        // Re-enable the button and reset text
                        $btn.prop('disabled', false).text('Purchase');
                        return;
                    }

                    // Trigger Razorpay Payment Modal
                    openRazorpayPayment(response, $btn);
                },
                error: function() {
                    Swal.fire('Error', 'Unable to process the payment. Please try again later.',
                        'error');
                    // Re-enable the button and reset text
                    $btn.prop('disabled', false).text('Purchase');
                }
            });
        });

        function openRazorpayPayment(data, $btn) {
            var options = {
                key: data.razorpayKey, // Your Razorpay key
                amount: data.amount, // The amount in paise
                currency: "INR",
                name: data.product.name,
                description: data.product.name,
                order_id: data.orderId, // Razorpay Order ID
                handler: function(response) {
                    // Send payment data to the backend for verification and saving the details
                    $.ajax({
                        url: "{{ route('customer.addons.callback') }}", // Use the URL from the product data (same UUID-based URL)
                        method: 'POST',
                        data: {
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: data.orderId,
                            razorpay_signature: response.razorpay_signature,
                            product_uuid: data.product.uuid, // Product UUID for record
                            _token: '{{ csrf_token() }}' // CSRF token for security
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success', 'Payment successfully done!', 'success').then(() => {
                                    // Delay for 1 second before reloading the page
                                    setTimeout(function() {
                                        location.reload(); // Reload the page
                                    }, 800);
                                });
                            } else {
                                Swal.fire('Error',
                                    'Payment verification failed. Please contact support.', 'error');
                            }

                            // Re-enable the button and reset text
                            $btn.prop('disabled', false).text('Purchase');
                        },
                        error: function() {
                            Swal.fire('Error', 'Unable to verify payment. Please try again later.',
                                'error');
                            // Re-enable the button and reset text
                            $btn.prop('disabled', false).text('Purchase');
                        }
                    });
                },
                prefill: {
                    name: 'Customer Name', // Replace with actual customer details if available
                    email: 'customer@example.com', // Replace with actual customer details if available
                },
                theme: {
                    color: "#F37254"
                }
            };

            var rzp1 = new Razorpay(options);
            rzp1.open();
        }
    </script>
@endpush