@php
    // A small palette of gradient pairs. We'll pick one randomly per card.
    $gradients = [
        ['#63e','#42a5f5'],
        ['#ff7043','#fbc02d'],
        ['#42a5f5','#3b5998'],
        ['#388e3c','#43a047'],
        ['#8e24aa','#5e35b1'],
        ['#e53935','#ffca28'],
        ['#0097a7','#00bcd4'],
    ];
@endphp

<div class="row">
    @forelse ($products as $product)
        @php
            // Pick a random gradient for this card's icon background:
            $pair       = $gradients[array_rand($gradients)];
            $gradientBg = "linear-gradient(135deg, {$pair[0]}, {$pair[1]})";

            // Check if this product has a payment record for the current user:
            $isPurchased = $product->payment !== null;

            // If purchased, this is the license key (or null if license row doesn't exist yet)
            $licenseKey = optional($product->license)->key;
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
                        {{-- <div class="addon-icon-bg mb-3" style="background: {{ $gradientBg }};">
                            <i class="fas {{ $product->icon ?? 'fa-cube' }} fa-2x text-white"></i>
                        </div> --}}
                        <img src="{{asset('storage/icons/'.$product->icon)}}" class="mb-3" width="60" height="60" alt="{{ $product->name }}">
                        
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
                        @if ($isPurchased)
                            <div class="d-flex gap-2">
                                {{-- Download & Copy License Key --}}
                                <a
                                    href="{{ route('customer.addons.download', ['uuid' => $product->uuid]) }}"
                                    class="btn btn-outline-success btn-sm w-50"
                                    download
                                >
                                    <i class="fas fa-download me-1"></i> Download
                                </a>

                                @if ($licenseKey)
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm w-50 btn-copy-license"
                                        data-key="{{ $licenseKey }}"
                                    >
                                        <i class="fas fa-copy me-1"></i> Copy License Key
                                    </button>
                                @endif
                            </div>
                        @else
                            {{-- Preview & Purchase --}}
                            <div class="d-flex gap-2">
                                {{-- <button
                                    class="btn btn-outline-info btn-sm w-50 btn-preview"
                                    data-name="{{ $product->name }}"
                                    data-desc="{{ htmlentities($product->description) }}"
                                    data-version="{{ $product->version ?? '–' }}"
                                    data-compatible="{{ $product->compatible ?? '–' }}"
                                    data-price="{{ $product->price ?? '0' }}"
                                    data-features="{{ implode('|', $product->features ?? []) }}"
                                    data-last_updated="{{ $product->last_updated ?? '–' }}"
                                    data-author="{{ $product->author ?? '–' }}"
                                    data-requirements="{{ $product->requirements ?? '–' }}"
                                >
                                    <i class="fas fa-eye me-1"></i> Preview
                                </button> --}}
                                <button
                                    class="btn btn-outline-primary btn-sm w-100 py-2 btn-purchase"
                                    data-name="{{ $product->name }}"
                                >
                                    <i class="fas fa-shopping-cart me-1"></i> Purchase
                                </button>
                            </div>
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