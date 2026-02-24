@extends('admin.layout.master')

@section('content')

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Seo</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage seo effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Seo</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0 float-left" style="float:left">All Pages</h5>
                <a href="{{ route('seo.create') }}" style="float:right" class="btn btn-primary float-right">Add New SEO</a>
            </div><!-- end card header -->

            <div class="card-body">



<table class="table">
    <thead>
        <tr>
            <th>Page</th>
            <th>Title</th>
            <th>Keywords</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($seoKeywords as $seo)
        <tr>
            <td>{{ $seo->page }}</td>
            <td>{{ $seo->title }}</td>
            <td>{{ $seo->keywords }}</td>
            <td>{{ $seo->description }}</td>
            <td>
                <a href="{{ route('seo.edit', $seo->id) }}" class="btn btn-info btn-sm">Edit</a>
                <form action="{{ route('seo.destroy', $seo->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
    </div>
        </div>
    </div>
</div>
@endsection