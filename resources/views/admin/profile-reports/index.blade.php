@extends('admin.layout.master')

@push('css')
<style>
/* ===== Profile Reports page: modern redesign ===== */
:root {
    --pr-primary: #6366f1;
    --pr-primary-dark: #4f46e5;
    --pr-success: #10b981;
    --pr-danger: #ef4444;
    --pr-warning: #f59e0b;
    --pr-info: #06b6d4;
    --pr-slate-50: #f8fafc;
    --pr-slate-100: #f1f5f9;
    --pr-slate-200: #e2e8f0;
    --pr-slate-300: #cbd5e1;
    --pr-slate-500: #64748b;
    --pr-slate-600: #475569;
    --pr-slate-700: #334155;
    --pr-slate-800: #1e293b;
}

/* Stats strip */
.pr-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.pr-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.pr-stat .pr-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.pr-stat .pr-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.pr-stat .pr-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.pr-stat.total    { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.pr-stat.pending  { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
.pr-stat.reviewed { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }
.pr-stat.resolved { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

/* Alert */
.pr-alert-success {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin-bottom: 14px;
    background: #d1fae5; color: #065f46;
    border: 0 !important;
}
.pr-alert-success .close {
    margin-left: auto;
    opacity: 0.5;
    text-shadow: none;
    color: #065f46;
}

/* Main card */
.pr-card {
    background: #fff;
    border: 1px solid var(--pr-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
}
.pr-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 18px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--pr-slate-100);
}
.pr-card-head .pr-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 16px; font-weight: 700; color: var(--pr-slate-800);
}
.pr-card-head .pr-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--pr-primary-dark); font-size: 15px;
}
.pr-card-head .pr-title small {
    display: block; font-weight: 400;
    color: var(--pr-slate-500); font-size: 12px; margin-top: 2px;
}

