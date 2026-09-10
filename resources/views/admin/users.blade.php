@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{smart_asset('admin/assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet" />

<style>
/* ===== Users page: modern redesign ===== */
:root {
    --u-primary: #6366f1;
    --u-primary-dark: #4f46e5;
    --u-success: #10b981;
    --u-danger: #ef4444;
    --u-warning: #f59e0b;
    --u-info: #06b6d4;
    --u-slate-50: #f8fafc;
    --u-slate-100: #f1f5f9;
    --u-slate-200: #e2e8f0;
    --u-slate-300: #cbd5e1;
    --u-slate-500: #64748b;
    --u-slate-600: #475569;
    --u-slate-700: #334155;
    --u-slate-800: #1e293b;
}

/* Datatable overrides */
span.input { display: none; }
.dataTables_wrapper .dataTables_filter input { margin-left: 0.5em; }
.dataTables_wrapper .dataTables_length select {
    padding: 4px 30px 4px 10px;
    min-width: 80px;
    width: auto;
}
@media (min-width: 576px) {
    .modal-dialog { max-width: 800px; }
}

/* Stats strip */
.u-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.u-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.u-stat .u-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.u-stat .u-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.u-stat .u-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.u-stat.total    { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.u-stat.active   { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.u-stat.pending  { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
.u-stat.verified { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }

/* Header row w/ Add button */
.u-page-actions {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 14px;
}
.u-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--u-primary) 0%, var(--u-primary-dark) 100%);
    color: #fff !important;
    border: 0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
    transition: box-shadow .15s;
    cursor: pointer;
}
.u-add-btn:hover { color: #fff; box-shadow: 0 6px 18px rgba(99, 102, 241, .5); }

/* Main card */
.u-card {
    background: #fff;
    border: 1px solid var(--u-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    /* overflow:visible so the row-level Actions dropdown can escape the
       card when the filtered result set puts the row near the bottom
       edge. Corner clipping is handled by the filter bar + pagination
       children below (they're the only children with backgrounds). */
    overflow: visible;
}

/* Filter bar (redesign of .q-filter-bar) */
.q-filter-bar {
    padding: 14px 20px !important;
    background: var(--u-slate-50) !important;
    border-bottom: 1px solid var(--u-slate-100) !important;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
}
.q-filter-bar #uFiltersForm { gap: 8px !important; }
.q-filter-bar .fa-filter {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex !important; align-items: center; justify-content: center;
    background: linear-gradient(135deg, var(--u-primary) 0%, var(--u-primary-dark) 100%);
    color: #fff !important;
    font-size: 13px !important;
    margin-right: 4px;
    box-shadow: 0 3px 10px rgba(99, 102, 241, .35);
}
.q-filter-bar > span:first-child {
    color: #fff !important; padding: 0 !important; margin-right: 6px !important;
}
.q-filter-bar .dropdown-menu {
    padding: 12px;
    border: 1px solid var(--u-slate-200) !important;
    border-radius: 10px;
    box-shadow: 0 12px 32px rgba(15,23,42,.15);
}
.q-filter-bar .dropdown-menu input,
.q-filter-bar .dropdown-menu select { cursor: auto; }
.q-filter-bar .dropdown-menu .form-control-sm {
    border-radius: 8px;
    border-color: var(--u-slate-200);
    padding: 7px 10px;
    font-size: 13px;
    height: 36px;
}
.q-filter-bar .dropdown-menu .form-control-sm:focus {
    border-color: var(--u-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.q-filter-bar .dropdown-menu .btn-primary {
    background: linear-gradient(135deg, var(--u-primary) 0%, var(--u-primary-dark) 100%);
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
    color: var(--u-slate-700) !important;
    background: #fff !important;
    border: 1px solid var(--u-slate-200) !important;
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
    color: var(--u-primary-dark) !important;
    background: #fff !important;
    border-color: var(--u-primary) !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12) !important;
}
.q-filter-bar .btn-outline-dark .filter-caret { color: var(--u-slate-500); opacity: 1; }
.q-filter-bar .btn-primary {
    background: #eef2ff !important;
    color: var(--u-primary-dark) !important;
    border: 1px solid var(--u-primary) !important;
    border-radius: 9px !important;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    height: 38px !important;
    display: inline-flex !important;
    align-items: center;
    box-shadow: none !important;
}
.q-filter-bar .btn-primary .filter-caret { color: var(--u-primary-dark); }
.q-filter-bar .btn-outline-danger {
    color: var(--u-danger) !important;
    background: #fff !important;
    border: 1px solid var(--u-slate-200) !important;
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
    border-color: var(--u-danger) !important;
    color: var(--u-danger) !important;
}
.q-filter-bar .ml-auto {
    background: #fff;
    border: 1px solid var(--u-slate-200);
    border-radius: 9px;
    padding: 6px 12px;
    height: 38px;
}
.q-filter-bar .ml-auto .text-muted { color: var(--u-slate-700) !important; font-weight: 600 !important; }
.q-filter-bar .ml-auto label { color: var(--u-slate-500) !important; }
.q-filter-bar .ml-auto .form-control-sm {
    border: 1px solid var(--u-slate-200);
    border-radius: 6px;
    font-size: 12px;
    height: 28px;
    padding: 2px 22px 2px 8px;
}

/* Users table */
.u-card .card-body { padding: 0; }
#usersTable { width: 100% !important; margin: 0 !important; font-size: 13.5px; border-collapse: separate; border-spacing: 0; }
#usersTable thead th {
    background: var(--u-slate-50);
    color: var(--u-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 14px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--u-slate-200) !important;
    text-align: left;
    white-space: nowrap;
}
#usersTable thead th:last-child { text-align: right; }
#usersTable tbody td {
    padding: 14px !important;
    vertical-align: middle;
    border: 0 !important;
    border-bottom: 1px solid var(--u-slate-100) !important;
    color: var(--u-slate-700);
    background: #fff !important;
}
#usersTable tbody tr:hover td { background: #fafbff !important; }
#usersTable tbody tr:last-child td { border-bottom: 0 !important; }

/* Cell primitives */
.u-date-cell {
    color: var(--u-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.u-date-cell small {
    display: block; color: var(--u-slate-500); font-size: 11px;
}
.u-name-cell {
    display: flex; align-items: center; gap: 10px;
    min-width: 180px;
}
.u-name-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; text-transform: uppercase;
    flex-shrink: 0;
}
.u-name-text {
    font-weight: 600; color: var(--u-slate-800); font-size: 13.5px;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    max-width: 240px;
}
.u-email {
    color: var(--u-slate-600); font-size: 13px;
    word-break: break-all;
}
.u-country-cell {
    display: inline-flex; align-items: center; gap: 6px;
    color: var(--u-slate-700); font-weight: 500; font-size: 13px;
}
.u-country-cell img { border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,.06); }
.u-country-na { color: var(--u-slate-300); font-weight: 500; }

/* Status pill (must include .status-badge-{id} for JS) */
#usersTable .badge.status-badge-,
#usersTable .badge[class*="status-badge-"] {
    display: inline-flex !important;
    align-items: center;
    gap: 5px;
    padding: 4px 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    line-height: 1.4 !important;
    text-transform: capitalize;
}
#usersTable .badge[class*="status-badge-"].bg-success { background: #d1fae5 !important; color: #065f46 !important; }
#usersTable .badge[class*="status-badge-"].bg-warning { background: #fef3c7 !important; color: #92400e !important; }
#usersTable .badge[class*="status-badge-"].bg-danger  { background: #fee2e2 !important; color: #991b1b !important; }

/* Verified icon */
.u-verified-cell { display: inline-flex; align-items: center; gap: 6px; }
.u-verified-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px;
    border-radius: 8px; font-size: 13px; font-weight: 700;
}
.u-verified-badge.yes { background: #d1fae5; color: #065f46; }
.u-verified-badge.no  { background: #fee2e2; color: #991b1b; }
.u-google-tag {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 8px; border-radius: 20px;
    background: var(--u-slate-100); color: var(--u-slate-600);
    font-size: 11px; font-weight: 600;
}
.u-google-tag img { width: 12px; height: 12px; }

/* Buttons - view / actions dropdown */
.u-view-btn {
    display: inline-flex !important;
    align-items: center; gap: 6px;
    padding: 7px 14px !important;
    background: #eef2ff !important;
    color: var(--u-primary-dark) !important;
    border: 0 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    transition: background .15s;
    line-height: 1 !important;
}
.u-view-btn:hover { background: #e0e7ff !important; color: var(--u-primary-dark) !important; }

.u-actions-btn {
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
    padding: 7px 14px !important;
    background: linear-gradient(135deg, var(--u-primary) 0%, var(--u-primary-dark) 100%) !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    box-shadow: 0 3px 8px rgba(99, 102, 241, .3);
    line-height: 1 !important;
}
.u-actions-btn::after {
    border-top-color: rgba(255,255,255,.8) !important;
    margin-left: 4px !important;
}
.u-actions-btn:hover, .u-actions-btn:focus {
    box-shadow: 0 5px 12px rgba(99, 102, 241, .45);
    color: #fff !important;
}

#usersTable .dropdown-menu {
    border: 1px solid var(--u-slate-200);
    border-radius: 10px;
    box-shadow: 0 12px 32px rgba(15,23,42,.15);
    padding: 6px;
    min-width: 200px;
    /* Items are absolute-siblings inside a padded box; if any one
       computes wider than the container (e.g. Bootstrap's default
       `width:100%` + our padding under content-box sizing), clip it
       rather than let the hover highlight bleed past the rounded
       corners on the right. */
    overflow: hidden;
}
#usersTable .dropdown-item {
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 13px;
    color: var(--u-slate-700);
    display: flex;
    align-items: center;
    gap: 8px;
    /* Force border-box + width:100% so padding is counted inside the
       item's box. Without this the item was extending ~18px past the
       menu's right edge (visible as a bleeding hover highlight on the
       "Login as User" row). */
    box-sizing: border-box;
    width: 100%;
    min-width: 0;
}
#usersTable .dropdown-item:hover { background: var(--u-slate-50); color: var(--u-slate-800); }
#usersTable .dropdown-item.text-danger:hover { background: #fef2f2; color: #b91c1c; }
#usersTable .dropdown-item i { width: 14px; text-align: center; color: var(--u-slate-500); }
#usersTable .dropdown-item.text-danger i { color: var(--u-danger); }
#usersTable .dropdown-divider { margin: 4px 2px; border-color: var(--u-slate-100); }

/* Pagination footer */
.u-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 16px 22px;
    border-top: 1px solid var(--u-slate-100);
    background: var(--u-slate-50);
    font-size: 13px;
    color: var(--u-slate-600);
    border-bottom-left-radius: 14px;
    border-bottom-right-radius: 14px;
}
.u-pagination .pagination { margin: 0; }
.u-pagination .pagination .page-link {
    color: var(--u-slate-600);
    border-color: var(--u-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.u-pagination .pagination .page-item.active .page-link {
    background: var(--u-primary);
    border-color: var(--u-primary);
    color: #fff;
}

/* ===== Modal — modern (scoped .u-modal) ===== */
.u-modal .modal-dialog { max-width: 900px; }
.u-modal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(15,23,42,.3);
}
.u-modal .modal-header {
    padding: 18px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--u-slate-100);
    align-items: center;
}
.u-modal .modal-title {
    display: flex; align-items: center; gap: 10px;
    font-size: 15px; font-weight: 700; color: var(--u-slate-800);
    margin: 0;
}
.u-modal .u-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--u-primary-dark); font-size: 14px;
}
.u-modal .modal-header .close {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: var(--u-slate-100) !important;
    color: var(--u-slate-600) !important;
    opacity: 1;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 20px; line-height: 1;
    text-shadow: none;
    padding: 0; margin: 0;
    border: 0;
    transition: background .15s, color .15s;
    top: auto !important;
    right: auto !important;
    position: static !important;
}
.u-modal .modal-header .close:hover {
    background: #fee2e2 !important;
    color: #991b1b !important;
}
.u-modal .modal-body {
    padding: 22px;
    background: var(--u-slate-50);
}

