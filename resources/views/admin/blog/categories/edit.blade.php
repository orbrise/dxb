@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Edit Category</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">{{ $category->name }}</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.blog.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" name="slug" id="slug" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               value="{{ old('slug', $category->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="form-control">{{ old('description', $category->description) }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" 
                               class="form-control" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                    </div>
                    
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" id="is_active" 
                               class="form-check-input" value="1" 
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Active</label>
                    </div>
                    
                    <hr>
                    <h6>SEO Settings</h6>
                    
                    <div class="form-group">
                        <label for="meta_title">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" 
                               class="form-control" value="{{ old('meta_title', $category->meta_title) }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="2" 
                                  class="form-control">{{ old('meta_description', $category->meta_description) }}</textarea>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Category
                    </button>
                    <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
