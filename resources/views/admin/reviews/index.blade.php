@extends('admin.layout.master')

@push('css')
<style>
/* ===== Reviews page: modern redesign ===== */
:root {
    --r-primary: #6366f1;
    --r-primary-dark: #4f46e5;
    --r-success: #10b981;
    --r-danger: #ef4444;
    --r-warning: #f59e0b;
    --r-info: #06b6d4;
    --r-star: #f59e0b;
    --r-slate-50: #f8fafc;
    --r-slate-100: #f1f5f9;
    --r-slate-200: #e2e8f0;
    --r-slate-300: #cbd5e1;
    --r-slate-500: #64748b;
    --r-slate-600: #475569;
    --r-slate-700: #334155;
    --r-slate-800: #1e293b;
}

/* Stats strip */
.r-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.r-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.r-stat .r-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.r-stat .r-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.r-stat .r-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.r-stat.total     { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.r-stat.pending   { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
.r-stat.approved  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.r-stat.avg       { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }

/* Success alert */
.r-alert-success {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin-bottom: 14px;
    background: #d1fae5; color: #065f46;
}

/* Main card */
.r-card {
    background: #fff;
    border: 1px solid var(--r-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
}

/* Filter bar (kept q-filter-bar class for JS-compat, restyled) */
.q-filter-bar {
    padding: 14px 20px !important;
    background: var(--r-slate-50) !important;
    border-bottom: 1px solid var(--r-slate-100) !important;
}
.q-filter-bar #rFiltersForm { gap: 8px !important; }
.q-filter-bar > form > span:first-child {
    display: inline-flex !important; align-items: center; justify-content: center;
    width: 34px; height: 34px; border-radius: 9px;
    background: linear-gradient(135deg, var(--r-primary) 0%, var(--r-primary-dark) 100%);
    color: #fff !important;
    margin-right: 4px !important; padding: 0 !important;
    box-shadow: 0 3px 10px rgba(99, 102, 241, .35);
}
.q-filter-bar > form > span:first-child i { color: #fff !important; font-size: 13px !important; }
.q-filter-bar .dropdown-menu {
    padding: 12px !important;
    border: 1px solid var(--r-slate-200) !important;
    border-radius: 10px !important;
    box-shadow: 0 12px 32px rgba(15,23,42,.15) !important;
}
.q-filter-bar .dropdown-menu input,
.q-filter-bar .dropdown-menu select { cursor: auto; }
.q-filter-bar .dropdown-menu .form-control-sm {
    border-radius: 8px;
    border-color: var(--r-slate-200);
    padding: 7px 10px;
    font-size: 13px;
    height: 36px;
}
.q-filter-bar .dropdown-menu .form-control-sm:focus {
    border-color: var(--r-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.q-filter-bar .dropdown-menu .btn-primary {
    background: linear-gradient(135deg, var(--r-primary) 0%, var(--r-primary-dark) 100%);
    border: 0;
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 3px 10px rgba(99, 102, 241, .3);
}
.q-filter-bar .dropdown-toggle::after { display: none !important; }
.q-filter-bar .filter-caret {
    display: inline-block; margin-left: 6px; font-size: 10px;
}
.q-filter-bar .btn-outline-dark {
    color: var(--r-slate-700) !important;
    background: #fff !important;
    border: 1px solid var(--r-slate-200) !important;
    border-radius: 9px !important;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    height: 38px !important;
    display: inline-flex !important;
    align-items: center;
    transition: all .15s;
}
.q-filter-bar .btn-outline-dark:hover,
.q-filter-bar .btn-outline-dark:focus {
    color: var(--r-primary-dark) !important;
    background: #fff !important;
    border-color: var(--r-primary) !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12) !important;
}
.q-filter-bar .btn-outline-dark .filter-caret { color: var(--r-slate-500); opacity: 1; }
.q-filter-bar .btn-primary {
    background: #eef2ff !important;
    color: var(--r-primary-dark) !important;
    border: 1px solid var(--r-primary) !important;
    border-radius: 9px !important;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    height: 38px !important;
    display: inline-flex !important;
    align-items: center;
    box-shadow: none !important;
}
.q-filter-bar .btn-primary .filter-caret { color: var(--r-primary-dark); }
.q-filter-bar .btn-outline-danger {
    color: var(--r-danger) !important;
    background: #fff !important;
    border: 1px solid var(--r-slate-200) !important;
    border-radius: 9px !important;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    height: 38px !important;
    display: inline-flex !important;
    align-items: center;
}
.q-filter-bar .btn-outline-danger:hover {
    background: #fef2f2 !important;
    border-color: var(--r-danger) !important;
    color: var(--r-danger) !important;
}
.q-filter-bar .ml-auto {
    background: #fff;
    border: 1px solid var(--r-slate-200);
    border-radius: 9px;
    padding: 6px 12px;
    height: 38px;
}
.q-filter-bar .ml-auto label { color: var(--r-slate-500) !important; }
.q-filter-bar .ml-auto .form-control-sm {
    border: 1px solid var(--r-slate-200);
    border-radius: 6px;
    font-size: 12px;
    height: 28px;
    padding: 2px 22px 2px 8px;
}

/* Table */
.r-card .card-body { padding: 0; }
.r-table {
    width: 100% !important;
    margin: 0;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
.r-table thead th {
    background: var(--r-slate-50);
    color: var(--r-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid var(--r-slate-200);
    text-align: left;
    white-space: nowrap;
}
.r-table thead th:last-child { text-align: right; }
.r-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--r-slate-100);
    color: var(--r-slate-700);
    background: #fff;
}
.r-table tbody tr:hover td { background: #fafbff; }
.r-table tbody tr:last-child td { border-bottom: 0; }

/* Cell primitives */
.r-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--r-slate-500);
    background: var(--r-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.r-id-chip.user    { background: #eef2ff; color: #4338ca; }
.r-id-chip.profile { background: #f3e8ff; color: #6b21a8; }

.r-email {
    color: var(--r-slate-600);
    font-size: 13px;
    word-break: break-all;
}
.r-review, .r-reply {
    color: var(--r-slate-700);
    font-size: 13.5px;
    line-height: 1.5;
    max-width: 320px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.r-reply { max-width: 240px; }
.r-empty-reply { color: var(--r-slate-300); font-weight: 500; }
.r-read-more {
    display: block;
    margin-top: 4px;
    color: var(--r-primary-dark) !important;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
}
.r-read-more:hover { text-decoration: underline; }

/* Stars */
.r-stars {
    display: inline-flex;
    align-items: center;
    gap: 2px;
    color: var(--r-star);
    font-size: 14px;
}
.r-stars .off { color: var(--r-slate-200); }
.r-stars-num {
    color: var(--r-slate-500);
    font-size: 12px;
    margin-left: 4px;
    font-weight: 600;
}

/* Status badges */
.r-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    line-height: 1.4;
    white-space: nowrap;
}
.r-status i { font-size: 10px; }
.r-status-approved { background: #d1fae5; color: #065f46; }
.r-status-pending  { background: #fef3c7; color: #92400e; }

.r-date {
    color: var(--r-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.r-date small {
    display: block; color: var(--r-slate-500); font-size: 11px;
}

/* Action buttons */
.r-actions-cell {
    display: inline-flex; gap: 6px; align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
}
.r-actions-cell form { margin: 0; display: inline; }
.r-btn {
    display: inline-flex !important;
    align-items: center; gap: 5px;
    padding: 7px 12px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    border: 0 !important;
    line-height: 1 !important;
    cursor: pointer;
    transition: background .15s;
    text-decoration: none;
}
.r-btn-view      { background: #eef2ff !important; color: var(--r-primary-dark) !important; }
.r-btn-view:hover { background: #e0e7ff !important; color: var(--r-primary-dark) !important; }
.r-btn-approve   { background: #d1fae5 !important; color: #065f46 !important; }
.r-btn-approve:hover { background: #a7f3d0 !important; }
.r-btn-disapprove { background: #fef3c7 !important; color: #92400e !important; }
.r-btn-disapprove:hover { background: #fde68a !important; }
.r-btn-delete    { background: #fee2e2 !important; color: #991b1b !important; }
.r-btn-delete:hover { background: #fecaca !important; color: #991b1b !important; }

/* Empty state */
.r-empty td {
    text-align: center !important;
    padding: 60px 20px !important;
    color: var(--r-slate-500) !important;
}

/* Pagination footer */
.r-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 16px 22px;
    border-top: 1px solid var(--r-slate-100);
    background: var(--r-slate-50);
    font-size: 13px;
    color: var(--r-slate-600);
}
.r-pagination .pagination { margin: 0; }
.r-pagination .pagination .page-link {
    color: var(--r-slate-600);
    border-color: var(--r-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.r-pagination .pagination .page-item.active .page-link {
    background: var(--r-primary);
    border-color: var(--r-primary);
    color: #fff;
}

/* Modal */
#reviewModal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(15,23,42,.3);
}
#reviewModal .modal-header {
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--r-slate-100);
    padding: 18px 22px;
}
#reviewModal .modal-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 16px; font-weight: 700; color: var(--r-slate-800);
}
#reviewModal .modal-body { padding: 22px; }
#reviewModal h6 {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--r-slate-500);
    margin-bottom: 8px;
}
#reviewModal h6 i { color: var(--r-primary); }
#reviewModal .p-3.bg-light {
    background: var(--r-slate-50) !important;
    border: 1px solid var(--r-slate-100);
    border-radius: 10px;
    padding: 14px 16px !important;
    color: var(--r-slate-800) !important;
    line-height: 1.6;
    font-size: 14px;
}
#reviewModal .modal-footer {
    padding: 14px 22px;
    background: var(--r-slate-50);
    border-top: 1px solid var(--r-slate-100);
}
#reviewModal .modal-footer .btn-secondary {
    background: #fff;
    color: var(--r-slate-700);
    border: 1px solid var(--r-slate-200);
    border-radius: 9px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    height: 40px;
    display: inline-flex; align-items: center; gap: 6px;
}
#reviewModal .modal-footer .btn-secondary:hover { background: var(--r-slate-50); }

/* Responsive */
@media (max-width: 992px) {
    .r-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .main-wrapper { padding-left: 8px !important; padding-right: 8px !important; }
    .container-fluid { padding-left: 6px !important; padding-right: 6px !important; }
    .r-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .q-filter-bar { padding: 12px !important; }
    .r-table thead { display: none; }
    .r-pagination { flex-direction: column; text-align: center; }
}

.modal-content .close {
    top: 0.14286em;
    right: 0.14286em;

}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Reviews</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage reviews effectively</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Reviews</li>
        </ol>
    </div>
</div>

@php
    $totalR         = $reviews->total();
    $onPagePending  = collect($reviews->items())->filter(fn($r) => $r->status == 0)->count();
    $onPageApproved = collect($reviews->items())->filter(fn($r) => $r->status == 1)->count();
    $onPageAvg      = collect($reviews->items())->avg('star');
    $onPageAvgLabel = $onPageAvg ? number_format($onPageAvg, 1) : '—';

    $pp = request('perPage', $reviews->perPage());
    $statusLabels = ['1' => 'Approved', '0' => 'Pending'];
    $replyLabels = ['yes' => 'With reply', 'no' => 'No reply'];
    $activeCount = collect(['id','user_id','profile_id','email','q','star','status','has_reply','date_from','date_to'])
        ->filter(fn($k) => request()->filled($k))
        ->count();
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="r-stats-row">
        <div class="r-stat total">
            <p class="r-stat-label">Total Reviews</p>
            <p class="r-stat-value">{{ number_format($totalR) }}</p>
            <i class="fas fa-star-half-alt r-stat-icon"></i>
        </div>
        <div class="r-stat pending">
            <p class="r-stat-label">Pending · On This Page</p>
            <p class="r-stat-value">{{ number_format($onPagePending) }}</p>
            <i class="fas fa-hourglass-half r-stat-icon"></i>
        </div>
        <div class="r-stat approved">
            <p class="r-stat-label">Approved · On This Page</p>
            <p class="r-stat-value">{{ number_format($onPageApproved) }}</p>
            <i class="fas fa-check-circle r-stat-icon"></i>
        </div>
        <div class="r-stat avg">
            <p class="r-stat-label">Avg Star · On This Page</p>
            <p class="r-stat-value">{{ $onPageAvgLabel }}</p>
            <i class="fas fa-star r-stat-icon"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="r-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- Main card --}}
    <div class="r-card">

        {{-- Filter bar --}}
        <div class="card-header q-filter-bar">
            <form method="GET" action="{{ route('reviews.index') }}" id="rFiltersForm" class="d-flex align-items-center flex-wrap" style="gap:6px;">
                <input type="hidden" name="perPage" value="{{ $pp }}">

                <span><i class="fa fa-filter"></i></span>

                {{-- ID --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-hashtag mr-1"></i> ID{{ request('id') ? ': '.request('id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:220px;">
                        <input type="number" name="id" value="{{ request('id') }}" class="form-control form-control-sm mb-2" placeholder="Review ID" min="1">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                {{-- User ID --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('user_id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user mr-1"></i> User ID{{ request('user_id') ? ': '.request('user_id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:220px;">
                        <input type="number" name="user_id" value="{{ request('user_id') }}" class="form-control form-control-sm mb-2" placeholder="User ID" min="1">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                {{-- Profile ID --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('profile_id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-id-badge mr-1"></i> Profile ID{{ request('profile_id') ? ': '.request('profile_id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:220px;">
                        <input type="number" name="profile_id" value="{{ request('profile_id') }}" class="form-control form-control-sm mb-2" placeholder="Profile ID" min="1">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                {{-- Email --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('email') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope mr-1"></i> Email{{ request('email') ? ': '.\Illuminate\Support\Str::limit(request('email'), 20) : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:260px;">
                        <input type="text" name="email" value="{{ request('email') }}" class="form-control form-control-sm mb-2" placeholder="user@example.com">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                {{-- Search --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('q') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-search mr-1"></i> Search{{ request('q') ? ': "'.\Illuminate\Support\Str::limit(request('q'), 20).'"' : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:280px;">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mb-2" placeholder="Search review or reply...">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                {{-- Star --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request()->filled('star') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-star mr-1"></i> Star{{ request()->filled('star') ? ': '.request('star').'★' : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:180px;">
                        <select name="star" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                            <option value="">All stars</option>
                            @for($s = 1; $s <= 5; $s++)
                                <option value="{{ $s }}" {{ (string) request('star') === (string) $s ? 'selected' : '' }}>{{ $s }} ★</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Status --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request()->filled('status') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-toggle-on mr-1"></i> Status{{ request()->filled('status') ? ': '.($statusLabels[request('status')] ?? '') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:180px;">
                        <select name="status" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Approved</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>

                {{-- Reply --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request()->filled('has_reply') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-reply mr-1"></i> Reply{{ request()->filled('has_reply') ? ': '.($replyLabels[request('has_reply')] ?? '') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:180px;">
                        <select name="has_reply" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                            <option value="">Any reply</option>
                            <option value="yes" {{ request('has_reply') === 'yes' ? 'selected' : '' }}>With reply</option>
                            <option value="no" {{ request('has_reply') === 'no' ? 'selected' : '' }}>No reply</option>
                        </select>
                    </div>
                </div>

                {{-- Date range --}}
                <div class="btn-group">
                    @php
                        $dateLabel = '';
                        if (request('date_from') && request('date_to')) $dateLabel = ': '.request('date_from').' → '.request('date_to');
                        elseif (request('date_from')) $dateLabel = ': from '.request('date_from');
                        elseif (request('date_to')) $dateLabel = ': to '.request('date_to');
                    @endphp
                    <button type="button" class="btn btn-sm {{ ($dateLabel !== '') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-calendar-alt mr-1"></i> Date{{ $dateLabel }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:260px;">
                        <label class="small mb-1">From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm mb-2">
                        <label class="small mb-1">To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm mb-2">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                @if($activeCount > 0)
                    <a href="{{ route('reviews.index') }}" class="btn btn-sm btn-outline-danger" title="Reset all filters">
                        <i class="fa fa-times"></i> Reset ({{ $activeCount }})
                    </a>
                @endif

                <div class="ml-auto d-flex align-items-center" style="gap: 8px;">
                    <label class="mb-0 small text-muted">Per page</label>
                    <select name="perPage" class="form-control form-control-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="10"  {{ $pp==10  ? 'selected' : '' }}>10</option>
                        <option value="25"  {{ $pp==25  ? 'selected' : '' }}>25</option>
                        <option value="50"  {{ $pp==50  ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </form>
        </div>

        <script>
            (function() {
                document.querySelectorAll('.q-filter-bar .dropdown-menu').forEach(function(m) {
                    m.addEventListener('click', function(e) { e.stopPropagation(); });
                });
            })();
        </script>

        <div class="card-body">
            <div style="overflow-x: auto;">
                <table class="table r-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">#</th>
                            <th>User ID</th>
                            <th>Profile ID</th>
                            <th>Email</th>
                            <th>Review</th>
                            <th>Reply</th>
                            <th>Star</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th style="width:280px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            @php $starN = (int) ($review->star ?? 0); @endphp
                            <tr>
                                <td><span class="r-id-chip">#{{ $review->id }}</span></td>
                                <td><span class="r-id-chip user">#{{ $review->user_id }}</span></td>
                                <td><span class="r-id-chip profile">#{{ $review->profile_id }}</span></td>
                                <td><span class="r-email">{{ $review->user->email ?? '-' }}</span></td>
                                <td>
                                    <div class="r-review">{{ $review->review }}</div>
                                    @if(strlen($review->review) > 120)
                                        <a href="javascript:void(0)" class="r-read-more" onclick="showReviewModal({{ $review->id }}, `{{ addslashes($review->review) }}`, `{{ addslashes($review->reply ?? '') }}`)">
                                            Read more →
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($review->reply)
                                        <div class="r-reply">{{ $review->reply }}</div>
                                        @if(strlen($review->reply) > 60)
                                            <a href="javascript:void(0)" class="r-read-more" onclick="showReviewModal({{ $review->id }}, `{{ addslashes($review->review) }}`, `{{ addslashes($review->reply) }}`)">
                                                Read more →
                                            </a>
                                        @endif
                                    @else
                                        <span class="r-empty-reply">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="r-stars" title="{{ $starN }} out of 5">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $starN)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="fas fa-star off"></i>
                                            @endif
                                        @endfor
                                        <span class="r-stars-num">{{ $starN }}/5</span>
                                    </span>
                                </td>
                                <td>
                                    @if($review->status == 1)
                                        <span class="r-status r-status-approved"><i class="fas fa-check"></i> Approved</span>
                                    @else
                                        <span class="r-status r-status-pending"><i class="fas fa-clock"></i> Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($review->created_at)
                                        <div class="r-date">
                                            {{ $review->created_at->format('M d, Y') }}
                                            <small>{{ $review->created_at->format('H:i') }}</small>
                                        </div>
                                    @else
                                        <span style="color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="r-actions-cell">
                                        <a href="javascript:void(0)" class="r-btn r-btn-view" onclick="showReviewModal({{ $review->id }}, `{{ addslashes($review->review) }}`, `{{ addslashes($review->reply ?? '') }}`)">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        @if($review->status == 0)
                                            <form action="{{ route('reviews.approve', $review->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="r-btn r-btn-approve"><i class="fa fa-check"></i> Approve</button>
                                            </form>
                                        @else
                                            <form action="{{ route('reviews.disapprove', $review->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="r-btn r-btn-disapprove"><i class="fa fa-ban"></i> Disapprove</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Delete this review?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="r-btn r-btn-delete"><i class="fa-solid fa-trash-can"></i> Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="r-empty">
                                <td colspan="10">
                                    <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <p style="color:#64748b; margin:0; font-size:15px;">No reviews match the selected filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="r-pagination">
            <div>
                @if($reviews->total() > 0)
                    Showing <strong>{{ $reviews->firstItem() }}</strong> to
                    <strong>{{ $reviews->lastItem() }}</strong> of
                    <strong>{{ number_format($reviews->total()) }}</strong> entries
                @else
                    No reviews found
                @endif
            </div>
            <div>{{ $reviews->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">
                    <i class="fas fa-star-half-alt"></i> Review Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6><i class="fas fa-comment"></i> Review</h6>
                    <div class="p-3 bg-light rounded" id="modalReviewText" style="white-space: pre-wrap;"></div>
                </div>
                <div id="modalReplySection" style="display: none;">
                    <h6><i class="fas fa-reply"></i> Reply</h6>
                    <div class="p-3 bg-light rounded" id="modalReplyText" style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showReviewModal(id, review, reply) {
    document.getElementById('modalReviewText').textContent = review;

    if (reply && reply.trim() !== '') {
        document.getElementById('modalReplyText').textContent = reply;
        document.getElementById('modalReplySection').style.display = 'block';
    } else {
        document.getElementById('modalReplySection').style.display = 'none';
    }

    $('#reviewModal').modal('show');
}
</script>

@endsection
