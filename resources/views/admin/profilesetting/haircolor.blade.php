@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<style>
  span.input { display: none; }
</style>
@endpush

@section("content")
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Hair Color</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage hair colors effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Hair Color</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>


<div class="row mt-3 ">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Create New</h5>
            </div><!-- end card header -->

            <div class="card-body">
                <form method="post" action="{{route('admin.addhaircolor')}}">
                    {{csrf_field()}}
                    <div class="mb-3">
                        <label for="hairColorInput" class="form-label">Hair Color</label>
                        <input type="text" class="form-control" name="name" aria-describedby="hairColorHelp" placeholder="Enter hair color">
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

@if(!empty($hairColors))
<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">All Hair Colors</h5>
            </div><!-- end card header -->

            <div class="card-body">
<div id="alert"></div>
                <table id="datatable_1" class="table table-bordered dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>Hair Color</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hairColors as $k => $hairColor)
                        <tr id="row{{$hairColor->id}}">
                            <td>
                                <span class="text" id="text{{$k}}">{{$hairColor->name}}</span>
                                <span class="input" id="input{{$k}}"><input type="text" value="{{$hairColor->name}}" id="name{{$k}}"></span>
                            </td>
                            <td>
                                <button type="button" key="{{$k}}" id="editthis" class="btn btn-info btn-sm"><i class="fa-solid fa-pen"></i> Edit</button>
                                <button type="button" key="{{$k}}" rid="{{$hairColor->id}}" id="updatethis" class="btn btn-success btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                                <button onclick="return confirm('Are you sure?')" type="button" id="del" key="{{$k}}" rid="{{$hairColor->id}}" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Delete</button>
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
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#datatable_1').DataTable({
        responsive: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "›",
                previous: "‹"
            }
        }
    });
});

    var token = "{{ csrf_token() }}";
var edited = 0;
    $('.cityselect2').select2();
    $('.cityselect3').select2();

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

        $.post("{{url('admin/updatehaircolor')}}", {_token: token, name: name, rid: rid}, function(data) {
            if (data == "success") {
                $("span#success" + id).html("<div style='color:green'>Success</div>").delay(3000).fadeOut('slow');
                 edited = 0;
            }
        });
    });

    $("button#del").click(function() {
        var id = $(this).attr("key");
        var rid = $(this).attr("rid");
        $.post("{{url('admin/delhaircolor')}}", {_token: token, rid: rid}, function(data) {
            if (data == "success") {
                $("tr#row" + rid).remove();
                $("div#alert").html("<div class='alert alert-success' style='color:green'>Hair Color has been Deleted Successfully</div>").delay(3000).fadeOut('slow');
            }
        });
    });
</script>
@endpush