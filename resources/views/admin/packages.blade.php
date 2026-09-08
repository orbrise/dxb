@extends("admin.layout.master")

@push('css')
<style>
/* ===== Packages page: modern redesign ===== */
:root {
    --pk-primary: #6366f1;
    --pk-primary-dark: #4f46e5;
    --pk-success: #10b981;
    --pk-danger: #ef4444;
    --pk-warning: #f59e0b;
    --pk-info: #06b6d4;
    --pk-slate-50: #f8fafc;
    --pk-slate-100: #f1f5f9;
    --pk-slate-200: #e2e8f0;
    --pk-slate-300: #cbd5e1;
    --pk-slate-500: #64748b;
    --pk-slate-600: #475569;
    --pk-slate-700: #334155;
    --pk-slate-800: #1e293b;
}

/* Country selector hero */
.pk-country-card {
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    border: 1px solid #c7d2fe;
    border-radius: 14px;
    padding: 22px;
    margin: 4px 0 18px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: center;
    box-shadow: 0 4px 16px rgba(99, 102, 241, .08);
}
.pk-country-card .pk-country-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 0 10px;
}
.pk-country-card .pk-country-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, var(--pk-primary) 0%, var(--pk-primary-dark) 100%);
    color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
}
.pk-country-card h5 {
    margin: 0;
    font-size: 16px; font-weight: 700; color: var(--pk-slate-800);
}
.pk-country-card .pk-country-title small {
    display: block; font-size: 12px; font-weight: 400;
    color: var(--pk-slate-500); margin-top: 2px;
}
.pk-country-card label {
    display: block;
    font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--pk-slate-500);
    margin-bottom: 6px;
}
.pk-country-card .form-control {
    height: 44px;
    padding: 8px 14px;
    font-size: 14px;
    color: var(--pk-slate-800);
    background: #fff;
    border: 1px solid var(--pk-slate-200);
    border-radius: 10px;
    transition: border-color .15s, box-shadow .15s;
}
.pk-country-card .form-control:focus {
    outline: none;
    border-color: var(--pk-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.pk-country-note {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    font-size: 13px;
    color: #1e3a8a;
    line-height: 1.5;
}
.pk-country-note .pk-note-icon {
    color: #2563eb;
    font-size: 15px;
    flex-shrink: 0;
    padding-top: 1px;
}
.pk-country-note strong { color: #1e40af; }

/* Main cards */
.pk-card {
    background: #fff;
    border: 1px solid var(--pk-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.pk-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 20px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--pk-slate-100);
}
.pk-card-head.pk-collapsible {
    cursor: pointer;
    transition: background .15s;
}
.pk-card-head.pk-collapsible:hover { background: var(--pk-slate-50); }
.pk-card-head .pk-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 15px; font-weight: 700; color: var(--pk-slate-800);
}
.pk-card-head .pk-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--pk-primary-dark); font-size: 14px;
}
.pk-country-name {
    color: var(--pk-primary-dark) !important;
    font-weight: 700;
}
.pk-card-body { padding: 20px; }

