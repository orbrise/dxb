@extends('admin.layout.master')

@push('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet" />
<style>
/* ===== Verifications page: modern redesign ===== */

:root {
    --v-primary: #6366f1;
    --v-primary-dark: #4f46e5;
    --v-success: #10b981;
    --v-danger: #ef4444;
    --v-warning: #f59e0b;
    --v-info: #06b6d4;
    --v-slate-50: #f8fafc;
    --v-slate-100: #f1f5f9;
    --v-slate-200: #e2e8f0;
    --v-slate-300: #cbd5e1;
    --v-slate-500: #64748b;
    --v-slate-600: #475569;
    --v-slate-700: #334155;
    --v-slate-800: #1e293b;
}

/* ---- Stats header strip ---- */
.v-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}
.v-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
}
.v-stat .v-stat-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .9;
    margin: 0 0 4px 0;
    font-weight: 600;
}
.v-stat .v-stat-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1.1;
    margin: 0;
}
.v-stat .v-stat-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 34px;
    opacity: .35;
}
.v-stat.pending  { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
.v-stat.today    { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.v-stat.week     { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }
.v-stat.filtered { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

/* ---- Main card ---- */
.v-card {
    background: #fff;
    border: 1px solid var(--v-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}
.v-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 18px 22px;
    border-bottom: 1px solid var(--v-slate-100);
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
}
.v-card-head .v-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    color: var(--v-slate-800);
    margin: 0;
}
.v-card-head .v-title .v-title-icon {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: rgba(99, 102, 241, .12);
    color: var(--v-primary-dark);
    font-size: 15px;
}
.v-card-head .v-title small {
    font-weight: 400;
    color: var(--v-slate-500);
    font-size: 12px;
    margin-left: 4px;
}
.v-perpage {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--v-slate-600);
}
.v-perpage select {
    border: 1px solid var(--v-slate-200);
    border-radius: 8px;
    padding: 5px 26px 5px 10px;
    font-size: 13px;
    background: #fff;
    color: var(--v-slate-700);
    cursor: pointer;
    height: auto;
}
.v-perpage select:focus {
    outline: none;
    border-color: var(--v-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .18);
}

/* ---- Filter panel ---- */
.v-filters {
    padding: 18px 22px 8px;
    background: var(--v-slate-50);
    border-bottom: 1px solid var(--v-slate-100);
}
.v-filters-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr)) auto;
    gap: 12px;
    align-items: end;
}
.v-field label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--v-slate-500);
    margin: 0 0 6px 2px;
}
.v-field label i {
    color: var(--v-primary);
    font-size: 11px;
}
.v-input {
    width: 100%;
    height: 40px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--v-slate-800);
    background: #fff;
    border: 1px solid var(--v-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.v-input::placeholder { color: var(--v-slate-300); }
.v-input:focus {
    outline: none;
    border-color: var(--v-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.v-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}
.v-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 40px;
    padding: 0 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    transition: transform .05s, box-shadow .15s, background .15s;
    white-space: nowrap;
}
.v-btn:active { transform: translateY(1px); }
.v-btn-primary {
    background: linear-gradient(135deg, var(--v-primary) 0%, var(--v-primary-dark) 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
}
.v-btn-primary:hover { color: #fff; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); }
.v-btn-ghost {
    background: #fff;
    color: var(--v-slate-600);
    border-color: var(--v-slate-200);
}
.v-btn-ghost:hover { background: var(--v-slate-50); color: var(--v-slate-800); }

/* ---- Table ---- */
.v-table-wrap {
    padding: 6px 0 0;
}
.table#verificationTable {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 13.5px;
}
.table#verificationTable thead th {
    background: var(--v-slate-50);
    color: var(--v-slate-500);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid var(--v-slate-200);
    white-space: nowrap;
    text-align: left;
}
.table#verificationTable tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    color: var(--v-slate-700);
    border: 0;
    border-bottom: 1px solid var(--v-slate-100);
    background: #fff;
}
.table#verificationTable tbody tr {
    transition: background .12s;
}
.table#verificationTable tbody tr:hover td {
    background: #fafbff;
}
.table#verificationTable tbody tr:last-child td {
    border-bottom: 0;
}

