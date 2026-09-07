@extends('admin.layout.master')

@push('css')
<style>
/* ===== Paid Ads page: modern redesign ===== */
:root {
    --pa-primary: #6366f1;
    --pa-primary-dark: #4f46e5;
    --pa-success: #10b981;
    --pa-danger: #ef4444;
    --pa-warning: #f59e0b;
    --pa-info: #06b6d4;
    --pa-slate-50: #f8fafc;
    --pa-slate-100: #f1f5f9;
    --pa-slate-200: #e2e8f0;
    --pa-slate-300: #cbd5e1;
    --pa-slate-500: #64748b;
    --pa-slate-600: #475569;
    --pa-slate-700: #334155;
    --pa-slate-800: #1e293b;
}

/* Cards */
.pa-card {
    background: #fff;
    border: 1px solid var(--pa-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.pa-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--pa-slate-100);
    flex-wrap: wrap;
}
.pa-card-head .pa-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 15px; font-weight: 700; color: var(--pa-slate-800);
}
.pa-card-head .pa-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--pa-primary-dark); font-size: 14px;
}
.pa-card-head .pa-title small {
    display: block; font-weight: 400;
    color: var(--pa-slate-500); font-size: 12px; margin-top: 2px;
}
.pa-card-body { padding: 20px 22px; }

