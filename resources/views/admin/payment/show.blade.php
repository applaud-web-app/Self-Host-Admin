@extends('admin.layout.app')

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    {{-- Include Select2 CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <section class="content-body">
        <div class="container-fluid position-relative">
            <div class="d-flex flex-wrap align-items-center justify-content-between text-head mb-3">
                <h2 class="me-auto mb-0">Payments</h2>
            </div>

            {{-- FILTERS --}}
            <div class="row mb-3">
                {{-- Single Search (Order ID OR Product Name) --}}
                <div class="col-md-4">
                    <input
                        type="text"
                        id="filter-search"
                        class="form-control"
                        placeholder="Search Order ID or Product Name"
                    >
                </div>

                {{-- User dropdown (Select2 AJAX) --}}
                <div class="col-md-4">
                    <select
                        id="filter-user"
                        class="form-select form-control"
                        style="width: 100%;"
                    >
                        <option value="">All Users</option>
                    </select>
                </div>

                {{-- Reset Filters --}}
                <div class="col-md-4">
                    <button id="filter-reset" class="btn btn-secondary w-100">
                        Reset Filters
                    </button>
                </div>
            </div>
            {{-- /FILTERS --}}

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table
                                    id="payments-table"
                                    class="table table-striped table-bordered"
                                    style="width:100%"
                                >
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Order Id</th>
                                            <th>Product</th>
                                            <th>Type</th>
                                            <th>User Email</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Paid At</th>
                                            <th>Generate Key</th>
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

        <div class="modal fade" id="generateKey" tabindex="-1" aria-labelledby="generateKeyLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.generate-key.show') }}" method="POST" id="generateKeyForm">
                        @csrf
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="generateKeyLabel">Generate Key</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="product_uuid" id="productUuidField">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 dropdown
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
            var table = $('#payments-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "{{ route('admin.payment.show') }}",
                    data: function(d) {
                        d.search_term = $('#filter-search').val().trim();
                        d.user_id     = $('#filter-user').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'razorpay_order_id' },
                    { data: 'product_name' },
                    { data: 'product_type' },
                    { data: 'user_email' },
                    { data: 'amount' },
                    { data: 'status' },
                    { data: 'paid_at' },
                    { data: 'generate_key', orderable: false, searchable: false }
                ],
                order: [[7, 'desc']],
                language: {
                    processing: "<span class='spinner-border spinner-border-sm'></span> Loading…",
                    paginate: {
                        previous: '<i class="fas fa-angle-double-left"></i>',
                        next: '<i class="fas fa-angle-double-right"></i>'
                    }
                }
            });

            // Redraw on filter changes
            $('#filter-search').keyup(function() { table.draw(); });
            $('#filter-user').change(function() { table.draw(); });
            $('#filter-reset').click(function() {
                $('#filter-search').val('');
                $('#filter-user').val(null).trigger('change');
                table.draw();
            });

            // Generate Key button
            $('#payments-table').on('click', '.btn-generate-key', function() {
                var uuid = $(this).data('uuid');
                $('#productUuidField').val(uuid);
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
                        // simple domain validation (no protocol)
                        pattern: /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
                    }
                },
                messages: {
                    server_ip: {
                        required: "Server IP is required",
                        ipv4:     "Enter a valid IPv4 address (e.g. 192.168.0.1)"
                    },
                    domain_name: {
                        required: "Domain Name is required",
                        pattern:  "Enter a valid domain (e.g. example.com)"
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
                    // prevent submission if invalid
                    e.preventDefault();
                    return;
                }
                var $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
            });
        });
    </script>
@endpush