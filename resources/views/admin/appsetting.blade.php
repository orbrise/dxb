@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  .form-label { font-weight: bold; }
</style>
@endpush

@section("content")
<style>
.form-check-input {
    margin-left: -0.25rem;
}

.card-header {
    padding: 0px 0px 11px 7px;
}
</style>
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

                    <!-- Auto-Archive Settings -->
                    <div class="card border-info mt-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-archive"></i> Auto-Archive and Auto-Delete Settings</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="auto_archive_enabled" class="form-check-input" id="auto_archive_enabled" 
                                               {{ $settings->auto_archive_enabled ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_archive_enabled">
                                            <strong>Enable Auto-Archive</strong>
                                            <small class="d-block text-muted">Automatically archive free profiles after specified days</small>
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Archive After Days</label>
                                        <input type="number" name="auto_archive_days" class="form-control" 
                                               value="{{ $settings->auto_archive_days ?? 30 }}" min="1" max="365">
                                        <small class="form-text text-muted">Number of days after which free profiles will be archived</small>
                                        @error("auto_archive_days")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <hr>
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="auto_delete_archived_enabled" class="form-check-input" id="auto_delete_archived_enabled" 
                                               {{ $settings->auto_delete_archived_enabled ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_delete_archived_enabled">
                                            <strong>Enable Auto-Delete (Archived)</strong>
                                            <small class="d-block text-muted">Automatically delete archived profiles after specified days</small>
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Delete Archived After Days</label>
                                        <input type="number" name="auto_delete_archived_days" class="form-control" 
                                               value="{{ $settings->auto_delete_archived_days ?? 60 }}" min="1" max="365">
                                        <small class="form-text text-muted">Number of days after which archived profiles will be permanently deleted</small>
                                        @error("auto_delete_archived_days")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="send_archive_warning" class="form-check-input" id="send_archive_warning" 
                                               {{ $settings->send_archive_warning ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label" for="send_archive_warning">
                                            <strong>Send Archive Warning</strong>
                                            <small class="d-block text-muted">Send email warning before archiving profiles</small>
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Warning Days Before</label>
                                        <input type="number" name="archive_warning_days" class="form-control" 
                                               value="{{ $settings->archive_warning_days ?? 3 }}" min="1" max="30">
                                        <small class="form-text text-muted">Days before archiving to send warning email</small>
                                        @error("archive_warning_days")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <hr>
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="auto_delete_inactive_enabled" class="form-check-input" id="auto_delete_inactive_enabled" 
                                               {{ $settings->auto_delete_inactive_enabled ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_delete_inactive_enabled">
                                            <strong>Enable Auto-Delete (Inactive)</strong>
                                            <small class="d-block text-muted">Automatically delete inactive profiles after specified days</small>
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Delete Inactive After Days</label>
                                        <input type="number" name="auto_delete_inactive_days" class="form-control" 
                                               value="{{ $settings->auto_delete_inactive_days ?? 90 }}" min="1" max="365">
                                        <small class="form-text text-muted">Number of days after which inactive profiles will be permanently deleted</small>
                                        @error("auto_delete_inactive_days")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Only free profiles (without premium packages) will be automatically archived. 
                                Premium profiles are not affected by auto-archiving. Users can always reactivate their archived profiles. 
                                Auto-delete actions will exclude premium profiles and will permanently remove all associated data for the deleted profile.
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3"><i class="fa-solid fa-floppy-disk"></i> Update Settings</button>
                    @if(session('success'))
                    <div class="alert alert-success mt-3">{{ session('success') }}</div>
                    @endif
                </form>  

                <!-- Geo Redirect Settings -->
                <div class="card border-primary mt-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-globe"></i> Regional Redirection Settings</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Control the regional/geo-based redirection system. When enabled, users will be automatically redirected to their country-specific domain (e.g., ae.massagerepublic.com.co for UAE, pk.massagerepublic.com.co for Pakistan).</p>
                        
                        <form method="POST" action="{{ route('admin.appsettings.update') }}">
                            @csrf
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="geo_redirect_enabled" class="form-check-input" id="geo_redirect_enabled" 
                                       {{ $settings->geo_redirect_enabled ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                                <label class="form-check-label ms-2" for="geo_redirect_enabled" style="margin-left: 10px;">
                                    <strong>Enable Regional Redirection</strong>
                                    <small class="d-block text-muted">Automatically redirect users to their regional domain based on their location</small>
                                </label>
                            </div>
                            
                            <input type="hidden" name="app_name" value="{{ $settings->app_name }}">
                            <input type="hidden" name="title" value="{{ $settings->title }}">
                            <input type="hidden" name="keywords" value="{{ $settings->keywords }}">
                            <input type="hidden" name="description" value="{{ $settings->description }}">
                            @if($settings->auto_archive_enabled)<input type="hidden" name="auto_archive_enabled" value="1">@endif
                            <input type="hidden" name="auto_archive_days" value="{{ $settings->auto_archive_days ?? 30 }}">
                            @if($settings->send_archive_warning ?? true)<input type="hidden" name="send_archive_warning" value="1">@endif
                            <input type="hidden" name="archive_warning_days" value="{{ $settings->archive_warning_days ?? 3 }}">
                            @if($settings->auto_delete_archived_enabled ?? false)<input type="hidden" name="auto_delete_archived_enabled" value="1">@endif
                            <input type="hidden" name="auto_delete_archived_days" value="{{ $settings->auto_delete_archived_days ?? 60 }}">
                            @if($settings->auto_delete_inactive_enabled ?? false)<input type="hidden" name="auto_delete_inactive_enabled" value="1">@endif
                            <input type="hidden" name="auto_delete_inactive_days" value="{{ $settings->auto_delete_inactive_days ?? 90 }}">
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Redirection Setting
                            </button>
                        </form>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> When disabled, users will stay on whichever domain they accessed the site from, without automatic redirection. Search engine bots are never redirected regardless of this setting.
                        </div>
                    </div>
                </div>

                <!-- Cache Management Section -->
                <div class="card border-danger mt-4">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fas fa-broom"></i> Cache Management</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Clear all application caches including views, routes, config, and compiled files. This is useful after making configuration changes or when experiencing caching issues.</p>
                        
                        <form method="POST" action="{{ route('admin.clear-cache') }}" onsubmit="return confirm('Are you sure you want to clear all caches?');">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i> Clear All Caches
                            </button>
                        </form>
                        
                        @if(session('cache_success'))
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle"></i> {{ session('cache_success') }}
                        </div>
                        @endif
                        
                        @if(session('cache_error'))
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-circle"></i> {{ session('cache_error') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush