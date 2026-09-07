@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
/* ===== Profiles page: modern redesign ===== */
:root {
    --p-primary: #6366f1;
    --p-primary-dark: #4f46e5;
    --p-success: #10b981;
    --p-danger: #ef4444;
    --p-warning: #f59e0b;
    --p-info: #06b6d4;
    --p-slate-50: #f8fafc;
    --p-slate-100: #f1f5f9;
    --p-slate-200: #e2e8f0;
    --p-slate-300: #cbd5e1;
    --p-slate-500: #64748b;
    --p-slate-600: #475569;
    --p-slate-700: #334155;
    --p-slate-800: #1e293b;
}

.w-5 { display: none; }

/* ---- Stats strip ---- */
.p-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.p-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.p-stat .p-stat-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .9;
    margin: 0 0 4px 0;
    font-weight: 600;
}
.p-stat .p-stat-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1.1;
    margin: 0;
}
.p-stat .p-stat-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 34px;
    opacity: .35;
}
.p-stat.total    { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.p-stat.active   { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.p-stat.premium  { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
.p-stat.showing  { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }

/* ---- Filter card ---- */
.p-filter-card {
    background: #fff;
    border: 1px solid var(--p-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    margin-bottom: 18px;
    overflow: hidden;
}

.navbar-filters {
    background: transparent;
    border: 0;
    border-radius: 0;
    padding: 14px 18px;
    margin: 0;
    box-shadow: none;
    position: relative;
    width: 100%;
    z-index: 6;
    height: auto !important;
    min-height: 0;
}

.navbar-filters ul.nav {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin: 0;
    padding: 0;
    list-style: none;
}

.navbar-filters .filter-icon {
    background: linear-gradient(135deg, var(--p-primary) 0%, var(--p-primary-dark) 100%);
    border-radius: 10px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 15px;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
}

.navbar-filters .dropdown {
    position: relative;
    width: auto;
    min-width: 130px;
}

.navbar-filters .dropdown-toggle {
    background: #fff;
    border: 1px solid var(--p-slate-200);
    border-radius: 9px;
    padding: 9px 14px;
    color: var(--p-slate-700);
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    transition: all .15s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    height: 40px;
}
.navbar-filters .dropdown-toggle:hover {
    background: var(--p-slate-50);
    border-color: var(--p-primary);
    color: var(--p-primary-dark);
    text-decoration: none;
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}
.navbar-filters .dropdown-toggle.filter-active {
    background: #eef2ff;
    border-color: var(--p-primary);
    color: var(--p-primary-dark);
}
.navbar-filters .dropdown-toggle::after {
    border-top-color: var(--p-slate-400, #94a3b8);
    margin-left: 4px;
}
.navbar-filters .dropdown-toggle i {
    color: var(--p-primary);
    font-size: 12px;
}

.navbar-filters .dropdown-menu {
    background: #fff;
    border: 1px solid var(--p-slate-200);
    border-radius: 10px;
    box-shadow: 0 12px 32px rgba(15,23,42,.12);
    padding: 14px;
    min-width: 250px;
    margin-top: 6px;
    position: absolute;
    z-index: 1000;
}

.backpack-filter { padding: 0; }
.backpack-filter .form-control,
.date-filter-container .form-control,
.select-filter select {
    border: 1px solid var(--p-slate-200);
    border-radius: 9px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--p-slate-800);
    background: #fff;
    height: 40px;
    transition: all .15s ease;
    width: 100%;
}
.backpack-filter .form-control:focus,
.date-filter-container .form-control:focus,
.select-filter select:focus {
    border-color: var(--p-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
    outline: none;
}

.date-filter-container .form-control { margin-bottom: 10px; }
.date-filter-container .btn-primary {
    background: linear-gradient(135deg, var(--p-primary) 0%, var(--p-primary-dark) 100%);
    border: 0;
    border-radius: 9px;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    box-shadow: 0 4px 12px rgba(99,102,241,.35);
}

.reset-filter-btn {
    background: #fff;
    border: 1px solid var(--p-slate-200);
    border-radius: 9px;
    padding: 9px 16px;
    color: var(--p-danger);
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    transition: all .15s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    height: 40px;
}
.reset-filter-btn:hover {
    background: #fef2f2;
    border-color: var(--p-danger);
    color: var(--p-danger);
    text-decoration: none;
}
.reset-filter-btn i { color: var(--p-danger); }

/* Autocomplete list refinement */
#city-suggestions .autocomplete-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 13px; }
#city-suggestions .autocomplete-item:hover { background: var(--p-slate-50); }
#city-suggestions .autocomplete-item:last-child { border-bottom: 0; }

/* ---- Main table card ---- */
.p-main-card {
    background: #fff;
    border: 1px solid var(--p-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
}
.p-main-card .card-header {
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--p-slate-100);
    padding: 18px 22px;
}
.p-main-card .card-body {
    padding: 0;
}
.p-header-flex {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
}
.p-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: var(--p-slate-800);
}
.p-header-title .p-title-icon {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: rgba(99,102,241,.12);
    color: var(--p-primary-dark);
    font-size: 15px;
}
.p-header-title small {
    font-weight: 400;
    color: var(--p-slate-500);
    font-size: 12px;
    margin-left: 4px;
}

/* Toolbar */
.p-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    padding: 14px 22px;
    background: var(--p-slate-50);
    border-bottom: 1px solid var(--p-slate-100);
}
.p-toolbar-left { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
.p-perpage {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--p-slate-600);
}
.p-perpage .form-select {
    border: 1px solid var(--p-slate-200);
    border-radius: 8px;
    padding: 5px 26px 5px 10px;
    font-size: 13px;
    background: #fff;
    color: var(--p-slate-700);
    cursor: pointer;
    height: auto;
}
.p-perpage .form-select:focus {
    outline: none;
    border-color: var(--p-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .18);
}
.p-entries-info {
    color: var(--p-slate-500);
    font-size: 13px;
    font-weight: 500;
}
.p-verify-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: linear-gradient(135deg, var(--p-primary) 0%, var(--p-primary-dark) 100%);
    color: #fff !important;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(99,102,241,.35);
    transition: box-shadow .15s;
    border: 0;
}
.p-verify-btn:hover { color: #fff; box-shadow: 0 6px 16px rgba(99,102,241,.5); text-decoration: none; }

/* Bulk actions */
.p-bulk-bar {
    background: #eef2ff;
    border-bottom: 1px solid #c7d2fe;
    padding: 12px 22px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    transition: all .3s ease;
}
.p-bulk-bar #selected-count {
    color: var(--p-primary-dark);
    font-weight: 700;
    font-size: 13px;
    margin-right: 6px;
}
.p-bulk-bar .btn {
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Table */
.table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

.p-main-card .table {
    width: 100% !important;
    max-width: 100%;
    margin: 0;
    font-size: 13.5px;
    border-collapse: separate;
    border-spacing: 0;
}
.p-main-card .table thead th {
    background: var(--p-slate-50);
    color: var(--p-slate-500);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: 14px 14px;
    border: 0;
    border-bottom: 1px solid var(--p-slate-200);
    white-space: nowrap;
    text-align: left;
}
.p-main-card .table tbody td {
    padding: 14px;
    vertical-align: middle;
    color: var(--p-slate-700);
    border: 0;
    border-bottom: 1px solid var(--p-slate-100);
    background: #fff;
    white-space: normal;
    word-wrap: break-word;
}
.p-main-card .table tbody tr { transition: background .12s; }
.p-main-card .table tbody tr:hover td { background: #fafbff; }
.p-main-card .table tbody tr.selected td { background: #eef2ff !important; }
.p-main-card .table tbody tr:last-child td { border-bottom: 0; }

.p-name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 150px;
}
.p-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
    text-transform: uppercase;
}
.p-name-link {
    font-weight: 600;
    color: var(--p-primary-dark) !important;
    line-height: 1.2;
    text-decoration: none;
}
.p-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    color: var(--p-slate-700);
    background: var(--p-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.p-date-cell {
    color: var(--p-slate-600);
    font-size: 12.5px;
    white-space: nowrap;
    line-height: 1.4;
}
.p-city-cell {
    color: var(--p-primary-dark);
    font-weight: 600;
}
.p-picture-thumb {
    width: 68px;
    height: 84px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--p-slate-200);
    background: var(--p-slate-100);
    box-shadow: 0 1px 3px rgba(15,23,42,.08);
    transition: transform .15s, box-shadow .15s;
}
.p-picture-thumb:hover { transform: scale(1.05); box-shadow: 0 6px 16px rgba(15,23,42,.18); }
.p-picture-empty {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 68px;
    height: 84px;
    border-radius: 8px;
    border: 1px dashed var(--p-slate-300);
    background: var(--p-slate-50);
    color: var(--p-slate-300);
    font-size: 11px;
    gap: 4px;
}

/* Badges (modern) */
.p-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.4;
    white-space: nowrap;
}
.p-badge i { font-size: 10px; }
.p-badge-muted   { background: var(--p-slate-100); color: var(--p-slate-500); }
.p-badge-warning { background: #fef3c7; color: #92400e; }
.p-badge-info    { background: #cffafe; color: #155e75; }
.p-badge-success { background: #d1fae5; color: #065f46; }
.p-badge-primary { background: #eef2ff; color: #4338ca; }
.p-badge-danger  { background: #fee2e2; color: #991b1b; }
.p-badge-archive { background: #f1f5f9; color: #475569; }

/* Phone cell */
.p-phone { color: var(--p-slate-700); font-size: 13px; }
.p-whatsapp {
    color: #25D366 !important;
    margin-left: 6px;
    font-size: 16px;
    vertical-align: middle;
}

/* Actions */
.p-action-btn {
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
    padding: 7px 14px !important;
    background: linear-gradient(135deg, var(--p-primary) 0%, var(--p-primary-dark) 100%) !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    box-shadow: 0 3px 8px rgba(99,102,241,.3);
    line-height: 1 !important;
}
.p-action-btn:hover, .p-action-btn:focus { box-shadow: 0 5px 12px rgba(99,102,241,.45); color: #fff !important; }
.p-action-btn::after {
    border-top-color: rgba(255,255,255,.8);
    margin-left: 2px;
}

/* Fancy dropdown menu */
.p-main-card .dropdown-menu {
    border: 1px solid var(--p-slate-200);
    border-radius: 10px;
    box-shadow: 0 12px 32px rgba(15,23,42,.15);
    padding: 6px;
    min-width: 180px;
}
.p-main-card .dropdown-item {
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 13px;
    color: var(--p-slate-700);
    display: flex;
    align-items: center;
    gap: 8px;
}
.p-main-card .dropdown-item:hover { background: var(--p-slate-50); color: var(--p-slate-800); }
.p-main-card .dropdown-item.text-danger:hover { background: #fef2f2; color: #b91c1c; }
.p-main-card .dropdown-item i { width: 14px; text-align: center; }
.p-main-card .dropdown-divider { margin: 4px 2px; border-color: var(--p-slate-100); }

/* Checkboxes */
.form-check-input { margin-left: .25rem; cursor: pointer; }
.profile-checkbox, #select-all { cursor: pointer; width: 16px; height: 16px; }

/* Legacy IE / bootstrap overrides */
.img-thumbnail { border: 0; }

/* Pagination */
.p-pagination-wrap {
    padding: 16px 22px;
    border-top: 1px solid var(--p-slate-100);
    background: var(--p-slate-50);
}
.p-pagination-wrap .pagination { margin: 0; justify-content: flex-end; }
.p-pagination-wrap .pagination .page-link {
    color: var(--p-slate-600);
    border-color: var(--p-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.p-pagination-wrap .pagination .page-item.active .page-link {
    background: var(--p-primary);
    border-color: var(--p-primary);
    color: #fff;
}

/* Select2 keeps clean look if used */
.select2-container--default .select2-selection--single {
    height: 40px;
    border: 1px solid var(--p-slate-200);
    border-radius: 9px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px;
    padding-left: 12px;
    color: var(--p-slate-800);
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
    right: 8px;
}

/* ---- Responsive ---- */
@media (max-width: 992px) {
    .p-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .p-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .p-stat { padding: 14px; }
    .p-stat .p-stat-value { font-size: 22px; }
    .navbar-filters { padding: 12px; }
    .navbar-filters #filter-form { width: 100%; }
    .navbar-filters ul.nav {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }
    .navbar-filters ul.nav > li:first-child { grid-column: 1 / -1; display: flex; justify-content: flex-start; }
    .navbar-filters .nav-item,
    .navbar-filters .dropdown { width: 100%; min-width: 0; }
    .navbar-filters ul.nav > li:last-child { grid-column: 1 / -1; }
    .navbar-filters .dropdown-toggle,
    .navbar-filters .reset-filter-btn {
        width: 100%;
        justify-content: center;
    }
    .navbar-filters .dropdown-menu {
        min-width: 90vw;
        max-width: 90vw;
        position: absolute !important;
        left: 0 !important;
        right: auto !important;
        transform: none !important;
    }
    .p-toolbar { padding: 12px; }
    .p-pagination-wrap { padding: 12px; }
    .p-pagination-wrap .pagination { justify-content: center; }
    .p-main-card .table thead { display: none; }
    .p-main-card .table tbody td { padding: 10px 12px; }
}
</style>
@endpush

@section("content")
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Profiles</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage profiles effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Profiles</li>
                    </ol>

                </div>
                <!-- /.page-title-right -->
        </div>

@php
    $hasAnyFilter = request()->hasAny(['id','title','phone','city','status','premium','start_date','end_date']);
    $totalProfiles = $profiles->total();
    $onPageActive  = $profiles->getCollection()->filter(fn($p) => $p->is_active && !$p->isArchived())->count();
    $onPagePremium = $profiles->getCollection()->filter(fn($p) => $p->getpackage || $p->activeAuction)->count();
@endphp

<div class="container-fluid px-0">

    {{-- ===== Stats strip ===== --}}
    <div class="p-stats-row">
        <div class="p-stat total">
            <p class="p-stat-label">Total Profiles</p>
            <p class="p-stat-value">{{ number_format($totalProfiles) }}</p>
            <i class="fas fa-users p-stat-icon"></i>
        </div>
        <div class="p-stat active">
            <p class="p-stat-label">Active · On This Page</p>
            <p class="p-stat-value">{{ number_format($onPageActive) }}</p>
            <i class="fas fa-check-circle p-stat-icon"></i>
        </div>
        <div class="p-stat premium">
            <p class="p-stat-label">Premium / Auction · On This Page</p>
            <p class="p-stat-value">{{ number_format($onPagePremium) }}</p>
            <i class="fas fa-crown p-stat-icon"></i>
        </div>
        <div class="p-stat showing">
            <p class="p-stat-label">{{ $hasAnyFilter ? 'Filtered Results' : 'Showing' }}</p>
            <p class="p-stat-value">{{ number_format($profiles->count()) }}</p>
            <i class="fas fa-filter p-stat-icon"></i>
        </div>
    </div>

    {{-- ===== Filters card ===== --}}
    <div class="p-filter-card">
        <nav class="navbar navbar-expand-lg navbar-filters">
            <form id="filter-form" action="{{ route('admin.profiles.index') }}" method="GET" class="w-100">
                <ul class="nav">
                    <li class="nav-item">
                        <div class="filter-icon" title="Filters">
                            <i class="fas fa-filter"></i>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle {{ request('id') ? 'filter-active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-hashtag"></i> ID/Reference
                        </a>
                        <div class="dropdown-menu">
                            <div class="backpack-filter">
                                <input type="text" name="id" id="text-filter-id" class="form-control" value="{{ request('id') }}" placeholder="Enter ID">
                            </div>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle {{ request('title') ? 'filter-active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-heading"></i> Title
                        </a>
                        <div class="dropdown-menu">
                            <div class="backpack-filter">
                                <input type="text" name="title" class="form-control" value="{{ request('title') }}" placeholder="Enter title">
                            </div>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle {{ request('phone') ? 'filter-active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-phone"></i> Phone
                        </a>
                        <div class="dropdown-menu">
                            <div class="backpack-filter">
                                <input type="text" name="phone" class="form-control" value="{{ request('phone') }}" placeholder="Enter phone number">
                            </div>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle {{ request('city') ? 'filter-active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-map-marker-alt"></i> City
                        </a>
                        <div class="dropdown-menu" style="min-width: 280px;">
                            <div class="city-autocomplete-wrapper" style="position: relative;">
                                <input type="text"
                                       name="city_search"
                                       id="city-autocomplete"
                                       class="form-control"
                                       placeholder="Type to search city..."
                                       autocomplete="off"
                                       value="{{ request('city') ? $cities->firstWhere('id', request('city'))?->name : '' }}">
                                <input type="hidden" name="city" id="city-id" value="{{ request('city') }}">
                                <div id="city-suggestions" class="autocomplete-suggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: #fff; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 8px 8px; z-index: 1050; box-shadow: 0 4px 12px rgba(15,23,42,.12);"></div>
                            </div>
                            @if(request('city'))
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clear-city-filter" style="width: 100%;">
                                <i class="fas fa-times"></i> Clear City Filter
                            </button>
                            @endif
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle {{ request('status') !== null && request('status') !== '' ? 'filter-active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-toggle-on"></i> Status
                        </a>
                        <div class="dropdown-menu">
                            <div class="select-filter">
                                <select name="status">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle {{ request('premium') ? 'filter-active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-crown"></i> Package
                        </a>
                        <div class="dropdown-menu">
                            <div class="select-filter">
                                <select name="premium">
                                    <option value="">All Packages</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" {{ request('premium') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                                    @endforeach
                                    <option value="auction" {{ request('premium') == 'auction' ? 'selected' : '' }}>Auction</option>
                                </select>
                            </div>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle {{ request('start_date') || request('end_date') ? 'filter-active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar-alt"></i> Date Range
                        </a>
                        <div class="dropdown-menu">
                            <div class="date-filter-container">
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item ml-auto">
                        <a href="{{ route('admin.profiles.index') }}" class="reset-filter-btn">
                            <i class="fas fa-eraser"></i> Reset
                        </a>
                    </li>
                </ul>
            </form>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius:10px;">{{session('success')}}</div>
    @endif

    {{-- ===== Main card ===== --}}
    <div class="p-main-card">

        <div class="card-header">
            <div class="p-header-flex">
                <h5 class="p-header-title">
                    <span class="p-title-icon"><i class="fas fa-users"></i></span>
                    All Profiles
                    <small>Manage, archive & moderate profiles</small>
                </h5>
                <a href="{{ route('admin.verifications') }}" class="p-verify-btn">
                    <i class="fas fa-user-shield"></i> Pending Photo Verify
                </a>
            </div>
        </div>

        <div class="p-toolbar">
            <div class="p-toolbar-left">
                <div class="p-perpage">
                    <span>Show</span>
                    <select name="per_page" id="per-page-select" class="form-select form-select-sm">
                        <option value="10"   {{ request('per_page', 10) == 10   ? 'selected' : '' }}>10</option>
                        <option value="50"   {{ request('per_page', 10) == 50   ? 'selected' : '' }}>50</option>
                        <option value="250"  {{ request('per_page', 10) == 250  ? 'selected' : '' }}>250</option>
                        <option value="500"  {{ request('per_page', 10) == 500  ? 'selected' : '' }}>500</option>
                        <option value="1000" {{ request('per_page', 10) == 1000 ? 'selected' : '' }}>1000</option>
                    </select>
                    <span>entries</span>
                </div>
                <div class="p-entries-info" id="entries-info">
                    @if($profiles->total() > 0)
                        Showing <strong>{{ $profiles->firstItem() }}</strong> to <strong>{{ $profiles->lastItem() }}</strong> of <strong>{{ number_format($profiles->total()) }}</strong> entries
                    @else
                        Showing 0 to 0 of 0 entries
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            <div id="table-container">
                @include('admin.profiles.table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Bootstrap 4 dropdowns in table on page load
        $('#table-container .dropdown-toggle').dropdown();
        
        // Setup AJAX headers for Laravel
        $.ajaxSetup({
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function updateURL(params) {
            const url = new URL(window.location);
            Object.entries(params).forEach(([key, value]) => {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            });
            window.history.pushState({}, '', url);
        }
    
        function fetchProfiles(url, isPagination = false) {
            let requestData = {};
            
            if (isPagination && url) {
                // For pagination, extract all parameters from the URL
                const urlObj = new URL(url);
                urlObj.searchParams.forEach((value, key) => {
                    requestData[key] = value;
                });
            } else {
                // For filters and per_page changes, build fresh data
                requestData = {
                    id: $('#text-filter-id').val(),
                    title: $('input[name="title"]').val(),
                    city: $('#city-id').val(),
                    status: $('select[name="status"]').val(),
                    premium: $('select[name="premium"]').val(),
                    start_date: $('input[name="start_date"]').val(),
                    end_date: $('input[name="end_date"]').val(),
                    per_page: $('#per-page-select').val()
                };
            }

            $.ajax({
                url: url || "{{ route('admin.profiles.index') }}",
                type: 'GET',
                data: requestData,
                success: function(response) {
                    if (typeof response === 'object' && response.table) {
                        // JSON response with table and entries info
                        $('#table-container').html(response.table);
                        $('#entries-info').html(response.entriesInfo);
                    } else {
                        // HTML response (fallback)
                        $('#table-container').html(response);
                    }
                    if (!isPagination) {
                        updateURL(requestData);
                    }
                    // Re-initialize Bootstrap 4 dropdowns after AJAX load
                    $('#table-container .dropdown-toggle').dropdown();
                },
                error: function(xhr, status, error) {
                    console.error('Filter error:', error);
                }
            });
        }
    
        // Initialize Select2 for other dropdowns (removed city)
        // City autocomplete is now handled separately

        // City Autocomplete
        const cities = @json($cities->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
        const cityInput = document.getElementById('city-autocomplete');
        const citySuggestions = document.getElementById('city-suggestions');
        const cityIdInput = document.getElementById('city-id');
        
        if (cityInput) {
            cityInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                citySuggestions.innerHTML = '';
                
                if (query.length < 1) {
                    citySuggestions.style.display = 'none';
                    return;
                }
                
                const matches = cities.filter(city => 
                    city.name.toLowerCase().includes(query)
                ).slice(0, 10);
                
                if (matches.length > 0) {
                    matches.forEach(city => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.style.cssText = 'padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;';
                        div.textContent = city.name;
                        div.dataset.id = city.id;
                        div.dataset.name = city.name;
                        
                        div.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#f5f5f5';
                        });
                        div.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = '#fff';
                        });
                        div.addEventListener('click', function() {
                            cityInput.value = this.dataset.name;
                            cityIdInput.value = this.dataset.id;
                            citySuggestions.style.display = 'none';
                            fetchProfiles();
                            cityInput.closest('.dropdown-menu').classList.remove('show');
                        });
                        
                        citySuggestions.appendChild(div);
                    });
                    citySuggestions.style.display = 'block';
                } else {
                    citySuggestions.style.display = 'none';
                }
            });
            
            cityInput.addEventListener('focus', function() {
                if (this.value.length >= 1) {
                    this.dispatchEvent(new Event('input'));
                }
            });
            
            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!cityInput.contains(e.target) && !citySuggestions.contains(e.target)) {
                    citySuggestions.style.display = 'none';
                }
            });
            
            // Clear city filter button
            const clearCityBtn = document.getElementById('clear-city-filter');
            if (clearCityBtn) {
                clearCityBtn.addEventListener('click', function() {
                    cityInput.value = '';
                    cityIdInput.value = '';
                    fetchProfiles();
                    cityInput.closest('.dropdown-menu').classList.remove('show');
                });
            }
        }

        // ID Filter
        $('#text-filter-id').closest('.dropdown-menu').find('button[type="submit"]').on('click', function(e) {
            e.preventDefault();
            fetchProfiles();
            $(this).closest('.dropdown-menu').removeClass('show');
        });
    
        $('#text-filter-id').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                fetchProfiles();
                $(this).closest('.dropdown-menu').removeClass('show');
            }
        });
    
        // Title Filter with debounce
        let titleTimeout = null;
        $('input[name="title"]').on('keyup', function() {
            clearTimeout(titleTimeout);
            titleTimeout = setTimeout(() => {
                fetchProfiles();
                $(this).closest('.dropdown-menu').removeClass('show');
            }, 500);
        });

        // Title Filter search button
        $('input[name="title"]').closest('.dropdown-menu').find('button[type="submit"]').on('click', function(e) {
            e.preventDefault();
            fetchProfiles();
            $(this).closest('.dropdown-menu').removeClass('show');
        });

        // Title Filter Enter key
        $('input[name="title"]').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                fetchProfiles();
                $(this).closest('.dropdown-menu').removeClass('show');
            }
        });
    
        // Select Filters (city is now handled separately via autocomplete)
        $('select[name="status"], select[name="premium"]').on('change', function() {
            fetchProfiles();
            $(this).closest('.dropdown-menu').removeClass('show');
        });

        // Per Page Dropdown
        $('#per-page-select').on('change', function() {
            fetchProfiles();
        });
    
        // Date Range Filters
        $('input[name="start_date"], input[name="end_date"]').on('change', function() {
            fetchProfiles();
        });

        // Date Range Apply Button
        $('.date-filter-container .btn-primary').on('click', function(e) {
            e.preventDefault();
            fetchProfiles();
            $(this).closest('.dropdown-menu').removeClass('show');
        });
    
        // Pagination
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            fetchProfiles($(this).attr('href'), true);
        });
    
        // Reset All Filters
        $('.reset-filter-btn').on('click', function(e) {
            e.preventDefault();
            $('#filter-form')[0].reset();
            $('.js-example-basic-single').val(null).trigger('change');
            $('#per-page-select').val('10').trigger('change');
            window.history.pushState({}, '', "{{ route('admin.profiles.index') }}");
            fetchProfiles();
        });

        // Enhanced dropdown behavior for navbar filters only
        $('.navbar-filters .dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Close other dropdowns
            $('.navbar-filters .dropdown-menu').not($(this).next()).removeClass('show');
            
            // Toggle current dropdown
            $(this).next('.dropdown-menu').toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.navbar-filters .dropdown').length) {
                $('.navbar-filters .dropdown-menu').removeClass('show');
            }
        });

        // Prevent dropdown from closing when clicking inside
        $('.navbar-filters .dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Bulk Delete Functionality
        function updateBulkActions() {
            const checkedBoxes = $('.profile-checkbox:checked');
            const count = checkedBoxes.length;
            
            if (count > 0) {
                $('#bulk-actions').show();
                $('#selected-count').text(count + ' selected');
            } else {
                $('#bulk-actions').hide();
            }
            
            // Update select all checkbox state
            const totalBoxes = $('.profile-checkbox').length;
            const selectAllCheckbox = $('#select-all');
            
            if (count === 0) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', false);
            } else if (count === totalBoxes) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', true);
            } else {
                selectAllCheckbox.prop('indeterminate', true);
                selectAllCheckbox.prop('checked', false);
            }
        }

        // Select All functionality
        $(document).on('change', '#select-all', function() {
            const isChecked = $(this).is(':checked');
            $('.profile-checkbox').prop('checked', isChecked);
            $('.profile-checkbox').closest('tr').toggleClass('selected', isChecked);
            updateBulkActions();
        });

        // Individual checkbox functionality
        $(document).on('change', '.profile-checkbox', function() {
            $(this).closest('tr').toggleClass('selected', $(this).is(':checked'));
            updateBulkActions();
        }); 

        // Row click functionality (excluding checkboxes and action buttons)
        $(document).on('click', '.clickable-cell', function() {
            const row = $(this).closest('tr');
            const profileId = row.data('profile-id');
            const gender = row.data('gender');
            const city = row.data('city');
            const slug = row.data('slug');
            
            if (gender && city && slug) {
                const url = `/${gender}-escorts-in-${city}/${profileId}/${slug}`;
                window.open(url, '_blank');
            }
        });

        // Bulk Delete functionality
        $(document).on('click', '#bulk-delete-btn', function() {
            const checkedBoxes = $('.profile-checkbox:checked');
            const profileIds = [];
            
            checkedBoxes.each(function() {
                profileIds.push($(this).val());
            });
            
            if (profileIds.length === 0) {
                alert('Please select profiles to delete');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${profileIds.length} selected profile(s)? This action cannot be undone.`)) {
                // Create a form and submit it
                const form = $('<form>', {
                    method: 'POST',
                    action: '{{ route("admin.profiles.bulk-delete") }}'
                });
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_method',
                    value: 'DELETE'
                }));
                
                profileIds.forEach(function(id) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'profile_ids[]',
                        value: id
                    }));
                });
                
                $('body').append(form);
                form.submit();
            }
        });

        // Individual Archive functionality
        $(document).on('click', '.archive-btn', function() {
            const profileId = $(this).data('profile-id');
            const reason = prompt('Enter archive reason (optional):');
            
            if (reason !== null) { // User didn't cancel
                $.ajax({
                    url: `/admin/profiles/${profileId}/archive`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        reason: reason
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Profile archived successfully');
                            fetchProfiles();
                        }
                    },
                    error: function() {
                        alert('Error archiving profile. Please try again.');
                    }
                });
            }
        });

        // Individual Repost functionality
        $(document).on('click', '.repost-btn', function() {
            const profileId = $(this).data('profile-id');
            
            if (confirm('Are you sure you want to repost this profile?')) {
                $.ajax({
                    url: `/admin/profiles/${profileId}/repost`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Profile reposted successfully');
                            fetchProfiles();
                        }
                    },
                    error: function() {
                        alert('Error reposting profile. Please try again.');
                    }
                });
            }
        });

        // Bulk Archive functionality
        $(document).on('click', '#bulk-archive-btn', function() {
            const checkedBoxes = $('.profile-checkbox:checked');
            const profileIds = [];
            
            checkedBoxes.each(function() {
                profileIds.push($(this).val());
            });
            
            if (profileIds.length === 0) {
                alert('Please select profiles to archive');
                return;
            }
            
            const reason = prompt(`Enter archive reason for ${profileIds.length} selected profile(s) (optional):`);
            
            if (reason !== null) { // User didn't cancel
                const form = $('<form>', {
                    method: 'POST',
                    action: '{{ route("admin.profiles.bulk-archive") }}'
                });
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'reason',
                    value: reason
                }));
                
                profileIds.forEach(function(id) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'profile_ids[]',
                        value: id
                    }));
                });
                
                $('body').append(form);
                form.submit();
            }
        });

        // Bulk Repost functionality
        $(document).on('click', '#bulk-repost-btn', function() {
            const checkedBoxes = $('.profile-checkbox:checked');
            const profileIds = [];
            
            checkedBoxes.each(function() {
                profileIds.push($(this).val());
            });
            
            if (profileIds.length === 0) {
                alert('Please select profiles to repost');
                return;
            }
            
            if (confirm(`Are you sure you want to repost ${profileIds.length} selected profile(s)?`)) {
                const form = $('<form>', {
                    method: 'POST',
                    action: '{{ route("admin.profiles.bulk-repost") }}'
                });
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));
                
                profileIds.forEach(function(id) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'profile_ids[]',
                        value: id
                    }));
                });
                
                $('body').append(form);
                form.submit();
            }
        });

        // Reset bulk selection when filters are applied
        function resetBulkSelection() {
            $('#select-all').prop('checked', false);
            $('#select-all').prop('indeterminate', false);
            $('#bulk-actions').hide();
        }

        // Update fetchProfiles to reset bulk selection
        const originalFetchProfiles = fetchProfiles;
        fetchProfiles = function(url, isPagination = false) {
            originalFetchProfiles.call(this, url, isPagination);
            resetBulkSelection();
        };
    });
</script>
@endpush