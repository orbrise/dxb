@extends('admin.layout.master')

@push('css')
<style>
/* ===== Balance Transfers page: modern redesign ===== */
:root {
    --bt-primary: #6366f1;
    --bt-primary-dark: #4f46e5;
    --bt-success: #10b981;
    --bt-danger: #ef4444;
    --bt-warning: #f59e0b;
    --bt-info: #06b6d4;
    --bt-slate-50: #f8fafc;
    --bt-slate-100: #f1f5f9;
    --bt-slate-200: #e2e8f0;
    --bt-slate-300: #cbd5e1;
    --bt-slate-500: #64748b;
    --bt-slate-600: #475569;
    --bt-slate-700: #334155;
    --bt-slate-800: #1e293b;
}

/* Cards */
.bt-card {
    background: #fff;
    border: 1px solid var(--bt-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.bt-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--bt-slate-100);
    flex-wrap: wrap;
}
.bt-card-head .bt-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 15px; font-weight: 700; color: var(--bt-slate-800);
}
.bt-card-head .bt-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--bt-primary-dark); font-size: 14px;
}
.bt-card-head .bt-title small {
    display: block; font-weight: 400;
    color: var(--bt-slate-500); font-size: 12px; margin-top: 2px;
}
.bt-card-body { padding: 20px 22px; }

