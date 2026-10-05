{{-- Shared form fields for Premium Plan create + edit.
     Pass $prefix ('new' or 'edit') to namespace the field IDs so the two
     forms on the same page don't collide. --}}
<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label"><i class="fas fa-box"></i> Plan Name</label>
        <input type="text" class="form-control" name="name" id="{{ $prefix }}_name" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label"><i class="fas fa-link"></i> Slug</label>
        <input type="text" class="form-control" name="slug" id="{{ $prefix }}_slug" pattern="[a-z0-9-]+" required>
        <div class="form-hint">URL-safe. e.g. <code>1m</code>, <code>6m</code>, <code>free</code>.</div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label"><i class="fas fa-dollar-sign"></i> Price</label>
        <input type="number" step="0.01" min="0" class="form-control" name="price" id="{{ $prefix }}_price" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label"><i class="fas fa-calendar"></i> Duration (days)</label>
        <input type="number" min="0" class="form-control" name="duration_days" id="{{ $prefix }}_duration_days" required>
        <div class="form-hint">0 = unlimited / free</div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label"><i class="fas fa-stream"></i> Period label</label>
        <input type="text" class="form-control" name="period_label" id="{{ $prefix }}_period_label" placeholder="/ month">
    </div>
</div>

<div class="mb-3">
    <label class="form-label"><i class="fas fa-align-left"></i> Description</label>
    <textarea class="form-control" name="description" id="{{ $prefix }}_description" rows="2"></textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label"><i class="fas fa-bullseye"></i> CTA label</label>
        <input type="text" class="form-control" name="cta_label" id="{{ $prefix }}_cta_label" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label"><i class="fas fa-tag"></i> Tag (optional)</label>
        <input type="text" class="form-control" name="tag" id="{{ $prefix }}_tag" placeholder="MONTHLY">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label"><i class="fas fa-palette"></i> Variant (card style)</label>
        <select class="form-control" name="variant" id="{{ $prefix }}_variant" required>
            <option value="free">Free (plain)</option>
            <option value="lime">Lime</option>
            <option value="pink">Pink</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label"><i class="fas fa-paint-brush"></i> Tag colour</label>
        <select class="form-control" name="tag_color" id="{{ $prefix }}_tag_color">
            <option value="">— none —</option>
            <option value="lime">Lime</option>
            <option value="pink">Pink</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label"><i class="fas fa-sort-numeric-down"></i> Sort order</label>
        <input type="number" min="0" class="form-control" name="sort_order" id="{{ $prefix }}_sort_order" value="0">
    </div>
</div>

<div class="mb-3">
    <label class="form-label"><i class="fas fa-list-check"></i> Features</label>
    <div class="pp-features" id="{{ $prefix }}_features"></div>
    <button type="button" class="pp-feature-add" onclick="ppAddFeature('{{ $prefix }}')">+ Add feature</button>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label style="display:inline-flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color: var(--pk-slate-700); cursor:pointer;">
            <input type="checkbox" name="is_free" id="{{ $prefix }}_is_free" value="1">
            This is the Free / "current" plan
        </label>
    </div>
    <div class="col-md-6 mb-3">
        <label style="display:inline-flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color: var(--pk-slate-700); cursor:pointer;">
            <input type="checkbox" name="is_active" id="{{ $prefix }}_is_active" value="1" checked>
            Show on the plan picker
        </label>
    </div>
</div>
