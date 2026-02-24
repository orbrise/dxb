@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  .form-label { font-weight: bold; }
</style>
@endpush

@section("content")

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">App Settings</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage app settings effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">App Settings</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="row">
    <div class="col-lg-12  mt-3 mb-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Update App Settings</h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.appsettings.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">App Name</label>
                        <input type="text" name="app_name" class="form-control" value="{{ $settings->app_name }}" required>
                        @error("app_name")
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">App Logo</label>
                        <input type="file" name="app_logo" class="form-control">
                        @if($settings['app_logo'])
                        <img src="{{ smart_asset($settings->app_logo) }}" alt="App Logo" class="mt-2" style="max-width: 100px;">
                        @endif
                        @error("app_logo")
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Favicon</label>
                        <input type="file" name="favicon" class="form-control">
                        @if($settings['favicon'])
                        <img src="{{ smart_asset($settings->favicon) }}" alt="Favicon" class="mt-2" style="max-width: 50px;">
                        @endif
                        @error("favicon")
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Admin Background Image</label>
                        <input type="file" name="admin_bg" class="form-control">
                        @if($settings['admin_bg'])
                        <img src="{{ smart_asset($settings->admin_bg) }}" alt="Admin Background" class="mt-2" style="max-width: 150px;">
                        @endif
                        @error("admin_bg")
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Collapse Logo</label>
                        <input type="file" name="collapse_icon" class="form-control">
                        @if($settings['collapse_icon'])
                        <img src="{{ smart_asset($settings->collapse_icon) }}" alt="Collapse Logo" class="mt-2" style="max-width: 100px;">
                        @endif
                        @error("collapse_icon")
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keywords</label>
                        <input type="text" name="keywords" class="form-control" value="{{ $settings->keywords }}">
                        @error("keywords")
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $settings->title }}">
                        @error("title")
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $settings->description }}</textarea>
                        @error("description")
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Settings</button>
                    @if(session('success'))
                    <div class="alert alert-success mt-3">{{ session('success') }}</div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush