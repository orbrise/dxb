@extends('admin.layout.master')

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">SEO</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage SEO effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">SEO</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0 float-left" style="float:left">Create New</h5>
            </div><!-- end card header -->

            <div class="card-body">

<form action="{{ route('seo.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="page" class="form-label">Page</label>
        <input type="text" name="page" class="form-control" placeholder="e.g., login, register">
    </div>
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" class="form-control">
    </div>
    <div class="mb-3">
        <label for="keywords" class="form-label">Keywords</label>
        <textarea name="keywords" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Save</button>
</form>
  </div>
        </div>
    </div>
</div>
@endsection