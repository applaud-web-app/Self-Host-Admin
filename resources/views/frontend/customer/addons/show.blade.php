@extends('frontend.customer.layout.app')
@section('title', 'Addons | Aplu Push')

@section('content')
<style>
    /* ─── Modern filter‐bar styles ────────────────────────────────────────────────── */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e3e3e3;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 0.25rem;
    }
    .filter-card .filter‐group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }
    .filter-card .filter‐item {
        flex: 1 1 200px;
        min-width: 150px;
    }
    .filter-card .btn-reset {
        border-color: #dee2e6;
        color: #495057;
    }
    @media (max-width: 575.98px) {
        .filter-card .filter‐item {
            flex: 1 1 100%;
        }
        .filter-card .filter‐actions {
            justify-content: flex-end;
        }
    }
    /* ─── Card container (no changes below) ──────────────────────────────────────── */
    .addon-card:hover { border-color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .addon-icon-bg {
        width: 60px; height: 60px; display: flex; align-items: center;
        justify-content: center; margin: 0 auto 16px auto; border-radius: 50%;
        color: #fff;
    }
    .addon-card .addon-badge {
        position: absolute; top: 14px; right: 18px; font-size: .85em;
    }
    .addon-title { font-size: 1.18rem; font-weight: 600; margin-bottom: .35rem; }
    .addon-desc { font-size: .95rem; color: #666; min-height: 32px; }
    .addon-actions {
        border-top: 1px solid #f1f1f1; padding-top: 15px; display: flex;
        flex-direction: column; gap: 8px; margin-top: 0px;
    }
    .card-content {
        margin-top: 45px !important;
    }
</style>

<section class="content-body">
    <div class="container-fluid position-relative">

        {{-- ─── FILTER BAR ──────────────────────────────────────────────────────────── --}}
        <div class="filter-card">
            <form id="filterForm" method="GET" action="{{ route('customer.addons.show') }}">
                <div class="filter‐group">
                    {{-- 1) Search by Name --}}
                    <div class="filter‐item">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="🔍 Search by name…"
                            value="{{ request('search') }}"
                        >
                    </div>

                    {{-- 2) Sort --}}
                    <div class="filter‐item">
                        <select name="sort" class="form-select form-control">
                            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>
                                Sort: Newest → Oldest
                            </option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                                Sort: Oldest → Newest
                            </option>
                        </select>
                    </div>

                    {{-- 3) Purchase Status (new) --}}
                    <div class="filter‐item">
                        <select name="purchase_status" class="form-select form-control">
                            <option value="" {{ request('purchase_status') === null ? 'selected' : '' }}>
                                All Status
                            </option>
                            <option value="purchased" {{ request('purchase_status') === 'purchased' ? 'selected' : '' }}>
                                Purchased
                            </option>
                            <option value="not_purchased" {{ request('purchase_status') === 'not_purchased' ? 'selected' : '' }}>
                                Not Purchased
                            </option>
                        </select>
                    </div>

                    {{-- 4) Actions: Filter + Reset --}}
                    <div class="filter‐item filter‐actions d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Apply
                        </button>
                        <a href="{{ route('customer.addons.show') }}" class="btn btn-reset">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
        {{-- ──────────────────────────────────────────────────────────────────────────── --}}

        {{-- === Cards & Pagination Container (AJAX‐reload here) === --}}
        <div id="addons-container">
            @include('frontend.customer.addons.partials.cards', [
                'products' => $products,
            ])
        </div>
    </div>
</section>

