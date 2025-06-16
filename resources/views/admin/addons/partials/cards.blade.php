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
            $licenseKey = optional($product->license)->raw_key;
        @endphp

        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card addon-card position-relative h-100">
                {{-- Badge: “Purchased” (green) or “Available to Purchase” (blue) --}}
                @if ($isPurchased)
                    <span class="addon-badge badge bg-success">Purchased</span>
                @else
                    <span class="addon-badge badge bg-secondary">Available to Purchase</span>
                @endif

                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div class="card-content">
                        {{-- <div class="addon-icon-bg mb-3" style="background: {{ $gradientBg }};">
                            <i class="fas {{  ?? 'fa-cube' }} fa-2x text-white"></i>
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
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.addons.download', ['uuid' => $product->uuid]) }}"
                                class="btn btn-outline-primary btn-sm w-50"
                                
                            >
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                            @php
                                
                            @endphp
                            <button data-modal="edit-addon" data-url="{{ route('admin.addons.edit', ['uuid' => $product->uuid]) }}"
                                data-name="{{ $product->name }}"
                                data-description="{{ $product->description }}"
                                data-price="{{ $product->price }}"
                                data-icon="{{ $product->icon }}"
                                data-version="{{ $product->version }}"
                                data-uuid="{{ $product->uuid }}"
                                class="btn btn-outline-secondary btn-sm w-50"
                                data-bs-toggle="modal"
                                data-bs-target="#editAddonModal">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                        </div>
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