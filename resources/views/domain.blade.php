@extends('layouts.master')

@section('content')
<section class="content-body">
    <div class="container-fluid position-relative">
        <div class="d-flex flex-wrap align-items-center justify-content-between text-head mb-3">
            <h2 class="me-auto mb-0">Domain Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                <i class="fas fa-plus pe-2"></i>Add Domain
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Connected Domains</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="domainTable" class="table display">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Domain Name</th>
                                <th>Date Added</th>
                                <th>Subscribers</th>
                                <th>Total Notifications</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $statuses = ['Active', 'Pending', 'Inactive'];
                                $domains = [];
                                for ($i = 1; $i <= 20; $i++) {
                                    $domains[] = [
                                        'id' => $i,
                                        'name' => "domain{$i}.com",
                                        'status' => $statuses[array_rand($statuses)],
                                        'date' => now()->subDays(rand(1, 60))->format('Y-m-d'),
                                        'subs' => rand(0, 1000),
                                        'notifications' => rand(100, 5000),
                                    ];
                                }
                            @endphp

                            @foreach ($domains as $domain)
                                @php
                                    $badgeClass = match(strtolower($domain['status'])) {
                                        'active' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'inactive' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $domain['id'] }}</td>
                                    <td>{{ $domain['name'] }}</td>
                                    <td>{{ $domain['date'] }}</td>
                                    <td>{{ $domain['subs'] }}</td>
                                    <td>{{ $domain['notifications'] }}</td>
                                    <td>
                                        <span class="badge light {{ $badgeClass }}">
                                            {{ $domain['status'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('integrate-domain') }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-plug me-1"></i> Integrate
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Domain Modal -->
<div class="modal fade" id="addDomainModal" tabindex="-1" aria-labelledby="addDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDomainModalLabel">Add New Domain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addDomainForm" method="POST" action="#">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="domainName" class="form-label">
                            Domain Name <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="domainName"
                            name="domain_name"
                            placeholder="example.com"
                            required
                        >
                        <small class="text-muted">
                            Enter the domain name (e.g., "example.com")
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Add Domain
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Ensure SweetAlert2 is loaded (omit if already included in your master layout) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#domainTable').DataTable({
            searching: false,
            lengthChange: false,
            info: false,
            pagingType: 'simple_numbers',
            language: {
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            },
            dom: '<"top"i>rt<"bottom"lp><"clear">'
        });

        // Focus on the input when modal opens
        $('#addDomainModal').on('shown.bs.modal', function() {
            $(this).find('input').focus();
        });

        // Handle Add Domain form submission (static demo)
        $('#addDomainForm').submit(function(e) {
            e.preventDefault();
            const domainName = $(this).find('input').val().trim();

            if (!domainName) {
                alert('Please enter a domain name');
                return;
            }

            $('#addDomainModal').modal('hide');
            $(this).trigger('reset');
            alert(`Domain "${domainName}" added successfully!`);
        });

        // SweetAlert2 confirmation for Delete button
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            // Find the <tr> that contains this delete button
            const row = $(this).closest('tr');
            // Grab the domain name from the second <td>
            const domainName = row.find('td:nth-child(2)').text().trim();

            Swal.fire({
                title: 'Are you sure?',
                text: `You won’t be able to revert deletion of "${domainName}"!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove the row from DataTable and redraw
                    const table = $('#domainTable').DataTable();
                    table.row(row).remove().draw();

                    Swal.fire(
                        'Deleted!',
                        `Domain "${domainName}" has been deleted.`,
                        'success'
                    );
                }
            });
        });
    });
    </script>
@endpush