/* Profile cell */
.v-profile-cell {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 160px;
}
.v-profile-cell .v-avatar {
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
.v-profile-cell .v-profile-name {
    font-weight: 600;
    color: var(--v-slate-800);
    text-decoration: none;
    line-height: 1.2;
}
.v-profile-cell .v-profile-name:hover {
    color: var(--v-primary-dark);
    text-decoration: underline;
}

/* ID chips */
.v-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    color: var(--v-slate-700);
    background: var(--v-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.v-chip.user { background: #eef2ff; color: #4338ca; }

/* Email */
.v-email {
    color: var(--v-slate-600);
    font-size: 13px;
}

/* Photo previews */
.v-photo-thumb {
    width: 68px;
    height: 84px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--v-slate-200);
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 1px 3px rgba(15,23,42,.08);
    background: var(--v-slate-100);
}
.v-photo-thumb:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(15,23,42,.18);
}
.v-photo-empty {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 68px;
    height: 84px;
    border-radius: 8px;
    border: 1px dashed var(--v-slate-300);
    background: var(--v-slate-50);
    color: var(--v-slate-300);
    font-size: 11px;
    text-align: center;
    line-height: 1.1;
    gap: 4px;
}

/* Photo code */
.v-code-badge {
    display: inline-block;
    padding: 6px 12px;
    font-size: 16px;
    font-weight: 700;
    color: #78350f;
    background: linear-gradient(135deg, #fde68a 0%, #fbbf24 100%);
    border-radius: 8px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
    letter-spacing: .04em;
    box-shadow: inset 0 -2px 0 rgba(120, 53, 15, .1);
}

/* Dates */
.v-date {
    color: var(--v-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.v-date small {
    display: block;
    color: var(--v-slate-500);
    font-size: 11px;
}
.v-time-ago {
    display: inline-block;
    padding: 3px 8px;
    background: var(--v-slate-100);
    color: var(--v-slate-600);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

/* Actions */
.v-action-cell {
    display: flex;
    gap: 6px;
    align-items: center;
    flex-wrap: nowrap;
}
.v-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 12px;
    background: #eef2ff;
    color: var(--v-primary-dark);
    border: 1px solid transparent;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
    white-space: nowrap;
}
.v-view-btn:hover { background: #e0e7ff; }
.v-action-select {
    height: 34px;
    padding: 0 26px 0 10px;
    font-size: 12px;
    color: var(--v-slate-700);
    background: #fff;
    border: 1px solid var(--v-slate-200);
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
}
.v-action-select:focus {
    outline: none;
    border-color: var(--v-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}

/* Empty state */
.v-empty {
    text-align: center;
    padding: 60px 20px;
}
.v-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--v-slate-100);
    color: var(--v-slate-300);
    font-size: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}
.v-empty p {
    color: var(--v-slate-500);
    margin: 0;
    font-size: 15px;
}

/* Pagination footer */
.v-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 16px 22px;
    border-top: 1px solid var(--v-slate-100);
    background: var(--v-slate-50);
    font-size: 13px;
    color: var(--v-slate-600);
}
.v-pagination .pagination {
    margin: 0;
}
.v-pagination .pagination .page-link {
    color: var(--v-slate-600);
    border-color: var(--v-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.v-pagination .pagination .page-item.active .page-link {
    background: var(--v-primary);
    border-color: var(--v-primary);
    color: #fff;
}

/* ---- Preserve modal styling ---- */
.modal-xl { max-width: 1200px; }
.profile-photo-thumb {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.profile-photo-thumb:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
}
.img-preview {
    max-width: 200px;
    cursor: pointer;
}
.g-2 { --bs-gutter-x: 0.5rem; --bs-gutter-y: 0.5rem; }
.swal2-modal .btn { min-width: 5.33333em; font-size: 12px; }
h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 { margin: 0; line-height: 24px; }
.modal-body h1, .modal-body h2, .modal-body h3, .modal-body h4, .modal-body h5, .modal-body h6 { color: #474747; }

/* ===== Photo Verification Review — modern popup ===== */
.vr-modal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(15, 23, 42, .3);
    background: #fff;
}
.vr-modal .modal-header {
    padding: 18px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--v-slate-100);
    align-items: center;
}
.vr-modal .modal-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    font-weight: 700;
    color: var(--v-slate-800);
    margin: 0;
}
.vr-modal .modal-title .vr-title-icon {
    width: 34px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 9px;
    background: rgba(99, 102, 241, .12);
    color: var(--v-primary-dark);
    font-size: 14px;
}
.vr-modal .modal-header .close {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: var(--v-slate-100) !important;
    color: var(--v-slate-600) !important;
    opacity: 1;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 20px; line-height: 1;
    text-shadow: none;
    padding: 0;
    margin: 0;
    transition: background .15s, color .15s;
    border: 0;
}
.vr-modal .modal-header .close:hover {
    background: #fee2e2 !important;
    color: #991b1b !important;
}
.vr-modal .modal-body { padding: 22px; background: #f8fafc; }
.vr-modal .modal-footer {
    padding: 14px 22px;
    background: #fff;
    border-top: 1px solid var(--v-slate-100);
    gap: 10px;
}

/* Sub-cards inside modal */
.vr-modal .vr-panel {
    background: #fff;
    border: 1px solid var(--v-slate-200);
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
    overflow: hidden;
    margin-bottom: 16px;
}
.vr-modal .vr-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--v-slate-100);
}
.vr-modal .vr-panel-head.head-verify   { background: linear-gradient(135deg, #fde68a 0%, #fbbf24 100%); }
.vr-modal .vr-panel-head.head-profile  { background: linear-gradient(135deg, #a5b4fc 0%, #818cf8 100%); }
.vr-modal .vr-panel-head.head-info     { background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%); }

.vr-modal .vr-panel-head .vr-panel-title {
    display: flex; align-items: center; gap: 8px;
    margin: 0;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.vr-modal .vr-panel-head.head-verify .vr-panel-title { color: #78350f; }
.vr-modal .vr-panel-head .vr-panel-icon {
    width: 28px; height: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px;
    background: rgba(255, 255, 255, .35);
    color: inherit;
    font-size: 12px;
}
.vr-modal .vr-panel-head.head-verify .vr-panel-icon { background: rgba(120, 53, 15, .18); }

.vr-modal .vr-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 999px;
    background: rgba(255, 255, 255, .85);
    color: var(--v-slate-800);
    letter-spacing: .04em;
}
.vr-modal .vr-badge.vr-code {
    background: #1e293b;
    color: #fde68a;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
    letter-spacing: .1em;
    padding: 5px 11px;
    font-size: 12px;
}

.vr-modal .vr-panel-body { padding: 16px; }
.vr-modal .vr-panel-body.pad-tight { padding: 14px; }

.vr-modal .vr-verify-img {
    display: block;
    max-width: 100%;
    max-height: 380px;
    margin: 0 auto;
    border-radius: 10px;
    background: var(--v-slate-100);
    border: 1px solid var(--v-slate-200);
    object-fit: contain;
}
.vr-modal .vr-verify-meta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 10px;
    color: var(--v-slate-500);
    font-size: 12px;
}

.vr-modal .vr-photo-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    max-height: 470px;
    overflow-y: auto;
    padding-right: 4px;
}
.vr-modal .vr-photo-grid::-webkit-scrollbar { width: 6px; }
.vr-modal .vr-photo-grid::-webkit-scrollbar-thumb {
    background: var(--v-slate-300);
    border-radius: 3px;
}
.vr-modal .vr-photo-cell {
    position: relative;
    aspect-ratio: 1 / 1;
    background: var(--v-slate-100);
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--v-slate-200);
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
}
.vr-modal .vr-photo-cell:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(15,23,42,.15);
}
.vr-modal .vr-photo-cell img {
    width: 100%; height: 100%; object-fit: cover; display: block;
}
.vr-modal .vr-main-badge {
    position: absolute;
    top: 6px; left: 6px;
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 8px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    box-shadow: 0 3px 8px rgba(16, 185, 129, .4);
}

.vr-modal .vr-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--v-slate-400, #94a3b8);
}
.vr-modal .vr-empty i { font-size: 32px; opacity: .5; margin-bottom: 8px; }

.vr-modal .vr-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px 22px;
}
.vr-modal .vr-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.vr-modal .vr-info-item .vr-info-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--v-slate-500);
    display: flex; align-items: center; gap: 5px;
}
.vr-modal .vr-info-item .vr-info-label i { color: var(--v-primary); font-size: 11px; }
.vr-modal .vr-info-item .vr-info-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--v-slate-800);
}