/* Sections within the body */
.u-modal .u-form-section {
    background: #fff;
    border: 1px solid var(--u-slate-200);
    border-radius: 12px;
    padding: 18px 18px 4px;
    margin-bottom: 14px;
    box-shadow: 0 3px 10px rgba(15,23,42,.04);
}
.u-modal .u-form-section-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    color: var(--u-slate-500);
    margin: 0 0 14px;
    padding-bottom: 10px;
    border-bottom: 1px dashed var(--u-slate-200);
}
.u-modal .u-form-section-title i {
    color: var(--u-primary);
    font-size: 12px;
}

/* Form controls inside the modal */
.u-modal .form-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--u-slate-500);
    margin-bottom: 6px;
}
.u-modal .form-label i { color: var(--u-primary); font-size: 11px; }
.u-modal .form-label .text-danger { color: var(--u-danger) !important; font-weight: 700; }

.u-modal .form-control,
.u-modal select.form-control {
    height: 44px;
    padding: 8px 14px;
    font-size: 14px;
    color: var(--u-slate-800);
    background: #fff;
    border: 1px solid var(--u-slate-200);
    border-radius: 10px;
    transition: border-color .15s, box-shadow .15s;
    box-shadow: none;
}
.u-modal textarea.form-control { height: auto; min-height: 84px; }
.u-modal .form-control:focus,
.u-modal select.form-control:focus {
    outline: none;
    border-color: var(--u-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.u-modal .form-control::placeholder { color: var(--u-slate-300); }
.u-modal .form-control[disabled],
.u-modal .form-control[readonly] {
    background: var(--u-slate-100);
    color: var(--u-slate-600);
    cursor: not-allowed;
}

/* Input-group (password + toggle) */
.u-modal .input-group { position: relative; }
.u-modal .input-group .form-control {
    border-top-right-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
    padding-right: 46px;
}
.u-modal .input-group .btn {
    position: absolute; top: 50%; right: 6px;
    transform: translateY(-50%);
    z-index: 4;
    width: 34px; height: 34px;
    padding: 0;
    background: transparent !important;
    border: 0 !important;
    color: var(--u-slate-500) !important;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
}
.u-modal .input-group .btn:hover {
    background: var(--u-slate-100) !important;
    color: var(--u-primary-dark) !important;
}
.u-modal .input-group .btn:focus { box-shadow: none; }

/* Password Management heading + info alert */
.u-modal hr {
    display: none;
}
.u-modal .u-alert-info {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 14px;
    background: #dbeafe;
    color: #1e40af;
    border: 0;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 14px;
}
.u-modal .u-alert-info i { margin-top: 2px; }

/* Small helper (min chars text) */
.u-modal small.text-muted {
    color: var(--u-slate-500) !important;
    font-size: 11px;
    margin-top: 4px;
    display: inline-block;
}

/* Generate password button (secondary style) */
.u-modal #generateRandomPassword {
    background: var(--u-slate-100);
    color: var(--u-slate-700);
    border: 1px solid var(--u-slate-200);
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s, color .15s;
}
.u-modal #generateRandomPassword:hover {
    background: #fef3c7;
    color: #92400e;
    border-color: #fde68a;
}