/* Filter form */
.pa-filters .form-group { margin-bottom: 14px; }
.pa-filters label {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--pa-slate-500);
    margin: 0 0 6px 2px;
}
.pa-filters label i { color: var(--pa-primary); font-size: 11px; }
.pa-filters .form-control {
    width: 100%; height: 40px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--pa-slate-800);
    background: #fff;
    border: 1px solid var(--pa-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.pa-filters .form-control:focus {
    outline: none;
    border-color: var(--pa-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.pa-filter-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding-top: 6px;
}
.pa-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 40px;
    padding: 0 18px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: box-shadow .15s;
    text-decoration: none;
}
.pa-btn-primary {
    background: linear-gradient(135deg, var(--pa-primary) 0%, var(--pa-primary-dark) 100%);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
}
.pa-btn-primary:hover { color: #fff !important; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); }
.pa-btn-ghost {
    background: #fff;
    color: var(--pa-slate-600) !important;
    border-color: var(--pa-slate-200);
}
.pa-btn-ghost:hover { background: var(--pa-slate-50); color: var(--pa-slate-800) !important; text-decoration: none; }
.pa-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, .35);
}
.pa-btn-success:hover { color: #fff !important; box-shadow: 0 6px 16px rgba(16, 185, 129, .45); text-decoration: none; }

/* Stats strip */
.pa-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.pa-stat {
    position: relative;
    padding: 18px 20px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.pa-stat .pa-stat-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .95; margin: 0 0 6px; font-weight: 600;
}
.pa-stat .pa-stat-value {
    font-size: 28px; font-weight: 800; line-height: 1.1; margin: 0 0 6px;
}
.pa-stat .pa-stat-sub {
    font-size: 12px; opacity: .85; margin: 0; font-weight: 500;
}
.pa-stat .pa-stat-icon {
    position: absolute; right: 16px; top: 50%;
    transform: translateY(-50%); font-size: 40px; opacity: .3;
}
.pa-stat.total     { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.pa-stat.revenue   { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.pa-stat.active    { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }
.pa-stat.avg       { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }

/* Chart wrapper */
.pa-chart-wrap {
    position: relative;
    padding: 10px;
    background: var(--pa-slate-50);
    border-radius: 10px;
    border: 1px solid var(--pa-slate-100);
    min-height: 260px;
}

/* Table */
.pa-card-body.no-pad { padding: 0; }
.pa-table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
.pa-table thead th {
    background: var(--pa-slate-50);
    color: var(--pa-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid var(--pa-slate-200);
    text-align: left;
    white-space: nowrap;
}
.pa-table thead th:last-child { text-align: right; }
.pa-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--pa-slate-100);
    color: var(--pa-slate-700);
    background: #fff;
}
.pa-table tbody tr:hover td { background: #fafbff; }
.pa-table tbody tr:last-child td { border-bottom: 0; }

/* Cell primitives */
.pa-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--pa-slate-500);
    background: var(--pa-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.pa-user-cell {
    display: flex; align-items: center; gap: 10px;
    min-width: 200px;
}
.pa-user-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; text-transform: uppercase;
    flex-shrink: 0;
}
.pa-user-info { display: flex; flex-direction: column; min-width: 0; }
.pa-user-name { font-weight: 600; color: var(--pa-slate-800); font-size: 13.5px; }
.pa-user-meta { color: var(--pa-slate-500); font-size: 11.5px; word-break: break-all; }
.pa-muted { color: var(--pa-slate-300); }

.pa-package-cell { display: flex; flex-direction: column; gap: 2px; min-width: 130px; }
.pa-pkg-name { font-weight: 700; color: var(--pa-slate-800); font-size: 13.5px; }
.pa-pkg-duration {
    display: inline-flex; align-items: center; gap: 4px;
    color: var(--pa-slate-500); font-size: 11.5px;
    font-weight: 500;
}
.pa-pkg-duration i { font-size: 10px; }

.pa-amount {
    display: inline-block;
    padding: 4px 12px;
    background: #d1fae5;
    color: #065f46;
    border-radius: 6px;
    font-weight: 700;
    font-size: 14px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

/* Status badges */
.pa-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    white-space: nowrap;
}
.pa-status i { font-size: 10px; }
.pa-status-completed { background: #d1fae5; color: #065f46; }
.pa-status-failed    { background: #fee2e2; color: #991b1b; }
.pa-status-pending   { background: #fef3c7; color: #92400e; }

.pa-promo {
    display: inline-flex; flex-direction: column; gap: 3px;
}
.pa-promo-active {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    background: #d1fae5; color: #065f46;
    width: fit-content;
}
.pa-promo-expired {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    background: var(--pa-slate-100); color: var(--pa-slate-600);
    width: fit-content;
}
.pa-promo-na {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    background: var(--pa-slate-100); color: var(--pa-slate-500);
    width: fit-content;
}
.pa-promo-meta { color: var(--pa-slate-500); font-size: 11px; font-weight: 500; }

.pa-date {
    color: var(--pa-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.pa-date small {
    display: block; color: var(--pa-slate-500); font-size: 11px;
}

/* Actions */
.pa-actions-cell { display: inline-flex; gap: 5px; justify-content: flex-end; flex-wrap: nowrap; }
.pa-icon-btn {
    display: inline-flex;
    align-items: center; justify-content: center;
    width: 34px; height: 34px;
    border: 0;
    border-radius: 8px;
    font-size: 13px;
    cursor: pointer;
    transition: box-shadow .15s;
    text-decoration: none;
}
.pa-icon-btn:hover { box-shadow: 0 3px 8px rgba(0,0,0,.12); text-decoration: none; }
.pa-icon-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.pa-icon-btn.profile { background: #f3e8ff; color: #6b21a8; }
.pa-icon-btn.profile:hover { background: #e9d5ff; color: #6b21a8; }
.pa-icon-btn.view    { background: #eef2ff; color: var(--pa-primary-dark); }
.pa-icon-btn.view:hover { background: #e0e7ff; color: var(--pa-primary-dark); }
.pa-icon-btn.stop    { background: #fee2e2; color: #991b1b; }
.pa-icon-btn.stop:hover { background: #fecaca; color: #991b1b; }
.pa-icon-btn.muted   { background: var(--pa-slate-100); color: var(--pa-slate-500); }

/* Empty */
.pa-empty td {
    text-align: center !important;
    padding: 60px 20px !important;
    color: var(--pa-slate-500) !important;
}

/* Pagination */
.pa-pagination {
    padding: 16px 22px;
    border-top: 1px solid var(--pa-slate-100);
    background: var(--pa-slate-50);
}
.pa-pagination .pagination { margin: 0; justify-content: flex-end; }
.pa-pagination .pagination .page-link {
    color: var(--pa-slate-600);
    border-color: var(--pa-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.pa-pagination .pagination .page-item.active .page-link {
    background: var(--pa-primary);
    border-color: var(--pa-primary);
    color: #fff;
}

/* Top packages table */
.pa-top-name { font-weight: 700; color: var(--pa-slate-800); }
.pa-top-price {
    display: inline-block;
    padding: 3px 10px;
    background: #d1fae5;
    color: #065f46;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12.5px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.pa-top-duration {
    color: var(--pa-slate-600);
    font-weight: 500;
}
.pa-top-revenue {
    color: var(--pa-primary-dark);
    font-weight: 700;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

/* Responsive */
@media (max-width: 992px) {
    .pa-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .pa-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .pa-table thead { display: none; }
    .pa-pagination .pagination { justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Paid Ads Reports</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Monitor paid advertisement purchases and revenue</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.reports.index')}}">Reports</a></li>
            <li class="breadcrumb-item active">Paid Ads</li>
        </ol>
    </div>
</div>

<div class="container-fluid px-0">

    {{-- Filters --}}
    <div class="pa-card">
        <div class="pa-card-head">
            <h5 class="pa-title">
                <span class="pa-title-icon"><i class="fas fa-filter"></i></span>
                Filters
                <small>Narrow down ad purchases and export as CSV</small>
            </h5>
        </div>
        <div class="pa-card-body pa-filters">
            <form method="GET" action="{{ route('admin.reports.paid-ads') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-day"></i> Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-day"></i> Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-gift"></i> Package</label>
                            <select name="package_id" class="form-control">
                                <option value="">All Packages</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-toggle-on"></i> Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed"    {{ request('status') == 'failed'    ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="pa-filter-actions">
                    <button type="submit" class="pa-btn pa-btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports.paid-ads') }}" class="pa-btn pa-btn-ghost">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                    <a href="{{ route('admin.reports.export', ['type' => 'ads'] + request()->all()) }}" class="pa-btn pa-btn-success">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="pa-stats-row">
        <div class="pa-stat total">
            <p class="pa-stat-label"><i class="fas fa-bullhorn"></i> Total Ad Purchases</p>
            <p class="pa-stat-value">{{ number_format($stats['total_purchases']) }}</p>
            <p class="pa-stat-sub">All time</p>
            <i class="fas fa-bullhorn pa-stat-icon"></i>
        </div>
        <div class="pa-stat revenue">
            <p class="pa-stat-label"><i class="fas fa-dollar-sign"></i> Total Revenue</p>
            <p class="pa-stat-value">${{ number_format($stats['total_revenue'], 2) }}</p>
            <p class="pa-stat-sub">From ads</p>
            <i class="fas fa-dollar-sign pa-stat-icon"></i>
        </div>
        <div class="pa-stat active">
            <p class="pa-stat-label"><i class="fas fa-star"></i> Active Promotions</p>
            <p class="pa-stat-value">{{ number_format($stats['active_promotions']) }}</p>
            <p class="pa-stat-sub">Currently running</p>
            <i class="fas fa-star pa-stat-icon"></i>
        </div>
        <div class="pa-stat avg">
            <p class="pa-stat-label"><i class="fas fa-calculator"></i> Average Purchase</p>
            <p class="pa-stat-value">${{ number_format($stats['average_purchase'], 2) }}</p>
            <p class="pa-stat-sub">Per transaction</p>
            <i class="fas fa-calculator pa-stat-icon"></i>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="pa-card">
                <div class="pa-card-head">
                    <h5 class="pa-title">
                        <span class="pa-title-icon"><i class="fas fa-chart-pie"></i></span>
                        Package Popularity
                        <small>Number of purchases per package</small>
                    </h5>
                </div>
                <div class="pa-card-body">
                    <div class="pa-chart-wrap">
                        <canvas id="packageChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="pa-card">
                <div class="pa-card-head">
                    <h5 class="pa-title">
                        <span class="pa-title-icon"><i class="fas fa-chart-bar"></i></span>
                        Revenue by Package
                        <small>Total revenue generated per package</small>
                    </h5>
                </div>
                <div class="pa-card-body">
                    <div class="pa-chart-wrap">
                        <canvas id="revenueChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Purchases table --}}
    <div class="pa-card">
        <div class="pa-card-head">
            <h5 class="pa-title">
                <span class="pa-title-icon"><i class="fas fa-bullhorn"></i></span>
                Paid Advertisement Purchases
                <small>{{ number_format($purchases->total()) }} purchase{{ $purchases->total() === 1 ? '' : 's' }}</small>
            </h5>
        </div>
        <div class="pa-card-body no-pad">
            <div class="table-responsive">
                <table class="pa-table">
                    <thead>
                        <tr>
                            <th style="width:80px;">ID</th>
                            <th>User</th>
                            <th>Package</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Promotion Status</th>
                            <th>Purchase Date</th>
                            <th style="width:130px; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            @php
                                $statusIcon = match($purchase->status) {
                                    'completed' => 'fas fa-check',
                                    'failed'    => 'fas fa-times',
                                    default     => 'fas fa-clock',
                                };
                            @endphp
                            <tr>
                                <td><span class="pa-id-chip">#{{ $purchase->id }}</span></td>
                                <td>
                                    @if($purchase->wallet && $purchase->wallet->user)
                                        <div class="pa-user-cell">
                                            <span class="pa-user-avatar">{{ strtoupper(mb_substr($purchase->wallet->user->name ?? 'U', 0, 1)) }}</span>
                                            <span class="pa-user-info">
                                                <span class="pa-user-name">{{ $purchase->wallet->user->name }}</span>
                                                <span class="pa-user-meta">{{ $purchase->wallet->user->email }}</span>
                                            </span>
                                        </div>
                                    @else
                                        <span class="pa-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($purchase->package)
                                        <div class="pa-package-cell">
                                            <span class="pa-pkg-name">{{ $purchase->package->name }}</span>
                                            <span class="pa-pkg-duration"><i class="fas fa-clock"></i> {{ $purchase->package->duration }} days</span>
                                        </div>
                                    @else
                                        <span class="pa-muted">Package not found</span>
                                    @endif
                                </td>
                                <td><span class="pa-amount">${{ number_format($purchase->amount, 2) }}</span></td>
                                <td>
                                    <span class="pa-status pa-status-{{ $purchase->status }}">
                                        <i class="{{ $statusIcon }}"></i> {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($purchase->promotion_status)
                                        <div class="pa-promo">
                                            @if($purchase->promotion_status['is_active'])
                                                <span class="pa-promo-active"><i class="fas fa-circle"></i> Active</span>
                                                <span class="pa-promo-meta">Until {{ $purchase->promotion_status['expires_at'] }}</span>
                                            @else
                                                <span class="pa-promo-expired"><i class="fas fa-hourglass-end"></i> Expired</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="pa-promo-na">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="pa-date">
                                        {{ $purchase->created_at->format('M d, Y') }}
                                        <small>{{ $purchase->created_at->format('H:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="pa-actions-cell">
                                        @if($purchase->wallet && $purchase->wallet->user)
                                            @php
                                                $profile = \App\Models\UsersProfile::where('user_id', $purchase->wallet->user->id)->first();
                                            @endphp
                                            @if($profile)
                                                <a href="{{ url('my-profile/' . $profile->slug . '/' . $profile->id) }}" class="pa-icon-btn profile" title="View Profile" target="_blank">
                                                    <i class="fa fa-user"></i>
                                                </a>
                                            @else
                                                <button class="pa-icon-btn muted" title="Profile Not Found" disabled>
                                                    <i class="fa fa-user"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button class="pa-icon-btn muted" title="User Not Found" disabled>
                                                <i class="fa fa-user"></i>
                                            </button>
                                        @endif
                                        <button class="pa-icon-btn view" title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        @if($purchase->status == 'completed' && $purchase->promotion_status && $purchase->promotion_status['is_active'])
                                            <button class="pa-icon-btn stop" title="Stop Promotion">
                                                <i class="fa fa-stop"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="pa-empty">
                                <td colspan="8">
                                    <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <p style="color:#64748b; margin:0; font-size:15px;">No paid advertisement purchases found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($purchases->hasPages())
                <div class="pa-pagination">
                    {{ $purchases->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Top packages --}}
    <div class="pa-card">
        <div class="pa-card-head">
            <h5 class="pa-title">
                <span class="pa-title-icon"><i class="fas fa-trophy"></i></span>
                Top Performing Packages
                <small>Sales, revenue and monthly averages per package</small>
            </h5>
        </div>
        <div class="pa-card-body no-pad">
            <div class="table-responsive">
                <table class="pa-table">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Total Sales</th>
                            <th>Revenue</th>
                            <th>Avg. Monthly Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['package_stats'] as $packageStat)
                            <tr>
                                <td><span class="pa-top-name">{{ $packageStat['name'] }}</span></td>
                                <td><span class="pa-top-price">${{ number_format($packageStat['price'], 2) }}</span></td>
                                <td><span class="pa-top-duration">{{ $packageStat['duration'] }} days</span></td>
                                <td>{{ number_format($packageStat['total_sales']) }}</td>
                                <td><span class="pa-top-revenue">${{ number_format($packageStat['revenue'], 2) }}</span></td>
                                <td>{{ number_format($packageStat['avg_monthly_sales'], 1) }}</td>
                            </tr>
                        @empty
                            <tr class="pa-empty">
                                <td colspan="6">
                                    <p style="color:#64748b; margin:0; font-size:14px;">No package sales data available yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    const colors = ['#6366f1', '#10b981', '#f59e0b', '#06b6d4', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];

    // Package popularity chart (doughnut)
    const packageCanvas = document.getElementById('packageChart');
    if (packageCanvas) {
        new Chart(packageCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: @json($stats['package_names'] ?? []),
                datasets: [{
                    data: @json($stats['package_counts'] ?? []),
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 8, padding: 14, color: '#334155', font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10, cornerRadius: 8,
                        titleFont: { weight: '600' }
                    }
                }
            }
        });
    }

    // Revenue by package chart (bar)
    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        new Chart(revenueCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($stats['package_names'] ?? []),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($stats['package_revenues'] ?? []),
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderColor: '#059669',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { usePointStyle: true, boxWidth: 8, padding: 14, color: '#334155', font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10, cornerRadius: 8,
                        titleFont: { weight: '600' },
                        callbacks: {
                            label: function(ctx) { return ' $' + Number(ctx.raw).toLocaleString(); }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            callback: function(value) { return '$' + value; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });
    }
});
</script>
@endpush