.vr-modal .vr-profile-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    background: #eef2ff;
    color: var(--v-primary-dark) !important;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #c7d2fe;
    text-decoration: none !important;
    transition: background .15s;
}
.vr-modal .vr-profile-btn:hover { background: #e0e7ff; color: var(--v-primary-dark) !important; }

/* Footer buttons */
.vr-modal .vr-btn {
    display: inline-flex; align-items: center; gap: 6px;
    height: 40px;
    padding: 0 18px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    border: 0;
    cursor: pointer;
    transition: box-shadow .15s, background .15s;
}
.vr-modal .vr-btn-close {
    background: #fff;
    color: var(--v-slate-700);
    border: 1px solid var(--v-slate-200);
}
.vr-modal .vr-btn-close:hover { background: var(--v-slate-50); }
.vr-modal .vr-action-select {
    height: 40px;
    padding: 0 32px 0 14px;
    font-size: 13px;
    font-weight: 500;
    color: var(--v-slate-700);
    background: #fff;
    border: 1px solid var(--v-slate-200);
    border-radius: 9px;
    cursor: pointer;
    min-width: 180px;
}
.vr-modal .vr-action-select:focus {
    outline: none;
    border-color: var(--v-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .18);
}

@media (max-width: 767px) {
    .vr-modal .vr-photo-grid { grid-template-columns: repeat(2, 1fr); }
    .vr-modal .vr-info-grid { grid-template-columns: 1fr; }
    .vr-modal .modal-footer { flex-direction: column-reverse; align-items: stretch; }
    .vr-modal .vr-action-select, .vr-modal .vr-btn { width: 100%; }
}

/* ---- Responsive ---- */
@media (max-width: 1200px) {
    .v-filters-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .v-actions { grid-column: 1 / -1; justify-content: flex-end; }
}
@media (max-width: 768px) {
    .main-wrapper { padding-left: 0 !important; padding-right: 0 !important; }
    .container-fluid { padding-left: 8px !important; padding-right: 8px !important; }
    .v-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .v-card-head { padding: 14px 14px; flex-wrap: wrap; }
    .v-filters { padding: 14px; }
    .v-filters-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .v-actions { grid-column: 1 / -1; justify-content: flex-start; }
    .v-pagination { flex-direction: column; text-align: center; padding: 14px; }
    .table#verificationTable thead { display: none; }
    .table#verificationTable tbody td { padding: 10px 14px; }
}
</style>
@endpush

@section('content')
 <div class="row page-title clearfix" style="padding: 0 2.78571em !important;">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Pending Profiles</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage pending profiles effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex" >
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active"></li>
                    </ol>

                </div>
                <!-- /.page-title-right -->
        </div>
@php
    $hasAnyFilter = request()->hasAny(['search', 'profile_id', 'user_id', 'email', 'date_from', 'date_to']);
    $totalPending = $photos->total();
    $todayCount = $photos->getCollection()->filter(fn($p) => $p->created_at && $p->created_at->isToday())->count();
    $weekCount  = $photos->getCollection()->filter(fn($p) => $p->created_at && $p->created_at->gte(now()->subDays(7)))->count();
@endphp

<div class="container-fluid">

    {{-- ===== Stats strip ===== --}}
    <div class="v-stats-row">
        <div class="v-stat pending">
            <p class="v-stat-label">Total Pending</p>
            <p class="v-stat-value">{{ number_format($totalPending) }}</p>
            <i class="fas fa-hourglass-half v-stat-icon"></i>
        </div>
        <div class="v-stat today">
            <p class="v-stat-label">On This Page · Today</p>
            <p class="v-stat-value">{{ number_format($todayCount) }}</p>
            <i class="fas fa-calendar-day v-stat-icon"></i>
        </div>
        <div class="v-stat week">
            <p class="v-stat-label">On This Page · Last 7 Days</p>
            <p class="v-stat-value">{{ number_format($weekCount) }}</p>
            <i class="fas fa-calendar-week v-stat-icon"></i>
        </div>
        <div class="v-stat filtered">
            <p class="v-stat-label">{{ $hasAnyFilter ? 'Filtered Results' : 'Showing' }}</p>
            <p class="v-stat-value">{{ number_format($photos->count()) }}</p>
            <i class="fas fa-filter v-stat-icon"></i>
        </div>
    </div>

    {{-- ===== Main card ===== --}}
    <div class="v-card">

        {{-- Header --}}
        <div class="v-card-head">
            <h5 class="v-title">
                <span class="v-title-icon"><i class="fas fa-user-shield"></i></span>
                Pending Photo Verifications
                <small>Review, approve or reject profile photo submissions</small>
            </h5>
            <div class="v-perpage">
                <span>Show</span>
                <select id="perPageSelect">
                    <option value="10"  {{ request('per_page') == 10  ? 'selected' : '' }}>10</option>
                    <option value="25"  {{ request('per_page') == 25  ? 'selected' : '' }}>25</option>
                    <option value="50"  {{ request('per_page') == 50  ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>entries</span>
            </div>
        </div>

        {{-- Filters --}}
        <div class="v-filters">
            <form method="GET" action="{{ route('admin.verifications') }}" id="searchForm">
                <input type="hidden" name="per_page" id="searchPerPage" value="{{ request('per_page', 15) }}">
                <div class="v-filters-grid">
                    <div class="v-field">
                        <label><i class="fas fa-user"></i> Profile Name</label>
                        <input type="text" name="search" class="v-input" placeholder="Search name..." value="{{ request('search') }}">
                    </div>
                    <div class="v-field">
                        <label><i class="fas fa-id-badge"></i> Profile ID</label>
                        <input type="number" name="profile_id" class="v-input" placeholder="1234" value="{{ request('profile_id') }}">
                    </div>
                    <div class="v-field">
                        <label><i class="fas fa-hashtag"></i> User ID</label>
                        <input type="number" name="user_id" class="v-input" placeholder="987" value="{{ request('user_id') }}">
                    </div>
                    <div class="v-field">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="text" name="email" class="v-input" placeholder="user@example.com" value="{{ request('email') }}">
                    </div>
                    <div class="v-field">
                        <label><i class="fas fa-calendar-alt"></i> From</label>
                        <input type="date" name="date_from" class="v-input" value="{{ request('date_from') }}">
                    </div>
                    <div class="v-field">
                        <label><i class="fas fa-calendar-alt"></i> To</label>
                        <input type="date" name="date_to" class="v-input" value="{{ request('date_to') }}">
                    </div>
                    <div class="v-actions">
                        <button class="v-btn v-btn-primary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                        @if($hasAnyFilter)
                            <a href="{{ route('admin.verifications') }}" class="v-btn v-btn-ghost">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="v-table-wrap table-responsive">
            <table class="table" id="verificationTable">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Profile ID</th>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>Verification</th>
                        <th>Profile Photo</th>
                        <th>Code</th>
                        <th>Created</th>
                        <th>Submitted</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($photos as $photo)
                        @if($photo->profile && $photo->profile->ggender && $photo->profile->gcity)
                        <tr id="row-{{$photo->id}}">
                            <td>
                                <div class="v-profile-cell">
                                    <span class="v-avatar">{{ strtoupper(mb_substr($photo->profile->name, 0, 1)) }}</span>
                                    <a class="v-profile-name" target="_blank"
                                       href="/{{ strtolower($photo->profile->ggender->name) }}-escorts-in-{{ strtolower($photo->profile->gcity->name) }}/{{ $photo->profile->id }}/{{ $photo->profile->name }}">
                                        {{ $photo->profile->name }}
                                    </a>
                                </div>
                            </td>
                            <td><span class="v-chip">#{{ $photo->profile->id }}</span></td>
                            <td><span class="v-chip user">#{{ $photo->user_id }}</span></td>
                            <td><span class="v-email">{{ $photo->user->email ?? '-' }}</span></td>
                            <td>
                                <img src="{{ smart_asset('userimages/'.$photo->user_id.'/verification/'.$photo->photo) }}"
                                     class="v-photo-thumb"
                                     data-toggle="modal"
                                     data-target="#photoModal{{ $photo->id }}"
                                     alt="Verification photo">
                            </td>
                            <td>
                                @if($photo->profile && $photo->profile->singleimg && $photo->profile->singleimg->image)
                                    <img src="{{ smart_asset('userimages/'.$photo->profile->user_id.'/'.$photo->profile->id.'/'.$photo->profile->singleimg->image) }}"
                                         class="v-photo-thumb"
                                         alt="Profile photo">
                                @else
                                    <span class="v-photo-empty">
                                        <i class="fas fa-image"></i>
                                        No image
                                    </span>
                                @endif
                            </td>
                            <td><span class="v-code-badge">{{ $photo->profile?->photo_code }}</span></td>
                            <td>
                                <div class="v-date">
                                    {{ $photo->created_at->format('M d, Y') }}
                                    <small>{{ $photo->created_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td><span class="v-time-ago">{{ $photo->created_at->diffForHumans() }}</span></td>
                            <td>
                                <div class="v-action-cell" style="justify-content:flex-end;">
                                    <button class="v-view-btn view-btn"
                                            data-toggle="modal"
                                            data-target="#viewModal{{ $photo->id }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <select class="v-action-select action-dropdown"
                                            data-id="{{ $photo->id }}"
                                            data-profile-name="{{ $photo->profile->name }}"
                                            data-profile-id="{{ $photo->profile->id }}"
                                            data-profile-slug="{{ $photo->profile->slug }}">
                                        <option value="">-- Action --</option>
                                        <option value="approve">✓ Approve</option>
                                        <option value="reject">✗ Reject</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            @if($photos->isEmpty())
                <div class="v-empty">
                    <div class="v-empty-icon"><i class="fas fa-inbox"></i></div>
                    <p>
                        @if($hasAnyFilter)
                            No verifications found matching the selected filters
                        @else
                            No pending verifications at the moment
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        <div class="v-pagination">
            <div>
                Showing <strong>{{ $photos->firstItem() ?? 0 }}</strong> to
                <strong>{{ $photos->lastItem() ?? 0 }}</strong> of
                <strong>{{ $photos->total() }}</strong> entries
                @if($hasAnyFilter)
                    <span class="text-muted">(filtered)</span>
                @endif
            </div>
            <div>{{ $photos->links() }}</div>
        </div>

    </div>
</div>

@foreach($photos as $photo)
@if($photo->profile && $photo->profile->ggender && $photo->profile->gcity)
<div class="modal fade vr-modal" id="photoModal{{ $photo->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="vr-title-icon"><i class="fas fa-id-card"></i></span>
                    Verification Photo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center;">
                <img src="{{ smart_asset('userimages/'.$photo->user_id.'/verification/'.$photo->photo) }}"
                     class="img-fluid" style="border-radius:12px; max-height:70vh;">
            </div>
        </div>
    </div>
</div>

{{-- View Modal with All Photos --}}
<div class="modal fade vr-modal" id="viewModal{{ $photo->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="vr-title-icon"><i class="fas fa-images"></i></span>
                    {{ $photo->profile->name }} &mdash; Photo Verification Review
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Verification Photo --}}
                    <div class="col-md-6">
                        <div class="vr-panel">
                            <div class="vr-panel-head head-verify">
                                <h6 class="vr-panel-title">
                                    <span class="vr-panel-icon"><i class="fas fa-id-card"></i></span>
                                    Verification Photo
                                </h6>
                                <span class="vr-badge vr-code">Code: {{ $photo->profile->photo_code }}</span>
                            </div>
                            <div class="vr-panel-body">
                                <img src="{{ smart_asset('userimages/'.$photo->user_id.'/verification/'.$photo->photo) }}"
                                     class="vr-verify-img"
                                     alt="Verification photo">
                                <div class="vr-verify-meta">
                                    <i class="fas fa-clock"></i>
                                    <span>Submitted {{ $photo->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Profile Photos --}}
                    <div class="col-md-6">
                        <div class="vr-panel">
                            <div class="vr-panel-head head-profile">
                                <h6 class="vr-panel-title">
                                    <span class="vr-panel-icon"><i class="fas fa-images"></i></span>
                                    Profile Photos
                                </h6>
                                <span class="vr-badge">
                                    {{ $photo->profile->allimages ? $photo->profile->allimages->count() : 0 }} photos
                                </span>
                            </div>
                            <div class="vr-panel-body pad-tight">
                                @if($photo->profile->allimages && $photo->profile->allimages->count() > 0)
                                    <div class="vr-photo-grid">
                                        @foreach($photo->profile->allimages as $image)
                                            <div class="vr-photo-cell"
                                                 data-toggle="modal"
                                                 data-target="#profilePhotoModal{{ $photo->id }}_{{ $image->id }}">
                                                <img src="{{ smart_asset('userimages/'.$photo->profile->user_id.'/'.$photo->profile->id.'/'.$image->image) }}"
                                                     alt="Profile photo">
                                                @if($image->is_main)
                                                    <span class="vr-main-badge">
                                                        <i class="fas fa-star"></i> Main
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="vr-empty">
                                        <i class="fas fa-image d-block"></i>
                                        <p class="mb-0">No profile photos available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Profile Info --}}
                <div class="vr-panel" style="margin-bottom:0;">
                    <div class="vr-panel-head head-info">
                        <h6 class="vr-panel-title">
                            <span class="vr-panel-icon"><i class="fas fa-user"></i></span>
                            Profile Information
                        </h6>
                    </div>
                    <div class="vr-panel-body">
                        <div class="vr-info-grid">
                            <div class="vr-info-item">
                                <span class="vr-info-label"><i class="fas fa-signature"></i> Name</span>
                                <span class="vr-info-value">{{ $photo->profile->name }}</span>
                            </div>
                            <div class="vr-info-item">
                                <span class="vr-info-label"><i class="fas fa-birthday-cake"></i> Age</span>
                                <span class="vr-info-value">{{ $photo->profile->age ?? 'N/A' }}</span>
                            </div>
                            <div class="vr-info-item">
                                <span class="vr-info-label"><i class="fas fa-city"></i> City</span>
                                <span class="vr-info-value">{{ $photo->profile->getcity->name ?? 'N/A' }}</span>
                            </div>
                            <div class="vr-info-item">
                                <span class="vr-info-label"><i class="fas fa-venus-mars"></i> Gender</span>
                                <span class="vr-info-value">{{ $photo->profile->ggender->name ?? 'N/A' }}</span>
                            </div>
                            <div class="vr-info-item" style="grid-column: 1 / -1;">
                                <span class="vr-info-label"><i class="fas fa-link"></i> Profile Link</span>
                                <div>
                                    <a href="/{{ strtolower($photo->profile->ggender->name) }}-escorts-in-{{ strtolower($photo->profile->getcity->name) }}/{{ $photo->profile->id }}/{{ $photo->profile->name }}"
                                       target="_blank" class="vr-profile-btn">
                                        <i class="fas fa-external-link-alt"></i> View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="vr-btn vr-btn-close" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <select class="vr-action-select action-dropdown"
                        data-id="{{ $photo->id }}"
                        data-profile-name="{{ $photo->profile->name }}"
                        data-profile-id="{{ $photo->profile->id }}"
                        data-profile-slug="{{ $photo->profile->slug }}">
                    <option value="">-- Select Action --</option>
                    <option value="approve">✓ Approve</option>
                    <option value="reject">✗ Reject</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Individual Profile Photo Modals --}}
