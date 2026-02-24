@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{smart_asset('assets/libs/simple-datatables/style.css')}}" rel="stylesheet" type="text/css" />
  <style>
  span.input{display:none;}
  </style>
@endpush

@section("content")
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Currencies</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage currencies effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Currencies</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>



                        <div class="row mt-3">
<div class="col-lg-12">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Create New</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <form method="post" action="{{route('admin.addcurrency')}}">
                                        	{{csrf_field()}}
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">Currency Name</label>
                                                <input type="text" class="form-control" name="name" aria-describedby="emailHelp" placeholder="Currency name">
                                                @error("name")
                                                <span class="text-danger">{{$message}}</span>
                                                @enderror

                                            </div>
                                            

                                            <div class="mb-3">
                                                <label for="exampleInputPassword1" class="form-label">Currency Code</label>
                                                <input type="text" class="form-control" name="code" placeholder="phone code">
                                            </div>

                                            <div class="mb-3">
                                                <label for="exampleInputPassword1" class="form-label">Currency Symbol</label>
                                                <input type="text" class="form-control" name="symbol" placeholder="phone code">
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



@if(!empty($currencies))
<div class="row mt-3 mb-3">
<div class="col-lg-12">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">All Currencies</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                 

                                        <table id="datatable_1" class="table table-bordered dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Currency Name</th>
                                                    <th>Currency Code</th>
                                                    <th>Currency Symbol</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	@foreach($currencies as $k => $currency)
                                                <tr id="row{{$currency->id}}">
                                                    <td> <span class="text" id="text{{$k}}">{{$currency->currency}}</span> <span class="input" id="input{{$k}}"><input type="text" value="{{$currency->currency}}" id="name{{$k}}"></span></td>

                                                    <td><span class="text" id="text{{$k}}">{{$currency->code}}</span><span id="input{{$k}}" class="input"><input type="text" value="{{$currency->code}}" id="code{{$k}}"></span></td>

                                                    <td><span class="text" id="text{{$k}}">{{$currency->symbol}}</span><span id="input{{$k}}" class="input"><input type="text" value="{{$currency->symbol}}" id="symbol{{$k}}"></span></td>


                                                    <td><button type="button" key="{{$k}}" id="editthis" class="btn btn-info btn-sm">Edit</button> 
                                                    	<button type="button" key="{{$k}}" rid="{{$currency->id}}" id="updatethis" class="btn btn-success btn-sm">Update</button>

                                                    	<button onclick="return confirm('are you sure?')" type="button" id="del" key="{{$k}}" rid="{{$currency->id}}" class="btn btn-danger btn-sm">Delete</button>
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
<!-- Datatables js -->
<script src="{{smart_asset('assets/libs/simple-datatables/umd/simple-datatables.js')}}"></script>
        <script src="{{smart_asset('assets/js/pages/datatable.init.js')}}"></script>  

<script>
	var token = "{{ csrf_token()}}";

 $('.cityselect2').select2();
 $('.cityselect3').select2();

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
$("button#updatethis").click(function(){
    if(edited == 0){
            alert("edit a record first");
            return false;
        }
 	var id = $(this).attr("key");
 	var rid = $(this).attr("rid");
 	var name = $("input#name"+id).val();
 	var code = $("input#code"+id).val();
 	var symbol = $("input#symbol"+id).val();
    console.log(name + " "+ code + " " + symbol);
    return false;
 	$.post("{{url('admin/updatecurrency')}}", {_token:token,name:name,code:code,symbol:symbol,rid:rid}, function(data){
 		if(data == "success"){
 			$("span#success"+id).html("<div style='color:green'>Success</div>").delay(3000).fadeOut('slow');
            edited = 0;
 		}
 	});
 });

$("button#del").click(function(){
 	var id = $(this).attr("key");
 	var rid = $(this).attr("rid");
 	$.post("{{url('admin/delcurrency')}}", {_token:token,rid:rid}, function(data){
 		if(data == "success"){
 			$("tr#row"+rid).remove();
 		}
 	});
 });

</script>
@endpush