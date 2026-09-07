@extends('admin.layout.master')

@push('css')
<style>
/* ===== Wallet Reports page: modern redesign ===== */
:root {
    --w-primary: #6366f1;
    --w-primary-dark: #4f46e5;
    --w-success: #10b981;
    --w-danger: #ef4444;
    --w-warning: #f59e0b;
    --w-info: #06b6d4;
    --w-slate-50: #f8fafc;
    --w-slate-100: #f1f5f9;
    --w-slate-200: #e2e8f0;
    --w-slate-300: #cbd5e1;
    --w-slate-500: #64748b;
    --w-slate-600: #475569;
    --w-slate-700: #334155;
    --w-slate-800: #1e293b;
}

/* Cards */
.w-card {
    background: #fff;
    border: 1px solid var(--w-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.w-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--w-slate-100);
    flex-wrap: wrap;
}
.w-card-head .w-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 15px; font-weight: 700; color: var(--w-slate-800);
}
.w-card-head .w-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--w-primary-dark); font-size: 14px;
}
.w-card-head .w-title small {
    display: block; font-weight: 400;
    color: var(--w-slate-500); font-size: 12px; margin-top: 2px;
}
.w-card-body { padding: 20px 22px; }

/* Filter form */
.w-filters .form-group { margin-bottom: 14px; }
.w-filters label {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--w-slate-500);
    margin: 0 0 6px 2px;
}
.w-filters label i { color: var(--w-primary); font-size: 11px; }
.w-filters .form-control {
    width: 100%; height: 40px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--w-slate-800);
    background: #fff;
    border: 1px solid var(--w-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.w-filters .form-control:focus {
    outline: none;
    border-color: var(--w-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.w-filter-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding-top: 6px;
}
.w-btn {
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
.w-btn-primary {
    background: linear-gradient(135deg, var(--w-primary) 0%, var(--w-primary-dark) 100%);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
}
.w-btn-primary:hover { color: #fff !important; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); }
.w-btn-ghost {
    background: #fff;
    color: var(--w-slate-600) !important;
    border-color: var(--w-slate-200);
}
.w-btn-ghost:hover { background: var(--w-slate-50); color: var(--w-slate-800) !important; text-decoration: none; }
.w-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, .35);
}
.w-btn-success:hover { color: #fff !important; box-shadow: 0 6px 16px rgba(16, 185, 129, .45); text-decoration: none; }

/* Stats strip */
.w-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.w-stat {
    position: relative;
    padding: 18px 20px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
    transition: transform .15s, box-shadow .15s;
}
.w-stat.linkable { cursor: pointer; }
.w-stat.linkable:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(15,23,42,.14); }
.w-stat .w-stat-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .95; margin: 0 0 6px; font-weight: 600;
}
.w-stat .w-stat-value {
    font-size: 28px; font-weight: 800; line-height: 1.1; margin: 0 0 6px;
}
.w-stat .w-stat-sub {
    font-size: 12px; opacity: .85; margin: 0; font-weight: 500;
}
.w-stat .w-stat-icon {
    position: absolute; right: 16px; top: 50%;
    transform: translateY(-50%); font-size: 40px; opacity: .3;
}
.w-stat.total     { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.w-stat.amount    { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.w-stat.completed { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }
.w-stat.failed    { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }

/* Table */
.w-card-body.no-pad { padding: 0; }
.w-table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
.w-table thead th {
    background: var(--w-slate-50);
    color: var(--w-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid var(--w-slate-200);
    text-align: left;
    white-space: nowrap;
}
.w-table thead th:last-child { text-align: right; }
.w-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--w-slate-100);
    color: var(--w-slate-700);
    background: #fff;
}
.w-table tbody tr:hover td { background: #fafbff; }
.w-table tbody tr:last-child td { border-bottom: 0; }

/* Cell primitives */
.w-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--w-slate-500);
    background: var(--w-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.w-user-cell {
    display: flex; align-items: center; gap: 10px;
    min-width: 200px;
}
.w-user-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; text-transform: uppercase;
    flex-shrink: 0;
}
.w-user-info { display: flex; flex-direction: column; min-width: 0; }
.w-user-name { font-weight: 600; color: var(--w-slate-800); font-size: 13.5px; }
.w-user-meta { color: var(--w-slate-500); font-size: 11.5px; word-break: break-all; }

/* Type badge */
.w-type {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    white-space: nowrap;
}
.w-type i { font-size: 10px; }
.w-type-deposit    { background: #d1fae5; color: #065f46; }
.w-type-withdrawal { background: #fee2e2; color: #991b1b; }
.w-type-transfer   { background: #dbeafe; color: #1e40af; }
.w-type-package    { background: #fef3c7; color: #92400e; }
.w-type-default    { background: var(--w-slate-100); color: var(--w-slate-600); }

.w-method {
    display: inline-block;
    padding: 3px 9px;
    background: var(--w-slate-100);
    color: var(--w-slate-700);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}
.w-amount {
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
.w-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    white-space: nowrap;
}
.w-status i { font-size: 10px; }
.w-status-completed { background: #d1fae5; color: #065f46; }
.w-status-failed    { background: #fee2e2; color: #991b1b; }
.w-status-cancelled { background: #fef3c7; color: #92400e; }
.w-status-abandoned { background: var(--w-slate-100); color: var(--w-slate-600); }
.w-status-pending   { background: #dbeafe; color: #1e40af; }

/* Reason cell */
.w-reason-fail { color: #b91c1c; font-weight: 600; font-size: 13px; }
.w-reason-cancel { color: #92400e; font-weight: 600; font-size: 13px; }
.w-reason-abandoned { color: var(--w-slate-600); font-weight: 600; font-size: 13px; }
.w-reason-pending { color: #1e40af; font-weight: 600; font-size: 13px; }
.w-reason-desc { color: var(--w-slate-600); font-size: 13px; }
.w-reason-meta { color: var(--w-slate-500); font-size: 11px; margin-top: 4px; display: block; }
.w-reason-meta code {
    background: var(--w-slate-100);
    color: var(--w-slate-700);
    padding: 1px 6px;
    border-radius: 4px;
    font-size: 11px;
}

.w-reference code {
    background: var(--w-slate-100);
    color: var(--w-slate-700);
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11.5px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.w-date {
    color: var(--w-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.w-date small {
    display: block; color: var(--w-slate-500); font-size: 11px;
}

/* Contact icons */
.w-contact {
    display: inline-flex; gap: 5px; align-items: center;
}
.w-contact-btn {
    display: inline-flex;
    align-items: center; justify-content: center;
    width: 34px; height: 34px;
    border-radius: 8px;
    font-size: 13px;
    text-decoration: none;
    transition: box-shadow .15s;
}
.w-contact-btn:hover { box-shadow: 0 3px 8px rgba(0,0,0,.12); text-decoration: none; }
.w-contact-btn.email {
    background: #eef2ff;
    color: var(--w-primary-dark);
}
.w-contact-btn.email:hover { background: #e0e7ff; color: var(--w-primary-dark); }
.w-contact-btn.wa {
    background: #d1fae5;
    color: #065f46;
}
.w-contact-btn.wa:hover { background: #a7f3d0; color: #065f46; }

/* Empty */
.w-empty td {
    text-align: center !important;
    padding: 60px 20px !important;
    color: var(--w-slate-500) !important;
}

/* Pagination */
.w-pagination {
    padding: 16px 22px;
    border-top: 1px solid var(--w-slate-100);
    background: var(--w-slate-50);
}
.w-pagination .pagination { margin: 0; justify-content: flex-end; }
.w-pagination .pagination .page-link {
    color: var(--w-slate-600);
    border-color: var(--w-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.w-pagination .pagination .page-item.active .page-link {
    background: var(--w-primary);
    border-color: var(--w-primary);
    color: #fff;
}

/* Muted N/A */
.w-muted { color: var(--w-slate-300); }

/* Responsive */
@media (max-width: 992px) {
    .w-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .w-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .w-table thead { display: none; }
    .w-pagination .pagination { justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Wallet Reports</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Detailed wallet transaction analysis</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.reports.index')}}">Reports</a></li>
            <li class="breadcrumb-item active">Wallet Reports</li>
        </ol>
    </div>
</div>

<div class="container-fluid px-0">

    {{-- Filters --}}
    <div class="w-card">
        <div class="w-card-head">
            <h5 class="w-title">
                <span class="w-title-icon"><i class="fas fa-filter"></i></span>
                Filters
                <small>Narrow down transactions and export as CSV</small>
            </h5>
        </div>
        <div class="w-card-body w-filters">
            <form method="GET" action="{{ route('admin.reports.wallet') }}">
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
                            <label><i class="fas fa-exchange-alt"></i> Transaction Type</label>
                            <select name="transaction_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="debit" {{ request('transaction_type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                <option value="credit" {{ request('transaction_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="package_purchase" {{ request('transaction_type') == 'package_purchase' ? 'selected' : '' }}>Package Purchase</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-toggle-on"></i> Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                                <option value="failed"    {{ request('status') == 'failed'    ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="w-filter-actions">
                    <button type="submit" class="w-btn w-btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports.wallet') }}" class="w-btn w-btn-ghost">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                    <a href="{{ route('admin.reports.export', ['type' => 'wallet'] + request()->all()) }}" class="w-btn w-btn-success">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="w-stats-row">
        <div class="w-stat total">
            <p class="w-stat-label"><i class="fas fa-list"></i> Total Transactions</p>
            <p class="w-stat-value">{{ number_format($stats['total_transactions']) }}</p>
            <p class="w-stat-sub">All time</p>
            <i class="fas fa-list w-stat-icon"></i>
        </div>
        <div class="w-stat amount">
            <p class="w-stat-label"><i class="fas fa-dollar-sign"></i> Total Amount</p>
            <p class="w-stat-value">${{ number_format($stats['total_amount'], 2) }}</p>
            <p class="w-stat-sub">All transactions</p>
            <i class="fas fa-dollar-sign w-stat-icon"></i>
        </div>
        <div class="w-stat completed">
            <p class="w-stat-label"><i class="fas fa-check-circle"></i> Completed</p>
            <p class="w-stat-value">{{ number_format($stats['completed_transactions']) }}</p>
            <p class="w-stat-sub">{{ number_format($stats['completion_rate'], 1) }}% success rate</p>
            <i class="fas fa-check-circle w-stat-icon"></i>
        </div>
        <a href="{{ route('admin.reports.wallet', array_merge(request()->query(), ['status' => 'failed'])) }}"
           class="w-stat failed linkable" style="text-decoration:none; color:#fff;">
            <p class="w-stat-label"><i class="fas fa-exclamation-triangle"></i> Failed</p>
            <p class="w-stat-value">{{ number_format($stats['failed_transactions']) }}</p>
            <p class="w-stat-sub">Click to review &amp; contact users</p>
            <i class="fas fa-exclamation-triangle w-stat-icon"></i>
        </a>
    </div>

    {{-- Transactions table --}}
    <div class="w-card">
        <div class="w-card-head">
            <h5 class="w-title">
                <span class="w-title-icon"><i class="fas fa-receipt"></i></span>
                Wallet Transactions
                <small>{{ number_format($transactions->total()) }} transaction{{ $transactions->total() === 1 ? '' : 's' }}</small>
            </h5>
        </div>
        <div class="w-card-body no-pad">
            <div class="table-responsive">
                <table class="w-table">
                    <thead>
                        <tr>
                            <th style="width:80px;">ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Reason / Description</th>
                            <th>Reference</th>
                            <th>Date</th>
                            <th style="width:110px; text-align:right;">Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            @php
                                $txUser = ($transaction->wallet && $transaction->wallet->user)
                                    ? $transaction->wallet->user
                                    : $transaction->user;

                                $typeSlug = strtolower($transaction->type ?? '');
                                if (str_contains($typeSlug, 'package'))    $typeClass = 'w-type-package';
                                elseif ($typeSlug === 'deposit')           $typeClass = 'w-type-deposit';
                                elseif ($typeSlug === 'withdrawal')        $typeClass = 'w-type-withdrawal';
                                elseif ($typeSlug === 'transfer')          $typeClass = 'w-type-transfer';
                                else                                       $typeClass = 'w-type-default';

                                $typeIcon = match(true) {
                                    str_contains($typeSlug, 'package') => 'fas fa-gift',
                                    $typeSlug === 'deposit'            => 'fas fa-arrow-down',
                                    $typeSlug === 'withdrawal'         => 'fas fa-arrow-up',
                                    $typeSlug === 'transfer'           => 'fas fa-exchange-alt',
                                    default                            => 'fas fa-coins',
                                };

                                $statusIcon = match($transaction->status) {
                                    'completed' => 'fas fa-check',
                                    'failed'    => 'fas fa-times',
                                    'cancelled' => 'fas fa-ban',
                                    'abandoned' => 'fas fa-clock',
                                    'pending'   => 'fas fa-hourglass-half',
                                    default     => 'fas fa-circle',
                                };
                            @endphp
                            <tr>
                                <td><span class="w-id-chip">#{{ $transaction->id }}</span></td>
                                <td>
                                    @if($txUser)
                                        <div class="w-user-cell">
                                            <span class="w-user-avatar">{{ strtoupper(mb_substr($txUser->name ?? 'U', 0, 1)) }}</span>
                                            <span class="w-user-info">
                                                <span class="w-user-name">{{ $txUser->name }}</span>
                                                <span class="w-user-meta">{{ $txUser->email }}</span>
                                                @if(!empty($txUser->phone))
                                                    <span class="w-user-meta">{{ $txUser->phone }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @else
                                        <span class="w-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="w-type {{ $typeClass }}">
                                        <i class="{{ $typeIcon }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($transaction->payment_method)
                                        <span class="w-method">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                                    @else
                                        <span class="w-muted">—</span>
                                    @endif
                                </td>
                                <td><span class="w-amount">${{ number_format($transaction->amount, 2) }}</span></td>
                                <td>
                                    <span class="w-status w-status-{{ $transaction->status }}">
                                        <i class="{{ $statusIcon }}"></i> {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if(in_array($transaction->status, ['failed', 'cancelled', 'abandoned']))
                                        @php
                                            $reasonClass = [
                                                'failed'    => 'w-reason-fail',
                                                'cancelled' => 'w-reason-cancel',
                                                'abandoned' => 'w-reason-abandoned',
                                            ][$transaction->status] ?? 'w-reason-abandoned';
                                        @endphp
                                        <div class="{{ $reasonClass }}">{{ $transaction->error_message ?: ucfirst($transaction->status) }}</div>
                                        @if($transaction->error_code || $transaction->decline_code)
                                            <span class="w-reason-meta">
                                                @if($transaction->error_code) code: <code>{{ $transaction->error_code }}</code>@endif
                                                @if($transaction->decline_code) &middot; decline: <code>{{ $transaction->decline_code }}</code>@endif
                                            </span>
                                        @endif
                                    @elseif($transaction->status === 'pending')
                                        <div class="w-reason-pending">Awaiting completion</div>
                                        <span class="w-reason-meta">Started {{ $transaction->created_at->diffForHumans() }}</span>
                                    @else
                                        <span class="w-reason-desc">{{ $transaction->description ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td class="w-reference">
                                    @if($transaction->reference)
                                        <code>{{ $transaction->reference }}</code>
                                    @else
                                        <span class="w-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="w-date">
                                        {{ $transaction->created_at->format('M d, Y') }}
                                        <small>{{ $transaction->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td style="text-align:right;">
                                    @if($txUser && $txUser->email)
                                        @php
                                            $subject = rawurlencode('About your recent payment (' . ($transaction->reference ?: '#' . $transaction->id) . ')');
                                            $body = rawurlencode(
                                                "Hi " . ($txUser->name ?: '') . ",\n\n" .
                                                "We noticed a recent payment attempt on our site could not be completed" .
                                                ($transaction->error_message ? " (reason: " . $transaction->error_message . ")" : "") .
                                                ". We'd like to help you complete it.\n\nAmount: $" . number_format($transaction->amount, 2) . "\n" .
                                                ($transaction->reference ? "Reference: " . $transaction->reference . "\n" : "") .
                                                "\nPlease reply to this email if you need any assistance."
                                            );
                                        @endphp
                                        <div class="w-contact">
                                            <a href="mailto:{{ $txUser->email }}?subject={{ $subject }}&body={{ $body }}"
                                               class="w-contact-btn email" title="Email user">
                                                <i class="fa fa-envelope"></i>
                                            </a>
                                            @if(!empty($txUser->phone))
                                                @php $wa = preg_replace('/\D+/', '', $txUser->phone); @endphp
                                                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                                                   class="w-contact-btn wa" title="WhatsApp user">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="w-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="w-empty">
                                <td colspan="10">
                                    <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <p style="color:#64748b; margin:0; font-size:15px;">No transactions found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="w-pagination">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