/* Alerts */
.pk-alert {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin-bottom: 14px;
    border: 0 !important;
}
.pk-alert.alert-success { background: #d1fae5 !important; color: #065f46 !important; }
.pk-alert.alert-danger  { background: #fee2e2 !important; color: #991b1b !important; }
.pk-alert.alert-info    { background: #dbeafe !important; color: #1e40af !important; }

/* Toggle switch (Global Package) */
.pk-switch-row {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 16px;
    background: var(--pk-slate-50);
    border: 1px solid var(--pk-slate-200);
    border-radius: 10px;
    margin-bottom: 18px;
}
.pk-switch-row .form-check.form-switch {
    padding-left: 3rem;
    margin: 0;
}
.pk-switch-row .form-check-input {
    width: 42px; height: 24px;
    border: 0;
    background-color: var(--pk-slate-300);
    box-shadow: none;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
}
.pk-switch-row .form-check-input:checked {
    background-color: var(--pk-primary);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
}
.pk-switch-row .form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.pk-switch-row .form-check-label {
    font-size: 14px; color: var(--pk-slate-800);
    cursor: pointer;
}
.pk-switch-row .form-check-label strong { color: var(--pk-primary-dark); }
.pk-switch-row small {
    display: block; margin-top: 4px;
    color: var(--pk-slate-500); font-size: 12px;
}

/* Forms */
.pk-card .form-label {
    display: flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--pk-slate-500);
    margin-bottom: 6px;
}
.pk-card .form-control {
    width: 100%; min-height: 42px;
    padding: 8px 12px;
    font-size: 14px;
    color: var(--pk-slate-800);
    background: #fff;
    border: 1px solid var(--pk-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.pk-card .form-control:focus {
    outline: none;
    border-color: var(--pk-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.pk-card h6 {
    font-size: 13px; font-weight: 700; color: var(--pk-slate-700);
    text-transform: uppercase; letter-spacing: .04em;
    margin: 0 0 4px;
}
.pk-card #priceTiers > p, .pk-card #editPriceTiers > p { font-size: 12px; margin-bottom: 12px; color: var(--pk-slate-500); }

/* Price tier row */
.price-tier {
    background: var(--pk-slate-50);
    border: 1px solid var(--pk-slate-100);
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 10px !important;
    align-items: end;
}

/* Add/Remove tier buttons */
.btn-add-tier, .btn-remove-tier, .btn-add-edit-tier, .btn-remove-edit-tier {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 42px;
    border-radius: 9px !important;
    font-size: 18px !important;
    font-weight: 700 !important;
    border: 0 !important;
    line-height: 1 !important;
    transition: transform .05s;
}
.btn-add-tier:active, .btn-remove-tier:active,
.btn-add-edit-tier:active, .btn-remove-edit-tier:active { transform: translateY(1px); }
.btn-add-tier, .btn-add-edit-tier {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #fff !important;
    box-shadow: 0 3px 8px rgba(16, 185, 129, .3);
}
.btn-add-edit-tier { width: auto; padding: 0 16px !important; font-size: 13px !important; gap: 6px; }
.btn-add-edit-tier i { font-size: 12px; }
.btn-remove-tier, .btn-remove-edit-tier {
    background: #fee2e2 !important;
    color: #991b1b !important;
}
.btn-remove-tier:hover, .btn-remove-edit-tier:hover { background: #fecaca !important; }

/* Primary submit button */
.pk-btn-primary, .btn-primary.btn-lg {
    display: inline-flex !important;
    align-items: center; gap: 8px;
    height: 46px !important;
    padding: 0 24px !important;
    background: linear-gradient(135deg, var(--pk-primary) 0%, var(--pk-primary-dark) 100%) !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 10px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
    transition: box-shadow .15s;
}
.pk-btn-primary:hover, .btn-primary.btn-lg:hover {
    color: #fff !important;
    box-shadow: 0 6px 18px rgba(99, 102, 241, .5);
}

/* Packages table (JS-generated) */
#packagesTable, .pk-table {
    width: 100% !important;
    margin: 0;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
    border: 0 !important;
}
#packagesTable thead th, .pk-table thead th {
    background: var(--pk-slate-50) !important;
    color: var(--pk-slate-500) !important;
    font-size: 11px !important; font-weight: 700 !important;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 12px 14px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--pk-slate-200) !important;
    text-align: left;
    white-space: nowrap;
}
#packagesTable tbody td, .pk-table tbody td {
    padding: 14px !important;
    vertical-align: middle !important;
    border: 0 !important;
    border-bottom: 1px solid var(--pk-slate-100) !important;
    color: var(--pk-slate-700) !important;
    background: #fff !important;
}
#packagesTable tbody tr:hover td, .pk-table tbody tr:hover td { background: #fafbff !important; }
#packagesTable tbody tr:last-child td, .pk-table tbody tr:last-child td { border-bottom: 0 !important; }

.pk-pkg-name {
    font-weight: 700; color: var(--pk-slate-800);
    font-size: 14px;
    display: block;
}
.pk-pkg-tagline {
    display: inline-block;
    color: var(--pk-slate-500);
    font-size: 13px;
    font-style: italic;
}
.pk-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px;
    font-size: 12px; font-weight: 600;
    border-radius: 6px;
    white-space: nowrap;
}
.pk-badge i { font-size: 10px; }
.pk-badge-global { background: #d1fae5; color: #065f46; }
.pk-badge-country { background: #eef2ff; color: #4338ca; }

.pk-tier-list {
    margin: 0; padding: 0; list-style: none;
    display: flex; flex-direction: column; gap: 4px;
}
.pk-tier-list li, .pk-tier {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 3px 10px;
    background: var(--pk-slate-50);
    border: 1px solid var(--pk-slate-100);
    border-radius: 6px;
    font-size: 12.5px;
    color: var(--pk-slate-700);
    width: fit-content;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.pk-tier strong, .pk-tier-list li strong {
    color: var(--pk-primary-dark); font-weight: 700;
}
.pk-desc-cell {
    color: var(--pk-slate-500);
    font-size: 13px;
    max-width: 300px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Action buttons in packages table */
.editPackage, .deletePackage {
    display: inline-flex !important;
    align-items: center; gap: 5px;
    padding: 7px 12px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    border: 0 !important;
    line-height: 1 !important;
    margin-right: 4px;
}
.editPackage {
    background: #eef2ff !important;
    color: var(--pk-primary-dark) !important;
}
.editPackage:hover { background: #e0e7ff !important; color: var(--pk-primary-dark) !important; }
.deletePackage {
    background: #fee2e2 !important;
    color: #991b1b !important;
}
.deletePackage:hover { background: #fecaca !important; color: #991b1b !important; }

/* Modal styling */
#editPackageModal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(15,23,42,.3);
}
#editPackageModal .modal-header {
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--pk-slate-100);
    padding: 18px 22px;
}
#editPackageModal .modal-title {
    font-size: 16px; font-weight: 700; color: var(--pk-slate-800);
    display: flex; align-items: center; gap: 8px;
}
#editPackageModal .modal-body { padding: 22px; }
#editPackageModal .modal-footer {
    padding: 14px 22px;
    background: var(--pk-slate-50);
    border-top: 1px solid var(--pk-slate-100);
    gap: 8px;
}
#editPackageModal .modal-footer .btn {
    border-radius: 9px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    border: 0;
    height: 40px;
    display: inline-flex; align-items: center; gap: 6px;
}
#editPackageModal .modal-footer .btn-secondary {
    background: #fff;
    color: var(--pk-slate-700);
    border: 1px solid var(--pk-slate-200);
}
#editPackageModal .modal-footer .btn-secondary:hover { background: var(--pk-slate-50); }
#editPackageModal .modal-footer .btn-primary {
    background: linear-gradient(135deg, var(--pk-primary) 0%, var(--pk-primary-dark) 100%);
    color: #fff;
    box-shadow: 0 3px 10px rgba(99,102,241,.35);
}
#editPackageModal .modal-footer .btn-primary:hover { box-shadow: 0 5px 14px rgba(99,102,241,.45); }

