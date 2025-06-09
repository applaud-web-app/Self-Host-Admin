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
                                            <th>User Email</th> {{-- NEW COLUMN --}}
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Paid At</th>
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
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    {{-- Include Select2 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 on the #filter-user dropdown
            $('#filter-user').select2({
                placeholder: 'Select a user (email)',
                allowClear: true,
                ajax: {
                    url: "{{ route('admin.users.ajax') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        // data should be an array of { id, text }
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // Initialize DataTable
            const table = $('#payments-table').DataTable({
                searching: false,
                paging: false,
                select: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.payment.show') }}",
                    data: function (d) {
                        // SINGLE search term
                        d.search_term = $('#filter-search').val().trim();

                        // user_id from the Select2
                        d.user_id = $('#filter-user').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'razorpay_order_id',
                        name: 'razorpay_order_id'
                    },
                    {
                        data: 'product_name',
                        name: 'product.name'
                    },
                    {
                        data: 'product_type',
                        name: 'product.type'
                    },
                    {
                        data: 'user_email',
                        name: 'user.email'
                    }, // NEW column definition
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'paid_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [7, 'desc'] // “Paid At” column is index 7 (0-based)
                ],
                language: {
                    processing: "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Loading…",
                    paginate: {
                        previous: '<i class="fas fa-angle-double-left"></i>',
                        next: '<i class="fas fa-angle-double-right"></i>'
                    }
                }
            });

            // Whenever the single search input changes, redraw the table
            $('#filter-search').on('keyup', function() {
                table.draw();
            });

            // Whenever the Select2 user filter changes, redraw
            $('#filter-user').on('change', function() {
                table.draw();
            });

            // Reset all filters
            $('#filter-reset').on('click', function() {
                $('#filter-search').val('');
                // Clear Select2 selection
                $('#filter-user').val(null).trigger('change');
                table.draw();
            });
        });
    </script>
@endpush