/* Filter form */
.bt-filters .form-group { margin-bottom: 14px; }
.bt-filters label {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--bt-slate-500);
    margin: 0 0 6px 2px;
}
.bt-filters label i { color: var(--bt-primary); font-size: 11px; }
.bt-filters .form-control {
    width: 100%; height: 40px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--bt-slate-800);
    background: #fff;
    border: 1px solid var(--bt-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.bt-filters .form-control:focus {
    outline: none;
    border-color: var(--bt-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.bt-filter-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding-top: 6px;
}
.bt-btn {
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
.bt-btn-primary {
    background: linear-gradient(135deg, var(--bt-primary) 0%, var(--bt-primary-dark) 100%);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
}
.bt-btn-primary:hover { color: #fff !important; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); }
.bt-btn-ghost {
    background: #fff;
    color: var(--bt-slate-600) !important;
    border-color: var(--bt-slate-200);
}
.bt-btn-ghost:hover { background: var(--bt-slate-50); color: var(--bt-slate-800) !important; text-decoration: none; }
.bt-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, .35);
}
.bt-btn-success:hover { color: #fff !important; box-shadow: 0 6px 16px rgba(16, 185, 129, .45); text-decoration: none; }

/* Stats strip */
.bt-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.bt-stat {
    position: relative;
    padding: 18px 20px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.bt-stat .bt-stat-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .95; margin: 0 0 6px; font-weight: 600;
}
.bt-stat .bt-stat-value {
    font-size: 28px; font-weight: 800; line-height: 1.1; margin: 0 0 6px;
}
.bt-stat .bt-stat-sub {
    font-size: 12px; opacity: .85; margin: 0; font-weight: 500;
}
.bt-stat .bt-stat-icon {
    position: absolute; right: 16px; top: 50%;
    transform: translateY(-50%); font-size: 40px; opacity: .3;
}
.bt-stat.total     { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.bt-stat.amount    { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bt-stat.success   { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }
.bt-stat.avg       { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }

/* Table */
.bt-card-body.no-pad { padding: 0; }
.bt-table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
.bt-table thead th {
    background: var(--bt-slate-50);
    color: var(--bt-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid var(--bt-slate-200);
    text-align: left;
    white-space: nowrap;
}
.bt-table thead th:last-child { text-align: right; }
.bt-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--bt-slate-100);
    color: var(--bt-slate-700);
    background: #fff;
}
.bt-table tbody tr:hover td { background: #fafbff; }
.bt-table tbody tr:last-child td { border-bottom: 0; }

/* Cell primitives */
.bt-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--bt-slate-500);
    background: var(--bt-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.bt-wallet-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: #4338ca;
    background: #eef2ff;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.bt-user-cell {
    display: flex; align-items: center; gap: 10px;
    min-width: 200px;
}
.bt-user-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; text-transform: uppercase;
    flex-shrink: 0;
}
.bt-user-info { display: flex; flex-direction: column; min-width: 0; }
.bt-user-name { font-weight: 600; color: var(--bt-slate-800); font-size: 13.5px; }
.bt-user-meta { color: var(--bt-slate-500); font-size: 11.5px; word-break: break-all; }
.bt-muted { color: var(--bt-slate-300); }

.bt-amount {
    display: inline-block;
    padding: 4px 12px;
    background: #d1fae5;
    color: #065f46;
    border-radius: 6px;
    font-weight: 700;
    font-size: 14px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

/* Status */
.bt-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    white-space: nowrap;
}
.bt-status i { font-size: 10px; }
.bt-status-completed { background: #d1fae5; color: #065f46; }
.bt-status-failed    { background: #fee2e2; color: #991b1b; }
.bt-status-pending   { background: #fef3c7; color: #92400e; }

.bt-desc { color: var(--bt-slate-600); font-size: 13px; }

.bt-date {
    color: var(--bt-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.bt-date small {
    display: block; color: var(--bt-slate-500); font-size: 11px;
}

/* Actions */
.bt-actions-cell { display: inline-flex; gap: 5px; justify-content: flex-end; }
.bt-icon-btn {
    display: inline-flex;
    align-items: center; justify-content: center;
    width: 34px; height: 34px;
    background: #eef2ff;
    color: var(--bt-primary-dark);
    border: 0;
    border-radius: 8px;
    font-size: 13px;
    cursor: pointer;
    transition: box-shadow .15s;
}
.bt-icon-btn:hover { box-shadow: 0 3px 8px rgba(0,0,0,.12); background: #e0e7ff; color: var(--bt-primary-dark); }

/* Empty */
.bt-empty td {
    text-align: center !important;
    padding: 60px 20px !important;
    color: var(--bt-slate-500) !important;
}

/* Pagination */
.bt-pagination {
    padding: 16px 22px;
    border-top: 1px solid var(--bt-slate-100);
    background: var(--bt-slate-50);
}
.bt-pagination .pagination { margin: 0; justify-content: flex-end; }
.bt-pagination .pagination .page-link {
    color: var(--bt-slate-600);
    border-color: var(--bt-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.bt-pagination .pagination .page-item.active .page-link {
    background: var(--bt-primary);
    border-color: var(--bt-primary);
    color: #fff;
}

/* Chart canvas wrapper */
.bt-chart-wrap {
    position: relative;
    padding: 10px;
    background: var(--bt-slate-50);
    border-radius: 10px;
    border: 1px solid var(--bt-slate-100);
    min-height: 300px;
}

/* Responsive */
@media (max-width: 992px) {
    .bt-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .bt-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .bt-table thead { display: none; }
    .bt-pagination .pagination { justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Balance Transfer Reports</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Track all balance transfer activities</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.reports.index')}}">Reports</a></li>
            <li class="breadcrumb-item active">Balance Transfers</li>
        </ol>
    </div>
</div>

<div class="container-fluid px-0">

    {{-- Filters --}}
    <div class="bt-card">
        <div class="bt-card-head">
            <h5 class="bt-title">
                <span class="bt-title-icon"><i class="fas fa-filter"></i></span>
                Filters
                <small>Narrow down transfers and export as CSV</small>
            </h5>
        </div>
        <div class="bt-card-body bt-filters">
            <form method="GET" action="{{ route('admin.reports.balance-transfers') }}">
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
                            <label><i class="fas fa-toggle-on"></i> Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed"    {{ request('status') == 'failed'    ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-dollar-sign"></i> Amount Range</label>
                            <select name="amount_range" class="form-control">
                                <option value="">All Amounts</option>
                                <option value="0-50"    {{ request('amount_range') == '0-50'    ? 'selected' : '' }}>$ 0 - $50</option>
                                <option value="51-100"  {{ request('amount_range') == '51-100'  ? 'selected' : '' }}>$ 51 - $ 100</option>
                                <option value="101-500" {{ request('amount_range') == '101-500' ? 'selected' : '' }}>$ 101 - $ 500</option>
                                <option value="500+"    {{ request('amount_range') == '500+'    ? 'selected' : '' }}>$ 500+</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bt-filter-actions">
                    <button type="submit" class="bt-btn bt-btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports.balance-transfers') }}" class="bt-btn bt-btn-ghost">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                    <a href="{{ route('admin.reports.export', ['type' => 'transfers'] + request()->all()) }}" class="bt-btn bt-btn-success">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="bt-stats-row">
        <div class="bt-stat total">
            <p class="bt-stat-label"><i class="fas fa-exchange-alt"></i> Total Transfers</p>
            <p class="bt-stat-value">{{ number_format($stats['total_transfers']) }}</p>
            <p class="bt-stat-sub">All time</p>
            <i class="fas fa-exchange-alt bt-stat-icon"></i>
        </div>
        <div class="bt-stat amount">
            <p class="bt-stat-label"><i class="fas fa-dollar-sign"></i> Total Amount</p>
            <p class="bt-stat-value">${{ number_format($stats['total_amount'], 2) }}</p>
            <p class="bt-stat-sub">Transferred</p>
            <i class="fas fa-dollar-sign bt-stat-icon"></i>
        </div>
        <div class="bt-stat success">
            <p class="bt-stat-label"><i class="fas fa-check-circle"></i> Success Rate</p>
            <p class="bt-stat-value">{{ number_format($stats['success_rate'], 1) }}%</p>
            <p class="bt-stat-sub">{{ number_format($stats['successful_transfers']) }} successful</p>
            <i class="fas fa-check-circle bt-stat-icon"></i>
        </div>
        <div class="bt-stat avg">
            <p class="bt-stat-label"><i class="fas fa-calculator"></i> Average Transfer</p>
            <p class="bt-stat-value">${{ number_format($stats['average_amount'], 2) }}</p>
            <p class="bt-stat-sub">Per transaction</p>
            <i class="fas fa-calculator bt-stat-icon"></i>
        </div>
    </div>

    {{-- Transfers table --}}
    <div class="bt-card">
        <div class="bt-card-head">
            <h5 class="bt-title">
                <span class="bt-title-icon"><i class="fas fa-exchange-alt"></i></span>
                Balance Transfers
                <small>{{ number_format($transfers->total()) }} transfer{{ $transfers->total() === 1 ? '' : 's' }}</small>
            </h5>
        </div>
        <div class="bt-card-body no-pad">
            <div class="table-responsive">
                <table class="bt-table">
                    <thead>
                        <tr>
                            <th style="width:80px;">ID</th>
                            <th>User</th>
                            <th>Wallet ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Transfer Date</th>
                            <th style="width:80px; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            @php
                                $statusIcon = match($transfer->status) {
                                    'completed' => 'fas fa-check',
                                    'failed'    => 'fas fa-times',
                                    default     => 'fas fa-clock',
                                };
                            @endphp
                            <tr>
                                <td><span class="bt-id-chip">#{{ $transfer->id }}</span></td>
                                <td>
                                    @if($transfer->wallet && $transfer->wallet->user)
                                        <div class="bt-user-cell">
                                            <span class="bt-user-avatar">{{ strtoupper(mb_substr($transfer->wallet->user->name ?? 'U', 0, 1)) }}</span>
                                            <span class="bt-user-info">
                                                <span class="bt-user-name">{{ $transfer->wallet->user->name }}</span>
                                                <span class="bt-user-meta">{{ $transfer->wallet->user->email }}</span>
                                            </span>
                                        </div>
                                    @else
                                        <span class="bt-muted">N/A</span>
                                    @endif
                                </td>
                                <td><span class="bt-wallet-chip">#{{ $transfer->wallet_id }}</span></td>
                                <td><span class="bt-amount">${{ number_format($transfer->amount, 2) }}</span></td>
                                <td>
                                    <span class="bt-status bt-status-{{ $transfer->status }}">
                                        <i class="{{ $statusIcon }}"></i> {{ ucfirst($transfer->status) }}
                                    </span>
                                </td>
                                <td><span class="bt-desc">{{ $transfer->description ?? 'Balance transfer' }}</span></td>
                                <td>
                                    <div class="bt-date">
                                        {{ $transfer->created_at->format('M d, Y') }}
                                        <small>{{ $transfer->created_at->format('H:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="bt-actions-cell">
                                        <button class="bt-icon-btn" title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="bt-empty">
                                <td colspan="8">
                                    <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <p style="color:#64748b; margin:0; font-size:15px;">No balance transfers found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
                <div class="bt-pagination">
                    {{ $transfers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Daily transfer chart --}}
    <div class="bt-card">
        <div class="bt-card-head">
            <h5 class="bt-title">
                <span class="bt-title-icon"><i class="fas fa-chart-line"></i></span>
                Daily Transfer Activity
                <small>Last 30 days</small>
            </h5>
        </div>
        <div class="bt-card-body">
            <div class="bt-chart-wrap">
                <canvas id="transferChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('transferChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($stats['daily_labels'] ?? []),
            datasets: [{
                label: 'Transfer Count',
                data: @json($stats['daily_counts'] ?? []),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#10b981',
                fill: true
            }, {
                label: 'Transfer Amount ($)',
                data: @json($stats['daily_amounts'] ?? []),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.12)',
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#6366f1',
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    labels: { usePointStyle: true, boxWidth: 8, padding: 14, color: '#334155', font: { size: 12, weight: '600' } }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    titleFont: { weight: '600' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    title: { display: true, text: 'Transfer Count', color: '#64748b', font: { weight: '600' } },
                    grid: { color: '#f1f5f9' },
                    ticks: { color: '#64748b' }
                },
                y1: {
                    type: 'linear', display: true, position: 'right',
                    title: { display: true, text: 'Amount ($)', color: '#64748b', font: { weight: '600' } },
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#64748b' }
                },
                x: {
                    grid: { color: '#f1f5f9' },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });
});
</script>
@endpush
