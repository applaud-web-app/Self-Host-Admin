@extends('frontend.customer.layout.app')

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <section class="content-body">
        <div class="container-fluid position-relative">
            <div class="d-flex flex-wrap align-items-center justify-content-between text-head mb-3">
                <h2 class="me-auto mb-0">My Payments</h2>
            </div>

            {{-- FILTERS --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <input
                        type="text"
                        id="filter-order-id"
                        class="form-control"
                        placeholder="Filter: Order ID"
                    >
                </div>
                <div class="col-md-3">
                    <input
                        type="text"
                        id="filter-product-name"
                        class="form-control"
                        placeholder="Filter: Product Name"
                    >
                </div>
                <div class="col-md-3">
                    <select id="filter-product-type" class="form-select form-control">
                        <option value="">All Types</option>
                        <option value="core">Core</option>
                        <option value="addon">Addon</option>
                        {{-- add more product types here if needed --}}
                    </select>
                </div>
                <div class="col-md-3">
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
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Invoice</th>
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
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#payments-table').DataTable({
                searching: false,
                paging: false,
                select: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('customer.payment.show') }}",
                    data: function (d) {
                        // Send our three filter values on each request
                        d.order_id = $('#filter-order-id').val().trim();
                        d.product_name = $('#filter-product-name').val().trim();
                        d.product_type = $('#filter-product-type').val();
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
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'invoice',
                        name: 'invoice'
                    },
                    {
                        data: 'paid_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [6, 'desc'] // “Paid At” column is index 6 (0-based), but you want to order by created_at behind the scenes
                ],
                language: {
                    processing: "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Loading…",
                    paginate: {
                        previous: '<i class="fas fa-angle-double-left"></i>',
                        next: '<i class="fas fa-angle-double-right"></i>'
                    }
                }
            });

            // Whenever any filter changes, re-draw the table
            $('#filter-order-id, #filter-product-name').on('keyup', function() {
                table.draw();
            });
            $('#filter-product-type').on('change', function() {
                table.draw();
            });

            // Reset all filters
            $('#filter-reset').on('click', function() {
                $('#filter-order-id').val('');
                $('#filter-product-name').val('');
                $('#filter-product-type').val('');
                table.draw();
            });
        });
    </script>
@endpush