{{-- ===== Preview Modal (unchanged) ===== --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Module Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table mb-0">
            <tbody>
                <tr><th scope="row" style="width:130px;">Title</th><td id="modalModuleTitle"></td></tr>
                <tr>
                    <th scope="row">Description</th>
                    <td style="max-width: 480px;">
                        <div id="modalModuleDesc" style="max-height:160px;overflow:auto;line-height:1.7"></div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Features</th>
                    <td>
                        <ul id="modalModuleFeatures" class="mb-0 ps-3"></ul>
                    </td>
                </tr>
                <tr><th scope="row">Version</th><td id="modalModuleVersion"></td></tr>
                <tr><th scope="row">Compatible</th><td id="modalModuleCompatible"></td></tr>
                <tr><th scope="row">Last Updated</th><td id="modalModuleUpdated"></td></tr>
                <tr><th scope="row">Author</th><td id="modalModuleAuthor"></td></tr>
                <tr><th scope="row">Requirements</th><td id="modalModuleRequirements"></td></tr>
                <tr><th scope="row">Price</th><td id="modalModulePrice"></td></tr>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="modalPurchaseBtn" style="display:none;">
            <i class="fas fa-shopping-cart me-1"></i> Purchase
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ===== Purchase Modal (unchanged) ===== --}}
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="purchaseForm" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title" id="purchaseModalLabel">Activate Module</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="purchaseModuleName" name="module">
          <div class="mb-3">
            <label for="purchaseCode" class="form-label">Purchase Code <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="purchaseCode" name="purchase_code" placeholder="Enter your purchase code" required>
          </div>
          <div class="mb-3">
            <label for="licenseKey" class="form-label">License Key <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="licenseKey" name="license_key" placeholder="Enter your license key" required>
          </div>
          <div class="mb-3">
            <label for="installation_path" class="form-label">Installation Path <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="installation_path" name="installation_path" placeholder="Enter your installation path" value="{{ base_path() }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100"><i class="fas fa-unlock me-1"></i> Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // 1) AJAX helper: fetch new cards + pagination HTML
    function fetchAddons(url) {
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(html) {
                $('#addons-container').html(html);
            }
        });
    }

    // 2) Intercept filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        let query = $(this).serialize();
        let url = $(this).attr('action') + '?' + query;
        fetchAddons(url);
    });

    // 3) Whenever any select changes, auto‐submit the filter form
    $('#filterForm select').on('change', function() {
        $('#filterForm').submit();
    });

    // 4) Intercept pagination link clicks inside #addons-container
    $(document).on('click', '#addons-container .pagination a', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        fetchAddons(url);
    });

    // 5) Copy License Key to clipboard (unchanged)
    $(document).on('click', '.btn-copy-license', function() {
        let key = $(this).data('key');
        navigator.clipboard.writeText(key).then(function() {
            Swal.fire('Copied!', 'License Key copied to clipboard.', 'success');
        });
    });

    // 6) “Preview” button logic (unchanged)
    function openPreviewModal(data) {
        $('#modalModuleTitle').text(data.name);
        $('#modalModuleDesc').html($('<div/>').html(data.desc).text());

        let features = data.features ? data.features.split('|') : [];
        let listHtml = '';
        features.forEach(function(f) {
            if (f) listHtml += `<li>${f}</li>`;
        });
        $('#modalModuleFeatures').html(listHtml);

        $('#modalModuleVersion').text(data.version || '–');
        $('#modalModuleCompatible').text(data.compatible || '–');
        $('#modalModuleUpdated').text(data.last_updated || '–');
        $('#modalModuleAuthor').text(data.author || '–');
        $('#modalModuleRequirements').text(data.requirements || '–');
        $('#modalModulePrice').text(data.price ? `₹${parseInt(data.price).toLocaleString()}` : '–');

        if (!data.isPurchased) {
            $('#modalPurchaseBtn').show().data('name', data.name);
        } else {
            $('#modalPurchaseBtn').hide();
        }

        $('#previewModal').modal('show');
    }

    $(document).on('click', '.btn-preview', function() {
        let $btn = $(this);
        openPreviewModal({
            name:          $btn.data('name'),
            desc:          $btn.data('desc'),
            version:       $btn.data('version'),
            compatible:    $btn.data('compatible'),
            price:         $btn.data('price'),
            features:      $btn.data('features'),
            last_updated:  $btn.data('last_updated'),
            author:        $btn.data('author'),
            requirements:  $btn.data('requirements'),
            isPurchased:   false
        });
    });

    // 7) “Purchase” button logic (unchanged)
    $(document).on('click', '.btn-purchase', function() {
        let name = $(this).data('name');
        $('#purchaseModuleName').val(name);
        $('#purchaseCode').val('');
        $('#licenseKey').val('');
        $('#purchaseModalLabel').text('Activate ' + name);
        $('#purchaseModal').modal('show');
    });

    $('#modalPurchaseBtn').click(function() {
        let name = $(this).data('name');
        $('#previewModal').modal('hide');
        setTimeout(function() {
            $('#purchaseModuleName').val(name);
            $('#purchaseCode').val('');
            $('#licenseKey').val('');
            $('#purchaseModalLabel').text('Activate ' + name);
            $('#purchaseModal').modal('show');
        }, 350);
    });

    $('#purchaseForm').submit(function(e) {
        e.preventDefault();
        let module = $('#purchaseModuleName').val();
        let purchaseCode = $('#purchaseCode').val();
        let licenseKey = $('#licenseKey').val();

        $('#purchaseModal').modal('hide');
        setTimeout(function() {
            Swal.fire(
                'Submitted!',
                `<b>${module}</b> activated with:<br>Purchase Code: <code>${purchaseCode}</code><br>License Key: <code>${licenseKey}</code>`,
                'success'
            );
        }, 350);
    });
});
</script>
@endpush