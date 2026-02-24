@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Profile Reports Management</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">View and manage user-submitted profile reports</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.reports.index')}}">Reports</a></li>
            <li class="breadcrumb-item active">Profile Reports</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Reports</h5>
            </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.profile-reports.index') }}" class="mb-4" id="filter-form">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <select name="type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="fake" {{ request('type') == 'fake' ? 'selected' : '' }}>Fake Profile</option>
                                        <option value="spam" {{ request('type') == 'spam' ? 'selected' : '' }}>Spam</option>
                                        <option value="inappropriate" {{ request('type') == 'inappropriate' ? 'selected' : '' }}>Inappropriate Content</option>
                                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Search by profile name or reporter email" value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Per Page</label>
                                    <select name="per_page" id="per-page-select" class="form-control">
                                        <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                                        <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                                        <option value="250" {{ request('per_page', 20) == 250 ? 'selected' : '' }}>250</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.profile-reports.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Entries Info -->
                    <div class="mb-3" id="entries-info">
                        @if($reports->total() > 0)
                            Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ number_format($reports->total()) }} entries
                        @else
                            Showing 0 to 0 of 0 entries
                        @endif
                    </div>

                    <!-- Reports Table -->
                    <div id="table-container">
                        @include('admin.profile-reports.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Report Modal -->
<div class="modal fade" id="viewReportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="reportDetails">
                <p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    // AJAX fetch function
    function fetchReports(url, isPagination = false) {
        let requestData = {};
        
        if (isPagination && url) {
            const urlObj = new URL(url);
            urlObj.searchParams.forEach((value, key) => {
                requestData[key] = value;
            });
        } else {
            requestData = {
                status: $('select[name="status"]').val(),
                type: $('select[name="type"]').val(),
                search: $('input[name="search"]').val(),
                per_page: $('#per-page-select').val()
            };
        }

        $.ajax({
            url: url || "{{ route('admin.profile-reports.index') }}",
            type: 'GET',
            data: requestData,
            success: function(response) {
                if (typeof response === 'object' && response.table) {
                    $('#table-container').html(response.table);
                    $('#entries-info').html(response.entriesInfo);
                }
            },
            error: function(xhr, status, error) {
                console.error('Filter error:', error);
            }
        });
    }

    // Per Page change
    $('#per-page-select').on('change', function() {
        fetchReports();
    });

    // Pagination clicks
    $(document).on('click', '#pagination-container .pagination a', function(e) {
        e.preventDefault();
        fetchReports($(this).attr('href'), true);
    });

    // Handle status change
    $(document).on('change', '.status-dropdown', function() {
        const reportId = $(this).data('report-id');
        const newStatus = $(this).val();
        const dropdown = $(this);
        
        if (confirm('Are you sure you want to change the status to "' + newStatus + '"?')) {
            $.ajax({
                url: `/admin/profile-reports/${reportId}/status`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error updating status. Please try again.');
                    location.reload();
                }
            });
        } else {
            location.reload();
        }
    });

    // Handle archive button click
    $(document).on('click', '.archive-btn', function() {
        const reportId = $(this).data('report-id');
        const button = $(this);
        takeAction(reportId, 'archive', button);
    });

    // Handle delete profile button click
    $(document).on('click', '.delete-profile-btn', function() {
        const reportId = $(this).data('report-id');
        const button = $(this);
        takeAction(reportId, 'delete', button);
    });

    // Handle view details button click
    $(document).on('click', '.view-details-btn', function() {
        const reportId = $(this).data('report-id');
        viewReportDetails(reportId);
    });

    // Handle delete report button click
    $(document).on('click', '.delete-report-btn', function() {
        const reportId = $(this).data('report-id');
        deleteReport(reportId);
    });
});

function viewReportDetails(reportId) {
    $('#viewReportModal').modal('show');
    $('#reportDetails').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
    
    $.ajax({
        url: `/admin/profile-reports/${reportId}`,
        method: 'GET',
        success: function(response) {
            $('#reportDetails').html(response);
        },
        error: function() {
            $('#reportDetails').html('<p class="text-danger">Error loading report details.</p>');
        }
    });
}

function takeAction(reportId, action, button) {
    let reason = '';
    
    if (action === 'archive') {
        reason = prompt('Enter reason for archiving (optional):');
        if (reason === null) return;
    } else if (action === 'delete') {
        if (!confirm('Are you sure you want to DELETE this profile? This action cannot be undone!')) {
            return;
        }
    }
    
    const originalHtml = button.html();
    button.prop('disabled', true);
    button.html('<i class="fa fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: `/admin/profile-reports/${reportId}/action`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            action: action,
            reason: reason
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                alert(response.message || 'Error taking action.');
                button.prop('disabled', false);
                button.html(originalHtml);
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error taking action. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMsg = 'Profile not found. It may have been already deleted.';
            } else if (xhr.status === 500) {
                errorMsg = 'Server error. Please check the logs.';
            }
            
            alert(errorMsg);
            button.prop('disabled', false);
            button.html(originalHtml);
        }
    });
}

function deleteReport(reportId) {
    if (confirm('Are you sure you want to delete this report?')) {
        $.ajax({
            url: `/admin/profile-reports/${reportId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function() {
                alert('Error deleting report. Please try again.');
            }
        });
    }
}
</script>
@endpush
