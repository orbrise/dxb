@extends('admin.layout.master')

@section('content')
<h1>Manage Pages</h1>

<a href="{{ route('pages.create') }}" class="btn btn-primary mb-3">Create New Page</a>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Slug</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pages as $page)
        <tr>
            <td>{{ $page->id }}</td>
            <td>{{ $page->title }}</td>
            <td>{{ $page->slug }}</td>
            <td>{{ $page->is_published ? 'Published' : 'Draft' }}</td>
            <td>
                <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('pages.destroy', $page->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection