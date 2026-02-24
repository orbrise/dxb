@extends("admin.layout.master")
@section('content')
<div class="container">
     <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Categories</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage Categories effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Categories</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

       <div class="row mt-3">
        <div class="col-md-12 mb-2">
        <h5>Categories</h5>
        <a href="{{ route('admin.listings.create') }}" class="btn btn-primary float-right">Add New</a>
    </div>
<div class="col-lg-12">
                                <div class="card mb-3">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">All Categories</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">


    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listings as $listing)
            <tr>
                <td>{{ $listing->id }}</td>
                <td>{{ $listing->name }}</td>
                <td>{{ $listing->created_at }}</td>
                <td>
                    <a href="{{ route('admin.listings.edit', $listing) }}" class="btn btn-sm btn-info">Edit</a>
                    <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $listings->links() }}
</div>
</div>
</div>
</div>
@endsection