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
                @if (isset($activatedDomain))
                    <span class="addon-badge badge bg-success">Activated</span>
                @else
                    @if ($isPurchased)
                        <span class="addon-badge badge bg-secondary">Purchased</span>
                    @else
                        <span class="addon-badge badge bg-primary">Available to Purchase</span>
                    @endif
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
                                    <a href="https://aplu.io/contact"
                                        class="btn btn-secondary btn-sm w-100">
                                        <i class="fas fa-support me-1"></i> Contact Support to integrate
                                    </a>

                                    {{-- @if ($licenseKey)
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm w-50 btn-copy-license"
                                            data-key="{{ $licenseKey }}">
                                            <i class="fas fa-copy me-1"></i> Copy License Key
                                        </button>
                                    @endif --}}
                                </div>
                            @else
                                {{-- Preview & Purchase --}}
                                <div class="d-flex gap-2">
                                    @php
                                        $purchaseUrl = route('customer.addons.checkout');
                                        $param = ['uuid' => $product->uuid];
                                        $encryptedUrl = encryptUrl($purchaseUrl, $param);
                                    @endphp
                                    <a href="{{$encryptedUrl}}" target="_blank" type="button" class="btn btn-outline-primary btn-sm w-100 py-2">
                                        <i class="fas fa-shopping-cart me-1"></i> Purchase
                                    </a>
                                    {{-- <button type="button" class="btn btn-outline-primary btn-sm w-100 py-2 btn-purchase"
                                        data-url="{{$encryptedUrl}}">
                                        <i class="fas fa-shopping-cart me-1"></i> Purchase
                                    </button> --}}
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