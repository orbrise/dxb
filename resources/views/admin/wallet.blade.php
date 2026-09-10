@extends('admin.layout.master')

@push('css')
@include('admin._partials.settings-modern')
<style>
/* ===== Wallet Management — modern redesign ===== */
:root {
    --wm-primary: #6366f1;
    --wm-primary-dark: #4f46e5;
    --wm-success: #10b981;
    --wm-danger: #ef4444;
    --wm-warning: #f59e0b;
    --wm-info: #06b6d4;
    --wm-slate-50: #f8fafc;
    --wm-slate-100: #f1f5f9;
    --wm-slate-200: #e2e8f0;
    --wm-slate-300: #cbd5e1;
    --wm-slate-500: #64748b;
    --wm-slate-600: #475569;
    --wm-slate-700: #334155;
    --wm-slate-800: #1e293b;
}

/* Stats strip */
.s-modern .wm-stats-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.s-modern .wm-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.s-modern .wm-stat .wm-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.s-modern .wm-stat .wm-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.s-modern .wm-stat .wm-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.s-modern .wm-stat.total    { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.s-modern .wm-stat.credits  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.s-modern .wm-stat.transfer { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }

/* Cards */
.s-modern .wm-card {
    background: #fff;
    border: 1px solid var(--wm-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.s-modern .wm-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 20px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--wm-slate-100);
}
.s-modern .wm-card-head .wm-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 15px; font-weight: 700; color: var(--wm-slate-800);
}
.s-modern .wm-card-head .wm-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--wm-primary-dark); font-size: 14px;
}
.s-modern .wm-card-body { padding: 22px; }

/* Tab pills */
.s-modern .wm-tabs-wrap {
    display: flex; justify-content: center; margin-bottom: 22px;
}
.s-modern .wm-tabs {
    display: inline-flex;
    padding: 4px;
    background: var(--wm-slate-100);
    border-radius: 12px;
    gap: 4px;
}
.s-modern .wm-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 42px;
    padding: 0 22px;
    background: transparent;
    border: 0;
    border-radius: 9px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--wm-slate-600);
    cursor: pointer;
    transition: background .15s, color .15s, box-shadow .15s;
    white-space: nowrap;
}
.s-modern .wm-tab-btn:hover { color: var(--wm-slate-800); }
.s-modern .wm-tab-btn.active {
    background: #fff;
    color: var(--wm-primary-dark);
    box-shadow: 0 3px 10px rgba(15,23,42,.08);
}
.s-modern .wm-pane { display: none; }
.s-modern .wm-pane.active { display: block; }

