@extends('admin.layout.master')

@push('css')
@include('admin._partials.settings-modern')
<style>
.s-modern .pg-editor-card .form-check {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin: 0;
}
.s-modern .pg-editor-card .form-check-input {
    position: static !important;
    margin: 0 !important;
    width: 18px; height: 18px;
    cursor: pointer;
}
.s-modern .pg-editor-card .form-check-label {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
}
.s-modern .pg-tiny-wrap .tox.tox-tinymce {
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    overflow: hidden;
}
</style>
@endpush

@section('content')
<div class="s-modern">

    <div class="row page-title clearfix">
        <div class="page-title-left">
            <h5 class="mr-0 mr-r-5">Pages</h5>
            <p class="mr-0 text-muted d-none d-md-inline-block">Update page details and content</p>
        </div>
        <div class="page-title-right d-none d-sm-inline-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pages.index') }}">Pages</a></li>
                <li class="breadcrumb-item active">Edit Page</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid mt-2 mb-3">
        <div class="row">
            <div class="col-12">
                <div class="card pg-editor-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-pen-to-square mr-2"></i> Edit Page &mdash; <span class="text-primary">{{ $page->title }}</span>
                        </h5>
                        <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('pages.update', $page->id) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading"></i> Title
                                </label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $page->title) }}" required>
                                @error('title')
                                    <span class="text-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mt-3 pg-tiny-wrap">
                                <label for="content" class="form-label">
                                    <i class="fas fa-align-left"></i> Content
                                </label>
                                <textarea name="content" id="content"
                                    class="form-control @error('content') is-invalid @enderror"
                                    rows="12" required>{!! old('content', $page->content) !!}</textarea>
                                @error('content')
                                    <span class="text-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mt-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_published" id="is_published"
                                        value="1" class="form-check-input"
                                        {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                                    <label for="is_published" class="form-check-label">
                                        <i class="fas fa-globe text-primary"></i> Publish this page
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mt-4 d-flex align-items-center gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Page
                                </button>
                                <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary ml-2">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /.s-modern --}}
@endsection

@push('js')
<script src="https://cdn.tiny.cloud/1/3arl7kd7bi1emf429o89drj6b16cmrwsmvdfwmidj6z90k59/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: '#content',
            plugins: 'advlist autolink lists link image charmap preview anchor code',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
            height: 420,
            menubar: 'file edit view insert format',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; color: #334155; }'
        });

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function () {
                tinymce.triggerSave();
            });
        }
    });
</script>
@endpush
