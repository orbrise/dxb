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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">All Categories</h5>
                                            <form method="GET" action="{{ route('admin.listings.index') }}" class="form-inline">
                                                <label class="mr-2">Per page</label>
                                                <select name="perPage" class="form-control" onchange="this.form.submit()">
                                                    @php($pp = request('perPage', $listings->perPage()))
                                                    <option value="10" {{ $pp==10 ? 'selected' : '' }}>10</option>
                                                    <option value="25" {{ $pp==25 ? 'selected' : '' }}>25</option>
                                                    <option value="50" {{ $pp==50 ? 'selected' : '' }}>50</option>
                                                    <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                                                </select>
                                            </form>
                                        </div>
                                    </div><!-- end card header -->

                                    <div class="card-body">


    <table class="table table-striped table-hover">
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
                    <a href="{{ route('admin.listings.edit', $listing) }}" class="btn btn-sm btn-info"><i class="fa-solid fa-pen"></i> Edit</a>
                    <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash-can"></i> Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Showing {{ $listings->firstItem() }} to {{ $listings->lastItem() }} of {{ $listings->total() }} entries
        </div>
        <div>
            {{ $listings->appends(request()->query())->links() }}
        </div>
    </div>
</div>
</div>
</div>
</div>
@endsection