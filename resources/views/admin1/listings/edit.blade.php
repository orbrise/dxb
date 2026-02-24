@extends("admin.layout.master")
@section('content')

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Edit Category</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage Edit Category effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Category</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>


<div class="container">
<div class="row mt-3">
<div class="col-lg-12">
                                <div class="card mb-3">

                                    <div class="card-header">
                                       <h5 class="card-title mb-0">Edit Category</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">

    
    <form action="{{ route('admin.listings.update', $listing) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $listing->name }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>
</div></div>
</div>
@endsection