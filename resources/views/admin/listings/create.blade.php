@extends("admin.layout.master")

@push('css')
<style>
:root {
    --lst-primary: #6366f1;
    --lst-primary-dark: #4f46e5;
    --lst-slate-100: #f1f5f9;
    --lst-slate-200: #e2e8f0;
    --lst-slate-500: #64748b;
    --lst-slate-700: #334155;
    --lst-slate-800: #1e293b;
}
.lst-form-card {
    background: #fff;
    border: 1px solid var(--lst-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    max-width: 720px;
    margin: 4px auto 18px;
}
.lst-form-head {
    display: flex; align-items: center; gap: 10px;
    padding: 18px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--lst-slate-100);
}
.lst-form-head .lst-title-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99,102,241,.12); color: var(--lst-primary-dark); font-size: 15px;
}
.lst-form-head h5 {
    margin: 0;
    font-size: 16px; font-weight: 700; color: var(--lst-slate-800);
}
.lst-form-head h5 small {
    display: block; font-weight: 400;
    color: var(--lst-slate-500); font-size: 12px; margin-top: 2px;
}
.lst-form-body { padding: 24px; }
.lst-form-body .form-group label,
.lst-form-body label:not(.form-check-label) {
    display: flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--lst-slate-500);
    margin-bottom: 6px;
}
.lst-form-body .form-control {
    width: 100%; height: 46px;
    padding: 10px 14px;
    font-size: 14px;
    color: var(--lst-slate-800);
    background: #fff;
    border: 1px solid var(--lst-slate-200);
    border-radius: 10px;
    transition: border-color .15s, box-shadow .15s;
}
.lst-form-body .form-control:focus {
    outline: none;
    border-color: var(--lst-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.lst-btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    height: 46px; padding: 0 24px;
    background: linear-gradient(135deg, var(--lst-primary) 0%, var(--lst-primary-dark) 100%);
    color: #fff !important;
    border: 0;
    border-radius: 10px;
    font-size: 14px; font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(99,102,241,.35);
    transition: box-shadow .15s;
    text-decoration: none;
}
.lst-btn-primary:hover { box-shadow: 0 6px 18px rgba(99,102,241,.5); }
.lst-btn-ghost {
    display: inline-flex; align-items: center; gap: 6px;
    height: 46px; padding: 0 20px;
    background: #fff;
    color: var(--lst-slate-700) !important;
    border: 1px solid var(--lst-slate-200);
    border-radius: 10px;
    font-size: 14px; font-weight: 600;
    text-decoration: none;
    transition: background .15s;
}
.lst-btn-ghost:hover { background: var(--lst-slate-100); text-decoration: none; }
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Create Category</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Add a new category</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.listings.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </div>
</div>

<div class="container-fluid px-0">
    <div class="lst-form-card">
        <div class="lst-form-head">
            <span class="lst-title-icon"><i class="fas fa-plus"></i></span>
            <h5>
                Create Category
                <small>Add a new category to the list</small>
            </h5>
        </div>
        <div class="lst-form-body">
            <form action="{{ route('admin.listings.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label><i class="fas fa-th-large"></i> Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter category name" required autofocus>
                    @error('name')
                        <div style="color:#b91c1c; font-size:12px; margin-top:6px;">{{ $message }}</div>
                    @enderror
                </div>
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="lst-btn-primary">
                        <i class="fas fa-plus-circle"></i> Create Category
                    </button>
                    <a href="{{ route('admin.listings.index') }}" class="lst-btn-ghost">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
