@extends('admin.layout.master')

@push('css')
<style>
/* ===== Reports Overview page: modern redesign ===== */
:root {
    --ro-primary: #6366f1;
    --ro-primary-dark: #4f46e5;
    --ro-success: #10b981;
    --ro-danger: #ef4444;
    --ro-warning: #f59e0b;
    --ro-info: #06b6d4;
    --ro-slate-50: #f8fafc;
    --ro-slate-100: #f1f5f9;
    --ro-slate-200: #e2e8f0;
    --ro-slate-300: #cbd5e1;
    --ro-slate-500: #64748b;
    --ro-slate-600: #475569;
    --ro-slate-700: #334155;
    --ro-slate-800: #1e293b;
}

/* Stats grid */
.ro-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin: 4px 0 18px;
}
.ro-stat {
    position: relative;
    padding: 20px;
    border-radius: 14px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(15,23,42,.08);
    display: flex;
    flex-direction: column;
}
.ro-stat-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
}
.ro-stat-label {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .95;
    margin: 0;
}
.ro-stat-icon-wrap {
    width: 38px; height: 38px; border-radius: 10px;
    background: rgba(255,255,255,.18);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 16px; color: #fff;
    backdrop-filter: blur(4px);
}
.ro-stat-value {
    font-size: 32px; font-weight: 800; line-height: 1.1;
    margin: 14px 0 4px;
}
.ro-stat-sub {
    font-size: 12.5px; opacity: .85; margin: 0 0 14px; font-weight: 500;
}
.ro-stat-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    background: rgba(255,255,255,.2);
    color: #fff !important;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    transition: background .15s;
    align-self: flex-start;
    backdrop-filter: blur(4px);
}
.ro-stat-btn:hover { background: rgba(255,255,255,.3); color: #fff !important; text-decoration: none; }
.ro-stat.wallet   { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.ro-stat.transfer { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.ro-stat.ads      { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
.ro-stat.revenue  { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }
.ro-stat.reports  { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }

/* Main card */
.ro-card {
    background: #fff;
    border: 1px solid var(--ro-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.ro-card-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--ro-slate-100);
}
.ro-card-head .ro-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--ro-primary-dark); font-size: 15px;
}
.ro-card-head h5 {
    margin: 0;
    font-size: 16px; font-weight: 700; color: var(--ro-slate-800);
}
.ro-card-head h5 small {
    display: block; font-weight: 400;
    color: var(--ro-slate-500); font-size: 12px; margin-top: 2px;
}
.ro-card-body { padding: 22px; }

/* Menu tiles */
.ro-menu-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}
.ro-tile {
    display: flex;
    flex-direction: column;
    padding: 20px;
    background: var(--ro-slate-50);
    border: 1px solid var(--ro-slate-100);
    border-radius: 12px;
    transition: transform .15s, box-shadow .15s, border-color .15s;
}
.ro-tile:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15,23,42,.08);
    border-color: var(--ro-slate-200);
}
.ro-tile-head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 10px;
}
.ro-tile-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(0,0,0,.08);
}
.ro-tile-icon.wallet   { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #fff; }
.ro-tile-icon.transfer { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; }
.ro-tile-icon.ads      { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); color: #fff; }
.ro-tile-icon.reports  { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: #fff; }
.ro-tile-icon.dashboard{ background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); color: #fff; }
.ro-tile-icon.export   { background: linear-gradient(135deg, #64748b 0%, #334155 100%); color: #fff; }
.ro-tile-title {
    font-size: 15px; font-weight: 700; color: var(--ro-slate-800);
    margin: 0;
}
.ro-tile-desc {
    color: var(--ro-slate-500);
    font-size: 13px;
    line-height: 1.55;
    margin: 0 0 16px;
    flex-grow: 1;
}
.ro-tile-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: box-shadow .15s;
    align-self: flex-start;
    border: 0;
    color: #fff !important;
}
.ro-tile-btn:hover { color: #fff !important; text-decoration: none; }
.ro-tile-btn.wallet    { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 3px 10px rgba(99, 102, 241, .35); }
.ro-tile-btn.wallet:hover    { box-shadow: 0 5px 14px rgba(99, 102, 241, .45); }
.ro-tile-btn.transfer  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 3px 10px rgba(16, 185, 129, .35); }
.ro-tile-btn.transfer:hover  { box-shadow: 0 5px 14px rgba(16, 185, 129, .45); }
.ro-tile-btn.ads       { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); box-shadow: 0 3px 10px rgba(245, 158, 11, .35); }
.ro-tile-btn.ads:hover       { box-shadow: 0 5px 14px rgba(245, 158, 11, .45); }
.ro-tile-btn.reports   { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); box-shadow: 0 3px 10px rgba(239, 68, 68, .35); }
.ro-tile-btn.reports:hover   { box-shadow: 0 5px 14px rgba(239, 68, 68, .45); }
.ro-tile-btn.dashboard { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); box-shadow: 0 3px 10px rgba(6, 182, 212, .35); }
.ro-tile-btn.dashboard:hover { box-shadow: 0 5px 14px rgba(6, 182, 212, .45); }

