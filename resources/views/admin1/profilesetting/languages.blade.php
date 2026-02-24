@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
  span.input { display: none; }
</style>
@endpush

@section("content")

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Languages</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage languages effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Languages</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>



<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Create New</h5>
            </div>

            <div class="card-body">
                <form method="post" action="{{route('admin.addlanguage')}}">
                    {{csrf_field()}}
                    <div class="mb-3">
                        <label for="languageInput" class="form-label">Language</label>
                        <input type="text" class="form-control" name="name" aria-describedby="languageHelp" placeholder="Enter language">
                        @error("name")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                    <br><br>
                    @if(!empty(session('success')))
                        <div class="alert alert-success">{{session('success')}}</div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

@if(!empty($languages))
<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">All Languages</h5>
            </div>

            <div class="card-body">
<div id="alert"></div>
                <table id="datatable_1" class="table table-bordered dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>Language</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($languages as $k => $language)
                        <tr id="row{{$language->id}}">
                            <td>
                                <span class="text" id="text{{$k}}">{{$language->name}}</span>
                                <span class="input" id="input{{$k}}"><input type="text" value="{{$language->name}}" id="name{{$k}}"></span>
                            </td>
                            <td>
                                <button type="button" key="{{$k}}" id="editthis" class="btn btn-info btn-sm">Edit</button>
                                <button type="button" key="{{$k}}" rid="{{$language->id}}" id="updatethis" class="btn btn-success btn-sm">Update</button>
                                <button onclick="return confirm('Are you sure?')" type="button" id="del" key="{{$k}}" rid="{{$language->id}}" class="btn btn-danger btn-sm">Delete</button>
                                <span id="success{{$k}}"></span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{smart_asset('assets/libs/simple-datatables/umd/simple-datatables.js')}}"></script>
        <script src="{{smart_asset('assets/js/pages/datatable.init.js')}}"></script>  
<script>
    var token = "{{ csrf_token() }}";
var edited = 0;
jQuery.fn.clickToggle = function(a, b) {
  return this.on("click", function(ev) { [b, a][this.$_io ^= 1].call(this, ev) })
};
    $("button#editthis").clickToggle(function() {
        var id = $(this).attr("key");
        $("span#text" + id).hide();
        $("span#input" + id).show();
        edited = 1;
    }, function() {
  var id = $(this).attr("key");
        $("span#text" + id).show();
        $("span#input" + id).hide();
        edited = 0;
    });

    $("button#updatethis").click(function() {

        if(edited == 0){
            alert("edit a record first");
            return false;
        }

        var id = $(this).attr("key");
        var rid = $(this).attr("rid");
        var name = $("input#name" + id).val();

        $.post("{{url('admin/updatelanguage')}}", {_token: token, name: name, rid: rid}, function(data) {
            if (data == "success") {
                $("span#success" + id).html("<div style='color:green'>Success</div>").delay(3000).fadeOut('slow');
                edited = 0;
            }
        });
    });

    $("button#del").click(function() {
        var id = $(this).attr("key");
        var rid = $(this).attr("rid");
        $.post("{{url('admin/dellanguage')}}", {_token: token, rid: rid}, function(data) {
            if (data == "success") {
                $("tr#row" + rid).remove();
                $("div#alert").html("<div class='alert alert-success' style='color:green'>Language has been Deleted Successfully</div>").delay(3000).fadeOut('slow');
            }
        });
    });
</script>
@endpush