/* Collapse toggle icon */
.pk-collapse-icon { color: var(--pk-slate-500); font-size: 14px; transition: transform .2s; }

/* Responsive */
@media (max-width: 992px) {
    .pk-country-card { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .pk-card-body { padding: 16px; }
    #packagesTable thead { display: none; }
    .price-tier .col-md-5, .price-tier .col-md-2 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 8px;
    }
}

.modal-content .close {
    top: 0.14286em;
    right: 0.14286em;

}
</style>
@endpush

@section("content")

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Packages Management</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage country-specific package pricing</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Packages</li>
        </ol>
    </div>
</div>

<div class="container-fluid px-0">

    {{-- Country selector --}}
    <div class="pk-country-card">
        <div>
            <div class="pk-country-title">
                <span class="pk-country-icon"><i class="fas fa-globe"></i></span>
                <h5>
                    Select Country
                    <small>Choose a country to manage its packages</small>
                </h5>
            </div>
            <label>Choose a country to manage packages</label>
            <select class="form-control" id="countryFilter">
                <option value="">-- Select Country --</option>
                @foreach($countries as $country)
                    <option value="{{$country->id}}">
                        {{$country->nicename}} @if($country->domain_prefix)({{$country->domain_prefix}}.domain.com)@endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="pk-country-note">
            <i class="fas fa-info-circle pk-note-icon"></i>
            <div>
                <strong>Note:</strong> Packages and pricing are managed per country. Select a country to view and edit its packages.
            </div>
        </div>
    </div>

    <div id="packagesSection" style="display: none;">

        {{-- Existing packages --}}
        <div class="pk-card">
            <div class="pk-card-head">
                <h5 class="pk-title">
                    <span class="pk-title-icon"><i class="fas fa-boxes"></i></span>
                    Packages for <span id="packagesCountryName" class="pk-country-name"></span>
                </h5>
            </div>
            <div class="pk-card-body">
                <div id="packagesTableContainer">
                    <!-- Packages will load here via AJAX -->
                </div>
            </div>
        </div>

        {{-- Create new package --}}
        <div class="pk-card">
            <div class="pk-card-head pk-collapsible" data-bs-toggle="collapse" data-bs-target="#createPackageCollapse">
                <h5 class="pk-title">
                    <span class="pk-title-icon"><i class="fas fa-plus-circle"></i></span>
                    Create New Package for <span id="selectedCountryName" class="pk-country-name"></span>
                </h5>
                <i class="fa fa-chevron-down pk-collapse-icon"></i>
            </div>
            <div id="createPackageCollapse" class="collapse">
                <div class="pk-card-body">
                    <div id="successMessage" style="display:none;" class="pk-alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> Package created successfully!
                        <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close" style="opacity:.6;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="packageForm" method="post" action="{{route('admin.addpackage')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="selected_country_id" id="selected_country_id">

                        {{-- Global toggle --}}
                        <div class="pk-switch-row">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_global" name="is_global">
                            </div>
                            <label class="form-check-label" for="is_global" style="cursor:pointer;">
                                <strong>Global Package</strong> — Available to all countries
                                <small>Enable this to create a package that works across all countries without country-specific pricing</small>
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-box"></i> Package Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-tag"></i> Tag Line</label>
                            <input type="text" class="form-control" name="tagline" required>
                        </div>

                        <div id="priceTiers" class="mb-3">
                            <h6>Price Tiers</h6>
                            <p>Define pricing options for different durations</p>
                            <div id="tiersContainer">
                                <div class="row price-tier">
                                    <div class="col-md-5">
                                        <label class="form-label"><i class="fas fa-calendar"></i> Days</label>
                                        <input type="number" class="form-control" name="tiers[0][days]" placeholder="e.g., 10" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label"><i class="fas fa-dollar-sign"></i> Price ($)</label>
                                        <input type="number" class="form-control" name="tiers[0][price]" placeholder="e.g., 30" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-success w-100 btn-add-tier">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-align-left"></i> Description</label>
                            <textarea class="form-control" id="desc" name="description" style="min-height: 120px;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-plus"></i> Create Package
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div><!-- End packagesSection -->
</div>