@if($photo->profile && $photo->profile->allimages)
@foreach($photo->profile->allimages as $image)
<div class="modal fade vr-modal" id="profilePhotoModal{{ $photo->id }}_{{ $image->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="vr-title-icon"><i class="fas fa-image"></i></span>
                    Photo Preview
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center;">
                <img src="{{ smart_asset('userimages/'.$photo->profile->user_id.'/'.$photo->profile->id.'/'.$image->image) }}"
                     class="img-fluid" style="border-radius:12px; max-height:70vh;">
            </div>
        </div>
    </div>
</div>
@endforeach
@endif
@endif

@endforeach

@endsection

@push('js')

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });

    // Handle per page change - preserve all current filter params
    $('#perPageSelect').on('change', function() {
        const perPage = $(this).val();
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });

    // Update hidden per_page input when searching
    $('#searchForm').on('submit', function() {
        $('#searchPerPage').val($('#perPageSelect').val());
    });

    // Handle action dropdown changes
    $(document).on('change', '.action-dropdown', function() {
        const action = $(this).val();
        const id = $(this).data('id');
        const profileName = $(this).data('profile-name');
        const profileId = $(this).data('profile-id');
        const profileSlug = $(this).data('profile-slug');
        const $dropdown = $(this);
        
        if (!action) return;
        
        // Reset dropdown
        $dropdown.val('');
        
        if (action === 'view') {
            const target = $dropdown.find('option:selected').data('target');
            if (target) {
                $(target).modal('show');
            }
        } else if (action === 'approve') {
            handleApprove(id);
        } else if (action === 'reject') {
            handleReject(id, profileName, profileId, profileSlug);
        }
    });

    function handleApprove(id) {
        const row = $(`#row-${id}`);
        
        // Close any open Bootstrap 4 modals first
        $('.modal').modal('hide');
        
        // Wait for modal to close, then show confirmation
        setTimeout(() => {
            swal({
                title: 'Approve Verification?',
                text: 'This will mark the photo as verified.',
                type: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result) {
                    $.ajax({
                        url: `/admin/verifications/${id}/approve`,
                        type: 'POST',
                        success: function() {
                            row.fadeOut();
                            swal('Approved!', 'Photo verified successfully', 'success');
                        },
                        error: function() {
                            swal('Error!', 'Failed to approve photo', 'error');
                        }
                    });
                }
            });
        }, 300);
    }

    function handleReject(id, profileName, profileId, profileSlug) {
        const row = $(`#row-${id}`);
        
        // Close any open Bootstrap 4 modals first
        $('.modal').modal('hide');
        
        // Wait for modal to close, then show rejection form
        setTimeout(() => {
            // Generate URLs
            const baseUrl = window.location.origin;
            const verifyUrl = `${baseUrl}/my-profile/${profileSlug}/${profileId}/verify-photo`;
            const editUrl = `${baseUrl}/edit-profile/${profileSlug}/${profileId}`;
            const upgradeUrl = `${baseUrl}/my-profile/${profileSlug}/${profileId}/upgrade`;
            
            swal({
                title: 'Rejection Details',
                html: `
                    <div class="form-group text-left mb-3">
                        <label for="rejection-reason" class="font-weight-bold">Rejection Reason *</label>
                        <input id="rejection-reason" class="swal2-input" placeholder="Enter reason for rejection" style="width: 100%; margin: 0;">
                    </div>
                    <div class="form-group text-left mb-3">
                        <label for="rejection-link" class="font-weight-bold">Action Link (Optional)</label>
                        <div class="input-group" style="margin-bottom: 10px;">
                            <input id="rejection-link" class="swal2-input" placeholder="e.g., https://example.com/help" style="width: 100%; margin: 0;">
                        </div>
                        <div class="btn-group btn-group-sm mt-2" role="group" style="display: flex; gap: 5px;">
                            <button type="button" class="btn btn-outline-primary quick-link-btn" data-url="${verifyUrl}" style="flex: 1;">
                                <i class="fas fa-check-circle"></i> Verify Photo
                            </button>
                            <button type="button" class="btn btn-outline-info quick-link-btn" data-url="${editUrl}" style="flex: 1;">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                            <button type="button" class="btn btn-outline-success quick-link-btn" data-url="${upgradeUrl}" style="flex: 1;">
                                <i class="fas fa-arrow-up"></i> Upgrade
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Click a button above to auto-fill the link, or enter a custom URL</small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                customClass: 'swal-wide'
            }).then((result) => {
                if (result) {
                    const reason = document.getElementById('rejection-reason').value;
                    const link = document.getElementById('rejection-link').value;
                    
                    if (!reason) {
                        swal('Error!', 'Please enter a rejection reason', 'error');
                        return false;
                    }
                    
                    $.ajax({
                        url: `/admin/verifications/${id}/reject`,
                        type: 'POST',
                        data: {
                            reason: reason,
                            link: link
                        },
                        success: function() {
                            row.fadeOut();
                            swal('Rejected!', 'Photo has been rejected', 'success');
                        },
                        error: function() {
                            swal('Error!', 'Failed to reject photo', 'error');
                        }
                    });
                }
            });
            
            // Add click handlers for quick link buttons after SweetAlert opens
            setTimeout(() => {
                document.querySelectorAll('.quick-link-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.getElementById('rejection-link').value = this.getAttribute('data-url');
                        
                        // Remove active class from all buttons
                        document.querySelectorAll('.quick-link-btn').forEach(b => {
                            b.classList.remove('active');
                            b.style.backgroundColor = '';
                            b.style.borderColor = '';
                            b.style.color = '';
                        });
                        
                        // Add active class to clicked button
                        this.classList.add('active');
                        const color = this.classList.contains('btn-outline-primary') ? '#007bff' :
                                    this.classList.contains('btn-outline-info') ? '#17a2b8' : '#28a745';
                        this.style.backgroundColor = color;
                        this.style.borderColor = color;
                        this.style.color = '#fff';
                    });
                });
            }, 100);
        }, 300);
    }
});
</script>
@endpush