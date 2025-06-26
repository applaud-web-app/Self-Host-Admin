@extends('admin.layout.app')

@push('styles')
    {{-- DataTables CSS --}}
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    
@endpush

@section('content')
    <section class="content-body">
        <div class="container-fluid position-relative">
            <div class="d-flex flex-wrap align-items-center justify-content-between text-head mb-3">
                <h2 class="me-auto mb-0">Users</h2>
            </div>

         

            {{-- TABLE --}}
            <div class="row">
                <div class="col-12">
   <div class="card h-auto">
                    <div class="card-body">
        {{-- FILTER: Single Search --}}
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" id="filter-search" class="form-control"
                            placeholder="Search by name, email, phone">
                    </div>
                    <div class="col-md-5">
                        <select id="filter-user" class="form-select form-control" style="width: 100%;">
                            <option value="">All Users</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="filter-reset" class="btn btn-secondary w-100">
                            Reset Filters
                        </button>
                    </div>
                </div>
                </div>
            </div>
       
                </div>
                <div class="col-12">
                    <div class="card h-auto">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="users-table" class="table display">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Join Date</th>
                                            <th>Actions</th>
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

        {{-- EDIT USER MODAL --}}
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="edit-user-form">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit-name" class="form-label">Username</label>
                                <input type="text" class="form-control" id="edit-name" name="name" required>
                                <input type="hidden" id="edit-action-url" name="action_url" value="">
                            </div>

                            <div class="mb-3">
                                <label for="edit-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit-email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="edit-country-code" class="form-label">Country Code</label>
                                <select name="country_code" class="form-control" id="edit-country-code" required>
                                    <option value="">Select Country Code</option>
                                    <option value="+91">+91 (India)</option>
                                    <option value="+1">+1 (USA)</option>
                                    <option value="+44">+44 (UK)</option>
                                    <option value="+61">+61 (Australia)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit-phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="edit-phone" name="phone"
                                    placeholder="9876543210">
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label for="edit-password" class="form-label">Password <small class="text-muted">(leave
                                        blank to keep current)</small></label>
                                <input type="password" class="form-control" id="edit-password" name="password"
                                    placeholder="New password (min. 8 characters)">
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mb-3">
                                <label for="edit-password-confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="edit-password-confirmation"
                                    name="password_confirmation" placeholder="Re-type new password">
                            </div>

                            <div id="edit-user-errors" class="alert alert-danger d-none">
                                {{-- Error messages will be injected here --}}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================= --}}
        {{-- USER FEATURES MODAL (NEW)        --}}
        {{-- ================================= --}}
        <div class="modal fade" id="userFeaturesModal" tabindex="-1" aria-labelledby="userFeaturesModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="userFeaturesModalLabel">User Features</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Loading spinner (shown while AJAX is in progress) --}}
                        <div id="features-loading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        {{-- Container where cards will be injected --}}
                        <div id="feature-list" class="row gy-3"></div>

                        {{-- “No features” placeholder (hidden by default) --}}
                        <div id="no-features" class="text-center text-muted py-4 d-none">
                            <p class="mb-0">No purchased features found for this user.</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
    {{-- DataTables JS --}}
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

   

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
                    data: function(params) {
                        return {
                            q: params.term
                        }; // search term
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        }; // data should be an array of { id, text }
                    },
                    cache: true
                }
            });

            // Initialize DataTable
            const table = $('#users-table').DataTable({
                searching: false,
                paging: false,
                select: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.users.show') }}",
                    data: function(d) {
                        d.search_term = $('#filter-search').val().trim();
                        d.user_id = $('#filter-user').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'full_phone',
                        name: 'phone'
                    },
                    {
                        data: 'join_date',
                        name: 'join_date'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ], // default sort by name
                language: {
                    processing: "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Loading…",
                    paginate: {
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>'
                    }
                }
            });

            // Trigger table redraw on search input
            $('#filter-search').on('keyup', function() {
                table.draw();
            });

            // Trigger table redraw when user changes the Select2 dropdown
            $('#filter-user').on('change', function() {
                table.draw();
            });

            // Reset filter
            $('#filter-reset').on('click', function() {
                $('#filter-search').val('');
                $('#filter-user').val(null).trigger('change');
                table.draw();
            });

            // =====================================
            // “Edit” button → open modal & populate
            // =====================================
            let editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            $('#users-table').on('click', '.btn-edit-user', function() {
                let url = $(this).data('url');
                $('#edit-user-errors').addClass('d-none').html('');

                // Clear previous values
                $('#edit-name').val('');
                $('#edit-email').val('');
                $('#edit-country-code').val('');
                $('#edit-phone').val('');
                $('#edit-password').val('');
                $('#edit-password-confirmation').val('');
                $('#edit-action-url').val('');

                // Fetch single user data via AJAX
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        // Populate form fields
                        $('#edit-name').val(response.name);
                        $('#edit-email').val(response.email);
                        $('#edit-country-code').val(response.country_code);
                        $('#edit-phone').val(response.phone);
                        $('#edit-action-url').val(response.url);

                        // Reset validation state each time we open
                        $('#edit-user-form').validate().resetForm();
                        $('#edit-user-form .is-invalid').removeClass('is-invalid');

                        // Show the modal
                        editModal.show();
                    },
                    error: function(xhr) {
                        alert('Could not fetch user data.');
                    }
                });
            });

            // ================================
            // jQuery Validation on the form
            // ================================
            $('#edit-user-form').validate({
                // Highlight invalid fields
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    country_code: {
                        required: true
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 7
                    },
                    password: {
                        // only validate password if it is not empty
                        minlength: 8
                    },
                    password_confirmation: {
                        equalTo: '#edit-password'
                    }
                },
                messages: {
                    name: {
                        required: "Please enter a username.",
                        minlength: "Name must be at least 2 characters."
                    },
                    email: {
                        required: "Please enter an email address.",
                        email: "Please enter a valid email address."
                    },
                    country_code: {
                        required: "Please select a country code."
                    },
                    phone: {
                        required: "Please enter a phone number.",
                        digits: "Phone must contain only digits.",
                        minlength: "Phone must be at least 7 digits."
                    },
                    password: {
                        minlength: "Password must be at least 8 characters."
                    },
                    password_confirmation: {
                        equalTo: "Passwords do not match."
                    }
                },
                // When the form is valid, perform AJAX submit:
                submitHandler: function(form) {
                    // Gather form data
                    let ajaxUrl = $('#edit-action-url').val();

                    var $btn = $(form).find('button[type="submit"]');
                    var originalText = $btn.text();
                    $btn.prop('disabled', true).text('Processing...');

                    let formData = {
                        name: $('#edit-name').val(),
                        email: $('#edit-email').val(),
                        country_code: $('#edit-country-code').val(),
                        phone: $('#edit-phone').val(),
                        password: $('#edit-password').val(),
                        password_confirmation: $('#edit-password-confirmation').val(),
                        _token: "{{ csrf_token() }}"
                    };

                    $.ajax({
                        url: ajaxUrl,
                        method: 'POST',
                        data: formData,
                        success: function(res) {
                            // Re-enable button and restore text
                            $btn.prop('disabled', false).text(originalText);
                            if (res.success) {
                                iziToast.success({
                                    title: 'Success',
                                    message: res.message,
                                    position: 'topRight'
                                });
                                editModal.hide();
                                table.draw(false); // redraw, but stay on current page
                            } else {
                                // In case the server returns a 200‐OK with success = false
                                iziToast.error({
                                    title: 'Error',
                                    message: res.message ||
                                        'An unexpected error occurred.',
                                    position: 'topRight'
                                });
                            }
                        },
                        error: function(xhr) {
                            // Re-enable button and restore text
                            $btn.prop('disabled', false).text(originalText);

                            // Parse JSON validation errors (422 Unprocessable Entity, etc.)
                            let errors = xhr.responseJSON?.errors || {};
                            let errorList = [];

                            $.each(errors, function(key, msgs) {
                                msgs.forEach(function(msg) {
                                    errorList.push(msg);
                                });
                            });

                            // Build an HTML list for inline display
                            let html = '<ul class="mb-0">';
                            errorList.forEach(function(msg) {
                                html += '<li>' + msg + '</li>';
                            });
                            html += '</ul>';

                            // Show the error block in the modal
                            $('#edit-user-errors')
                                .removeClass('d-none')
                                .html(html);

                            // Show iziToast error toast (join messages with a line break)
                            iziToast.error({
                                title: 'Error',
                                message: errorList.join('<br>'),
                                position: 'topRight'
                            });
                        }
                    });
                    return false; // prevent normal form submission
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // ================================
            // “Features” button → open modal & fetch feature data
            // ================================
            let featuresModal = new bootstrap.Modal(document.getElementById('userFeaturesModal'));

            $('#users-table').on('click', '.btn-user-feature', function() {
                let url = $(this).data('url');

                // Reset/hide everything inside the modal
                $('#feature-list').empty();
                $('#no-features').addClass('d-none');
                $('#features-loading').removeClass('d-none');

                // Show the modal immediately (spinner visible)
                featuresModal.show();

                // Fire AJAX GET to fetch “paid” features
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        // Hide spinner
                        $('#features-loading').addClass('d-none');

                        // response.data is expected to be an array of feature objects,
                        // each containing: product (with fields), purchased_at, amount
                        let features = response.data || [];

                        if (features.length === 0) {
                            // No paid features found
                            $('#no-features').removeClass('d-none');
                            return;
                        }

                        // Otherwise, build a card for each feature
                        features.forEach(function(feature) {
                            // Format purchase date and amount
                            let purchasedAt = feature.purchased_at;
                            let amountText = '₹' + feature.amount; // customize currency symbol as needed

                            // Build the HTML for one card
                            let cardHtml = `
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 shadow-sm d-flex flex-column">
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center position-relative">
                                            <!-- Product type badge in top right -->
                                            <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                                                ${feature.product.type.charAt(0).toUpperCase() + feature.product.type.slice(1)}
                                            </span>

                                            <img src="/storage/icons/${feature.product.icon}" class="mb-3" width="60" height="60" alt="">

                                            <h5 class="card-title mb-2 text-center">${feature.product.name}</h5>
                                            <p class="card-text text-center mb-1">
                                                ${feature.product.description}
                                            </p>
                                            <p class="card-text mb-0">
                                                <small class="text-muted d-block text-center">
                                                    Purchased on: <span class="text-primary">${purchasedAt}</span><br>Amount: <span class="text-primary">${amountText}</span>
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#feature-list').append(cardHtml);
                        });
                    },
                    error: function(xhr) {
                        // Hide spinner
                        $('#features-loading').addClass('d-none');

                        // Show an inline error message if needed
                        let errorMsg = 'Could not fetch features.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }

                        // Display iziToast notification
                        iziToast.error({
                            title: 'Error',
                            message: errorMsg,
                            position: 'topRight'
                        });

                        // Also show “no features” text
                        $('#no-features').removeClass('d-none').text(errorMsg);
                    }
                });
            });
        });
    </script>
@endpush