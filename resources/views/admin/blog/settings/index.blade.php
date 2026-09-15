@extends('admin.layout.master')
@section('title', 'Blog SEO Settings')

@push('css')
@include('admin._partials.settings-modern')
@endpush

@section('content')
<div class="s-modern">
<div class="main-content-wrapper">
    <div class="row d-flex justify-content-center no-gutters">
        <div class="col-lg-10 col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Blog SEO Settings</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('admin.blog.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- SEO Settings -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0"><i class="material-icons align-middle">search</i> SEO Settings</h6>
                            </div>
                            <div class="card-body">
                                @foreach($seoSettings as $blogSetting)
                                    <div class="form-group row">
                                        <label for="{{ $blogSetting->key }}" class="col-sm-3 col-form-label">
                                            {{ $blogSetting->label }}
                                        </label>
                                        <div class="col-sm-9">
                                            @if($blogSetting->type === 'textarea')
                                                <textarea name="{{ $blogSetting->key }}" id="{{ $blogSetting->key }}"
                                                    class="form-control" rows="3">{{ $blogSetting->value }}</textarea>
                                            @elseif($blogSetting->type === 'boolean')
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="{{ $blogSetting->key }}" name="{{ $blogSetting->key }}"
                                                        value="1" {{ $blogSetting->value ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="{{ $blogSetting->key }}">
                                                        {{ $blogSetting->value ? 'Enabled' : 'Disabled' }}
                                                    </label>
                                                </div>
                                            @else
                                                <input type="text" name="{{ $blogSetting->key }}" id="{{ $blogSetting->key }}"
                                                    class="form-control" value="{{ $blogSetting->value }}">
                                            @endif
                                            @if($blogSetting->description)
                                                <small class="form-text text-muted">{{ $blogSetting->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Social/Open Graph Settings -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0"><i class="material-icons align-middle">share</i> Social Media / Open Graph</h6>
                            </div>
                            <div class="card-body">
                                @foreach($socialSettings as $blogSetting)
                                    <div class="form-group row">
                                        <label for="{{ $blogSetting->key }}" class="col-sm-3 col-form-label">
                                            {{ $blogSetting->label }}
                                        </label>
                                        <div class="col-sm-9">
                                            @if($blogSetting->type === 'image')
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            id="{{ $blogSetting->key }}" name="{{ $blogSetting->key }}"
                                                            accept="image/*">
                                                        <label class="custom-file-label" for="{{ $blogSetting->key }}">
                                                            {{ $blogSetting->value ? basename($blogSetting->value) : 'Choose file...' }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @if($blogSetting->value)
                                                    <div class="mt-2">
                                                        <img src="{{ asset($blogSetting->value) }}" alt="Current image"
                                                            style="max-width: 200px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                                                        <small class="d-block text-muted">Current image</small>
                                                    </div>
                                                @endif
                                            @else
                                                <input type="text" name="{{ $blogSetting->key }}" id="{{ $blogSetting->key }}"
                                                    class="form-control" value="{{ $blogSetting->value }}">
                                            @endif
                                            @if($blogSetting->description)
                                                <small class="form-text text-muted">{{ $blogSetting->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Analytics Settings -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="m-0"><i class="material-icons align-middle">analytics</i> Analytics</h6>
                            </div>
                            <div class="card-body">
                                @foreach($analyticsSettings as $blogSetting)
                                    <div class="form-group row">
                                        <label for="{{ $blogSetting->key }}" class="col-sm-3 col-form-label">
                                            {{ $blogSetting->label }}
                                        </label>
                                        <div class="col-sm-9">
                                            <input type="text" name="{{ $blogSetting->key }}" id="{{ $blogSetting->key }}"
                                                class="form-control" value="{{ $blogSetting->value }}">
                                            @if($blogSetting->description)
                                                <small class="form-text text-muted">{{ $blogSetting->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Schema/Structured Data Settings -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning">
                                <h6 class="m-0"><i class="material-icons align-middle">code</i> Schema / Structured Data</h6>
                            </div>
                            <div class="card-body">
                                @foreach($schemaSettings as $blogSetting)
                                    <div class="form-group row">
                                        <label for="{{ $blogSetting->key }}" class="col-sm-3 col-form-label">
                                            {{ $blogSetting->label }}
                                        </label>
                                        <div class="col-sm-9">
                                            @if($blogSetting->type === 'image')
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            id="{{ $blogSetting->key }}" name="{{ $blogSetting->key }}"
                                                            accept="image/*">
                                                        <label class="custom-file-label" for="{{ $blogSetting->key }}">
                                                            {{ $blogSetting->value ? basename($blogSetting->value) : 'Choose file...' }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @if($blogSetting->value)
                                                    <div class="mt-2">
                                                        <img src="{{ asset($blogSetting->value) }}" alt="Current image"
                                                            style="max-width: 200px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                                                        <small class="d-block text-muted">Current image</small>
                                                    </div>
                                                @endif
                                            @else
                                                <input type="text" name="{{ $blogSetting->key }}" id="{{ $blogSetting->key }}"
                                                    class="form-control" value="{{ $blogSetting->value }}">
                                            @endif
                                            @if($blogSetting->description)
                                                <small class="form-text text-muted">{{ $blogSetting->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="material-icons align-middle">save</i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>{{-- /.s-modern --}}
@endsection

@push('scripts')
<script>
    // Update file input label when file is selected
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file...';
            var label = this.nextElementSibling;
            label.textContent = fileName;
        });
    });
</script>
@endpush
