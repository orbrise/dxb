@extends('admin.layout.master')

@section('content')

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Add Default SEO Setting</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Create a new default SEO fallback setting</p>
    </div>
    <!-- /.page-title-left -->
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('seo.index')}}">SEO Management</a></li>
            <li class="breadcrumb-item"><a href="{{route('default-seo.index')}}">Default SEO</a></li>
            <li class="breadcrumb-item active">Add New</li>
        </ol>
    </div>
    <!-- /.page-title-right -->
</div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0 float-left">Create Default SEO Setting</h5>
                <a href="{{ route('default-seo.index') }}" style="float:right" class="btn btn-secondary float-right">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div><!-- end card header -->

            <div class="card-body">

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle mr-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Info Box -->
                <div class="alert alert-info" role="alert">
                    <i class="fa fa-info-circle mr-2"></i>
                    <strong>Usage Examples:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>global:</strong> Site-wide fallback for all pages</li>
                        <li><strong>escorts:</strong> Default for all escort-related pages</li>
                        <li><strong>homepage:</strong> Specific fallback for the homepage</li>
                        <li><strong>city-pages:</strong> Default for city-based pages</li>
                    </ul>
                </div>

                <form action="{{ route('default-seo.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Setting Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" placeholder="e.g., global, escorts, homepage"
                                       pattern="[a-z0-9\-]+" title="Only lowercase letters, numbers, and hyphens allowed">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Use lowercase letters, numbers, and hyphens only</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="priority">Priority <span class="text-danger">*</span></label>
                                <input type="number" name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" 
                                       value="{{ old('priority', 1) }}" min="0" max="100">
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Higher numbers = higher priority</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="is_active">Status</label>
                                <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" placeholder="Default page title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">This will be used as the page title when no specific SEO is found</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Meta Description</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Default meta description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Recommended length: 150-160 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="keywords">Meta Keywords</label>
                        <input type="text" name="keywords" id="keywords" class="form-control @error('keywords') is-invalid @enderror" 
                               value="{{ old('keywords') }}" placeholder="keyword1, keyword2, keyword3">
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Comma-separated keywords</small>
                    </div>

                    <div class="form-group">
                        <label for="content">Additional SEO Content</label>
                        <textarea name="content" id="content" rows="5" class="form-control @error('content') is-invalid @enderror" 
                                  placeholder="Additional content for SEO purposes (optional)">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Optional content that can be used in templates</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Create Default SEO Setting
                        </button>
                        <a href="{{ route('default-seo.index') }}" class="btn btn-secondary ml-2">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    // Character counter for description
    $('#description').on('input', function() {
        const length = $(this).val().length;
        const recommended = 160;
        let color = 'text-muted';
        let message = `${length} characters`;
        
        if (length > recommended) {
            color = 'text-danger';
            message += ` (${length - recommended} over recommended)`;
        } else if (length > recommended - 10) {
            color = 'text-warning';
            message += ` (${recommended - length} remaining)`;
        } else if (length > 0) {
            color = 'text-success';
            message += ` (good length)`;
        }
        
        $(this).siblings('.form-text').removeClass('text-muted text-success text-warning text-danger')
                                     .addClass(color)
                                     .text(message);
    });
    
    // Normalize name field
    $('#name').on('input', function() {
        let value = $(this).val();
        // Convert to lowercase and replace spaces/special chars with hyphens
        value = value.toLowerCase()
                    .replace(/[^a-z0-9\-]/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
        $(this).val(value);
    });
});
</script>
@endpush