/* Export tile */
.ro-export-tile { grid-column: 1 / -1; }
@media (min-width: 992px) {
    .ro-export-tile { grid-column: span 2; }
}
.ro-export-btns {
    display: flex; flex-wrap: wrap; gap: 8px;
}
.ro-export-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px;
    background: #fff;
    color: var(--ro-slate-700) !important;
    border: 1px solid var(--ro-slate-200);
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s;
}
.ro-export-btn:hover {
    background: var(--ro-slate-50);
    border-color: var(--ro-primary);
    color: var(--ro-primary-dark) !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    text-decoration: none;
}
.ro-export-btn i { color: var(--ro-primary); }

/* Responsive */
@media (max-width: 1200px) {
    .ro-stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .ro-menu-grid  { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .ro-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .ro-menu-grid  { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
    .ro-stats-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Reports Dashboard</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Comprehensive reports for wallet activities, transfers, and paid ads</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </div>
</div>

@php
    $walletCount    = \App\Models\WalletTransaction::count();
    $transferCount  = \App\Models\WalletTransaction::where('type', 'transfer')->count();
    $adCount        = \App\Models\WalletTransaction::where('type', 'package_purchase')->count();
    $totalRevenue   = \App\Models\WalletTransaction::where('status', 'completed')->sum('amount');
    $pendingReports = \App\Models\Report::where('status', 'pending')->count();
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="ro-stats-grid">
        <div class="ro-stat wallet">
            <div class="ro-stat-head">
                <p class="ro-stat-label">Wallet Reports</p>
                <span class="ro-stat-icon-wrap"><i class="fas fa-wallet"></i></span>
            </div>
            <p class="ro-stat-value">{{ number_format($walletCount) }}</p>
            <p class="ro-stat-sub">Total Transactions</p>
            <a href="{{ route('admin.reports.wallet') }}" class="ro-stat-btn">
                <i class="fas fa-arrow-right"></i> View Details
            </a>
        </div>

        <div class="ro-stat transfer">
            <div class="ro-stat-head">
                <p class="ro-stat-label">Balance Transfers</p>
                <span class="ro-stat-icon-wrap"><i class="fas fa-exchange-alt"></i></span>
            </div>
            <p class="ro-stat-value">{{ number_format($transferCount) }}</p>
            <p class="ro-stat-sub">Total Transfers</p>
            <a href="{{ route('admin.reports.balance-transfers') }}" class="ro-stat-btn">
                <i class="fas fa-arrow-right"></i> View Details
            </a>
        </div>

        <div class="ro-stat ads">
            <div class="ro-stat-head">
                <p class="ro-stat-label">Paid Ads</p>
                <span class="ro-stat-icon-wrap"><i class="fas fa-bullhorn"></i></span>
            </div>
            <p class="ro-stat-value">{{ number_format($adCount) }}</p>
            <p class="ro-stat-sub">Ad Purchases</p>
            <a href="{{ route('admin.reports.paid-ads') }}" class="ro-stat-btn">
                <i class="fas fa-arrow-right"></i> View Details
            </a>
        </div>

        <div class="ro-stat revenue">
            <div class="ro-stat-head">
                <p class="ro-stat-label">Revenue</p>
                <span class="ro-stat-icon-wrap"><i class="fas fa-dollar-sign"></i></span>
            </div>
            <p class="ro-stat-value">${{ number_format($totalRevenue, 2) }}</p>
            <p class="ro-stat-sub">Total Revenue</p>
            <a href="{{ route('admin.reports.dashboard') }}" class="ro-stat-btn">
                <i class="fas fa-chart-line"></i> View Dashboard
            </a>
        </div>
    </div>

    {{-- Profile reports (single card row) --}}
    <div class="ro-stats-grid" style="grid-template-columns: minmax(0, 1fr) 3fr;">
        <div class="ro-stat reports">
            <div class="ro-stat-head">
                <p class="ro-stat-label">Profile Reports</p>
                <span class="ro-stat-icon-wrap"><i class="fas fa-flag"></i></span>
            </div>
            <p class="ro-stat-value">{{ number_format($pendingReports) }}</p>
            <p class="ro-stat-sub">Pending Reports</p>
            <a href="{{ route('admin.profile-reports.index') }}" class="ro-stat-btn">
                <i class="fas fa-arrow-right"></i> View Reports
            </a>
        </div>
        <div style="display:flex; align-items:center; padding: 24px; background: #fff; border: 1px dashed var(--ro-slate-200); border-radius: 14px; color: var(--ro-slate-500); font-size: 13.5px; line-height: 1.6;">
            <i class="fas fa-lightbulb" style="color:#f59e0b; font-size:22px; margin-right:14px;"></i>
            <div>
                <strong style="color: var(--ro-slate-800);">Tip:</strong>
                Click <strong>View Reports</strong> to review pending profile reports.
                Quick actions include archiving, deleting profiles and marking reports resolved directly from the list.
            </div>
        </div>
    </div>

    {{-- Reports Menu --}}
    <div class="ro-card">
        <div class="ro-card-head">
            <span class="ro-title-icon"><i class="fas fa-th-large"></i></span>
            <h5>
                Reports Menu
                <small>Jump directly to a report or export data</small>
            </h5>
        </div>
        <div class="ro-card-body">
            <div class="ro-menu-grid">
                <div class="ro-tile">
                    <div class="ro-tile-head">
                        <span class="ro-tile-icon wallet"><i class="fas fa-wallet"></i></span>
                        <h6 class="ro-tile-title">Wallet Reports</h6>
                    </div>
                    <p class="ro-tile-desc">View detailed wallet transaction reports with filters for date range, transaction type, and status.</p>
                    <a href="{{ route('admin.reports.wallet') }}" class="ro-tile-btn wallet">
                        <i class="fas fa-arrow-right"></i> Open Report
                    </a>
                </div>

                <div class="ro-tile">
                    <div class="ro-tile-head">
                        <span class="ro-tile-icon transfer"><i class="fas fa-exchange-alt"></i></span>
                        <h6 class="ro-tile-title">Balance Transfers</h6>
                    </div>
                    <p class="ro-tile-desc">Track all balance transfer activities with success rates and amounts transferred.</p>
                    <a href="{{ route('admin.reports.balance-transfers') }}" class="ro-tile-btn transfer">
                        <i class="fas fa-arrow-right"></i> Open Report
                    </a>
                </div>

                <div class="ro-tile">
                    <div class="ro-tile-head">
                        <span class="ro-tile-icon ads"><i class="fas fa-bullhorn"></i></span>
                        <h6 class="ro-tile-title">Paid Ads Reports</h6>
                    </div>
                    <p class="ro-tile-desc">Monitor paid advertisement purchases, package popularity, and revenue generation.</p>
                    <a href="{{ route('admin.reports.paid-ads') }}" class="ro-tile-btn ads">
                        <i class="fas fa-arrow-right"></i> Open Report
                    </a>
                </div>

                <div class="ro-tile">
                    <div class="ro-tile-head">
                        <span class="ro-tile-icon reports"><i class="fas fa-flag"></i></span>
                        <h6 class="ro-tile-title">Profile Reports</h6>
                    </div>
                    <p class="ro-tile-desc">View and manage user-submitted profile reports for inappropriate or fake content.</p>
                    <a href="{{ route('admin.profile-reports.index') }}" class="ro-tile-btn reports">
                        <i class="fas fa-arrow-right"></i> Open Report
                    </a>
                </div>

                <div class="ro-tile">
                    <div class="ro-tile-head">
                        <span class="ro-tile-icon dashboard"><i class="fas fa-chart-line"></i></span>
                        <h6 class="ro-tile-title">Dashboard Overview</h6>
                    </div>
                    <p class="ro-tile-desc">Get a comprehensive overview of all activities with summary statistics and recent transactions.</p>
                    <a href="{{ route('admin.reports.dashboard') }}" class="ro-tile-btn dashboard">
                        <i class="fas fa-arrow-right"></i> Open Dashboard
                    </a>
                </div>

                <div class="ro-tile ro-export-tile">
                    <div class="ro-tile-head">
                        <span class="ro-tile-icon export"><i class="fas fa-download"></i></span>
                        <h6 class="ro-tile-title">Export Data</h6>
                    </div>
                    <p class="ro-tile-desc">Export reports to CSV format for external analysis and record keeping.</p>
                    <div class="ro-export-btns">
                        <a href="{{ route('admin.reports.export', ['type' => 'wallet']) }}" class="ro-export-btn">
                            <i class="fas fa-file-csv"></i> Wallet CSV
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'transfers']) }}" class="ro-export-btn">
                            <i class="fas fa-file-csv"></i> Transfers CSV
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'ads']) }}" class="ro-export-btn">
                            <i class="fas fa-file-csv"></i> Ads CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
