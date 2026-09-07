@extends('admin.layout.master')

@push('css')
<style>
/* ===== Questions page: modern redesign ===== */
:root {
    --q-primary: #6366f1;
    --q-primary-dark: #4f46e5;
    --q-success: #10b981;
    --q-danger: #ef4444;
    --q-warning: #f59e0b;
    --q-info: #06b6d4;
    --q-slate-50: #f8fafc;
    --q-slate-100: #f1f5f9;
    --q-slate-200: #e2e8f0;
    --q-slate-300: #cbd5e1;
    --q-slate-500: #64748b;
    --q-slate-600: #475569;
    --q-slate-700: #334155;
    --q-slate-800: #1e293b;
}

/* Stats strip */
.q-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.q-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.q-stat .q-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.q-stat .q-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.q-stat .q-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.q-stat.total     { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.q-stat.pending   { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
.q-stat.approved  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.q-stat.answered  { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }

/* Success alert */
.q-alert-success {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin-bottom: 14px;
    background: #d1fae5; color: #065f46;
}

/* Main card */
.q-card {
    background: #fff;
    border: 1px solid var(--q-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
}

/* Filter bar (kept dropdown pattern for JS compat, restyled) */
.q-filter-bar {
    padding: 14px 20px !important;
    background: var(--q-slate-50) !important;
    border-bottom: 1px solid var(--q-slate-100) !important;
}
.q-filter-bar #qFiltersForm { gap: 8px !important; }
.q-filter-bar > form > span:first-child {
    display: inline-flex !important; align-items: center; justify-content: center;
    width: 34px; height: 34px; border-radius: 9px;
    background: linear-gradient(135deg, var(--q-primary) 0%, var(--q-primary-dark) 100%);
    color: #fff !important;
    margin-right: 4px !important; padding: 0 !important;
    box-shadow: 0 3px 10px rgba(99, 102, 241, .35);
}
.q-filter-bar > form > span:first-child i { color: #fff !important; font-size: 13px !important; }
.q-filter-bar .dropdown-menu {
    padding: 12px !important;
    border: 1px solid var(--q-slate-200) !important;
    border-radius: 10px !important;
    box-shadow: 0 12px 32px rgba(15,23,42,.15) !important;
}
.q-filter-bar .dropdown-menu input,
.q-filter-bar .dropdown-menu select { cursor: auto; }
.q-filter-bar .dropdown-menu .form-control-sm {
    border-radius: 8px;
    border-color: var(--q-slate-200);
    padding: 7px 10px;
    font-size: 13px;
    height: 36px;
}
.q-filter-bar .dropdown-menu .form-control-sm:focus {
    border-color: var(--q-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.q-filter-bar .dropdown-menu .btn-primary {
    background: linear-gradient(135deg, var(--q-primary) 0%, var(--q-primary-dark) 100%);
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
    color: var(--q-slate-700) !important;
    background: #fff !important;
    border: 1px solid var(--q-slate-200) !important;
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
    color: var(--q-primary-dark) !important;
    background: #fff !important;
    border-color: var(--q-primary) !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12) !important;
}
.q-filter-bar .btn-outline-dark .filter-caret { color: var(--q-slate-500); opacity: 1; }
.q-filter-bar .btn-primary {
    background: #eef2ff !important;
    color: var(--q-primary-dark) !important;
    border: 1px solid var(--q-primary) !important;
    border-radius: 9px !important;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    height: 38px !important;
    display: inline-flex !important;
    align-items: center;
    box-shadow: none !important;
}
.q-filter-bar .btn-primary .filter-caret { color: var(--q-primary-dark); }
.q-filter-bar .btn-outline-danger {
    color: var(--q-danger) !important;
    background: #fff !important;
    border: 1px solid var(--q-slate-200) !important;
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
    border-color: var(--q-danger) !important;
    color: var(--q-danger) !important;
}
.q-filter-bar .ml-auto {
    background: #fff;
    border: 1px solid var(--q-slate-200);
    border-radius: 9px;
    padding: 6px 12px;
    height: 38px;
}
.q-filter-bar .ml-auto label { color: var(--q-slate-500) !important; }
.q-filter-bar .ml-auto .form-control-sm {
    border: 1px solid var(--q-slate-200);
    border-radius: 6px;
    font-size: 12px;
    height: 28px;
    padding: 2px 22px 2px 8px;
}

/* Table */
.q-card .card-body { padding: 0; }
.q-table {
    width: 100% !important;
    margin: 0;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
.q-table thead th {
    background: var(--q-slate-50);
    color: var(--q-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid var(--q-slate-200);
    text-align: left;
    white-space: nowrap;
}
.q-table thead th:last-child { text-align: right; }
.q-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--q-slate-100);
    color: var(--q-slate-700);
    background: #fff;
}
.q-table tbody tr:hover td { background: #fafbff; }
.q-table tbody tr:last-child td { border-bottom: 0; }

/* Cell primitives */
.q-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--q-slate-500);
    background: var(--q-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.q-id-chip.user    { background: #eef2ff; color: #4338ca; }
.q-id-chip.profile { background: #f3e8ff; color: #6b21a8; }

.q-email {
    color: var(--q-slate-600);
    font-size: 13px;
    word-break: break-all;
}
.q-question, .q-answer {
    color: var(--q-slate-700);
    font-size: 13.5px;
    line-height: 1.5;
    max-width: 320px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.q-answer { max-width: 240px; }
.q-empty-answer { color: var(--q-slate-300); font-weight: 500; }
.q-read-more {
    display: block;
    margin-top: 4px;
    color: var(--q-primary-dark) !important;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
}
.q-read-more:hover { text-decoration: underline; }

/* Status badges */
.q-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
    line-height: 1.4;
    white-space: nowrap;
}
.q-status i { font-size: 10px; }
.q-status-approved { background: #d1fae5; color: #065f46; }
.q-status-pending  { background: #fef3c7; color: #92400e; }

.q-date {
    color: var(--q-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.q-date small {
    display: block; color: var(--q-slate-500); font-size: 11px;
}

/* Action buttons */
.q-actions-cell {
    display: inline-flex; gap: 6px; align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
}
.q-actions-cell form { margin: 0; display: inline; }
.q-btn {
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
.q-btn-view      { background: #eef2ff !important; color: var(--q-primary-dark) !important; }
.q-btn-view:hover { background: #e0e7ff !important; color: var(--q-primary-dark) !important; }
.q-btn-approve   { background: #d1fae5 !important; color: #065f46 !important; }
.q-btn-approve:hover { background: #a7f3d0 !important; }
.q-btn-disapprove { background: #fef3c7 !important; color: #92400e !important; }
.q-btn-disapprove:hover { background: #fde68a !important; }
.q-btn-delete    { background: #fee2e2 !important; color: #991b1b !important; }
.q-btn-delete:hover { background: #fecaca !important; color: #991b1b !important; }

/* Empty state */
.q-empty td {
    text-align: center !important;
    padding: 60px 20px !important;
    color: var(--q-slate-500) !important;
}

/* Pagination footer */
.q-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 16px 22px;
    border-top: 1px solid var(--q-slate-100);
    background: var(--q-slate-50);
    font-size: 13px;
    color: var(--q-slate-600);
}
.q-pagination .pagination { margin: 0; }
.q-pagination .pagination .page-link {
    color: var(--q-slate-600);
    border-color: var(--q-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.q-pagination .pagination .page-item.active .page-link {
    background: var(--q-primary);
    border-color: var(--q-primary);
    color: #fff;
}

/* Modal styling */
#questionModal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(15,23,42,.3);
}
#questionModal .modal-header {
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--q-slate-100);
    padding: 18px 22px;
}
#questionModal .modal-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 16px; font-weight: 700; color: var(--q-slate-800);
}
#questionModal .modal-body { padding: 22px; }
#questionModal h6 {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--q-slate-500);
    margin-bottom: 8px;
}
#questionModal h6 i { color: var(--q-primary); }
#questionModal .p-3.bg-light {
    background: var(--q-slate-50) !important;
    border: 1px solid var(--q-slate-100);
    border-radius: 10px;
    padding: 14px 16px !important;
    color: var(--q-slate-800) !important;
    line-height: 1.6;
    font-size: 14px;
}
#questionModal .modal-footer {
    padding: 14px 22px;
    background: var(--q-slate-50);
    border-top: 1px solid var(--q-slate-100);
}
#questionModal .modal-footer .btn-secondary {
    background: #fff;
    color: var(--q-slate-700);
    border: 1px solid var(--q-slate-200);
    border-radius: 9px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    height: 40px;
    display: inline-flex; align-items: center; gap: 6px;
}
#questionModal .modal-footer .btn-secondary:hover { background: var(--q-slate-50); }

/* Responsive */
@media (max-width: 992px) {
    .q-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .main-wrapper { padding-left: 8px !important; padding-right: 8px !important; }
    .container-fluid { padding-left: 6px !important; padding-right: 6px !important; }
    .q-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .q-filter-bar { padding: 12px !important; }
    .q-table thead { display: none; }
    .q-pagination { flex-direction: column; text-align: center; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Questions</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage questions effectively</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Questions</li>
        </ol>
    </div>
</div>

@php
    $totalQ    = $questions->total();
    $onPagePending  = collect($questions->items())->filter(fn($q) => $q->status == 0)->count();
    $onPageApproved = collect($questions->items())->filter(fn($q) => $q->status == 1)->count();
    $onPageAnswered = collect($questions->items())->filter(fn($q) => !empty($q->answer))->count();

    $pp = request('perPage', $questions->perPage());
    $statusLabels = ['1' => 'Approved', '0' => 'Pending'];
    $answerLabels = ['yes' => 'Answered', 'no' => 'Unanswered'];
    $activeCount = collect(['id','user_id','profile_id','email','q','status','has_answer','date_from','date_to'])
        ->filter(fn($k) => request()->filled($k))
        ->count();
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="q-stats-row">
        <div class="q-stat total">
            <p class="q-stat-label">Total Questions</p>
            <p class="q-stat-value">{{ number_format($totalQ) }}</p>
            <i class="fas fa-question-circle q-stat-icon"></i>
        </div>
        <div class="q-stat pending">
            <p class="q-stat-label">Pending · On This Page</p>
            <p class="q-stat-value">{{ number_format($onPagePending) }}</p>
            <i class="fas fa-hourglass-half q-stat-icon"></i>
        </div>
        <div class="q-stat approved">
            <p class="q-stat-label">Approved · On This Page</p>
            <p class="q-stat-value">{{ number_format($onPageApproved) }}</p>
            <i class="fas fa-check-circle q-stat-icon"></i>
        </div>
        <div class="q-stat answered">
            <p class="q-stat-label">Answered · On This Page</p>
            <p class="q-stat-value">{{ number_format($onPageAnswered) }}</p>
            <i class="fas fa-comment-dots q-stat-icon"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="q-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- Main card --}}
    <div class="q-card">

        {{-- Filter bar --}}
        <div class="card-header q-filter-bar">
            <form method="GET" action="{{ route('questions.index') }}" id="qFiltersForm" class="d-flex align-items-center flex-wrap" style="gap:6px;">
                <input type="hidden" name="perPage" value="{{ $pp }}">

                <span><i class="fa fa-filter"></i></span>

                {{-- ID --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-hashtag mr-1"></i> ID{{ request('id') ? ': '.request('id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:220px;">
                        <input type="number" name="id" value="{{ request('id') }}" class="form-control form-control-sm mb-2" placeholder="Question ID" min="1">
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
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mb-2" placeholder="Search question or answer...">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
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

                {{-- Answer --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request()->filled('has_answer') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-comment mr-1"></i> Answer{{ request()->filled('has_answer') ? ': '.($answerLabels[request('has_answer')] ?? '') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:180px;">
                        <select name="has_answer" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                            <option value="">Any answer</option>
                            <option value="yes" {{ request('has_answer') === 'yes' ? 'selected' : '' }}>Answered</option>
                            <option value="no" {{ request('has_answer') === 'no' ? 'selected' : '' }}>Unanswered</option>
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
                    <a href="{{ route('questions.index') }}" class="btn btn-sm btn-outline-danger" title="Reset all filters">
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
                // Prevent Bootstrap 4 dropdown from closing when clicking inside its input
                document.querySelectorAll('.q-filter-bar .dropdown-menu').forEach(function(m) {
                    m.addEventListener('click', function(e) { e.stopPropagation(); });
                });
            })();
        </script>

        <div class="card-body">
            <div style="overflow-x: auto;">
                <table class="table q-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">#</th>
                            <th>User ID</th>
                            <th>Profile ID</th>
                            <th>Email</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th style="width:280px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                        <tr>
                            <td><span class="q-id-chip">#{{ $question->id }}</span></td>
                            <td><span class="q-id-chip user">#{{ $question->user_id }}</span></td>
                            <td><span class="q-id-chip profile">#{{ $question->profile_id }}</span></td>
                            <td><span class="q-email">{{ $question->askedBy->email ?? '-' }}</span></td>
                            <td>
                                <div class="q-question">{{ $question->question }}</div>
                                @if(strlen($question->question) > 120)
                                    <a href="javascript:void(0)" class="q-read-more" onclick="showQuestionModal({{ $question->id }}, `{{ addslashes($question->question) }}`, `{{ addslashes($question->answer ?? '') }}`)">
                                        Read more →
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($question->answer)
                                    <div class="q-answer">{{ $question->answer }}</div>
                                    @if(strlen($question->answer) > 60)
                                        <a href="javascript:void(0)" class="q-read-more" onclick="showQuestionModal({{ $question->id }}, `{{ addslashes($question->question) }}`, `{{ addslashes($question->answer) }}`)">
                                            Read more →
                                        </a>
                                    @endif
                                @else
                                    <span class="q-empty-answer">—</span>
                                @endif
                            </td>
                            <td>
                                @if($question->status == 1)
                                    <span class="q-status q-status-approved"><i class="fas fa-check"></i> Approved</span>
                                @else
                                    <span class="q-status q-status-pending"><i class="fas fa-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($question->created_at)
                                    <div class="q-date">
                                        {{ $question->created_at->format('M d, Y') }}
                                        <small>{{ $question->created_at->format('H:i') }}</small>
                                    </div>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="q-actions-cell">
                                    <a href="javascript:void(0)" class="q-btn q-btn-view" onclick="showQuestionModal({{ $question->id }}, `{{ addslashes($question->question) }}`, `{{ addslashes($question->answer ?? '') }}`)">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    @if($question->status == 0)
                                        <form action="{{ route('questions.approve', $question->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="q-btn q-btn-approve"><i class="fa fa-check"></i> Approve</button>
                                        </form>
                                    @else
                                        <form action="{{ route('questions.disapprove', $question->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="q-btn q-btn-disapprove"><i class="fa fa-ban"></i> Disapprove</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="q-btn q-btn-delete"><i class="fa-solid fa-trash-can"></i> Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="q-empty">
                            <td colspan="9">
                                <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <p style="color:#64748b; margin:0; font-size:15px;">No questions match the selected filters.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="q-pagination">
            <div>
                @if($questions->total() > 0)
                    Showing <strong>{{ $questions->firstItem() }}</strong> to
                    <strong>{{ $questions->lastItem() }}</strong> of
                    <strong>{{ number_format($questions->total()) }}</strong> entries
                @else
                    No questions found
                @endif
            </div>
            <div>{{ $questions->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>

<!-- Question Modal -->
<div class="modal fade" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">
                    <i class="fas fa-question-circle"></i> Question Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6><i class="fas fa-question"></i> Question</h6>
                    <div class="p-3 bg-light rounded" id="modalQuestionText" style="white-space: pre-wrap;"></div>
                </div>
                <div id="modalAnswerSection" style="display: none;">
                    <h6><i class="fas fa-comment-dots"></i> Answer</h6>
                    <div class="p-3 bg-light rounded" id="modalAnswerText" style="white-space: pre-wrap;"></div>
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
function showQuestionModal(id, question, answer) {
    document.getElementById('modalQuestionText').textContent = question;

    if (answer && answer.trim() !== '') {
        document.getElementById('modalAnswerText').textContent = answer;
        document.getElementById('modalAnswerSection').style.display = 'block';
    } else {
        document.getElementById('modalAnswerSection').style.display = 'none';
    }

    $('#questionModal').modal('show');
}
</script>

@endsection
