@extends('admin.layout.app')

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <section class="content-body">
        <div class="container-fluid position-relative">
            <div class="d-flex flex-wrap align-items-center justify-content-between text-head mb-3">
                <h2 class="me-auto mb-0">Licenses</h2>
                <a href="{{route('admin.generate-script')}}" class="btn btn-primary" target="_blank"> <i class="fas fa-code me-1"></i> Generate Script</a>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card h-auto">
                        <div class="card-body">
                            {{-- FILTERS --}}
                            <div class="row">
                                {{-- Single Search (License Key or Product Name) --}}
                                <div class="col-md-3">
                                    <input
                                        type="text"
                                        id="filter-search"
                                        class="form-control"
                                        placeholder="Search License Key or Product Name"
                                    >
                                </div>

                                {{-- User dropdown (Select2 AJAX) --}}
                                <div class="col-md-3">
                                    <select
                                        id="filter-user"
                                        class="form-select form-control"
                                        style="width: 100%;"
                                    >
                                        <option value="">All Users</option>
                                    </select>
                                </div>

                                {{-- Product Type Dropdown --}}
                                <div class="col-md-3">
                                    <select id="filter-product-type" class="form-select form-control" style="width: 100%;">
                                        <option value="">All Types</option>
                                        <option value="addon">Addon</option>
                                        <option value="core">Core</option>
                                    </select>
                                </div>

                                {{-- Reset Filters --}}
                                <div class="col-md-3">
                                    <button id="filter-reset" class="btn btn-secondary w-100">
                                        Reset Filters
                                    </button>
                                </div>
                            </div>
                            {{-- /FILTERS --}}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="licenses-table" class="table display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Info</th>
                                            <th>User Info</th>
                                            <th>Product Info</th>
                                            <th>Activation Info</th>
                                            <th>Amount</th>
                                            <th>Paid At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- DataTables will inject rows here --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Modal for Generating License Key --}}
        <div class="modal fade" id="generateKey" tabindex="-1" aria-labelledby="generateKeyLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.generate-key.show') }}" method="POST" id="generateKeyForm" autocomplete="off">
                        @csrf
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="generateKeyLabel">Generate Key</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="product_uuid" id="productUuidField">
                            <input type="hidden" name="licence_id" id="licenceID">
                            <div class="mb-3">
                                <label for="server_ip" class="form-label">Server IP <span class="text-danger">*</span></label>
                                <input type="text" name="server_ip" id="server_ip" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                                <input type="text" name="domain_name" id="domain_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer border-0 justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Generate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 dropdown for user filtering
            $('#filter-user').select2({
                placeholder: 'Select a user (email)',
                allowClear: true,
                ajax: {
                    url: "{{ route('admin.users.ajax') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) { return { q: params.term }; },
                    processResults: function(data) { return { results: data }; },
                    cache: true
                }
            });

            // Initialize DataTable
            var table = $('#licenses-table').DataTable({
                searching: false,
                paging: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.license.show') }}",
                    data: function(d) {
                        d.search_term = $('#filter-search').val().trim();
                        d.user_id = $('#filter-user').val();
                        d.product_type = $('#filter-product-type').val();  // Added product type filter
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },  // For index
                    { data: 'payment_details', orderable: false },  // Payment Details (Order ID, Status, User Info)
                    { data: 'user_info', orderable: false },  // Payment Details (Order ID, Status, User Info)
                    { data: 'product_info', orderable: false },  // Product Info (Name, Type, Activation Info)
                    { data: 'activation_info', orderable: false },  // Product Info (Name, Type, Activation Info)
                    { data: 'amount' },  // Amount
                    { data: 'paid_at' },  // Paid At (Date of Payment)
                    { data: 'action', orderable: false, searchable: false }  // Action (Generate Key, Copy Key)
                ],
                order: [[4, 'desc']],
                language: {
                    processing: "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Loading…",
                    paginate: {
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>'
                    }
                }
            });

            // Redraw on filter changes
            $('#filter-search').keyup(function() { table.draw(); });
            $('#filter-user').change(function() { table.draw(); });
            $('#filter-product-type').change(function() { table.draw(); });  // Redraw on product type filter change
            $('#filter-reset').click(function() {
                $('#filter-search').val('');
                $('#filter-user').val(null).trigger('change');
                $('#filter-product-type').val('').trigger('change');  // Reset product type filter
                table.draw();
            });

            // Generate Key button
            $('#licenses-table').on('click', '.btn-generate-key', function() {
                var uuid = $(this).data('uuid');
                var id = $(this).data('id');
                $('#productUuidField').val(uuid);
                $('#licenceID').val(id);
                var modal = new bootstrap.Modal(document.getElementById('generateKey'));
                modal.show();
            });

            // Set up jQuery Validation on the modal form
            $('#generateKeyForm').validate({
                rules: {
                    server_ip: {
                        required: true,
                        ipv4: true
                    },
                    domain_name: {
                        required: true,
                        pattern: /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
                    }
                },
                messages: {
                    server_ip: {
                        required: "Server IP is required",
                        ipv4: "Enter a valid IPv4 address (e.g. 192.168.0.1)"
                    },
                    domain_name: {
                        required: "Domain Name is required",
                        pattern: "Enter a valid domain (e.g. example.com)"
                    }
                },
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).addClass('is-valid').removeClass('is-invalid');
                }
            });

            // Button-processing state on submit
            $('#generateKeyForm').on('submit', function(e) {
                if (!$(this).valid()) {
                    e.preventDefault();
                    return;
                }
                var $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true).html('Processing...');
            });
        });
    </script>

    <script>
        // Copy Key button
        $('#licenses-table').on('click', '.btn-copy-key', function() {
            var key = $(this).data('key');

            // Use the Clipboard API to copy
            navigator.clipboard.writeText(key).then(() => {
                var $btn = $(this);
                // give instant feedback
                $btn
                .text('Copied!')
                .removeClass('btn-primary')
                .addClass('btn-success');

                // revert after 2s
                setTimeout(() => {
                    $btn
                    .text('Copy Key')
                    .removeClass('btn-success')
                    .addClass('btn-primary');
                }, 2000);
            }).catch(err => {
                console.error('Copy failed', err);
                alert('Failed to copy key to clipboard.');
            });
        });
    </script>
@endpush