/* Filter section */
.pr-filters {
    padding: 18px 22px 8px;
    background: var(--pr-slate-50);
    border-bottom: 1px solid var(--pr-slate-100);
}
.pr-filters-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
    gap: 12px;
    align-items: end;
}
.pr-filters .form-group { margin: 0; }
.pr-filters label {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--pr-slate-500);
    margin: 0 0 6px 2px;
}
.pr-filters label i { color: var(--pr-primary); font-size: 11px; }
.pr-filters .form-control {
    width: 100%; height: 40px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--pr-slate-800);
    background: #fff;
    border: 1px solid var(--pr-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.pr-filters .form-control::placeholder { color: var(--pr-slate-300); }
.pr-filters .form-control:focus {
    outline: none;
    border-color: var(--pr-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.pr-actions {
    display: flex; gap: 8px; align-items: end;
}
.pr-btn {
    display: inline-flex;
    align-items: center; gap: 6px;
    height: 40px; padding: 0 18px;
    border-radius: 9px;
    font-size: 13px; font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: box-shadow .15s;
    text-decoration: none;
}
.pr-btn-primary {
    background: linear-gradient(135deg, var(--pr-primary) 0%, var(--pr-primary-dark) 100%);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
}
.pr-btn-primary:hover { color: #fff !important; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); }
.pr-btn-ghost {
    background: #fff;
    color: var(--pr-slate-600) !important;
    border-color: var(--pr-slate-200);
    text-decoration: none;
}
.pr-btn-ghost:hover { background: var(--pr-slate-50); color: var(--pr-slate-800) !important; text-decoration: none; }

/* Entries info */
.pr-entries {
    padding: 12px 22px;
    background: #fff;
    border-bottom: 1px solid var(--pr-slate-100);
    color: var(--pr-slate-500);
    font-size: 13px;
    font-weight: 500;
}
.pr-entries strong { color: var(--pr-slate-700); }

/* Table */
.pr-card .card-body { padding: 0; }
#table-container .table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
#table-container .table thead th {
    background: var(--pr-slate-50) !important;
    color: var(--pr-slate-500) !important;
    font-size: 11px !important; font-weight: 700 !important;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--pr-slate-200) !important;
    text-align: left;
    white-space: nowrap;
}
#table-container .table thead th:last-child { text-align: right; }
#table-container .table tbody td {
    padding: 14px 16px !important;
    vertical-align: middle !important;
    border: 0 !important;
    border-bottom: 1px solid var(--pr-slate-100) !important;
    color: var(--pr-slate-700) !important;
    background: #fff !important;
}
#table-container .table tbody tr:hover td { background: #fafbff !important; }
#table-container .table tbody tr:last-child td { border-bottom: 0 !important; }

.pr-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--pr-slate-500);
    background: var(--pr-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.pr-profile-cell {
    display: flex; align-items: center; gap: 10px;
}
.pr-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; text-transform: uppercase;
    flex-shrink: 0;
}
.pr-avatar.deleted { background: var(--pr-slate-300); }
.pr-profile-link {
    font-weight: 600;
    color: var(--pr-primary-dark) !important;
    text-decoration: none;
}
.pr-profile-link:hover { text-decoration: underline; color: var(--pr-primary-dark) !important; }
.pr-deleted {
    color: var(--pr-slate-400, #94a3b8);
    font-style: italic;
    font-size: 13px;
}
.pr-email {
    color: var(--pr-slate-600);
    font-size: 13px;
    word-break: break-all;
}

/* Type badges */
.pr-type {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
}
.pr-type i { font-size: 10px; }
.pr-type-fake          { background: #fee2e2; color: #991b1b; }
.pr-type-spam          { background: #fef3c7; color: #92400e; }
.pr-type-inappropriate { background: #ede9fe; color: #5b21b6; }
.pr-type-other         { background: var(--pr-slate-100); color: var(--pr-slate-600); }

/* Description */
.pr-description {
    max-width: 320px;
    color: var(--pr-primary-dark) !important;
    font-size: 13px;
    cursor: pointer;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    text-decoration: none;
    border-bottom: 1px dashed transparent;
    transition: border-color .15s;
}
.pr-description:hover { border-bottom-color: var(--pr-primary-dark); }

/* Status dropdown */
#table-container .status-dropdown {
    height: 36px !important;
    padding: 6px 26px 6px 10px !important;
    font-size: 12.5px !important;
    font-weight: 600;
    color: var(--pr-slate-700) !important;
    background: #fff !important;
    border: 1px solid var(--pr-slate-200) !important;
    border-radius: 8px !important;
    cursor: pointer;
    width: 130px !important;
    transition: border-color .15s, box-shadow .15s;
}
#table-container .status-dropdown:focus {
    outline: none;
    border-color: var(--pr-primary) !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important;
}

.pr-date {
    color: var(--pr-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.pr-date small {
    display: block; color: var(--pr-slate-500); font-size: 11px;
}

/* Action buttons */
#table-container .btn-group {
    display: inline-flex;
    gap: 5px;
    flex-wrap: nowrap;
}
#table-container .btn-group .btn {
    display: inline-flex !important;
    align-items: center; justify-content: center;
    width: 34px; height: 34px;
    padding: 0 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    border: 0 !important;
    transition: box-shadow .15s;
    margin: 0 !important;
}
#table-container .btn-group .btn:hover { box-shadow: 0 3px 8px rgba(0,0,0,.12); }
#table-container .btn-group .btn:disabled { opacity: 0.5; cursor: not-allowed; }
#table-container .btn-group .btn.btn-info      { background: #eef2ff !important; color: #4338ca !important; }
#table-container .btn-group .btn.btn-warning   { background: #fef3c7 !important; color: #92400e !important; }
#table-container .btn-group .btn.btn-danger    { background: #fee2e2 !important; color: #991b1b !important; }
#table-container .btn-group .btn.btn-secondary { background: var(--pr-slate-100) !important; color: var(--pr-slate-700) !important; }
#table-container .btn-group .btn.btn-dark      { background: #1e293b !important; color: #fff !important; }
#table-container .btn-group .btn.btn-dark:hover { background: #0f172a !important; }

/* Empty */
#table-container .table tbody tr td[colspan="8"] {
    text-align: center !important;
    padding: 60px 20px !important;
    color: var(--pr-slate-500) !important;
    font-size: 15px;
    background: #fff !important;
}

/* Pagination */
#pagination-container {
    padding: 16px 22px !important;
    border-top: 1px solid var(--pr-slate-100);
    background: var(--pr-slate-50);
    margin: 0 !important;
}
#pagination-container .pagination { margin: 0; justify-content: flex-end; }
#pagination-container .pagination .page-link {
    color: var(--pr-slate-600);
    border-color: var(--pr-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
#pagination-container .pagination .page-item.active .page-link {
    background: var(--pr-primary);
    border-color: var(--pr-primary);
    color: #fff;
}

/* Modal */
#viewReportModal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(15,23,42,.3);
}
#viewReportModal .modal-header {
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--pr-slate-100);
    padding: 18px 22px;
}
#viewReportModal .modal-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 16px; font-weight: 700; color: var(--pr-slate-800);
}
#viewReportModal .modal-body { padding: 22px; }
#viewReportModal .modal-footer {
    padding: 14px 22px;
    background: var(--pr-slate-50);
    border-top: 1px solid var(--pr-slate-100);
}
#viewReportModal .modal-footer .btn-secondary {
    background: #fff;
    color: var(--pr-slate-700);
    border: 1px solid var(--pr-slate-200);
    border-radius: 9px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    height: 40px;
    display: inline-flex; align-items: center; gap: 6px;
}
#viewReportModal .modal-footer .btn-secondary:hover { background: var(--pr-slate-50); }

