@extends('admin.layout.app')
@section('content')
    <section class="content-body">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center text-head ">
                <h2 class="mb-3 me-auto">Coupons List</h2>
                <a href="{{ route('admin.coupons.add') }}" class="btn btn-primary mb-3">Add New Coupon</a>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row mb-4 justify-content-end">
                                <div class="col-xl-3 col-sm-6">
                                    <div class="position-relative mb-xl-0 mb-3">
                                        <input type="text" class="form-control pe-3" name="coupon_search" id="coupon_search" placeholder="Search Coupon Code....">
                                        <i class="fas fa-search text-primary position-absolute top-50 translate-middle-y" style="right: 10px;"></i>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-sm-6 mb-3 mb-xl-0">
                                    <div>
                                        <button id="resetFilters" type="button" class="w-100 btn btn-primary light" title="Click here to remove filter"><i class="fas fa-undo me-1"></i>Reset</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="display table" id="coupon_tb" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Coupon Code</th>
                                            <th>Discount Amount</th>
                                            <th>Expiry Date</th>
                                            <th>Usage Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteUser">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-24 ">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalContent">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.deleteBtn', function () {
                const url = $(this).data('url');
                $modalContent = `<div>
                            <div class="text-center mb-3">
                                <h4>Are you sure you want to delete this coupon?</h4>
                            </div>
                            <div class="text-center">
                                <a href="${url}" class="btn btn-primary">Yes</a>
                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal" aria-label="Close">No</button>
                            </div>
                        </div>`;
                $('#modalContent').html($modalContent);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with server-side processing
            var table = $('#coupon_tb').DataTable({
                searching: false,
                paging: false,
                select: false,
                language: {
                    paginate: {
                        previous: '<i class="fas fa-angle-double-left"></i>',
                        next: '<i class="fas fa-angle-double-right"></i>'
                    }
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{route('admin.coupons.show')}}", // Ensure route name matches your backend route
                    type: "GET",
                    data: function(d) {
                        d.coupon = $('#coupon_search').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coupon',
                        name: 'coupon'
                    },
                    {
                        data: 'discount_amount',
                        name: 'discount_amount'
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date'
                    },
                    {
                        data: 'usage_type',
                        name: 'usage_type'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {}
            });

            // Reload DataTable on filter change
            $('#coupon_search').on('keyup', function() {
                table.ajax.reload();
            });

            // Reset filters without reloading the page
            $('#resetFilters').on('click', function() {
                $('#coupon_search').val('');
                table.ajax.reload();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.updateStatus', function() {
                const url = $(this).data('url');
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {"_token": "{{csrf_token()}}"},
                    success: function(response) {
                        if (response.status == true) {
                            iziToast.success({
                                title: 'success',
                                message: response.message,
                                position: 'topRight'
                            });
                        } else {
                            iziToast.error({
                                title: 'error',
                                message: response.message,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        iziToast.error({
                            title: 'error',
                            message: 'Something Went Wrong : '.error,
                            position: 'topRight'
                        });
                    }
                });
            });
        });
    </script>
@endpush