<!-- Edit Package Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-pen"></i> Edit Package for <span id="editCountryName" class="pk-country-name"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPackageForm">
                    <input type="hidden" id="edit_package_id">
                    <input type="hidden" id="edit_country_id">

                    <div class="pk-switch-row">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_global" name="is_global">
                        </div>
                        <label class="form-check-label" for="edit_is_global" style="cursor:pointer;">
                            <strong>Global Package</strong> — Available to all countries
                            <small>Enable this to make package available across all countries</small>
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-box"></i> Package Name</label>
                        <input type="text" class="form-control" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-tag"></i> Tag Line</label>
                        <input type="text" class="form-control" id="edit_tagline" required>
                    </div>

                    <div id="editPriceTiers" class="mb-3">
                        <h6>Price Tiers</h6>
                        <div id="editTiersContainer">
                            <!-- Tiers will be loaded here -->
                        </div>
                        <button type="button" class="btn btn-success btn-sm btn-add-edit-tier mt-2">
                            <i class="fa fa-plus"></i> Add Tier
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-align-left"></i> Description</label>
                        <textarea class="form-control" id="edit_description" style="min-height: 120px;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal-btn" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="savePackageChanges">
                    <i class="fas fa-save"></i> Save changes
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
var token = "{{ csrf_token() }}";
var countries = @json($countries);
var selectedCountryId = null;
var tierIndex = 1;

