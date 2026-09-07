@extends("admin.layout.master")

@push('css')
<style>
/* ===== Global Packages page: modern redesign ===== */
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

/* Stats strip */
.pk-stats-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.pk-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.pk-stat .pk-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.pk-stat .pk-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.pk-stat .pk-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.pk-stat.total  { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.pk-stat.tiers  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.pk-stat.action { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }

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
.pk-card-head.pk-collapsible { cursor: pointer; transition: background .15s; }
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
.pk-card #priceTiers > p { font-size: 12px; margin-bottom: 12px; color: var(--pk-slate-500); }

.price-tier {
    background: var(--pk-slate-50);
    border: 1px solid var(--pk-slate-100);
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 10px !important;
    align-items: end;
}

/* Add/Remove tier buttons */
.btn-add-tier, .btn-remove-tier {
    display: inline-flex !important;
    align-items: center; justify-content: center;
    width: 100%; height: 42px;
    border-radius: 9px !important;
    font-size: 18px !important; font-weight: 700 !important;
    border: 0 !important;
    line-height: 1 !important;
    transition: transform .05s;
}
.btn-add-tier:active, .btn-remove-tier:active { transform: translateY(1px); }
.btn-add-tier {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #fff !important;
    box-shadow: 0 3px 8px rgba(16, 185, 129, .3);
}
.btn-remove-tier {
    background: #fee2e2 !important;
    color: #991b1b !important;
}
.btn-remove-tier:hover { background: #fecaca !important; }

/* Primary submit button */
.btn-primary.btn-lg {
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
.btn-primary.btn-lg:hover {
    color: #fff !important;
    box-shadow: 0 6px 18px rgba(99, 102, 241, .5);
}

/* Table */
.pk-table {
    width: 100% !important;
    margin: 0;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
    border: 0 !important;
}
.pk-table thead th {
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
.pk-table thead th:last-child { text-align: right; }
.pk-table tbody td {
    padding: 14px !important;
    vertical-align: middle !important;
    border: 0 !important;
    border-bottom: 1px solid var(--pk-slate-100) !important;
    color: var(--pk-slate-700) !important;
    background: #fff !important;
}
.pk-table tbody tr:hover td { background: #fafbff !important; }
.pk-table tbody tr:last-child td { border-bottom: 0 !important; }

.pk-pkg-name {
    font-weight: 700; color: var(--pk-slate-800);
    font-size: 14px;
    display: block;
}
.pk-pkg-tagline {
    color: var(--pk-slate-500);
    font-size: 13px;
    font-style: italic;
}
.pk-tier-list {
    margin: 0; padding: 0; list-style: none;
    display: flex; flex-direction: column; gap: 4px;
}
.pk-tier-list li {
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
.pk-tier-list li strong { color: var(--pk-primary-dark); font-weight: 700; }
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

/* Action buttons */
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

/* Empty */
.pk-empty td {
    text-align: center !important;
    padding: 50px 20px !important;
    color: var(--pk-slate-500) !important;
}

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

.pk-collapse-icon { color: var(--pk-slate-500); font-size: 14px; transition: transform .2s; }

/* Responsive */
@media (max-width: 992px) {
    .pk-stats-row { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .pk-card-body { padding: 16px; }
    .pk-table thead { display: none; }
    .price-tier .col-md-5, .price-tier .col-md-2 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 8px;
    }
}
</style>
@endpush

@section("content")

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Global Packages Management</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage global packages available to all countries</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Global Packages</li>
        </ol>
    </div>
</div>

@php
    $totalGlobal = collect($packages ?? [])->count();
    $totalTiers  = collect($packages ?? [])->sum(function($p) {
        $t = json_decode($p->price_tiers, true);
        return is_array($t) ? count($t) : 0;
    });
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="pk-stats-row">
        <div class="pk-stat total">
            <p class="pk-stat-label">Total Global Packages</p>
            <p class="pk-stat-value">{{ number_format($totalGlobal) }}</p>
            <i class="fas fa-globe pk-stat-icon"></i>
        </div>
        <div class="pk-stat tiers">
            <p class="pk-stat-label">Total Price Tiers</p>
            <p class="pk-stat-value">{{ number_format($totalTiers) }}</p>
            <i class="fas fa-layer-group pk-stat-icon"></i>
        </div>
        <div class="pk-stat action">
            <p class="pk-stat-label">Quick Action</p>
            <p class="pk-stat-value" style="font-size:16px; padding-top:6px;">Create a new Global Package</p>
            <i class="fas fa-plus-circle pk-stat-icon"></i>
        </div>
    </div>

    {{-- Existing global packages --}}
    <div class="pk-card">
        <div class="pk-card-head">
            <h5 class="pk-title">
                <span class="pk-title-icon"><i class="fas fa-globe"></i></span>
                All Global Packages
            </h5>
        </div>
        <div id="packagesTableContainer">
            <div style="overflow-x: auto;">
                <table class="pk-table">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Price Tiers</th>
                            <th>Description</th>
                            <th style="width:200px; text-align:right; padding-right:20px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="packagesTable">
                        @forelse($packages as $package)
                        <tr data-package-id="{{$package->id}}">
                            <td>
                                <span class="pk-pkg-name">{{$package->name}}</span>
                                <span class="pk-pkg-tagline">{{$package->tagline}}</span>
                            </td>
                            <td>
                                @php
                                    $tiers = json_decode($package->price_tiers, true) ?? [];
                                @endphp
                                @if(count($tiers) > 0)
                                    <ul class="pk-tier-list">
                                        @foreach($tiers as $tier)
                                            <li><strong>{{$tier['days']}}</strong> days · ${{$tier['price']}}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span style="color:#cbd5e1;">No tiers</span>
                                @endif
                            </td>
                            <td>
                                @php $descText = trim(strip_tags($package->description ?? '')); @endphp
                                <div class="pk-desc-cell">{{ $descText ?: '—' }}</div>
                            </td>
                            <td style="text-align:right; padding-right:20px;">
                                <button class="btn editPackage" data-id="{{$package->id}}">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                <button class="btn deletePackage" data-id="{{$package->id}}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr class="pk-empty">
                            <td colspan="4">
                                <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <p style="color:#64748b; margin:0; font-size:15px;">No global packages found.</p>
                                <p style="color:#94a3b8; margin:6px 0 0; font-size:13px;">Create one using the form below.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create new global package --}}
    <div class="pk-card">
        <div class="pk-card-head pk-collapsible" id="createPackageHeader">
            <h5 class="pk-title">
                <span class="pk-title-icon"><i class="fas fa-plus-circle"></i></span>
                Create New Global Package
            </h5>
            <i class="fa fa-chevron-down pk-collapse-icon" id="toggleIcon"></i>
        </div>
        <div id="createPackageCollapse" style="display: none;">
            <div class="pk-card-body">
                <div id="successMessage" style="display:none;" class="pk-alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> Package created successfully!
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="opacity:.6;"></button>
                </div>

                <form id="packageForm">
                    {{csrf_field()}}
                    <input type="hidden" name="is_global" value="1">

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
                        <p>Define pricing options for different durations (global pricing)</p>
                        <div id="tiersContainer">
                            <div class="row price-tier">
                                <div class="col-md-5">
                                    <label class="form-label"><i class="fas fa-calendar"></i> Days</label>
                                    <input type="number" class="form-control" name="tiers[0][days]" placeholder="e.g., 10" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label"><i class="fas fa-dollar-sign"></i> Price ($)</label>
                                    <input type="number" step="0.01" class="form-control" name="tiers[0][price]" placeholder="e.g., 30" required>
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
                        <textarea class="form-control" name="description" rows="4" style="min-height: 120px;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-plus"></i> Create Global Package
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pen"></i> Edit Global Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPackageForm">
                    {{csrf_field()}}
                    <input type="hidden" name="package_id" id="edit_package_id">
                    <input type="hidden" name="is_global" value="1">

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-box"></i> Package Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-tag"></i> Tag Line</label>
                        <input type="text" class="form-control" name="tagline" id="edit_tagline" required>
                    </div>

                    <div class="mb-3">
                        <h6>Price Tiers</h6>
                        <div id="editTiersContainer"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-align-left"></i> Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="4" style="min-height: 120px;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveEditPackage">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    let tierIndex = 1;

    // Toggle create package form
    $('#createPackageHeader').on('click', function() {
        $('#createPackageCollapse').slideToggle();
        $('#toggleIcon').toggleClass('fa-chevron-down fa-chevron-up');
    });

    // Add tier
    $(document).on('click', '.btn-add-tier', function() {
        const container = $(this).closest('#tiersContainer, #editTiersContainer');
        const newTier = `
            <div class="row price-tier">
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-calendar"></i> Days</label>
                    <input type="number" class="form-control" name="tiers[${tierIndex}][days]" placeholder="e.g., 10" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-dollar-sign"></i> Price ($)</label>
                    <input type="number" step="0.01" class="form-control" name="tiers[${tierIndex}][price]" placeholder="e.g., 30" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger w-100 btn-remove-tier">-</button>
                </div>
            </div>
        `;
        container.append(newTier);
        tierIndex++;
    });

    // Remove tier
    $(document).on('click', '.btn-remove-tier', function() {
        $(this).closest('.price-tier').remove();
    });

    // Create package
    $('#packageForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: '{{route("admin.addpackage")}}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#successMessage').show();
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                alert('Error creating package: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Edit package
    $(document).on('click', '.editPackage', function() {
        const packageId = $(this).data('id');

        $.ajax({
            url: `/admin/packages/${packageId}`,
            method: 'GET',
            success: function(pkg) {
                $('#edit_package_id').val(pkg.id);
                $('#edit_name').val(pkg.name);
                $('#edit_tagline').val(pkg.tagline);
                $('#edit_description').val(pkg.description);

                // Load tiers
                $('#editTiersContainer').empty();
                const tiers = JSON.parse(pkg.price_tiers || '[]');
                tiers.forEach((tier, index) => {
                    const tierHtml = `
                        <div class="row price-tier">
                            <div class="col-md-5">
                                <label class="form-label"><i class="fas fa-calendar"></i> Days</label>
                                <input type="number" class="form-control" name="tiers[${index}][days]" value="${tier.days}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label"><i class="fas fa-dollar-sign"></i> Price ($)</label>
                                <input type="number" step="0.01" class="form-control" name="tiers[${index}][price]" value="${tier.price}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn ${index === 0 ? 'btn-success btn-add-tier' : 'btn-danger btn-remove-tier'} w-100">${index === 0 ? '+' : '-'}</button>
                            </div>
                        </div>
                    `;
                    $('#editTiersContainer').append(tierHtml);
                });

                tierIndex = tiers.length;
                $('#editPackageModal').modal('show');
            }
        });
    });

    // Save edited package
    $('#saveEditPackage').on('click', function() {
        const formData = $('#editPackageForm').serialize();
        const packageId = $('#edit_package_id').val();

        $.ajax({
            url: `/admin/packages/${packageId}`,
            method: 'POST',
            data: formData + '&_method=PUT',
            success: function(response) {
                $('#editPackageModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error updating package: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Delete package
    $(document).on('click', '.deletePackage', function() {
        if (!confirm('Are you sure you want to delete this package?')) return;

        const packageId = $(this).data('id');

        $.ajax({
            url: `/admin/packages/${packageId}`,
            method: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                _method: 'DELETE'
            },
            success: function() {
                $(`tr[data-package-id="${packageId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert('Error deleting package: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });
});
</script>
@endpush
