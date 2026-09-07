@extends('admin.layout.master')

@push('css')
<style>
/* ===== Dashboard: modern redesign ===== */
:root {
    --d-primary: #6366f1;
    --d-primary-dark: #4f46e5;
    --d-success: #10b981;
    --d-danger: #ef4444;
    --d-warning: #f59e0b;
    --d-info: #06b6d4;
    --d-slate-50: #f8fafc;
    --d-slate-100: #f1f5f9;
    --d-slate-200: #e2e8f0;
    --d-slate-300: #cbd5e1;
    --d-slate-500: #64748b;
    --d-slate-600: #475569;
    --d-slate-700: #334155;
    --d-slate-800: #1e293b;
}

/* Stats grid */
.d-stats-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.d-stat {
    position: relative;
    padding: 20px 22px;
    border-radius: 14px;
    color: #fff !important;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(15,23,42,.08);
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    min-height: 140px;
    transition: transform .15s, box-shadow .15s;
}
.d-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 26px rgba(15,23,42,.16);
    color: #fff !important;
    text-decoration: none !important;
}
.d-stat-head {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;
    margin-bottom: 8px;
}
.d-stat-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .95;
    margin: 0;
}
.d-stat-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(255,255,255,.18);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 18px; color: #fff;
}
.d-stat-value {
    font-size: 38px;
    font-weight: 800;
    line-height: 1;
    margin: auto 0 4px;
    color: #fff;
}
.d-stat-sub {
    font-size: 12px; opacity: .85; margin: 0; font-weight: 500;
}
.d-stat.users    { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
.d-stat.total    { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.d-stat.active   { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.d-stat.inactive { background: linear-gradient(135deg, #64748b 0%, #334155 100%); }
.d-stat.verified { background: linear-gradient(135deg, #8b5cf6 0%, #6b21a8 100%); }

/* Modern card */
.d-card {
    background: #fff;
    border: 1px solid var(--d-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.d-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--d-slate-100);
}
.d-card-head .d-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 15px; font-weight: 700; color: var(--d-slate-800);
}
.d-card-head .d-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99,102,241,.12); color: var(--d-primary-dark); font-size: 14px;
}
.d-view-all {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    background: linear-gradient(135deg, var(--d-primary) 0%, var(--d-primary-dark) 100%);
    color: #fff !important;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 3px 10px rgba(99,102,241,.35);
    transition: box-shadow .15s;
}
.d-view-all:hover { box-shadow: 0 5px 14px rgba(99,102,241,.45); color: #fff !important; text-decoration: none; }
.d-card-body { padding: 16px 22px; }
.d-card-body.pad-chart { padding: 12px; }

/* Chart wrapper */
.d-chart-wrap {
    padding: 8px;
    background: var(--d-slate-50);
    border-radius: 10px;
    border: 1px solid var(--d-slate-100);
    min-height: 340px;
}

/* Tables */
.d-table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
.d-table thead th {
    background: var(--d-slate-50);
    color: var(--d-slate-500);
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: 12px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--d-slate-200) !important;
    text-align: left;
    white-space: nowrap;
}
.d-table tbody td {
    padding: 12px 16px !important;
    vertical-align: middle;
    border: 0 !important;
    border-bottom: 1px solid var(--d-slate-100) !important;
    color: var(--d-slate-700);
    background: #fff !important;
}
.d-table tbody tr:hover td { background: #fafbff !important; }
.d-table tbody tr:last-child td { border-bottom: 0 !important; }

.d-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--d-slate-500);
    background: var(--d-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.d-name-cell {
    display: inline-flex; align-items: center; gap: 10px;
}
.d-name-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 12px; text-transform: uppercase;
    flex-shrink: 0;
}
.d-name-link {
    font-weight: 600;
    color: var(--d-primary-dark) !important;
    text-decoration: none;
}
.d-name-link:hover { text-decoration: underline; color: var(--d-primary-dark) !important; }
.d-name-text {
    font-weight: 600;
    color: var(--d-slate-800);
}
.d-email {
    color: var(--d-slate-600);
    font-size: 13px;
    word-break: break-all;
}
.d-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    white-space: nowrap;
}
.d-status i { font-size: 9px; }
.d-status.active { background: #d1fae5; color: #065f46; }
.d-status.inactive { background: #fee2e2; color: #991b1b; }
.d-gender {
    color: var(--d-slate-600);
    font-weight: 500;
    text-transform: capitalize;
    font-size: 13px;
}
.d-date {
    color: var(--d-slate-600);
    font-size: 12.5px;
    white-space: nowrap;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 1200px) {
    .d-stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .d-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .d-stat { min-height: 120px; padding: 16px 18px; }
    .d-stat-value { font-size: 30px; }
}
@media (max-width: 480px) {
    .d-stats-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Dashboard</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">statistics, charts, events and reports</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
</div>

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="d-stats-grid">
        <a href="{{ route('admin.users') }}" class="d-stat users">
            <div class="d-stat-head">
                <p class="d-stat-label">Total Users</p>
                <span class="d-stat-icon"><i class="fas fa-users"></i></span>
            </div>
            <p class="d-stat-value">{{ number_format($stats['total_users']) }}</p>
            <p class="d-stat-sub">All registered users</p>
        </a>

        <a href="{{ route('admin.profiles.index') }}" class="d-stat total">
            <div class="d-stat-head">
                <p class="d-stat-label">Total Profiles</p>
                <span class="d-stat-icon"><i class="fas fa-id-badge"></i></span>
            </div>
            <p class="d-stat-value">{{ number_format($stats['total_profiles']) }}</p>
            <p class="d-stat-sub">All profiles</p>
        </a>

        <a href="{{ url('admin/profiles?id=&start_date=&end_date=&title=&city=&status=1&premium=') }}" class="d-stat active">
            <div class="d-stat-head">
                <p class="d-stat-label">Active Profiles</p>
                <span class="d-stat-icon"><i class="fas fa-check-circle"></i></span>
            </div>
            <p class="d-stat-value">{{ number_format($stats['active_profiles']) }}</p>
            <p class="d-stat-sub">Currently live</p>
        </a>

        <a href="{{ url('admin/profiles?id=&start_date=&end_date=&title=&city=&status=0&premium=') }}" class="d-stat inactive">
            <div class="d-stat-head">
                <p class="d-stat-label">Inactive Profiles</p>
                <span class="d-stat-icon"><i class="fas fa-times-circle"></i></span>
            </div>
            <p class="d-stat-value">{{ number_format($stats['inactive_profiles']) }}</p>
            <p class="d-stat-sub">Not currently live</p>
        </a>

        <a href="{{ url('admin/profiles?id=&start_date=&end_date=&title=&city=&status=verified&premium=') }}" class="d-stat verified">
            <div class="d-stat-head">
                <p class="d-stat-label">Verified Profiles</p>
                <span class="d-stat-icon"><i class="fas fa-shield-alt"></i></span>
            </div>
            <p class="d-stat-value">{{ number_format($stats['verified_profiles']) }}</p>
            <p class="d-stat-sub">Photo-verified</p>
        </a>
    </div>

    {{-- Charts --}}
    <div class="row">
        <div class="col-md-6">
            <div class="d-card">
                <div class="d-card-head">
                    <h5 class="d-title">
                        <span class="d-title-icon"><i class="fas fa-chart-bar"></i></span>
                        Profile Statistics
                    </h5>
                </div>
                <div class="d-card-body pad-chart">
                    <div class="d-chart-wrap">
                        <div id="profileChart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="d-card">
                <div class="d-card-head">
                    <h5 class="d-title">
                        <span class="d-title-icon"><i class="fas fa-chart-line"></i></span>
                        User Statistics
                    </h5>
                </div>
                <div class="d-card-body pad-chart">
                    <div class="d-chart-wrap">
                        <div id="userChart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Latest tables --}}
    <div class="row">
        <div class="col-md-6 col-lg-6">
            <div class="d-card">
                <div class="d-card-head">
                    <h5 class="d-title">
                        <span class="d-title-icon"><i class="fas fa-id-badge"></i></span>
                        Latest Profiles
                    </h5>
                    <a href="{{ url('admin/profiles') }}" class="d-view-all">
                        <i class="fas fa-arrow-right"></i> View All
                    </a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="d-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">ID</th>
                                <th>Title</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latest_profiles as $profile)
                                <tr>
                                    <td><span class="d-id-chip">#{{ $profile->id }}</span></td>
                                    <td>
                                        <div class="d-name-cell">
                                            <span class="d-name-avatar">{{ strtoupper(mb_substr($profile->name, 0, 1)) }}</span>
                                            <a class="d-name-link"
                                               href="/{{ strtolower($profile->ggender->name ?? 'female') }}-escorts-in-{{ strtolower($profile->gcity->name ?? 'dubai') }}/{{ $profile->id }}/{{ $profile->slug }}"
                                               target="_blank">
                                                {{ $profile->name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td><span class="d-gender">{{ $profile->ggender->name ?? '-' }}</span></td>
                                    <td>
                                        @if($profile->is_active)
                                            <span class="d-status active"><i class="fas fa-circle"></i> Active</span>
                                        @else
                                            <span class="d-status inactive"><i class="fas fa-circle"></i> Inactive</span>
                                        @endif
                                    </td>
                                    <td><span class="d-date">{{ $profile->created_at->format('d M Y') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 40px 20px; color:#64748b;">
                                        No profiles yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6">
            <div class="d-card">
                <div class="d-card-head">
                    <h5 class="d-title">
                        <span class="d-title-icon"><i class="fas fa-users"></i></span>
                        Latest Users
                    </h5>
                    <a href="{{ url('admin/users') }}" class="d-view-all">
                        <i class="fas fa-arrow-right"></i> View All
                    </a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="d-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latest_users as $user)
                                <tr>
                                    <td><span class="d-id-chip">#{{ $user->id }}</span></td>
                                    <td>
                                        <div class="d-name-cell">
                                            <span class="d-name-avatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                            <span class="d-name-text">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="d-email">{{ $user->email }}</span></td>
                                    <td><span class="d-date">{{ $user->created_at->format('d M Y') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding: 40px 20px; color:#64748b;">
                                        No users yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{smart_asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{smart_asset('assets/js/pages/index.init.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Shared modern chart options
    const modernChartBase = {
        chart: {
            type: 'bar',
            height: 340,
            fontFamily: 'inherit',
            foreColor: '#64748b',
            toolbar: {
                show: true,
                tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 6,
                borderRadiusApplication: 'end',
                dataLabels: { position: 'top' }
            }
        },
        dataLabels: {
            enabled: true,
            offsetY: -20,
            style: { fontSize: '11px', fontWeight: 600, colors: ['#334155'] },
            formatter: function (val) { return val > 0 ? val : ''; }
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'right',
            markers: { width: 10, height: 10, radius: 3 },
            fontSize: '12px',
            fontWeight: 600,
            labels: { colors: '#334155' }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            padding: { left: 10, right: 10 }
        },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '12px' }
        }
    };

    // User Statistics chart
    var userOptions = Object.assign({}, modernChartBase, {
        series: [{
            name: 'Active Users',
            data: Object.values(@json($activeUserStats))
        }, {
            name: 'Inactive Users',
            data: Object.values(@json($inactiveUserStats))
        }],
        colors: ['#6366f1', '#94a3b8'],
        xaxis: {
            categories: Object.keys(@json($activeUserStats)),
            labels: {
                rotate: 0,
                style: { fontSize: '11px', colors: '#64748b', fontWeight: 500 },
                hideOverlappingLabels: true,
                showDuplicates: false
            },
            tickAmount: 5,
            tickPlacement: 'between',
            axisBorder: { color: '#e2e8f0' },
            axisTicks: { color: '#e2e8f0' }
        },
        yaxis: {
            title: { text: 'Number of Users', style: { color: '#64748b', fontSize: '12px', fontWeight: 600 } },
            labels: { style: { colors: '#64748b', fontSize: '11px' } },
            min: 0
        },
        title: {
            text: 'Daily User Registration (Active vs Inactive)',
            align: 'center',
            style: { fontSize: '14px', fontWeight: 700, color: '#1e293b' }
        },
        tooltip: Object.assign({}, modernChartBase.tooltip, {
            y: { formatter: function (val) { return val + " users"; } }
        })
    });

    // Profile Statistics chart
    var profileOptions = Object.assign({}, modernChartBase, {
        series: [{
            name: 'Active Profiles',
            data: Object.values(@json($activeProfileStats))
        }, {
            name: 'Inactive Profiles',
            data: Object.values(@json($inactiveProfileStats))
        }],
        colors: ['#10b981', '#94a3b8'],
        xaxis: {
            categories: Object.keys(@json($activeProfileStats)),
            labels: {
                rotate: 0,
                style: { fontSize: '11px', colors: '#64748b', fontWeight: 500 },
                hideOverlappingLabels: true,
                showDuplicates: false
            },
            tickAmount: 5,
            tickPlacement: 'between',
            axisBorder: { color: '#e2e8f0' },
            axisTicks: { color: '#e2e8f0' }
        },
        yaxis: {
            title: { text: 'Number of Profiles', style: { color: '#64748b', fontSize: '12px', fontWeight: 600 } },
            labels: { style: { colors: '#64748b', fontSize: '11px' } },
            min: 0
        },
        title: {
            text: 'Daily Profile Creation (Active vs Inactive)',
            align: 'center',
            style: { fontSize: '14px', fontWeight: 700, color: '#1e293b' }
        },
        tooltip: Object.assign({}, modernChartBase.tooltip, {
            y: { formatter: function (val) { return val + " profiles"; } }
        })
    });

    new ApexCharts(document.querySelector("#userChart"), userOptions).render();
    new ApexCharts(document.querySelector("#profileChart"), profileOptions).render();
});
</script>
@endpush
