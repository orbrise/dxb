@extends('admin.layout.master')

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Questions</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage questions effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Questions</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>


@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0 float-left" style="float:left">Questions</h5>
            </div><!-- end card header -->

            <div class="card-body">
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>User ID</th>
            <th>Profile ID</th>
            <th>Question</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($questions as $question)
        <tr>
            <td>{{ $question->id }}</td>
            <td>{{ $question->user_id }}</td>
            <td>{{ $question->profile_id }}</td>
            <td>{{ $question->question }}</td>
            <td>{{ $question->status }}</td>
            <td>
                @if($question->status == 0)
                <form action="{{ route('questions.approve', $question->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success btn-sm">Approve</button>
                </form>
                @endif
                @if($question->status == 1)
                <form action="{{ route('questions.disapprove', $question->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-warning btn-sm">Disapprove</button>
                </form>
                @endif
                <form action="{{ route('questions.destroy', $question->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete</button>
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