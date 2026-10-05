@extends("admin.layout.master")

@push('css')
<style>
/* ===== Premium Plans page — matches Global Packages modern design ===== */
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
.pk-stat.action { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); cursor: pointer; }

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
.pk-collapse-icon { color: var(--pk-slate-500); font-size: 14px; transition: transform .2s; }

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
.pk-card select.form-control { appearance: auto; }
.pk-card textarea.form-control { min-height: 80px; }
.pk-card .form-hint { font-size: 12px; color: var(--pk-slate-500); margin-top: 4px; }

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

.pk-pkg-name { font-weight: 700; color: var(--pk-slate-800); font-size: 14px; display: block; }
.pk-pkg-slug {
    color: var(--pk-slate-500); font-size: 12px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
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

/* Variant chip */
.pk-variant {
    display: inline-block; padding: 3px 10px; border-radius: 999px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em;
}
.pk-variant.free { background: #e2e8f0; color: #475569; }
.pk-variant.lime { background: #ecfccb; color: #4d7c0f; }
.pk-variant.pink { background: #fce7f3; color: #9d174d; }

/* Status badge */
.pk-status {
    display: inline-block; padding: 3px 10px; border-radius: 999px;
    font-size: 11px; font-weight: 700;
}
.pk-status.on  { background: #d1fae5; color: #065f46; }
.pk-status.off { background: #fee2e2; color: #991b1b; }

/* Action buttons */
.editPlan, .deletePlan {
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
.editPlan {
    background: #eef2ff !important;
    color: var(--pk-primary-dark) !important;
}
.editPlan:hover { background: #e0e7ff !important; color: var(--pk-primary-dark) !important; }
.deletePlan {
    background: #fee2e2 !important;
    color: #991b1b !important;
}
.deletePlan:hover { background: #fecaca !important; color: #991b1b !important; }
.deletePlan form { display: inline; margin: 0; }

/* Empty */
.pk-empty td {
    text-align: center !important;
    padding: 50px 20px !important;
    color: var(--pk-slate-500) !important;
}

/* Features repeater */
.pp-features {
    border: 1px dashed var(--pk-slate-300);
    border-radius: 10px;
    padding: 12px;
    background: var(--pk-slate-50);
}
.pp-feature-row {
    display: flex; gap: 8px; align-items: center; margin-bottom: 8px;
}
.pp-feature-row:last-child { margin-bottom: 0; }
.pp-feature-row .form-control { flex: 1 1 auto; }
.pp-feature-row .pp-included {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 14px; background: #fff; border: 1px solid var(--pk-slate-200);
    border-radius: 8px; font-size: 12px; color: var(--pk-slate-600);
    cursor: pointer; white-space: nowrap; margin: 0; font-weight: 500;
}
.pp-feature-row .pp-included input { margin: 0; }
.pp-feature-row .pp-remove {
    background: #fee2e2; color: #991b1b; border: none; padding: 9px 12px;
    border-radius: 8px; cursor: pointer; font-weight: 700;
}
.pp-feature-add {
    background: #eef2ff; color: var(--pk-primary-dark); border: none;
    padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; margin-top: 10px;
}

/* Modal */
#editPlanModal .modal-content {
    border: 0; border-radius: 16px; overflow: hidden;
    box-shadow: 0 20px 50px rgba(15,23,42,.3);
}
#editPlanModal .modal-header {
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--pk-slate-100);
    padding: 18px 22px;
}
#editPlanModal .modal-title {
    font-size: 16px; font-weight: 700; color: var(--pk-slate-800);
    display: flex; align-items: center; gap: 8px;
}
#editPlanModal .modal-body { padding: 22px; }
#editPlanModal .modal-footer {
    padding: 14px 22px;
    background: var(--pk-slate-50);
    border-top: 1px solid var(--pk-slate-100);
    gap: 8px;
}
#editPlanModal .modal-footer .btn {
    border-radius: 9px; padding: 8px 18px;
    font-size: 13px; font-weight: 600; border: 0;
    height: 40px; display: inline-flex; align-items: center; gap: 6px;
}
#editPlanModal .modal-footer .btn-secondary {
    background: #fff; color: var(--pk-slate-700);
    border: 1px solid var(--pk-slate-200);
}
#editPlanModal .modal-footer .btn-secondary:hover { background: var(--pk-slate-50); }
#editPlanModal .modal-footer .btn-primary {
    background: linear-gradient(135deg, var(--pk-primary) 0%, var(--pk-primary-dark) 100%);
    color: #fff;
    box-shadow: 0 3px 10px rgba(99,102,241,.35);
}
#editPlanModal .modal-footer .btn-primary:hover { box-shadow: 0 5px 14px rgba(99,102,241,.45); }

/* Responsive */
@media (max-width: 992px) {
    .pk-stats-row { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .pk-card-body { padding: 16px; }
}
</style>
@endpush

@section("content")

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Premium Plans Management</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage account upgrade packages shown at /premium-account</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Premium Plans</li>
        </ol>
    </div>
</div>

@php
    $totalPlans  = $plans->count();
    $activePlans = $plans->where('is_active', true)->count();
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="pk-stats-row">
        <div class="pk-stat total">
            <p class="pk-stat-label">Total Premium Plans</p>
            <p class="pk-stat-value">{{ number_format($totalPlans) }}</p>
            <i class="fas fa-crown pk-stat-icon"></i>
        </div>
        <div class="pk-stat tiers">
            <p class="pk-stat-label">Active Plans</p>
            <p class="pk-stat-value">{{ number_format($activePlans) }}</p>
            <i class="fas fa-check-circle pk-stat-icon"></i>
        </div>
        <div class="pk-stat action" onclick="document.getElementById('createPlanHeader').click();">
            <p class="pk-stat-label">Quick Action</p>
            <p class="pk-stat-value" style="font-size:16px; padding-top:6px;">Create a new Premium Plan</p>
            <i class="fas fa-plus-circle pk-stat-icon"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="pk-alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Existing plans --}}
    <div class="pk-card">
        <div class="pk-card-head">
            <h5 class="pk-title">
                <span class="pk-title-icon"><i class="fas fa-crown"></i></span>
                All Premium Plans
            </h5>
        </div>
        <div style="overflow-x: auto;">
            <table class="pk-table">
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Price / Duration</th>
                        <th>Variant</th>
                        <th>Tag</th>
                        <th>Status</th>
                        <th style="width:200px; text-align:right; padding-right:20px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td>
                                <span class="pk-pkg-name">{{ $plan->name }}</span>
                                <span class="pk-pkg-slug">slug: {{ $plan->slug }}</span>
                            </td>
                            <td>
                                <ul class="pk-tier-list">
                                    <li>
                                        <strong>${{ number_format($plan->price, 2) }}</strong>
                                        · {{ $plan->duration_days > 0 ? $plan->duration_days . ' days' : 'unlimited' }}
                                    </li>
                                </ul>
                            </td>
                            <td><span class="pk-variant {{ $plan->variant }}">{{ $plan->variant }}</span></td>
                            <td>
                                @if($plan->tag)
                                    <span style="color: var(--pk-slate-700); font-weight:600; font-size:12px;">{{ $plan->tag }}</span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($plan->is_active)
                                    <span class="pk-status on"><i class="fas fa-check"></i> Active</span>
                                @else
                                    <span class="pk-status off">Inactive</span>
                                @endif
                            </td>
                            <td style="text-align:right; padding-right:20px;">
                                <button type="button" class="btn editPlan"
                                        data-plan='@json($plan)'
                                        onclick="ppOpenEdit(JSON.parse(this.getAttribute('data-plan')))">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('admin.premium-plans.destroy', $plan->id) }}" method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Delete {{ $plan->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn deletePlan">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="pk-empty">
                            <td colspan="6">
                                <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <p style="color:#64748b; margin:0; font-size:15px;">No premium plans found.</p>
                                <p style="color:#94a3b8; margin:6px 0 0; font-size:13px;">Create one using the form below.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create new plan --}}
    <div class="pk-card">
        <div class="pk-card-head pk-collapsible" id="createPlanHeader" onclick="ppToggleCreate()">
            <h5 class="pk-title">
                <span class="pk-title-icon"><i class="fas fa-plus-circle"></i></span>
                Create New Premium Plan
            </h5>
            <i class="fa fa-chevron-down pk-collapse-icon" id="ppToggleIcon"></i>
        </div>
        <div id="createPlanCollapse" style="display: none;">
            <div class="pk-card-body">
                <form action="{{ route('admin.premium-plans.store') }}" method="POST">
                    @csrf
                    @include('admin._premium-plan-form', ['prefix' => 'new'])
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-plus"></i> Create Plan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit modal --}}
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="ppEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit" style="color: var(--pk-primary-dark);"></i>
                        <span id="ppEditTitle">Edit Plan</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('admin._premium-plan-form', ['prefix' => 'edit'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    const PP_UPDATE_URL = @json(url('admin/premium-plans')); // + /{id}

    function ppToggleCreate() {
        const box = document.getElementById('createPlanCollapse');
        const icon = document.getElementById('ppToggleIcon');
        if (box.style.display === 'none') {
            box.style.display = '';
            icon.style.transform = 'rotate(180deg)';
        } else {
            box.style.display = 'none';
            icon.style.transform = '';
        }
    }

    function ppOpenEdit(plan) {
        document.getElementById('ppEditTitle').textContent = 'Edit ' + plan.name;
        document.getElementById('ppEditForm').action = PP_UPDATE_URL + '/' + plan.id;

        // Populate fields (prefix "edit")
        document.getElementById('edit_name').value = plan.name || '';
        document.getElementById('edit_slug').value = plan.slug || '';
        document.getElementById('edit_price').value = plan.price || 0;
        document.getElementById('edit_duration_days').value = plan.duration_days || 0;
        document.getElementById('edit_period_label').value = plan.period_label || '';
        document.getElementById('edit_description').value = plan.description || '';
        document.getElementById('edit_cta_label').value = plan.cta_label || '';
        document.getElementById('edit_tag').value = plan.tag || '';
        document.getElementById('edit_variant').value = plan.variant || 'free';
        document.getElementById('edit_tag_color').value = plan.tag_color || '';
        document.getElementById('edit_sort_order').value = plan.sort_order ?? 0;
        document.getElementById('edit_is_free').checked = !!plan.is_free;
        document.getElementById('edit_is_active').checked = !!plan.is_active;

        ppSetFeatures('edit', Array.isArray(plan.features) ? plan.features : []);

        // Show modal (Bootstrap 4 jQuery API)
        if (window.jQuery) jQuery('#editPlanModal').modal('show');
    }

    function ppSetFeatures(prefix, list) {
        const box = document.getElementById(prefix + '_features');
        if (!box) return;
        box.innerHTML = '';
        (list.length ? list : [{label:'', included:true}]).forEach(f => ppAddFeature(prefix, f.label, !!f.included));
    }

    function ppAddFeature(prefix, label = '', included = true) {
        const box = document.getElementById(prefix + '_features');
        if (!box) return;
        const idx = box.children.length;
        const row = document.createElement('div');
        row.className = 'pp-feature-row';
        row.innerHTML = `
            <input type="text" class="form-control" name="features[${idx}][label]" placeholder="Feature label" value="${String(label).replace(/"/g,'&quot;')}">
            <label class="pp-included">
                <input type="checkbox" name="features[${idx}][included]" value="1" ${included ? 'checked' : ''}> Included
            </label>
            <button type="button" class="pp-remove" onclick="this.parentElement.remove()">×</button>
        `;
        box.appendChild(row);
    }

    // Seed one empty feature row in the Create form so users see the shape.
    document.addEventListener('DOMContentLoaded', function () {
        ppSetFeatures('new', []);
    });
</script>
@endpush
@endsection