/* Buttons — general inside .u-modal (body + footer) */
.u-modal .btn {
    height: 42px;
    padding: 0 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    border: 0;
    transition: box-shadow .15s, background .15s, color .15s;
    white-space: nowrap;
    line-height: 1;
}
.u-modal .btn-sm {
    height: 32px;
    padding: 0 12px;
    font-size: 12px;
    border-radius: 8px;
    gap: 5px;
}
.u-modal .btn-primary {
    background: linear-gradient(135deg, var(--u-primary) 0%, var(--u-primary-dark) 100%);
    color: #fff;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .3);
}
.u-modal .btn-primary:hover {
    box-shadow: 0 6px 18px rgba(99, 102, 241, .45);
    color: #fff;
}
.u-modal .btn-secondary {
    background: var(--u-slate-100);
    color: var(--u-slate-700);
}
.u-modal .btn-secondary:hover {
    background: var(--u-slate-200);
    color: var(--u-slate-800);
}
.u-modal .btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    box-shadow: 0 4px 14px rgba(16, 185, 129, .3);
}
.u-modal .btn-success:hover {
    box-shadow: 0 6px 18px rgba(16, 185, 129, .45);
    color: #fff;
}
.u-modal .btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #fff;
    box-shadow: 0 4px 14px rgba(239, 68, 68, .3);
}
.u-modal .btn-danger:hover {
    box-shadow: 0 6px 18px rgba(239, 68, 68, .45);
    color: #fff;
}
.u-modal .btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #fff;
    box-shadow: 0 4px 14px rgba(245, 158, 11, .3);
}
.u-modal .btn-warning:hover {
    box-shadow: 0 6px 18px rgba(245, 158, 11, .45);
    color: #fff;
}

/* Footer */
.u-modal .modal-footer {
    padding: 14px 22px;
    background: #fff;
    border-top: 1px solid var(--u-slate-100);
    gap: 8px;
}

/* Section head (title + right-aligned action) inside a .u-form-section */
.u-modal .u-section-head {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    margin: 0 0 12px;
    padding-bottom: 10px;
    border-bottom: 1px dashed var(--u-slate-200);
}
.u-modal .u-section-head h5,
.u-modal .u-section-head .u-section-label {
    margin: 0;
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    color: var(--u-slate-500);
}
.u-modal .u-section-head i { color: var(--u-primary); font-size: 12px; }

