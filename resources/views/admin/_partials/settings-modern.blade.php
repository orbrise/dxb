{{-- Shared modern styling for admin Settings pages.
     Scoped under .s-modern so it doesn't affect other admin views. --}}
<style>
:root {
    --sm-primary: #6366f1;
    --sm-primary-dark: #4f46e5;
    --sm-success: #10b981;
    --sm-danger: #ef4444;
    --sm-warning: #f59e0b;
    --sm-info: #06b6d4;
    --sm-slate-50: #f8fafc;
    --sm-slate-100: #f1f5f9;
    --sm-slate-200: #e2e8f0;
    --sm-slate-300: #cbd5e1;
    --sm-slate-500: #64748b;
    --sm-slate-600: #475569;
    --sm-slate-700: #334155;
    --sm-slate-800: #1e293b;
}

/* ---------- Cards ---------- */
.s-modern .card {
    background: #fff;
    border: 1px solid var(--sm-slate-200) !important;
    border-radius: 14px !important;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.s-modern .card > .card-header,
.s-modern .card-header {
    display: flex !important;
    align-items: center;
    gap: 10px;
    padding: 16px 22px !important;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%) !important;
    border-bottom: 1px solid var(--sm-slate-100) !important;
    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
}
.s-modern .card-header .card-title,
.s-modern .card-header h5,
.s-modern .card-header h6 {
    margin: 0;
    font-size: 15px !important;
    font-weight: 700 !important;
    color: var(--sm-slate-800) !important;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.s-modern .card-header .card-title::before,
.s-modern .card-header > h5::before,
.s-modern .card-header > h6::before {
    content: "";
    display: inline-block;
    width: 4px; height: 20px;
    background: linear-gradient(180deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%);
    border-radius: 3px;
    margin-right: 4px;
}
.s-modern .card > .card-body,
.s-modern .card-body {
    padding: 22px !important;
}

/* ---------- Alerts ---------- */
.s-modern .alert {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    border: 0 !important;
    margin-bottom: 14px;
}
.s-modern .alert-success { background: #d1fae5 !important; color: #065f46 !important; }
.s-modern .alert-danger  { background: #fee2e2 !important; color: #991b1b !important; }
.s-modern .alert-warning { background: #fef3c7 !important; color: #92400e !important; }
.s-modern .alert-info    { background: #dbeafe !important; color: #1e40af !important; }
.s-modern .alert .close, .s-modern .alert .btn-close {
    margin-left: auto;
    color: currentColor;
    opacity: .5;
    text-shadow: none;
}

/* ---------- Form controls ---------- */
.s-modern .form-label,
.s-modern .form-group > label,
.s-modern label.form-label {
    display: flex !important;
    align-items: center;
    gap: 5px;
    font-size: 12px !important;
    font-weight: 600 !important;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--sm-slate-500) !important;
    margin-bottom: 6px !important;
}
.s-modern .form-label i,
.s-modern .form-group > label i {
    color: var(--sm-primary);
    font-size: 11px;
}
.s-modern .form-control,
.s-modern .form-select {
    min-height: 42px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    color: var(--sm-slate-800) !important;
    background: #fff !important;
    border: 1px solid var(--sm-slate-200) !important;
    border-radius: 9px !important;
    transition: border-color .15s, box-shadow .15s;
    box-shadow: none !important;
}
.s-modern textarea.form-control { min-height: 100px !important; line-height: 1.55; }
.s-modern .form-control:focus,
.s-modern .form-select:focus {
    border-color: var(--sm-primary) !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important;
}
.s-modern .form-control-sm { min-height: 34px !important; font-size: 13px !important; padding: 5px 10px !important; }
.s-modern .form-check-input {
    width: 16px; height: 16px;
    cursor: pointer;
    border-color: var(--sm-slate-300);
}
.s-modern .form-check-input:checked {
    background-color: var(--sm-primary);
    border-color: var(--sm-primary);
}
.s-modern .form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
    border-color: var(--sm-primary);
}
.s-modern .form-check-label {
    font-size: 14px;
    color: var(--sm-slate-700);
    padding-left: 4px;
    cursor: pointer;
    font-weight: 500;
    text-transform: none;
    letter-spacing: 0;
}
.s-modern .form-switch .form-check-input {
    width: 40px; height: 22px;
    background-color: var(--sm-slate-300);
    border: 0;
}
.s-modern .form-switch .form-check-input:checked {
    background-color: var(--sm-primary);
}
.s-modern .text-danger { color: #b91c1c !important; font-size: 12px; }
.s-modern .text-muted { color: var(--sm-slate-500) !important; }
.s-modern small, .s-modern .small { font-size: 12px; color: var(--sm-slate-500); }

/* Uploaded image previews */
.s-modern input[type=file] + img,
.s-modern .form-group img {
    border-radius: 8px;
    border: 1px solid var(--sm-slate-200);
    padding: 4px;
    background: #fff;
}

/* ---------- Buttons ---------- */
.s-modern .btn {
    display: inline-flex !important;
    align-items: center; justify-content: center;
    gap: 6px;
    height: 42px;
    padding: 0 20px !important;
    border-radius: 9px !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
    border: 1px solid transparent !important;
    box-shadow: none;
    transition: box-shadow .15s;
    line-height: 1 !important;
    text-decoration: none;
}
.s-modern .btn-sm,
.s-modern .btn-group-sm > .btn,
.s-modern .btn-group-sm > form > .btn,
.s-modern .btn-group-sm form .btn {
    height: 34px !important;
    padding: 0 14px !important;
    font-size: 12.5px !important;
}
/* Buttons inside forms nested in a .btn-group behave like siblings —
   Bootstrap normally collapses their border-radius; we round every
   button in the group so icon-only actions look consistent. */
.s-modern .btn-group form { display: inline-flex; margin: 0; }
.s-modern .btn-group > .btn,
.s-modern .btn-group > form > .btn {
    border-radius: 9px !important;
    margin-left: 4px;
}
.s-modern .btn-group > .btn:first-child,
.s-modern .btn-group > form:first-child > .btn { margin-left: 0; }
.s-modern .btn-lg {
    height: 48px !important;
    padding: 0 24px !important;
    font-size: 14.5px !important;
}
.s-modern .btn-primary {
    background: linear-gradient(135deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
}
.s-modern .btn-primary:hover, .s-modern .btn-primary:focus {
    box-shadow: 0 6px 16px rgba(99, 102, 241, .45);
    color: #fff !important;
}
.s-modern .btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, .35);
}
.s-modern .btn-success:hover { box-shadow: 0 6px 16px rgba(16, 185, 129, .45); color: #fff !important; }
.s-modern .btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, .3);
}
.s-modern .btn-danger:hover { box-shadow: 0 6px 16px rgba(239, 68, 68, .4); color: #fff !important; }
.s-modern .btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(245, 158, 11, .3);
}
.s-modern .btn-warning:hover { box-shadow: 0 6px 16px rgba(245, 158, 11, .4); color: #fff !important; }
.s-modern .btn-info {
    background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(6, 182, 212, .3);
}
.s-modern .btn-info:hover { box-shadow: 0 6px 16px rgba(6, 182, 212, .4); color: #fff !important; }
.s-modern .btn-secondary {
    background: #fff !important;
    color: var(--sm-slate-700) !important;
    border-color: var(--sm-slate-200) !important;
}
.s-modern .btn-secondary:hover { background: var(--sm-slate-50) !important; color: var(--sm-slate-800) !important; }

.s-modern .btn-outline-primary {
    background: #fff !important;
    color: var(--sm-primary-dark) !important;
    border-color: var(--sm-primary) !important;
}
.s-modern .btn-outline-primary:hover {
    background: #eef2ff !important;
    color: var(--sm-primary-dark) !important;
}
.s-modern .btn-outline-secondary {
    background: #fff !important;
    color: var(--sm-slate-700) !important;
    border-color: var(--sm-slate-200) !important;
}
.s-modern .btn-outline-secondary:hover {
    background: var(--sm-slate-50) !important;
    color: var(--sm-slate-800) !important;
    border-color: var(--sm-slate-300) !important;
}
.s-modern .btn-outline-danger {
    background: #fff !important;
    color: #b91c1c !important;
    border-color: #fecaca !important;
}
.s-modern .btn-outline-danger:hover {
    background: #fef2f2 !important;
    color: #b91c1c !important;
    border-color: #ef4444 !important;
}
.s-modern .btn-outline-success {
    background: #fff !important;
    color: #065f46 !important;
    border-color: #a7f3d0 !important;
}
.s-modern .btn-outline-success:hover {
    background: #d1fae5 !important;
    color: #065f46 !important;
    border-color: #10b981 !important;
}
.s-modern .btn-outline-warning {
    background: #fff !important;
    color: #92400e !important;
    border-color: #fde68a !important;
}
.s-modern .btn-outline-warning:hover {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-color: #f59e0b !important;
}
.s-modern .btn-outline-info {
    background: #fff !important;
    color: #155e75 !important;
    border-color: #a5f3fc !important;
}
.s-modern .btn-outline-info:hover {
    background: #cffafe !important;
    color: #155e75 !important;
    border-color: #06b6d4 !important;
}

/* ---------- Tables ---------- */
.s-modern .table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
.s-modern .card-body > .table,
.s-modern .card-body > .table-responsive > .table {
    margin: -12px 0 !important;
}
.s-modern .table thead th {
    background: var(--sm-slate-50) !important;
    color: var(--sm-slate-500) !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: 14px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--sm-slate-200) !important;
    white-space: nowrap;
}
.s-modern .table tbody td {
    padding: 14px 16px !important;
    vertical-align: middle !important;
    border: 0 !important;
    border-bottom: 1px solid var(--sm-slate-100) !important;
    color: var(--sm-slate-700) !important;
    background: #fff !important;
}
.s-modern .table.table-striped tbody tr:nth-of-type(odd) td { background: #fbfbff !important; }
.s-modern .table tbody tr:hover td { background: #f5f7ff !important; }
.s-modern .table tbody tr:last-child td { border-bottom: 0 !important; }
.s-modern .table-bordered { border: 0 !important; }
.s-modern .table-bordered td, .s-modern .table-bordered th { border: 0 !important; }

/* ---------- Badges ---------- */
.s-modern .badge {
    display: inline-flex !important;
    align-items: center;
    gap: 4px;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    letter-spacing: .02em;
}
.s-modern .badge.bg-success, .s-modern .badge.badge-success { background: #d1fae5 !important; color: #065f46 !important; }
.s-modern .badge.bg-danger,  .s-modern .badge.badge-danger  { background: #fee2e2 !important; color: #991b1b !important; }
.s-modern .badge.bg-warning, .s-modern .badge.badge-warning { background: #fef3c7 !important; color: #92400e !important; }
.s-modern .badge.bg-info,    .s-modern .badge.badge-info    { background: #cffafe !important; color: #155e75 !important; }
.s-modern .badge.bg-primary, .s-modern .badge.badge-primary { background: #eef2ff !important; color: #4338ca !important; }
.s-modern .badge.bg-secondary,.s-modern .badge.badge-secondary { background: var(--sm-slate-100) !important; color: var(--sm-slate-600) !important; }
.s-modern .badge.bg-light, .s-modern .badge.badge-light { background: var(--sm-slate-100) !important; color: var(--sm-slate-700) !important; }
.s-modern .badge.bg-dark, .s-modern .badge.badge-dark { background: var(--sm-slate-800) !important; color: #fff !important; }

/* ---------- Tabs / Nav pills ---------- */
.s-modern .nav-tabs {
    border-bottom: 1px solid var(--sm-slate-200) !important;
    padding: 0 4px;
}
.s-modern .nav-tabs .nav-link {
    color: var(--sm-slate-600) !important;
    border: 0 !important;
    padding: 12px 18px !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
    margin-bottom: -1px;
    border-bottom: 2px solid transparent !important;
    background: transparent !important;
}
.s-modern .nav-tabs .nav-link:hover {
    color: var(--sm-slate-800) !important;
    background: var(--sm-slate-50) !important;
    border-radius: 8px 8px 0 0;
}
.s-modern .nav-tabs .nav-link.active {
    color: var(--sm-primary-dark) !important;
    background: transparent !important;
    border-bottom: 2px solid var(--sm-primary) !important;
    font-weight: 700 !important;
}

.s-modern .nav-pills {
    display: inline-flex;
    background: #fff;
    border: 1px solid var(--sm-slate-200);
    border-radius: 12px;
    padding: 5px;
    gap: 3px;
}
.s-modern .nav-pills .nav-link {
    color: var(--sm-slate-600) !important;
    padding: 8px 16px !important;
    border-radius: 8px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    background: transparent !important;
}
.s-modern .nav-pills .nav-link:hover { color: var(--sm-slate-800) !important; background: var(--sm-slate-50) !important; }
.s-modern .nav-pills .nav-link.active {
    color: #fff !important;
    background: linear-gradient(135deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%) !important;
    box-shadow: 0 3px 10px rgba(99, 102, 241, .35);
}

/* ---------- Pagination ---------- */
.s-modern .pagination { margin: 0 !important; }
.s-modern .pagination .page-link {
    color: var(--sm-slate-600) !important;
    border-color: var(--sm-slate-200) !important;
    padding: 6px 12px !important;
    font-size: 13px !important;
    margin: 0 2px;
    border-radius: 7px !important;
}
.s-modern .pagination .page-item.active .page-link {
    background: var(--sm-primary) !important;
    border-color: var(--sm-primary) !important;
    color: #fff !important;
}

/* ---------- Modals ---------- */
.s-modern .modal-content,
body .modal-content {
    /* also apply to modals attached to body */
}
.s-modern .modal-content {
    border: 0 !important;
    border-radius: 16px !important;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(15,23,42,.3) !important;
}
.s-modern .modal-header {
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%) !important;
    border-bottom: 1px solid var(--sm-slate-100) !important;
    padding: 18px 22px !important;
}
.s-modern .modal-title {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: var(--sm-slate-800) !important;
}
.s-modern .modal-body { padding: 22px !important; }
.s-modern .modal-footer {
    padding: 14px 22px !important;
    background: var(--sm-slate-50) !important;
    border-top: 1px solid var(--sm-slate-100) !important;
    gap: 8px !important;
}

/* ---------- Chips / inline code ---------- */
.s-modern code {
    background: var(--sm-slate-100);
    color: var(--sm-slate-700);
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

/* ---------- Utilities ---------- */
.s-modern hr {
    border: 0;
    border-top: 1px solid var(--sm-slate-100);
    margin: 20px 0;
}

/* ---------- InputGroup ---------- */
.s-modern .input-group > .form-control,
.s-modern .input-group > .form-select {
    border-radius: 9px 0 0 9px !important;
}
.s-modern .input-group > .input-group-text,
.s-modern .input-group > .btn:last-child {
    border-radius: 0 9px 9px 0 !important;
}
.s-modern .input-group-text {
    background: var(--sm-slate-50);
    border: 1px solid var(--sm-slate-200);
    color: var(--sm-slate-700);
    font-size: 13px;
}

/* Fix Bootstrap 4 & 5 close button rendering */
.s-modern .btn-close {
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23334155'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
    opacity: .6;
    padding: 0;
    width: 20px; height: 20px;
    border: 0;
}
.s-modern .btn-close:hover { opacity: 1; }
</style>