/* Responsive */
@media (max-width: 992px) {
    .pr-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .pr-filters-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .pr-actions { grid-column: 1 / -1; justify-content: flex-end; }
}
@media (max-width: 768px) {
    #table-container .table thead { display: none; }
    #pagination-container .pagination { justify-content: center; }
}
</style>
@endpush

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

@php
    $totalR = $reports->total();
    $onPagePending  = collect($reports->items())->filter(fn($r) => $r->status === 'pending')->count();
    $onPageReviewed = collect($reports->items())->filter(fn($r) => $r->status === 'reviewed')->count();
    $onPageResolved = collect($reports->items())->filter(fn($r) => $r->status === 'resolved')->count();
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="pr-stats-row">
        <div class="pr-stat total">
            <p class="pr-stat-label">Total Reports</p>
            <p class="pr-stat-value">{{ number_format($totalR) }}</p>
            <i class="fas fa-flag pr-stat-icon"></i>
        </div>
        <div class="pr-stat pending">
            <p class="pr-stat-label">Pending · On This Page</p>
            <p class="pr-stat-value">{{ number_format($onPagePending) }}</p>
            <i class="fas fa-hourglass-half pr-stat-icon"></i>
        </div>
        <div class="pr-stat reviewed">
            <p class="pr-stat-label">Reviewed · On This Page</p>
            <p class="pr-stat-value">{{ number_format($onPageReviewed) }}</p>
            <i class="fas fa-eye pr-stat-icon"></i>
        </div>
        <div class="pr-stat resolved">
            <p class="pr-stat-label">Resolved · On This Page</p>
            <p class="pr-stat-value">{{ number_format($onPageResolved) }}</p>
            <i class="fas fa-check-circle pr-stat-icon"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="pr-alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Main card --}}
    <div class="pr-card">
        <div class="pr-card-head">
            <h5 class="pr-title">
                <span class="pr-title-icon"><i class="fas fa-flag"></i></span>
                Profile Reports
                <small>Review, moderate & take action on flagged profiles</small>
            </h5>
        </div>

        {{-- Filters --}}
        <div class="pr-filters">
            <form method="GET" action="{{ route('admin.profile-reports.index') }}" id="filter-form">
                <div class="pr-filters-grid">
                    <div class="form-group">
                        <label><i class="fas fa-toggle-on"></i> Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Report Type</label>
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            <option value="fake"          {{ request('type') == 'fake'          ? 'selected' : '' }}>Fake Profile</option>
                            <option value="spam"          {{ request('type') == 'spam'          ? 'selected' : '' }}>Spam</option>
                            <option value="inappropriate" {{ request('type') == 'inappropriate' ? 'selected' : '' }}>Inappropriate Content</option>
                            <option value="other"         {{ request('type') == 'other'         ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-search"></i> Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search by profile name or reporter email" value="{{ request('search') }}">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-list-ol"></i> Per Page</label>
                        <select name="per_page" id="per-page-select" class="form-control">
                            <option value="10"  {{ request('per_page', 20) == 10  ? 'selected' : '' }}>10</option>
                            <option value="20"  {{ request('per_page', 20) == 20  ? 'selected' : '' }}>20</option>
                            <option value="50"  {{ request('per_page', 20) == 50  ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                            <option value="250" {{ request('per_page', 20) == 250 ? 'selected' : '' }}>250</option>
                        </select>
                    </div>
                    <div class="pr-actions">
                        <button type="submit" class="pr-btn pr-btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.profile-reports.index') }}" class="pr-btn pr-btn-ghost">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Entries info --}}
        <div class="pr-entries" id="entries-info">
            @if($reports->total() > 0)
                Showing <strong>{{ $reports->firstItem() }}</strong> to
                <strong>{{ $reports->lastItem() }}</strong> of
                <strong>{{ number_format($reports->total()) }}</strong> entries
            @else
                Showing 0 to 0 of 0 entries
            @endif
        </div>

        <div class="card-body">
            <div id="table-container">
                @include('admin.profile-reports.table')
            </div>
        </div>
    </div>
</div>

<!-- View Report Modal -->
<div class="modal fade" id="viewReportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-flag"></i> Report Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="reportDetails">
                <p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
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
