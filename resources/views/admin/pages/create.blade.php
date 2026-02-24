@extends('admin.layout.master')

@section('content')
<h1>Create New Page</h1>

<form action="{{ route('pages.store') }}" method="POST" novalidate>
    @csrf
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" id="title" class="form-control" required>
        @error('title')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="content" class="form-label">Content</label>
        <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
        @error('content')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="is_published" class="form-check-label">Publish</label>
        <input type="checkbox" name="is_published" id="is_published" class="form-check-input">
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
</form>
@endsection

@push('js')

<script src="https://cdn.tiny.cloud/1/3arl7kd7bi1emf429o89drj6b16cmrwsmvdfwmidj6z90k59/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content', // Target the textarea with ID "content"
            plugins: 'advlist autolink lists link image charmap preview anchor',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | link image',
            height: 300,
        });

        // Handle form submission
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function () {
                // Trigger TinyMCE to update the hidden textarea
                tinymce.triggerSave();
            });
        }
    });
</script>

@endpush