/* Tables inside .u-modal */
.u-modal .table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
    border: 0 !important;
}
.u-modal .table thead th {
    background: var(--u-slate-50) !important;
    color: var(--u-slate-500) !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: 12px 14px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--u-slate-200) !important;
    text-align: left;
    white-space: nowrap;
}
.u-modal .table tbody td {
    padding: 12px 14px !important;
    vertical-align: middle !important;
    border: 0 !important;
    border-bottom: 1px solid var(--u-slate-100) !important;
    color: var(--u-slate-700) !important;
    background: #fff !important;
}
.u-modal .table tbody tr:last-child td { border-bottom: 0 !important; }
.u-modal .table tbody tr:hover td { background: #fafbff !important; }
.u-modal .table td .btn + .btn { margin-left: 6px; }
.u-modal .table-responsive-wrap {
    background: #fff;
    border: 1px solid var(--u-slate-200);
    border-radius: 10px;
    overflow: hidden;
}

/* Responsive */
@media (max-width: 992px) {
    .u-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .main-wrapper { padding-left: 8px !important; padding-right: 8px !important; }
    .container-fluid { padding-left: 6px !important; padding-right: 6px !important; }
    .u-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .q-filter-bar { padding: 12px !important; }
    .u-add-btn { width: 100%; justify-content: center; }
    .u-pagination { flex-direction: column; text-align: center; }
    #usersTable thead { display: none; }
    #usersTable tbody td { padding: 10px 12px !important; }
}
</style>
@endpush

@section("content")

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Users</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage users effectively</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </div>
</div>

@php
    $totalUsers      = !empty($users) ? $users->total() : 0;
    $onPageActive    = !empty($users) ? collect($users->items())->filter(fn($u) => ($u->status ?? 'pending') === 'active')->count() : 0;
    $onPagePending   = !empty($users) ? collect($users->items())->filter(fn($u) => ($u->status ?? 'pending') !== 'active')->count() : 0;
    $onPageVerified  = !empty($users) ? collect($users->items())->filter(fn($u) => $u->verified == 1)->count() : 0;
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="u-stats-row">
        <div class="u-stat total">
            <p class="u-stat-label">Total Users</p>
            <p class="u-stat-value">{{ number_format($totalUsers) }}</p>
            <i class="fas fa-users u-stat-icon"></i>
        </div>
        <div class="u-stat active">
            <p class="u-stat-label">Active · On This Page</p>
            <p class="u-stat-value">{{ number_format($onPageActive) }}</p>
            <i class="fas fa-check-circle u-stat-icon"></i>
        </div>
        <div class="u-stat pending">
            <p class="u-stat-label">Pending · On This Page</p>
            <p class="u-stat-value">{{ number_format($onPagePending) }}</p>
            <i class="fas fa-hourglass-half u-stat-icon"></i>
        </div>
        <div class="u-stat verified">
            <p class="u-stat-label">Verified · On This Page</p>
            <p class="u-stat-value">{{ number_format($onPageVerified) }}</p>
            <i class="fas fa-shield-alt u-stat-icon"></i>
        </div>
    </div>

    {{-- Add User button --}}
    <div class="u-page-actions">
        <button type="button" class="u-add-btn" id="addUserBtn">
            <i class="fa fa-plus"></i> Add New User
        </button>
    </div>

    {{-- Main card --}}
    @if(!empty($users))
    <div class="u-card">
        @php
            $statusLabels = ['active' => 'Active', 'pending' => 'Pending'];
            $verifiedLabels = ['1' => 'Verified', '0' => 'Not verified'];
            $activeCount = collect(['id','name','email','status','verified','date_from','date_to'])
                ->filter(fn($k) => request()->filled($k))
                ->count();
            $pp = (int) request('per_page', 25);
        @endphp

        {{-- Filter bar --}}
        <div class="card-header q-filter-bar">
            <form method="GET" action="{{ route('admin.users') }}" id="uFiltersForm" class="d-flex align-items-center flex-wrap" style="gap:6px;">
                <span><i class="fa fa-filter"></i></span>

                {{-- ID --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-hashtag mr-1"></i> ID{{ request('id') ? ': '.request('id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:220px;">
                        <input type="number" name="id" value="{{ request('id') }}" class="form-control form-control-sm mb-2" placeholder="User ID" min="1">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                {{-- Name --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('name') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user mr-1"></i> Name{{ request('name') ? ': '.\Illuminate\Support\Str::limit(request('name'), 20) : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:240px;">
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control form-control-sm mb-2" placeholder="Search by name...">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                {{-- Email --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request('email') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope mr-1"></i> Email{{ request('email') ? ': '.\Illuminate\Support\Str::limit(request('email'), 22) : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:260px;">
                        <input type="text" name="email" value="{{ request('email') }}" class="form-control form-control-sm mb-2" placeholder="Search by email...">
                        <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                    </div>
                </div>

                {{-- Status --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request()->filled('status') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-toggle-on mr-1"></i> Status{{ request()->filled('status') ? ': '.($statusLabels[request('status')] ?? request('status')) : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:180px;">
                        <select name="status" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>

                {{-- Verified --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-sm {{ request()->filled('verified') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-shield-alt mr-1"></i> Verified{{ request()->filled('verified') ? ': '.($verifiedLabels[request('verified')] ?? '') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:180px;">
                        <select name="verified" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                            <option value="">Any</option>
                            <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified</option>
                            <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Not verified</option>
                        </select>
                    </div>
                </div>

                {{-- Registered (date range) --}}
                <div class="btn-group">
                    @php
                        $dateLabel = '';
                        if (request('date_from') && request('date_to')) $dateLabel = ': '.request('date_from').' → '.request('date_to');
                        elseif (request('date_from')) $dateLabel = ': from '.request('date_from');
                        elseif (request('date_to')) $dateLabel = ': to '.request('date_to');
                    @endphp
                    <button type="button" class="btn btn-sm {{ ($dateLabel !== '') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-calendar-alt mr-1"></i> Registered{{ $dateLabel }} <i class="fa fa-chevron-down filter-caret"></i>
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
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-danger" title="Reset all filters">
                        <i class="fa fa-times"></i> Reset ({{ $activeCount }})
                    </a>
                @endif

                <div class="ml-auto d-flex align-items-center" style="gap:10px;">
                    <span class="text-muted small">{{ number_format($users->total()) }} user(s)</span>
                    <label class="mb-0 small text-muted">Per page</label>
                    <select name="per_page" class="form-control form-control-sm" style="width:auto;" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $opt)
                            <option value="{{ $opt }}" {{ $pp === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
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
            <table id="usersTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Verified Email</th>
                        <th>Profiles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr id="row{{$user->id}}">
                        <td>
                            @if($user->created_at)
                                <div class="u-date-cell">
                                    {{ $user->created_at->format('M d, Y') }}
                                    <small>{{ $user->created_at->format('H:i') }}</small>
                                </div>
                            @else
                                <span style="color:#cbd5e1;">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="u-name-cell">
                                <span class="u-name-avatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                <span class="u-name-text">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td><span class="u-email">{{ $user->email }}</span></td>
                        <td>
                            @if($user->country)
                                <span class="u-country-cell">
                                    <img src="https://flagcdn.com/16x12/{{ strtolower($user->country) }}.png" alt="{{ $user->country }}">
                                    {{ strtoupper($user->country) }}
                                </span>
                            @else
                                <span class="u-country-na">N/A</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge status-badge-{{$user->id}} {{ $user->status == 'active' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($user->status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="u-verified-cell">
                                @if($user->verified == 1)
                                    <span class="badge bg-success u-verified-badge yes">✓</span>
                                @else
                                    <span class="badge bg-danger u-verified-badge no">✗</span>
                                @endif
                                @if($user->google_id)
                                    <span class="u-google-tag" title="Google Account">
                                        <img src="https://www.google.com/favicon.ico" alt="Google">
                                        Google
                                    </span>
                                @endif
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm u-view-btn" onclick="showProfiles({{$user->id}})">
                                <i class="fa fa-eye"></i> View
                            </button>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle u-actions-btn" type="button" id="dropdownMenuButton{{$user->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-cog"></i> Actions
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{$user->id}}">
                                    <a class="dropdown-item edit-user" href="javascript:void(0)" data-id="{{$user->id}}">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a class="dropdown-item toggle-status" href="javascript:void(0)"
                                       data-id="{{$user->id}}"
                                       data-status="{{$user->status}}">
                                        <i class="fa {{ $user->status == 'active' ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                        {{ $user->status == 'active' ? 'Deactivate' : 'Activate' }}
                                    </a>
                                    @if(!$user->email_verified_at && !$user->verified)
                                    <a class="dropdown-item send-verification" href="javascript:void(0)" data-id="{{$user->id}}" data-email="{{$user->email}}">
                                        <i class="fa fa-envelope"></i> Send Verification
                                    </a>
                                    @endif
                                    <a class="dropdown-item impersonate-user" href="javascript:void(0)" data-id="{{$user->id}}" data-name="{{$user->name}}">
                                        <i class="fa fa-sign-in"></i> Login as User
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger delete-user" href="javascript:void(0)" data-id="{{$user->id}}">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="u-pagination">
            <div>
                @if($users->total() > 0)
                    Showing <strong>{{ $users->firstItem() }}</strong> to
                    <strong>{{ $users->lastItem() }}</strong> of
                    <strong>{{ number_format($users->total()) }}</strong> entries
                @else
                    No users match the selected filters
                @endif
            </div>
            <div>{{ $users->links() }}</div>
        </div>
    </div>
    @endif
</div>

<!-- User Profiles Modal -->
<div class="modal fade u-modal" id="profilesModal" tabindex="-1" aria-labelledby="profilesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profilesModalLabel">
                    <span class="u-title-icon"><i class="fas fa-id-badge"></i></span>
                    User Profiles
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add Profile Form -->
                <div id="addProfileSection" class="u-form-section" style="display: none;">
                    <h6 class="u-form-section-title"><i class="fas fa-plus-circle"></i> Add New Profile</h6>
                    <form id="addProfileForm">
                        @csrf
                        <input type="hidden" name="user_id" id="profileUserId">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="profileName" class="form-label"><i class="fas fa-user"></i> Name</label>
                                    <input type="text" class="form-control" name="name" id="profileName" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="profileCity" class="form-label"><i class="fas fa-map-marker-alt"></i> City</label>
                                    <input type="text" class="form-control" name="city" id="profileCity">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="profilePhone" class="form-label"><i class="fas fa-phone"></i> Phone</label>
                                    <input type="text" class="form-control" name="phone" id="profilePhone">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="profileGender" class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                                    <select class="form-control" name="gender" id="profileGender">
                                        <option value="" selected disabled>Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Save Profile
                        </button>
                    </form>
                </div>

                <!-- Profiles List -->
                <div class="u-form-section">
                    <div class="u-section-head">
                        <h5><i class="fas fa-list"></i> Profiles</h5>
                        <button class="btn btn-success btn-sm" onclick="showAddProfileForm()">
                            <i class="fas fa-plus"></i> Add Profile
                        </button>
                    </div>
                    <div class="table-responsive-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>City</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="profilesBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editProfileForm">
                @csrf
                <input type="hidden" name="id" id="editProfileId">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCity" class="form-label">City</label>
                        <input type="text" name="city" id="editCity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editGender" class="form-label">Gender</label>
                        <input type="text" name="gender" id="editGender" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="editPhone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="assignPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Package</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignPackageForm">
                    <input type="hidden" id="profile_id" name="profile_id">
                    <div class="mb-3">
                        <label class="form-label">Select Package</label>
                        <select class="form-select form-control" name="package_id" id="package_id">
                            <option value="">Select Package</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" 
                                        data-price="{{ $package->price }}" 
                                        data-days="{{ $package->promo_days }}">
                                    {{ $package->name }} - ${{ $package->price }} ({{ $package->promo_days }} days)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="packageDetails" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Package Details:</strong><br>
                            <span id="packageName"></span><br>
                            <strong>Price:</strong> $<span id="packagePrice"></span><br>
                            <strong>Duration:</strong> <span id="packageDays"></span> days
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                            <label class="form-check-label">Featured Profile</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePackageAssignment">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade u-modal" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="u-title-icon"><i class="fas fa-user-edit"></i></span>
                    Edit User Information
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    @csrf
                    <input type="hidden" id="editUserId" name="id">

                    {{-- Basic Info --}}
                    <div class="u-form-section">
                        <h6 class="u-form-section-title"><i class="fas fa-id-card"></i> Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-user"></i> Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editUserName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-envelope"></i> Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="editUserEmail" name="email" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-briefcase"></i> Account Type</label>
                                    <select class="form-control" id="editUserType" name="type">
                                        <option value="2">Individual advertiser</option>
                                        <option value="3">Agency</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-toggle-on"></i> Status</label>
                                    <select class="form-control" id="editUserStatus" name="status">
                                        <option value="pending">Pending</option>
                                        <option value="active">Active</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-shield-alt"></i> Email Verified</label>
                                    <select class="form-control" id="editUserVerified" name="verified">
                                        <option value="0">Not Verified</option>
                                        <option value="1">Verified</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-globe"></i> Registration Country</label>
                                    <input type="text" class="form-control" id="editUserRegistrationCountry" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="u-form-section">
                        <h6 class="u-form-section-title"><i class="fas fa-address-book"></i> Contact</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-flag"></i> Country Code</label>
                                    <select class="form-control" id="editUserCountryCode" name="country_code">
                                        <option value="">Select</option>
                                        @foreach($countries as $country)
                                            <option value="+{{ $country->phonecode }}">+{{ $country->phonecode }} ({{ $country->iso }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                                    <input type="text" class="form-control" id="editUserPhone" name="phone" placeholder="Phone number">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-align-left"></i> About</label>
                            <textarea class="form-control" id="editUserAbout" name="about" rows="3" placeholder="User bio or description"></textarea>
                        </div>
                    </div>

                    {{-- Account Metadata (read-only) --}}
                    <div class="u-form-section">
                        <h6 class="u-form-section-title"><i class="fas fa-info-circle"></i> Account Metadata</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="far fa-clock"></i> Created At</label>
                                    <input type="text" class="form-control" id="editUserCreatedAt" readonly disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fab fa-google"></i> Google Account</label>
                                    <input type="text" class="form-control" id="editUserGoogleId" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-network-wired"></i> Registration IP</label>
                                    <input type="text" class="form-control" id="editUserRegistrationIp" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Password Management --}}
                    <div class="u-form-section">
                        <h6 class="u-form-section-title"><i class="fas fa-key"></i> Password Management</h6>

                        <div class="u-alert-info">
                            <i class="fas fa-info-circle"></i>
                            <div><strong>Note:</strong> Passwords are encrypted and cannot be viewed. You can only set a new password.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-lock"></i> Set New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="editUserNewPassword" name="new_password" placeholder="Leave blank to keep current">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword" title="Show/Hide Password">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-lock"></i> Confirm New Password</label>
                                    <input type="password" class="form-control" id="editUserConfirmPassword" name="confirm_password" placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-warning" id="generateRandomPassword">
                                    <i class="fa fa-random"></i> Generate Random Password
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="saveUserEdit">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('admin.adduser')}}" id="addUserForm">
                    {{csrf_field()}}
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter user name" required>
                        @error("name")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter user email" required>
                        @error("email")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                        @error("password")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
var token = "{{ csrf_token() }}";

// Show Add Profile Form
function showAddProfileForm() {
    $('#addProfileSection').slideToggle(); // Toggle the form display
}

// Submit New Profile
$("#addProfileForm").submit(function (e) {
    e.preventDefault();

    var formData = $(this).serialize(); // Serialize form inputs
    $.post("{{ route('admin.addprofile') }}", formData, function (response) {
        if (response.success) {
            alert(response.success);
            $('#addProfileForm')[0].reset(); // Reset form
            showProfiles($("#profileUserId").val()); // Reload profiles
        }
    }).fail(function (xhr) {
        alert("Error: " + xhr.responseJSON.message);
    });
});

// Load Profiles
function showProfiles(userId) {
    $("#profileUserId").val(userId); // Set user_id for form
    $("input#user_id").val(userId);
    $.post("{{ route('admin.getprofiles') }}", { _token: token, user_id: userId }, function (data) {
        console.log('Profiles data received:', data);
        console.log('Data type:', typeof data);
        console.log('Data length:', data.length);
        
        if (data && data.trim() !== '') {
            $('#profilesBody').html(data); // Update profiles list
        } else {
            $('#profilesBody').html('<tr><td colspan="5" class="text-center">No profiles found</td></tr>');
        }
        
        $('#profilesModal').modal('show');
    }).fail(function(xhr, status, error) {
        console.error('Error loading profiles:', error);
        console.error('Response:', xhr.responseText);
        $('#profilesBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading profiles</td></tr>');
        $('#profilesModal').modal('show');
    });
}

// Delete User
{{-- $(document).on('click', '.delete-profile', function () {
    var profileId = $(this).data('id');
    if (confirm("Are you sure you want to delete this profile?")) {
         $.post("{{ route('admin.deleteprofile') }}", {_token: token, id: profileId}, function (response) {
            if (response.success) {
                alert(response.success);
                showProfiles($("#profileUserId").val()); // Reload profiles
            }
        }).fail(function (xhr) {
            alert("Error: " + xhr.responseJSON.message);
        });
    } 
});--}}

$(document).on('click', '.edit-profile', function () {
    var profileId = $(this).data('id');
    $.get("{{ route('admin.getprofile') }}", {id: profileId}, function (profile) {
        $("#editProfileId").val(profile.id);
        $("#editName").val(profile.name);
        $("#editCity").val(profile.city);
        $("#editGender").val(profile.gender);
        $("#editPhone").val(profile.phone);
        $('#editProfileModal').modal('show');
    }).fail(function () {
        alert("Failed to fetch profile data.");
    });
});

$("#editProfileForm").submit(function (e) {
    e.preventDefault();

    var formData = $(this).serialize();
    {{-- $.post("{{ route('admin.updateprofile') }}", formData, function (response) {
        if (response.success) {
            alert(response.success);
            $('#editProfileModal').modal('hide');
            showProfiles($("#profileUserId").val()); // Reload profiles
        }
    }).fail(function (xhr) {
        alert("Error: " + xhr.responseJSON.message);
    }); --}}
});
    // Edit User (similar logic can be applied for UserProfile CRUD)

    $(document).on('click', '.assign-package', function() {
    var profileId = $(this).data('id');
    var isFeatured = $(this).data('featured');
    var packageId = $(this).data('package');
    
    $('#profile_id').val(profileId);
    $('#package_id').val(packageId);
    $('#is_featured').prop('checked', isFeatured == 1);
    
    // Show package details if a package is already assigned
    if (packageId) {
        var selectedOption = $('#package_id option:selected');
        if (selectedOption.length) {
            $('#packageName').text(selectedOption.text());
            $('#packagePrice').text(selectedOption.data('price'));
            $('#packageDays').text(selectedOption.data('days'));
            $('#packageDetails').show();
        }
    }
    
    $('#assignPackageModal').modal('show');
});

// Update package details when selection changes
$('#package_id').change(function() {
    var selectedOption = $(this).find('option:selected');
    if (selectedOption.val()) {
        $('#packageName').text(selectedOption.text());
        $('#packagePrice').text(selectedOption.data('price'));
        $('#packageDays').text(selectedOption.data('days'));
        $('#packageDetails').show();
    } else {
        $('#packageDetails').hide();
    }
});

$('#savePackageAssignment').click(function() {
    var formData = {
        _token: token,
        profile_id: $('#profile_id').val(),
        package_id: $('#package_id').val(),
        is_featured: $('#is_featured').is(':checked') ? 1 : 0
    };

    $.post("{{ route('admin.assignpackage') }}", formData, function(response) {
        if(response.success) {
            $('#assignPackageModal').modal('hide');
            showProfiles($("#profileUserId").val());
            alert(response.success);
        }
    }).fail(function(xhr) {
        alert('Error: ' + (xhr.responseJSON?.message || 'Failed to assign package'));
    });
});

$(document).on('click', '.edit-user', function() {
    var userId = $(this).data('id');
    
    // Fetch user data via AJAX
    $.ajax({
        url: "{{ route('admin.users') }}",
        type: 'GET',
        data: { id: userId },
        success: function(response) {
            // Find the user in the response
            var row = $('#row' + userId);
            
            // Make another AJAX call to get full user details
            $.ajax({
                url: '/admin/getuser/' + userId,
                type: 'GET',
                success: function(user) {
                    $('#editUserId').val(user.id);
                    $('#editUserName').val(user.name);
                    $('#editUserEmail').val(user.email);
                    $('#editUserType').val(user.type || '1');
                    $('#editUserStatus').val(user.status || 'pending');
                    $('#editUserVerified').val(user.verified || '0');
                    // Normalize country code - add + prefix if not present
                    var countryCode = user.country_code || '';
                    if (countryCode && !countryCode.startsWith('+')) {
                        countryCode = '+' + countryCode;
                    }
                    $('#editUserCountryCode').val(countryCode);
                    $('#editUserPhone').val(user.phone || '');
                    $('#editUserAbout').val(user.about || '');
                    $('#editUserCreatedAt').val(user.created_at ? new Date(user.created_at).toLocaleString() : 'N/A');
                    $('#editUserGoogleId').val(user.google_id ? 'Connected' : 'Not Connected');
                    $('#editUserRegistrationIp').val(user.registration_ip || 'N/A');
                    $('#editUserRegistrationCountry').val(user.registration_country || 'N/A');
                    
                    // Show password hash (first 20 chars) for reference
                    if (user.password_hash) {
                        $('#editUserCurrentPassword').val(user.password_hash);
                        $('#passwordHashInfo').text('Encrypted: ' + user.password_hash.substring(0, 15) + '...');
                    } else {
                        $('#editUserCurrentPassword').val('••••••••');
                        $('#passwordHashInfo').text('Password is set');
                    }
                    
                    // Clear new password fields
                    $('#editUserNewPassword').val('');
                    $('#editUserConfirmPassword').val('');
                    
                    $('#editUserModal').modal('show');
                },
                error: function() {
                    // Fallback to row data
                    var name = row.find('td:eq(1)').text();
                    var email = row.find('td:eq(2)').text();
                    var status = row.find('.status-badge-' + userId).text().trim().toLowerCase();
                    
                    $('#editUserId').val(userId);
                    $('#editUserName').val(name);
                    $('#editUserEmail').val(email);
                    $('#editUserStatus').val(status);
                    $('#editUserModal').modal('show');
                }
            });
        }
    });
});

$('#saveUserEdit').click(function() {
    var userId = $('#editUserId').val();
    var formData = {
        _token: token,
        id: userId,
        name: $('#editUserName').val(),
        email: $('#editUserEmail').val(),
        type: $('#editUserType').val(),
        status: $('#editUserStatus').val(),
        verified: $('#editUserVerified').val(),
        country_code: $('#editUserCountryCode').val(),
        phone: $('#editUserPhone').val(),
        about: $('#editUserAbout').val(),
        new_password: $('#editUserNewPassword').val()
    };
    
    // Validate password confirmation
    if (formData.new_password && formData.new_password !== $('#editUserConfirmPassword').val()) {
        alert('New password and confirmation do not match!');
        return;
    }
    
    if (formData.new_password && formData.new_password.length < 6) {
        alert('Password must be at least 6 characters!');
        return;
    }

    $.ajax({
        url: "{{ route('admin.updateuser', '') }}/" + userId,
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#editUserModal').modal('hide');
            
            // Clear password fields
            $('#editUserNewPassword').val('');
            $('#editUserConfirmPassword').val('');
            
            // Update the table row
            var row = $('#row' + userId);
            row.find('td:eq(1)').text(formData.name);
            row.find('td:eq(2)').text(formData.email);
            
            // Update status badge
            var statusBadge = row.find('.status-badge-' + userId);
            statusBadge.removeClass('bg-success bg-warning bg-danger');
            if(formData.status === 'active') {
                statusBadge.addClass('bg-success');
            } else if(formData.status === 'suspended') {
                statusBadge.addClass('bg-danger');
            } else {
                statusBadge.addClass('bg-warning');
            }
            statusBadge.text(formData.status.charAt(0).toUpperCase() + formData.status.slice(1));
            
            // Update verified badge
            var verifiedCell = row.find('td:eq(5)');
            if(formData.verified == '1') {
                verifiedCell.find('.badge').removeClass('bg-danger').addClass('bg-success').text('✓');
            } else {
                verifiedCell.find('.badge').removeClass('bg-success').addClass('bg-danger').text('✗');
            }
            
            alert(response.success);
            location.reload(); // Reload to reflect all changes
        },
        error: function(xhr) {
            alert("Error: " + (xhr.responseJSON?.message || 'Failed to update user'));
        }
    });
});

$(document).on('click', '.delete-user', function() {
    var userId = $(this).data('id');
    
    if(confirm('Are you sure you want to delete this user?')) {
        $.ajax({
            url: "{{ route('admin.deleteuser') }}",
            type: 'POST',
            data: {
                _token: token,
                id: userId
            },
            success: function(response) {
                $('#row' + userId).remove();
                alert('User deleted successfully');
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.message);
            }
        });
    }
});

// Send verification email
$(document).on('click', '.send-verification', function() {
    var userId = $(this).data('id');
    var userEmail = $(this).data('email');
    var button = $(this);
    
    if(confirm('Send verification email to ' + userEmail + '?')) {
        button.prop('disabled', true);
        button.html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('admin.sendverification') }}",
            type: 'POST',
            data: {
                _token: token,
                id: userId
            },
            success: function(response) {
                button.prop('disabled', false);
                button.html('<i class="fa fa-envelope"></i>');
                alert(response.success || 'Verification email sent successfully!');
            },
            error: function(xhr) {
                button.prop('disabled', false);
                button.html('<i class="fa fa-envelope"></i>');
                alert("Error: " + (xhr.responseJSON?.message || 'Failed to send verification email'));
            }
        });
    }
});

// Impersonate user functionality
$(document).on('click', '.impersonate-user', function() {
    var userId = $(this).data('id');
    var userName = $(this).data('name');
    
    if(confirm('Are you sure you want to login as "' + userName + '"? This will open a new window where you will be logged in as this user.')) {
        // Create a form and submit it to open in new window
        var form = $('<form>', {
            'method': 'POST',
            'action': "{{ route('admin.impersonate') }}",
            'target': '_blank'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': token
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'user_id',
            'value': userId
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
    }
});

// Impersonate user via profile functionality
$(document).on('click', '.impersonate-profile', function() {
    var userId = $(this).data('user-id');
    var profileId = $(this).data('profile-id');
    var profileName = $(this).data('name');
    
    if(confirm('Are you sure you want to login as "' + profileName + '" profile? This will open a new window where you will be logged in as this user.')) {
        // Create a form and submit it to open in new window
        var form = $('<form>', {
            'method': 'POST',
            'action': "{{ route('admin.impersonate') }}",
            'target': '_blank'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': token
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'user_id',
            'value': userId
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'profile_id',
            'value': profileId
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
    }
});

// Add User Modal Trigger
$('#addUserBtn').click(function() {
    $('#addUserModal').modal('show');
});

// Toggle User Status (Activate/Deactivate)
$(document).on('click', '.toggle-status', function() {
    var userId = $(this).data('id');
    var currentStatus = $(this).data('status');
    var newStatus = currentStatus === 'active' ? 'pending' : 'active';
    var actionText = newStatus === 'active' ? 'activate' : 'deactivate';
    
    if(confirm('Are you sure you want to ' + actionText + ' this user?')) {
        $.ajax({
            url: "{{ route('admin.toggleuserstatus') }}",
            type: 'POST',
            data: {
                _token: token,
                id: userId,
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    var row = $('#row' + userId);
                    
                    // Update button
                    var btn = $('button.toggle-status[data-id="' + userId + '"]');
                    btn.data('status', newStatus);
                    
                    if(newStatus === 'active') {
                        btn.removeClass('btn-success').addClass('btn-secondary');
                        btn.attr('title', 'Deactivate');
                        btn.html('<i class="fa fa-ban"></i>');
                    } else {
                        btn.removeClass('btn-secondary').addClass('btn-success');
                        btn.attr('title', 'Activate');
                        btn.html('<i class="fa fa-check-circle"></i>');
                    }
                    
                    // Update status badge
                    var badge = $('.status-badge-' + userId);
                    badge.removeClass('bg-success bg-warning');
                    badge.addClass(newStatus === 'active' ? 'bg-success' : 'bg-warning');
                    badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    
                    // Update email verified badge
                    var verifiedCell = row.find('td:eq(5)'); // Verified Email column
                    if(newStatus === 'active' && response.verified == 1) {
                        verifiedCell.html('<span class="badge bg-success">✓</span>');
                    } else {
                        verifiedCell.html('<span class="badge bg-danger">✗</span>');
                    }
                    
                    alert(response.success);
                }
            },
            error: function(xhr) {
                alert("Error: " + (xhr.responseJSON?.message || 'Failed to update status'));
            }
        });
    }
});

// Server-side pagination is handled by Laravel; DataTables here is only
// used for click-to-sort on the current page. Paging/search/length UI is
// disabled to avoid two competing paginations.
$(document).ready(function() {
    $('#usersTable').DataTable({
        "paging": false,
        "info": false,
        "searching": false,
        "lengthChange": false,
        "order": [],
        "columnDefs": [
            { "orderable": false, "targets": [6, 7] }
        ],
        "responsive": true,
        "autoWidth": false
    });
    
    // Toggle password visibility for new password
    $('#toggleNewPassword').click(function() {
        var input = $('#editUserNewPassword');
        var icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Generate random password
    $('#generateRandomPassword').click(function() {
        var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        var password = '';
        for (var i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        $('#editUserNewPassword').val(password).attr('type', 'text');
        $('#editUserConfirmPassword').val(password);
        $('#toggleNewPassword i').removeClass('fa-eye').addClass('fa-eye-slash');
        
        // Copy to clipboard
        navigator.clipboard.writeText(password).then(function() {
            alert('Generated password: ' + password + '\n\nPassword has been copied to clipboard!');
        }).catch(function() {
            alert('Generated password: ' + password + '\n\nPlease copy this password manually.');
        });
    });
    
    // Clear password fields when modal is closed
    $('#editUserModal').on('hidden.bs.modal', function() {
        $('#editUserNewPassword').val('').attr('type', 'password');
        $('#editUserConfirmPassword').val('');
        $('#toggleNewPassword i').removeClass('fa-eye-slash').addClass('fa-eye');
    });
});

</script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
@endpush