$(document).ready(function() {

    // Toggle collapse manually
    $('[data-bs-toggle="collapse"]').click(function() {
        const target = $(this).data('bs-target');
        $(target).collapse('toggle');
    });

    // Country Filter Change
    $('#countryFilter').change(function() {
        selectedCountryId = $(this).val();

        if (selectedCountryId) {
            const selectedCountryName = $(this).find('option:selected').text();
            $('#selectedCountryName').text(selectedCountryName);
            $('#packagesCountryName').text(selectedCountryName);
            $('#selected_country_id').val(selectedCountryId);
            $('#packagesSection').slideDown();

            // Load packages for this country
            loadPackagesForCountry(selectedCountryId);
        } else {
            $('#packagesSection').slideUp();
        }
    });

    // Load packages for selected country
    function loadPackagesForCountry(countryId) {
        $.get(`{{ url('admin/packages/by-country') }}/${countryId}`, function(data) {
            let tableHtml = '';

            if (data.packages && data.packages.length > 0) {
                tableHtml = `
                    <div class="table-responsive">
                        <table class="table pk-table" id="packagesTable">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Type</th>
                                    <th>Price Tiers</th>
                                    <th>Description</th>
                                    <th style="width:200px; text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>`;

                data.packages.forEach(pkg => {
                    const isGlobal = pkg.is_global;
                    const countryPrice = pkg.country_prices ? pkg.country_prices.find(cp => cp.country_id == countryId) : null;
                    let tiersHtml = '';

                    if (isGlobal) {
                        // For global packages, use price_tiers from package
                        const tiers = typeof pkg.price_tiers === 'string' ? JSON.parse(pkg.price_tiers) : pkg.price_tiers;
                        if (tiers && tiers.length) {
                            tiersHtml = '<ul class="pk-tier-list">' + tiers.map(t => `<li><strong>${t.days}</strong> days · $${t.price}</li>`).join('') + '</ul>';
                        }
                    } else if (countryPrice && countryPrice.price_tiers) {
                        const tiers = JSON.parse(countryPrice.price_tiers);
                        tiersHtml = '<ul class="pk-tier-list">' + tiers.map(t => `<li><strong>${t.days}</strong> days · $${t.price}</li>`).join('') + '</ul>';
                    }

                    const packageType = isGlobal
                        ? '<span class="pk-badge pk-badge-global"><i class="fas fa-globe"></i> Global</span>'
                        : '<span class="pk-badge pk-badge-country"><i class="fas fa-map-marker-alt"></i> Country-Specific</span>';

                    const descText = (pkg.description || '').replace(/<[^>]+>/g, '').trim();

                    tableHtml += `
                        <tr id="row${pkg.id}">
                            <td>
                                <span class="pk-pkg-name">${pkg.name}</span>
                                <span class="pk-pkg-tagline">${pkg.tagline || ''}</span>
                            </td>
                            <td>${packageType}</td>
                            <td>${tiersHtml || '<span style="color:#cbd5e1;">No pricing</span>'}</td>
                            <td><div class="pk-desc-cell">${descText || '<span style="color:#cbd5e1;">—</span>'}</div></td>
                            <td style="text-align:right;">
                                <button class="btn editPackage" data-id="${pkg.id}" data-is-global="${isGlobal}" data-country-price-id="${countryPrice ? countryPrice.id : ''}">
                                    <i class="fa fa-pen"></i> Edit
                                </button>
                                <button class="btn deletePackage" data-id="${pkg.id}" data-country-id="${countryId}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    `;
                });

                tableHtml += `
                            </tbody>
                        </table>
                    </div>`;
            } else {
                tableHtml = `
                    <div style="text-align:center; padding: 50px 20px;">
                        <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <p style="color:#64748b; margin:0; font-size:15px;">No packages found for this country.</p>
                        <p style="color:#94a3b8; margin:6px 0 0; font-size:13px;">Click the header below to create your first package.</p>
                    </div>`;
            }

            $('#packagesTableContainer').html(tableHtml);
        });
    }

    // Add Tier (Create Form)
    $(document).on('click', '.btn-add-tier', function() {
        const newTier = `
            <div class="row price-tier">
                <div class="col-md-5">
                    <input type="number" class="form-control" name="tiers[${tierIndex}][days]" placeholder="e.g., 20" required>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" name="tiers[${tierIndex}][price]" placeholder="e.g., 60" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger w-100 btn-remove-tier">-</button>
                </div>
            </div>
        `;
        $('#tiersContainer').append(newTier);
        tierIndex++;
    });

    // Remove Tier
    $(document).on('click', '.btn-remove-tier', function() {
        if ($('.price-tier').length > 1) {
            $(this).closest('.price-tier').remove();
        } else {
            alert('At least one price tier is required');
        }
    });

    // Submit Package Form
    $('#packageForm').submit(function(e) {
        e.preventDefault();

        const isGlobal = $('#is_global').is(':checked');

        if (!isGlobal && !selectedCountryId) {
            alert('Please select a country first or enable Global Package');
            return;
        }

        let tiers = [];
        $('#tiersContainer .price-tier').each(function() {
            const days = $(this).find('input[name*="[days]"]').val();
            const price = $(this).find('input[name*="[price]"]').val();
            if (days && price) {
                tiers.push({ days: parseInt(days), price: parseFloat(price) });
            }
        });

        const formData = {
            _token: token,
            name: $('input[name="name"]').val(),
            tagline: $('input[name="tagline"]').val(),
            description: $('#desc').val(),
            is_global: isGlobal ? 1 : 0
        };

        if (isGlobal) {
            formData.tiers = tiers;
        } else {
            formData.country_prices = [{
                country_id: selectedCountryId,
                tiers: tiers
            }];
        }

        console.log('Submitting form data:', formData);

        $.ajax({
            url: '{{ route("admin.addpackage") }}',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(formData),
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function(response) {
            if (response.success) {
                $('#successMessage').slideDown();
                $('#packageForm')[0].reset();
                $('#is_global').prop('checked', false);
                tierIndex = 1;
                $('#tiersContainer').html(`
                    <div class="row price-tier">
                        <div class="col-md-5">
                            <label class="form-label"><i class="fas fa-calendar"></i> Days</label>
                            <input type="number" class="form-control" name="tiers[0][days]" placeholder="e.g., 10" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label"><i class="fas fa-dollar-sign"></i> Price ($)</label>
                            <input type="number" class="form-control" name="tiers[0][price]" placeholder="e.g., 30" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-success w-100 btn-add-tier">+</button>
                        </div>
                    </div>
                `);

                loadPackagesForCountry(selectedCountryId);

                setTimeout(() => {
                    $('#successMessage').slideUp();
                }, 3000);
            }
        },
        error: function(xhr) {
            alert('Error creating package: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
        });
    });

    // Edit Package
    $(document).on('click', '.editPackage', function() {
        const packageId = $(this).data('id');
        const isGlobal = $(this).data('is-global');
        const countryName = $('#countryFilter option:selected').text();

        $('#editCountryName').text(countryName);
        $('#edit_country_id').val(selectedCountryId);

        $.get(`{{ url('admin/package') }}/${packageId}`, function(pkg) {
            $('#edit_package_id').val(pkg.id);
            $('#edit_name').val(pkg.name);
            $('#edit_tagline').val(pkg.tagline);
            $('#edit_description').val(pkg.description);
            $('#edit_is_global').prop('checked', pkg.is_global);

            let tiers = [];

            if (pkg.is_global) {
                tiers = typeof pkg.price_tiers === 'string' ? JSON.parse(pkg.price_tiers) : pkg.price_tiers;
            } else {
                const countryPrice = (pkg.country_prices || pkg.countryPrices || []).find(cp => cp.country_id == selectedCountryId);
                if (countryPrice && countryPrice.price_tiers) {
                    tiers = JSON.parse(countryPrice.price_tiers);
                }
            }

            $('#editTiersContainer').html('');

            if (tiers && tiers.length) {
                tiers.forEach((tier, index) => {
                    const tierHtml = `
                        <div class="row price-tier">
                            <div class="col-md-5">
                                <input type="number" class="form-control" value="${tier.days}" placeholder="Days" required>
                            </div>
                            <div class="col-md-5">
                                <input type="number" class="form-control" value="${tier.price}" placeholder="Price" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger w-100 btn-remove-edit-tier">-</button>
                            </div>
                        </div>
                    `;
                    $('#editTiersContainer').append(tierHtml);
                });
            }

            $('#editPackageModal').modal('show');
        });
    });

    // Add Tier in Edit Modal
    $(document).on('click', '.btn-add-edit-tier', function() {
        const newTier = `
            <div class="row price-tier">
                <div class="col-md-5">
                    <input type="number" class="form-control" placeholder="Days" required>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" placeholder="Price" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger w-100 btn-remove-edit-tier">-</button>
                </div>
            </div>
        `;
        $('#editTiersContainer').append(newTier);
    });

    // Remove Edit Tier
    $(document).on('click', '.btn-remove-edit-tier', function() {
        if ($('#editTiersContainer .price-tier').length > 1) {
            $(this).closest('.price-tier').remove();
        } else {
            alert('At least one price tier is required');
        }
    });

    // Save Package Changes
    $('#savePackageChanges').click(function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const isGlobal = $('#edit_is_global').is(':checked');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        let tiers = [];
        $('#editTiersContainer .price-tier').each(function() {
            const days = $(this).find('input').eq(0).val();
            const price = $(this).find('input').eq(1).val();
            if (days && price) {
                tiers.push({ days: parseInt(days), price: parseFloat(price) });
            }
        });

        console.log('Collected tiers:', tiers);
        console.log('Is Global:', isGlobal);
        console.log('Selected Country ID:', selectedCountryId);

        const formData = {
            _token: token,
            rid: $('#edit_package_id').val(),
            name: $('#edit_name').val(),
            tagline: $('#edit_tagline').val(),
            description: $('#edit_description').val(),
            is_global: isGlobal ? 1 : 0
        };

        if (isGlobal) {
            formData.tiers = tiers;
        } else {
            formData.country_prices = [{
                country_id: selectedCountryId,
                tiers: tiers
            }];
        }

        console.log('Updating form data:', formData);

        $.ajax({
            url: "{{ route('admin.updatepackage') }}",
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(formData),
            headers: {
                'X-CSRF-TOKEN': token
            }
        })
        .done(function(response) {
            if (response.success) {
                $('#editPackageModal .modal-body').prepend(`
                    <div class="pk-alert alert-success alert-dismissible fade show">
                        <i class="fa fa-check-circle"></i> Package updated successfully!
                        <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close" style="opacity:.6;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);

                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        })
        .fail(function(xhr) {
            $btn.prop('disabled', false).html(originalText);

            let errorMsg = 'An error occurred while saving.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }

            $('#editPackageModal .modal-body').prepend(`
                <div class="pk-alert alert-danger alert-dismissible fade show">
                    <i class="fa fa-exclamation-triangle"></i> ${errorMsg}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="opacity:.6;"></button>
                </div>
            `);
        });
    });

    // Delete Package
    $(document).on('click', '.deletePackage', function() {
        if (confirm('Are you sure you want to delete this package?')) {
            const packageId = $(this).data('id');

            $.post("{{ route('admin.delpackage') }}", {
                _token: token,
                rid: packageId
            }, function(response) {
                if (response == 'success') {
                    $(`#row${packageId}`).fadeOut(function() {
                        $(this).remove();
                    });
                }
            });
        }
    });

    // Collapse toggle animation
    $('#createPackageCollapse').on('shown.bs.collapse', function() {
        $(this).prev('.pk-card-head').find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }).on('hidden.bs.collapse', function() {
        $(this).prev('.pk-card-head').find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Modal close handler
    $('.close-modal-btn').click(function() {
        $('#editPackageModal').modal('hide');
    });

});
</script>

<script src="https://cdn.tiny.cloud/1/3arl7kd7bi1emf429o89drj6b16cmrwsmvdfwmidj6z90k59/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
  tinymce.init({
    selector: 'textarea#desc, textarea#edit_description',
    plugins: 'code table lists image',
    toolbar: 'undo redo | image | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
  });
</script>
@endpush