/* Form */
.s-modern .wm-form { max-width: 560px; margin: 0 auto; }
.s-modern .wm-form.wm-form-wide { max-width: 880px; }
.s-modern .wm-form .wm-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--wm-slate-500);
    margin-bottom: 6px;
}
.s-modern .wm-form .wm-label i { color: var(--wm-primary); font-size: 11px; }
.s-modern .wm-form .wm-input {
    width: 100%;
    height: 46px;
    padding: 8px 14px;
    font-size: 14px;
    color: var(--wm-slate-800);
    background: #fff;
    border: 1px solid var(--wm-slate-200);
    border-radius: 10px;
    transition: border-color .15s, box-shadow .15s;
}
.s-modern .wm-form .wm-input::placeholder { color: var(--wm-slate-300); }
.s-modern .wm-form .wm-input:focus {
    outline: none;
    border-color: var(--wm-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.s-modern .wm-form .wm-fg { margin-bottom: 16px; }

/* Buttons */
.s-modern .wm-btn {
    display: inline-flex;
    align-items: center; justify-content: center; gap: 8px;
    width: 100%;
    height: 48px;
    padding: 0 24px;
    background: linear-gradient(135deg, var(--wm-primary) 0%, var(--wm-primary-dark) 100%);
    color: #fff;
    border: 0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
    transition: box-shadow .15s, transform .05s;
    cursor: pointer;
}
.s-modern .wm-btn:hover:not(:disabled) {
    color: #fff;
    box-shadow: 0 6px 18px rgba(99, 102, 241, .5);
}
.s-modern .wm-btn:active { transform: translateY(1px); }
.s-modern .wm-btn:disabled {
    background: var(--wm-slate-300);
    box-shadow: none;
    cursor: not-allowed;
}
.s-modern .wm-btn.wm-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 14px rgba(16, 185, 129, .35);
}
.s-modern .wm-btn.wm-btn-success:hover:not(:disabled) { box-shadow: 0 6px 18px rgba(16, 185, 129, .5); }

/* Transfer layout */
.s-modern .wm-transfer-row {
    display: flex;
    align-items: end;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: nowrap;
}
.s-modern .wm-transfer-field { flex: 1 1 auto; min-width: 0; }
.s-modern .wm-transfer-arrow {
    flex-shrink: 0;
    align-self: end;
    padding-bottom: 6px;
}
.s-modern .wm-transfer-arrow i {
    color: var(--wm-success);
    font-size: 14px;
    background: #d1fae5;
    border-radius: 50%;
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
}
.s-modern .wm-amount-note-row {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 12px;
    margin-bottom: 18px;
}

/* Spinner + alerts */
.s-modern .wm-spinner-wrap { display: none; text-align: center; margin: 15px 0; }
.s-modern .wm-spinner {
    width: 30px; height: 30px;
    border: 3px solid var(--wm-slate-100);
    border-top: 3px solid var(--wm-primary);
    border-radius: 50%;
    animation: wm-spin 1s linear infinite;
    margin: 0 auto;
}
@keyframes wm-spin { to { transform: rotate(360deg); } }

.s-modern .wm-alert {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 14px;
    border-radius: 10px;
    margin-top: 14px;
    font-size: 13px;
    font-weight: 500;
}
.s-modern .wm-alert.wm-alert-err { background: #fee2e2; color: #991b1b; }
.s-modern .wm-alert.wm-alert-ok  { background: #d1fae5; color: #065f46; }

/* Modal */
.wm-modal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(15,23,42,.3);
}
.wm-modal .modal-header {
    padding: 18px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--wm-slate-100);
    align-items: center;
}
.wm-modal .modal-title {
    display: flex; align-items: center; gap: 10px;
    font-size: 15px; font-weight: 700; color: var(--wm-slate-800);
    margin: 0;
}
.wm-modal .modal-title .wm-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(16, 185, 129, .12); color: #059669; font-size: 14px;
}
.wm-modal .modal-header .close {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: var(--wm-slate-100) !important;
    color: var(--wm-slate-600) !important;
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
.wm-modal .modal-header .close:hover {
    background: #fee2e2 !important;
    color: #991b1b !important;
}
.wm-modal .modal-body { padding: 22px; background: #f8fafc; }

.wm-modal .wm-user-card {
    background: #fff;
    border: 1px solid var(--wm-slate-200);
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 16px;
    text-align: center;
    box-shadow: 0 3px 10px rgba(15,23,42,.04);
}
.wm-modal .wm-user-avatar {
    width: 66px; height: 66px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--wm-primary) 0%, var(--wm-primary-dark) 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
    box-shadow: 0 6px 18px rgba(99,102,241,.35);
    overflow: hidden;
}
.wm-modal .wm-user-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--wm-slate-800);
    margin-bottom: 3px;
}
.wm-modal .wm-user-email {
    color: var(--wm-slate-500);
    font-size: 13px;
    margin-bottom: 14px;
}
.wm-modal .wm-balance-box {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}
.wm-modal .wm-balance-label {
    font-size: 11px;
    color: #065f46;
    text-transform: uppercase;
    letter-spacing: .05em;
    font-weight: 700;
}
.wm-modal .wm-balance-amount {
    font-size: 22px;
    font-weight: 700;
    color: #065f46;
}
.wm-modal .wm-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--wm-slate-500);
    margin-bottom: 6px;
}
.wm-modal .wm-label i { color: var(--wm-primary); font-size: 11px; }
.wm-modal .wm-input {
    width: 100%;
    height: 46px;
    padding: 8px 14px;
    font-size: 14px;
    color: var(--wm-slate-800);
    background: #fff;
    border: 1px solid var(--wm-slate-200);
    border-radius: 10px;
    transition: border-color .15s, box-shadow .15s;
}
.wm-modal .wm-input:focus {
    outline: none;
    border-color: var(--wm-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.wm-modal .wm-fg { margin-bottom: 14px; }
.wm-modal .wm-btn {
    display: inline-flex;
    align-items: center; justify-content: center; gap: 8px;
    width: 100%;
    height: 48px;
    padding: 0 24px;
    background: linear-gradient(135deg, var(--wm-primary) 0%, var(--wm-primary-dark) 100%);
    color: #fff;
    border: 0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
    cursor: pointer;
}
.wm-modal .wm-alert {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 14px;
    border-radius: 10px;
    margin-top: 14px;
    font-size: 13px;
    font-weight: 500;
}
.wm-modal .wm-alert.wm-alert-err { background: #fee2e2; color: #991b1b; }
.wm-modal .wm-alert.wm-alert-ok  { background: #d1fae5; color: #065f46; }

/* Transactions table */
.s-modern .wm-table {
    width: 100% !important;
    margin: 0;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
    border: 0 !important;
}
.s-modern .wm-table thead th {
    background: var(--wm-slate-50) !important;
    color: var(--wm-slate-500) !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: 12px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--wm-slate-200) !important;
    text-align: left;
    white-space: nowrap;
}
.s-modern .wm-table tbody td {
    padding: 14px 16px !important;
    vertical-align: middle !important;
    border: 0 !important;
    border-bottom: 1px solid var(--wm-slate-100) !important;
    color: var(--wm-slate-700) !important;
    background: #fff !important;
}
.s-modern .wm-table tbody tr:hover td { background: #fafbff !important; }
.s-modern .wm-table tbody tr:last-child td { border-bottom: 0 !important; }

.s-modern .wm-user-cell {
    display: flex; align-items: center; gap: 10px;
    font-weight: 600;
    color: var(--wm-slate-800);
}
.s-modern .wm-user-cell .wm-avatar-sm {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--wm-primary) 0%, #8b5cf6 100%);
    color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 12px;
    flex-shrink: 0;
    text-transform: uppercase;
}
.s-modern .wm-amount {
    font-weight: 700;
    color: var(--wm-slate-800);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.s-modern .wm-type-chip {
    display: inline-block;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 999px;
    letter-spacing: .03em;
}
.s-modern .wm-type-chip.chip-credit                     { background: #d1fae5; color: #065f46; }
.s-modern .wm-type-chip.chip-debit                      { background: #fee2e2; color: #991b1b; }
.s-modern .wm-type-chip.chip-topup                      { background: #ede9fe; color: #5b21b6; }
.s-modern .wm-type-chip.chip-transfer                   { background: #dbeafe; color: #1e40af; }
.s-modern .wm-type-chip.chip-refund                     { background: #fef9c3; color: #854d0e; }
.s-modern .wm-type-chip.chip-package_purchase           { background: #ffedd5; color: #9a3412; }
.s-modern .wm-type-chip.chip-primary_gateway_payment    { background: #cffafe; color: #155e75; }

.s-modern .wm-date { color: var(--wm-slate-600); font-size: 13px; white-space: nowrap; }
.s-modern .wm-note { color: var(--wm-slate-600); font-size: 13px; line-height: 1.4; }

/* Per-page selector */
.s-modern .wm-perpage {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--wm-slate-600);
    margin: 0;
}
.s-modern .wm-perpage select {
    border: 1px solid var(--wm-slate-200);
    border-radius: 8px;
    padding: 5px 26px 5px 10px;
    font-size: 13px;
    background: #fff;
    color: var(--wm-slate-700);
    cursor: pointer;
    height: 34px;
}
.s-modern .wm-perpage select:focus {
    outline: none;
    border-color: var(--wm-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .18);
}

/* Pagination footer */
.s-modern .wm-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-top: 1px solid var(--wm-slate-100);
    background: var(--wm-slate-50);
    font-size: 13px;
    color: var(--wm-slate-600);
}
.s-modern .wm-pagination .pagination { margin: 0; }
.s-modern .wm-pagination .pagination .page-link {
    color: var(--wm-slate-600);
    border-color: var(--wm-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.s-modern .wm-pagination .pagination .page-item.active .page-link {
    background: var(--wm-primary);
    border-color: var(--wm-primary);
    color: #fff;
}

/* Empty state */
.s-modern .wm-empty {
    text-align: center;
    padding: 50px 20px;
    color: var(--wm-slate-500);
}
.s-modern .wm-empty-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: var(--wm-slate-100);
    color: var(--wm-slate-300);
    font-size: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 12px;
}

/* Responsive */
@media (max-width: 992px) {
    .s-modern .wm-stats-row { grid-template-columns: 1fr; }
    .s-modern .wm-amount-note-row { grid-template-columns: 1fr; }
}
@media (max-width: 767px) {
    .s-modern .wm-card-body { padding: 18px; }
    .s-modern .wm-transfer-row { flex-direction: column; }
    .s-modern .wm-transfer-arrow { align-self: center; padding: 0; }
    .s-modern .wm-transfer-arrow i { transform: rotate(90deg); }
    .s-modern .wm-transfer-field { width: 100%; }
    .s-modern .wm-tabs { width: 100%; }
    .s-modern .wm-tab-btn { flex: 1; padding: 0 8px; }
    .s-modern .wm-pagination { flex-direction: column; text-align: center; }
}
</style>
@endpush

@section('content')

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Wallet Management</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage user wallet balances</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Wallet</li>
        </ol>
    </div>
</div>

@php
    $txTotal       = $transactions->total();
    $creditsOnPage = $transactions->getCollection()->where('type', 'credit')->count();
    $volumeOnPage  = $transactions->getCollection()->sum('amount');
@endphp

<div class="s-modern">
<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="wm-stats-row">
        <div class="wm-stat total">
            <p class="wm-stat-label">Total Transactions</p>
            <p class="wm-stat-value">{{ number_format($txTotal) }}</p>
            <i class="fas fa-receipt wm-stat-icon"></i>
        </div>
        <div class="wm-stat credits">
            <p class="wm-stat-label">Credits On This Page</p>
            <p class="wm-stat-value">{{ number_format($creditsOnPage) }}</p>
            <i class="fas fa-arrow-down wm-stat-icon"></i>
        </div>
        <div class="wm-stat transfer">
            <p class="wm-stat-label">Volume On This Page</p>
            <p class="wm-stat-value">${{ number_format($volumeOnPage, 2) }}</p>
            <i class="fas fa-coins wm-stat-icon"></i>
        </div>
    </div>

    {{-- Actions --}}
    <div class="wm-card">
        <div class="wm-card-head">
            <h5 class="wm-title">
                <span class="wm-title-icon"><i class="fas fa-wallet"></i></span>
                Wallet Actions
            </h5>
        </div>
        <div class="wm-card-body">
            <div class="wm-tabs-wrap">
                <div class="wm-tabs" role="tablist">
                    <button type="button" class="wm-tab-btn active" data-tab="add-credits">
                        <i class="fas fa-plus-circle"></i> Add Credits
                    </button>
                    <button type="button" class="wm-tab-btn" data-tab="transfer-credits">
                        <i class="fas fa-exchange-alt"></i> Transfer Credits
                    </button>
                </div>
            </div>

            {{-- Add Credits --}}
            <div id="pane-add-credits" class="wm-pane active">
                <div class="wm-form">
                    <form id="emailSearchForm">
                        <div class="wm-fg">
                            <label class="wm-label"><i class="fas fa-envelope"></i> User Email Address</label>
                            <input type="email" id="userEmail" class="wm-input" placeholder="Enter user's email address..." required>
                        </div>
                        <button type="submit" class="wm-btn">
                            <i class="fas fa-search"></i> Find User
                        </button>
                    </form>

                    <div class="wm-spinner-wrap" id="loadingSpinner">
                        <div class="wm-spinner"></div>
                        <p style="margin-top: 10px; color: var(--wm-slate-500); font-size: 13px;">Searching...</p>
                    </div>

                    <div id="errorMessage" class="wm-alert wm-alert-err" style="display: none;"></div>
                </div>
            </div>

            {{-- Transfer Credits --}}
            <div id="pane-transfer-credits" class="wm-pane">
                <div class="wm-form wm-form-wide">
                    <form id="transferForm">
                        <div class="wm-transfer-row">
                            <div class="wm-transfer-field">
                                <label class="wm-label"><i class="fas fa-user-minus"></i> From (Sender Email)</label>
                                <input type="email" id="fromEmail" class="wm-input" placeholder="Enter sender's email..." required>
                            </div>

                            <div class="wm-transfer-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>

                            <div class="wm-transfer-field">
                                <label class="wm-label"><i class="fas fa-user-plus"></i> To (Receiver Email)</label>
                                <input type="email" id="toEmail" class="wm-input" placeholder="Enter receiver's email..." required>
                            </div>
                        </div>

                        <div class="wm-amount-note-row">
                            <div>
                                <label class="wm-label"><i class="fas fa-dollar-sign"></i> Amount ($)</label>
                                <input type="number" id="transferAmount" class="wm-input" placeholder="0.00" min="1" step="0.01" required>
                            </div>

                            <div>
                                <label class="wm-label"><i class="fas fa-sticky-note"></i> Note (Optional)</label>
                                <input type="text" id="transferNote" class="wm-input" placeholder="Enter transfer note...">
                            </div>
                        </div>

                        <button type="submit" class="wm-btn wm-btn-success" id="transferBtn">
                            <i class="fas fa-exchange-alt"></i> Transfer Credits
                        </button>
                    </form>

                    <div class="wm-spinner-wrap" id="transferLoadingSpinner">
                        <div class="wm-spinner"></div>
                        <p style="margin-top: 10px; color: var(--wm-slate-500); font-size: 13px;">Processing...</p>
                    </div>

                    <div id="transferErrorMessage" class="wm-alert wm-alert-err" style="display: none;"></div>
                    <div id="transferSuccessMessage" class="wm-alert wm-alert-ok" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="wm-card">
        <div class="wm-card-head">
            <h5 class="wm-title">
                <span class="wm-title-icon"><i class="fas fa-history"></i></span>
                Recent Transactions
            </h5>
            <form method="GET" action="{{ route('admin.wallet') }}" class="wm-perpage">
                <label>Per page</label>
                @php($pp = request('perPage', $transactions->perPage()))
                <select name="perPage" onchange="this.form.submit()">
                    <option value="10"  {{ $pp==10 ? 'selected' : '' }}>10</option>
                    <option value="25"  {{ $pp==25 ? 'selected' : '' }}>25</option>
                    <option value="50"  {{ $pp==50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>

        <div style="overflow-x:auto;">
            <table class="wm-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Note</th>
                        <th style="text-align:right;">Date</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($transactions as $transaction)
                    <?php
                        $uName   = $transaction->wallet->user->name ?? 'N/A';
                        $initial = strtoupper(mb_substr($uName, 0, 1));
                        $t       = strtolower($transaction->type ?? '');
                    ?>
                    <tr>
                        <td>
                            <div class="wm-user-cell">
                                <span class="wm-avatar-sm">{{ $initial }}</span>
                                <span>{{ $uName }}</span>
                            </div>
                        </td>
                        <td><span class="wm-amount">$ {{ number_format($transaction->amount, 2) }}</span></td>
                        <td>
                            <span class="wm-type-chip chip-{{ $t }}">
                                {{ str_replace('_', ' ', ucfirst($transaction->type)) }}
                            </span>
                        </td>
                        <td><span class="wm-note">{{ $transaction->description }}</span></td>
                        <td style="text-align:right;"><span class="wm-date">{{ $transaction->created_at->format('d M Y H:i') }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="wm-empty">
                                <div class="wm-empty-icon"><i class="fas fa-receipt"></i></div>
                                <p style="margin:0; font-size:14px;">No transactions yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->count())
        <div class="wm-pagination">
            <div>
                Showing <strong>{{ $transactions->firstItem() }}</strong> to
                <strong>{{ $transactions->lastItem() }}</strong> of
                <strong>{{ $transactions->total() }}</strong> entries
            </div>
            <div>{{ $transactions->appends(request()->query())->links() }}</div>
        </div>
        @endif
    </div>

</div>
</div>

{{-- User Modal --}}
<div class="modal fade wm-modal" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="wm-title-icon"><i class="fas fa-wallet"></i></span>
                    Add Balance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="wm-user-card">
                    <div class="wm-user-avatar" id="userAvatar"></div>
                    <div class="wm-user-name" id="userName"></div>
                    <div class="wm-user-email" id="userEmailDisplay"></div>

                    <div class="wm-balance-box">
                        <span class="wm-balance-label">Current Balance</span>
                        <span class="wm-balance-amount">$ <span id="currentBalance">0.00</span></span>
                    </div>
                </div>

                <form id="addBalanceForm">
                    <input type="hidden" id="selectedUserId" name="user_id">

                    <div class="wm-fg">
                        <label class="wm-label"><i class="fas fa-dollar-sign"></i> Amount to Add ($)</label>
                        <input type="number" id="amountInput" name="amount" class="wm-input" placeholder="Enter amount..." min="1" step="0.01" required>
                    </div>

                    <div class="wm-fg">
                        <label class="wm-label"><i class="fas fa-sticky-note"></i> Note (Optional)</label>
                        <input type="text" id="noteInput" name="note" class="wm-input" placeholder="Add a note for this transaction...">
                    </div>

                    <button type="submit" class="wm-btn" id="addBalanceBtn">
                        <i class="fas fa-plus"></i> Add Balance
                    </button>
                </form>

                <div id="modalErrorMessage" class="wm-alert wm-alert-err" style="display: none;"></div>
                <div id="modalSuccessMessage" class="wm-alert wm-alert-ok" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
function wm_number_format(number, decimals = 2) {
    return parseFloat(number).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

// Tab switching
document.querySelectorAll('.wm-tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var tab = this.getAttribute('data-tab');
        document.querySelectorAll('.wm-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.wm-tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('pane-' + tab).classList.add('active');
        this.classList.add('active');
    });
});

$(document).ready(function() {
    // Email search
    $('#emailSearchForm').submit(function(e) {
        e.preventDefault();
        const email = $('#userEmail').val().trim();
        if (!email) return;

        $('#loadingSpinner').show();
        $('#errorMessage').hide();

        $.ajax({
            url: "{{ route('admin.wallet.validate-user') }}",
            method: 'POST',
            data: { email: email, _token: "{{ csrf_token() }}" },
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.success) {
                    showUserModal(response.user);
                } else {
                    showError('User not found');
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                let msg = 'User not found with this email address';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showError(msg);
            }
        });
    });

    function showUserModal(user) {
        $('#selectedUserId').val(user.id);
        $('#userName').text(user.name);
        $('#userEmailDisplay').text(user.email);
        $('#currentBalance').text(user.current_balance);

        if (user.avatar) {
            $('#userAvatar').html(`<img src="${user.avatar}" alt="${user.name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`);
        } else {
            $('#userAvatar').text(user.name.charAt(0).toUpperCase());
        }

        $('#amountInput').val('');
        $('#noteInput').val('');
        $('#modalErrorMessage').hide();
        $('#modalSuccessMessage').hide();

        $('#userModal').modal('show');
    }

    $('#addBalanceForm').submit(function(e) {
        e.preventDefault();

        const formData = {
            user_id: $('#selectedUserId').val(),
            amount: $('#amountInput').val(),
            note: $('#noteInput').val() || 'Admin wallet topup',
            _token: "{{ csrf_token() }}"
        };

        $('#addBalanceBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        $('#modalErrorMessage').hide();
        $('#modalSuccessMessage').hide();

        $.ajax({
            url: "{{ route('admin.wallet.topup') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#modalSuccessMessage').text('Balance added successfully!').show();

                    const newBalance = parseFloat($('#currentBalance').text().replace(',', '')) + parseFloat(formData.amount);
                    $('#currentBalance').text(wm_number_format(newBalance, 2));

                    $('#amountInput').val('');
                    $('#noteInput').val('');

                    setTimeout(function() {
                        $('#userModal').modal('hide');
                        setTimeout(() => location.reload(), 500);
                    }, 2000);
                } else {
                    $('#modalErrorMessage').text('Failed to add balance. Please try again.').show();
                }
            },
            error: function() {
                $('#modalErrorMessage').text('An error occurred. Please try again.').show();
            },
            complete: function() {
                $('#addBalanceBtn').prop('disabled', false).html('<i class="fas fa-plus"></i> Add Balance');
            }
        });
    });

    function showError(message) {
        $('#errorMessage').text(message).show();
        setTimeout(function() { $('#errorMessage').fadeOut(); }, 5000);
    }

    $('#userModal').on('hidden.bs.modal', function() {
        $('#userEmail').val('');
        $('#modalErrorMessage').hide();
        $('#modalSuccessMessage').hide();
    });

    // Transfer
    $('#transferForm').submit(function(e) {
        e.preventDefault();

        const fromEmail = $('#fromEmail').val().trim();
        const toEmail   = $('#toEmail').val().trim();
        const amount    = $('#transferAmount').val();
        const note      = $('#transferNote').val().trim();

        if (!fromEmail || !toEmail || !amount) {
            $('#transferErrorMessage').text('Please fill in all required fields.').show();
            return;
        }
        if (fromEmail === toEmail) {
            $('#transferErrorMessage').text('Sender and receiver email cannot be the same.').show();
            return;
        }
        if (parseFloat(amount) <= 0) {
            $('#transferErrorMessage').text('Amount must be greater than 0.').show();
            return;
        }

        processTransfer(fromEmail, toEmail, amount, note);
    });

    function processTransfer(fromEmail, toEmail, amount, note) {
        $('#transferLoadingSpinner').show();
        $('#transferErrorMessage').hide();
        $('#transferSuccessMessage').hide();
        $('#transferBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        const formData = {
            from_email: fromEmail,
            to_email: toEmail,
            amount: amount,
            note: note || 'Admin credit transfer',
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: "{{ route('admin.wallet.transfer') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#transferLoadingSpinner').hide();
                $('#transferBtn').prop('disabled', false).html('<i class="fas fa-exchange-alt"></i> Transfer Credits');

                if (response.success) {
                    $('#transferSuccessMessage').html(
                        '<strong>Transfer Successful!</strong>&nbsp;' +
                        'Transferred $ ' + wm_number_format(amount, 2) +
                        ' from ' + fromEmail + ' to ' + toEmail
                    ).show();

                    $('#transferForm')[0].reset();

                    setTimeout(function() { $('#transferSuccessMessage').hide(); }, 5000);
                } else {
                    $('#transferErrorMessage').text(response.message || 'Transfer failed. Please try again.').show();
                }
            },
            error: function(xhr) {
                $('#transferLoadingSpinner').hide();
                $('#transferBtn').prop('disabled', false).html('<i class="fas fa-exchange-alt"></i> Transfer Credits');

                let msg = 'Transfer failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }

                $('#transferErrorMessage').text(msg).show();
            }
        });
    }
});
</script>
@endpush

@endsection
