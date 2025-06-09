@extends('admin.layout.app')
@section('title', 'Addons | Aplu Push')

@section('content')
    @push('styles')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet" />
        <style>
            /* ─── Modern filter‐bar styles ────────────────────────────────────────────────── */
            .filter-card {
                background: #ffffff;
                border: 1px solid #e3e3e3;
                border-radius: 0.375rem;
                padding: 1rem;
                margin-bottom: 1.5rem;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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
            .addon-card:hover {
                border-color: var(--primary);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .addon-icon-bg {
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px auto;
                border-radius: 50%;
                color: #fff;
            }

            .addon-card .addon-badge {
                position: absolute;
                top: 14px;
                right: 18px;
                font-size: .85em;
            }

            .addon-title {
                font-size: 1.18rem;
                font-weight: 600;
                margin-bottom: .35rem;
            }

            .addon-desc {
                font-size: .95rem;
                color: #666;
                min-height: 32px;
            }

            .addon-actions {
                border-top: 1px solid #f1f1f1;
                padding-top: 15px;
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-top: 0px;
            }

            .card-content {
                margin-top: 45px !important;
            }
        </style>
    @endpush
    <section class="content-body">
        <div class="container-fluid position-relative">

            {{-- ─── FILTER BAR ──────────────────────────────────────────────────────────── --}}
            <div class="filter-card">
                <form id="filterForm" method="GET" action="{{ route('admin.addons.show') }}">
                    <div class="filter‐group">
                        {{-- 1) Search by Name --}}
                        <div class="filter‐item">
                            <input type="text" name="search" class="form-control" placeholder="🔍 Search by name…"
                                value="{{ request('search') }}">
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
                        {{-- <div class="filter‐item">
                            <select name="purchase_status" class="form-select form-control">
                                <option value="" {{ request('purchase_status') === null ? 'selected' : '' }}>
                                    All Status
                                </option>
                                <option value="purchased"
                                    {{ request('purchase_status') === 'purchased' ? 'selected' : '' }}>
                                    Purchased
                                </option>
                                <option value="not_purchased"
                                    {{ request('purchase_status') === 'not_purchased' ? 'selected' : '' }}>
                                    Not Purchased
                                </option>
                            </select>
                        </div> --}}

                        {{-- 4) Actions: Filter + Reset --}}
                        <div class="filter‐item filter‐actions d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Apply
                            </button>
                            <a href="{{ route('admin.addons.show') }}" class="btn btn-reset">
                                Reset
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createAddonModal">
                                <i class="fas fa-plus me-1"></i>
                                Add New
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            {{-- ──────────────────────────────────────────────────────────────────────────── --}}

            {{-- === Cards & Pagination Container (AJAX‐reload here) === --}}
            <div id="addons-container">
                @include('admin.addons.partials.cards', [
                    'products' => $products,
                ])
            </div>

            {{-- CREATE ADDON MODAL --}}
            <div class="modal fade" id="createAddonModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="createAddonForm" method="POST" action="{{ route('admin.addons.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Create New Addon</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                {{-- NAME --}}
                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input name="name" class="form-control" required>
                                </div>

                                {{-- DESC --}}
                                <div class="mb-3">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="3" required></textarea>
                                </div>

                                {{-- ICON --}}
                                <div class="mb-3">
                                    <label class="form-label">Icon (jpg/png/svg) <span class="text-danger">*</span></label>
                                    <input type="file" name="icon" class="form-control" accept=".jpg,.jpeg,.png,.svg"
                                        required>
                                    <small class="text-muted">
                                        Recommended size: 60x60px. SVG preferred for scalability.
                                    </small>
                                </div>

                                {{-- ZIP DROPZONE --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        Zip File <span class="text-danger">*</span>
                                    </label>
                                    <div id="zip-dropzone" class="dropzone border rounded">
                                        <div class="dz-message py-5">
                                            Drop .zip here or click (max 3 GB)
                                        </div>
                                    </div>
                                    <input type="hidden" name="zip_file" id="zip_file">
                                    <small class="text-muted">
                                        We upload in 2 MB chunks and auto-cleanup temp files.
                                    </small>
                                </div>

                                {{-- PRICE & VERSION --}}
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                                        <input type="number" min="0" name="price" class="form-control"
                                            placeholder="eg: 999" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Version <span class="text-danger">*</span></label>
                                        <input type="text" name="version" class="form-control" placeholder="1.0.0"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Addon
                                </button>
                            </div>
                        </form>
                    </div>
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
                            <tr>
                                <th scope="row" style="width:130px;">Title</th>
                                <td id="modalModuleTitle"></td>
                            </tr>
                            <tr>
                                <th scope="row">Description</th>
                                <td style="max-width: 480px;">
                                    <div id="modalModuleDesc" style="max-height:160px;overflow:auto;line-height:1.7">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Features</th>
                                <td>
                                    <ul id="modalModuleFeatures" class="mb-0 ps-3"></ul>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Version</th>
                                <td id="modalModuleVersion"></td>
                            </tr>
                            <tr>
                                <th scope="row">Compatible</th>
                                <td id="modalModuleCompatible"></td>
                            </tr>
                            <tr>
                                <th scope="row">Last Updated</th>
                                <td id="modalModuleUpdated"></td>
                            </tr>
                            <tr>
                                <th scope="row">Author</th>
                                <td id="modalModuleAuthor"></td>
                            </tr>
                            <tr>
                                <th scope="row">Requirements</th>
                                <td id="modalModuleRequirements"></td>
                            </tr>
                            <tr>
                                <th scope="row">Price</th>
                                <td id="modalModulePrice"></td>
                            </tr>
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
                            <label for="purchaseCode" class="form-label">Purchase Code <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="purchaseCode" name="purchase_code"
                                placeholder="Enter your purchase code" required>
                        </div>
                        <div class="mb-3">
                            <label for="licenseKey" class="form-label">License Key <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="licenseKey" name="license_key"
                                placeholder="Enter your license key" required>
                        </div>
                        <div class="mb-3">
                            <label for="installation_path" class="form-label">Installation Path <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="installation_path" name="installation_path"
                                placeholder="Enter your installation path" value="{{ base_path() }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-unlock me-1"></i>
                            Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT ADDON MODAL -->
    <div class="modal fade" id="editAddonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <form id="editAddonForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title">Edit Addon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <!-- NAME -->
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input name="name" id="edit_name" class="form-control" required>
                </div>
                <!-- DESCRIPTION -->
                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                </div>
                <!-- ICON UPLOAD -->
                <div class="mb-3">
                    <label class="form-label">Icon (jpg/png/svg)</label>
                    <input type="file" name="icon" id="edit_icon" class="form-control" accept=".jpg,.jpeg,.png,.svg">
                    <small class="text-muted">Leave blank to keep current icon.</small><br>
                    <input type="hidden" name="existing_icon" id="existing_icon">
                    <input type="hidden" name="delete_icon" id="delete_icon" value="0">
                    <img src="" id="icon_img" width="60px" alt="icon_img">
                </div>
                <!-- ZIP DROPZONE -->
                <div class="mb-3">
                    <label class="form-label">Zip File</label>
                    <div id="edit-zip-dropzone" class="dropzone border rounded">
                    <div class="dz-message py-5">Drop .zip here or click (max 3 GB)</div>
                    </div>
                    <input type="hidden" name="zip_file" id="edit_zip_file">
                    <input type="hidden" name="existing_zip" id="existing_zip">
                    <small class="text-muted">Uploading new ZIP will replace the old one.</small>
                </div>
                <!-- PRICE & VERSION -->
                <div class="row g-3">
                    <div class="col-md-6">
                    <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="price" id="edit_price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                    <label class="form-label">Version <span class="text-danger">*</span></label>
                    <input type="text" name="version" id="edit_version" class="form-control" required>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                </div>
            </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
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
                    name: $btn.data('name'),
                    desc: $btn.data('desc'),
                    version: $btn.data('version'),
                    compatible: $btn.data('compatible'),
                    price: $btn.data('price'),
                    features: $btn.data('features'),
                    last_updated: $btn.data('last_updated'),
                    author: $btn.data('author'),
                    requirements: $btn.data('requirements'),
                    isPurchased: false
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
    <!-- Dropzone & iziToast JS (you’ll include iziToast CSS/JS CDN in layout) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

    <script>
        Dropzone.autoDiscover = false;

        $(function() {
            // AJAX filter + pagination
            function fetchAddons(url) {
                $.get(url, html => $('#addons-container').html(html));
            }
            $('#filterForm').on('submit', e => {
                e.preventDefault();
                fetchAddons(e.currentTarget.action + '?' + $(e.currentTarget).serialize());
            });
            $('#filterForm select').on('change', () => $('#filterForm').submit());
            $(document).on('click', '#addons-container .pagination a', e => {
                e.preventDefault();
                fetchAddons(e.currentTarget.href);
            });

            // Dropzone init
            var zipZone = new Dropzone("#zip-dropzone", {
                url: "{{ route('admin.addons.uploadZip') }}",
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 3000, // in MB
                acceptedFiles: ".zip",
                addRemoveLinks: true,
                chunking: true,
                forceChunking: true,
                chunkSize: 2 * 1024 * 1024, // 2 MB
                retryChunks: true,
                retryChunksLimit: 3,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },

                init: function() {
                    this.on("addedfile", file => {
                        if (this.files.length > 1) this.removeFile(this.files[0]);
                    });

                    this.on("success", (file, resp) => {
                        if (resp.filename) {
                            $('#zip_file').val(resp.filename);
                            iziToast.success({
                                title: 'Uploaded',
                                message: 'Zip uploaded successfully!'
                            });
                        }
                    });

                    this.on("error", (file, err) => {
                        iziToast.error({
                            title: 'Upload Error',
                            message: (err.error || err).toString()
                        });
                    });

                    this.on("removedfile", file => {
                        let fn = $('#zip_file').val();
                        if (!fn) return;
                        $('#zip_file').val('');
                        $.post("{{ route('admin.addons.deleteZip') }}", {
                                filename: fn,
                                _token: "{{ csrf_token() }}"
                            })
                            .done(() => {
                                iziToast.info({
                                    title: 'Removed',
                                    message: 'Server file deleted.'
                                });
                            })
                            .fail((xhr) => {
                                iziToast.error({
                                    title: 'Oops',
                                    message: xhr.responseJSON?.error ||
                                        'Could not delete file'
                                });
                            });
                    });
                }
            });

            // block submit until zip
            $('#createAddonForm').on('submit', function(e) {
                if (!$('#zip_file').val()) {
                    e.preventDefault();
                    iziToast.warning({
                        title: 'Hold on',
                        message: 'Please upload a ZIP file first!'
                    });
                }
            });
        });
    </script>

    <script>
        $(function() {
            // Initialize Dropzone for edit modal only once
            Dropzone.autoDiscover = false;
            var editZipZone = new Dropzone("#edit-zip-dropzone", {
            url: "{{ route('admin.addons.uploadZip') }}",
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 3000,
            acceptedFiles: ".zip",
            addRemoveLinks: true,
            chunking: true,
            forceChunking: true,
            chunkSize: 2 * 1024 * 1024,
            retryChunks: true,
            retryChunksLimit: 3,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            init: function() {
                this.on('addedfile', file => { if (this.files.length > 1) this.removeFile(this.files[0]); });
                this.on('success', (file, resp) => {
                if (resp.filename) {
                    $('#edit_zip_file').val(resp.filename);
                    // delete previous ZIP
                    if ($('#existing_zip').val()) {
                    $.post("{{ route('admin.addons.deleteZip') }}", { filename: $('#existing_zip').val(), _token: "{{ csrf_token() }}" });
                    }
                }
                });
                this.on('removedfile', file => {
                let fn = $('#edit_zip_file').val();
                if (!fn) return;
                $('#edit_zip_file').val('');
                $.post("{{ route('admin.addons.deleteZip') }}", { filename: fn, _token: "{{ csrf_token() }}" });
                });
            }
            });

            // Bind click on Edit button to populate modal
            $(document).on('click', '[data-modal="edit-addon"]', function() {
            var btn = $(this);
            var form = $('#editAddonForm');
            form.attr('action', btn.data('url'));
            $('#edit_name').val(btn.data('name'));
            $('#edit_description').val(btn.data('description'));
            $('#edit_price').val(btn.data('price'));
            $('#edit_version').val(btn.data('version'));
            // icon
            $('#existing_icon').val(btn.data('icon'));
            $('#icon_img').attr('src', btn.data('icon') ? "{{ asset('storage/icons/') }}/" + btn.data('icon') : '');
            $('#delete_icon').val(0);
            $('#edit_icon').val('');
            // zip
            $('#existing_zip').val(btn.data('zip') || '');
            $('#edit_zip_file').val('');
            editZipZone.removeAllFiles(true);
            $('#editAddonModal').modal('show');
            });

            // Mark icon deletion when new file selected
            $('#edit_icon').on('change', function() {
            $('#delete_icon').val(this.files.length ? 1 : 0);
            });
        });
    </script>
@endpush
