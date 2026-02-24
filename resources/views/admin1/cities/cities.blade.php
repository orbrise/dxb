@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{smart_asset('assets/libs/simple-datatables/style.css')}}" rel="stylesheet" type="text/css" />
<link href="{{smart_asset('assets/libs/mobius1-selectr/selectr.min.css')}}" rel="stylesheet" type="text/css" />
  <style>
  span.input{display:none;}
  </style>
@endpush

@section("content")
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Cities</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage cities effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Cities</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>



                        <div class="row mt-3">
<div class="col-lg-12">
                                <div class="card mb-3">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Create New</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <form method="post" action="{{route('admin.addcity')}}">
                                        	{{csrf_field()}}
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">City Name</label>
                                                <input type="text" class="form-control" name="cityname" aria-describedby="emailHelp" placeholder="city name">
                                                @error("cityname")
                                                <span class="text-danger">{{$message}}</span>
                                                @enderror

                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleInputPassword1" class="form-label">Country Name</label>
                                                <select  name="countryname" class="form-control cityselect3">
                                               	<option value="">Select</option>
                                               	@foreach($countries as $country)
                                               		<option value="{{$country->country}}" @if(request()->input('country') == $country->country) selected @endif>{{$country->country}}</option>
                                               	@endforeach
                                               </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="exampleInputPassword1" class="form-label">ISO</label>
                                                <input type="text" class="form-control" name="iso" placeholder="iso">
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


<div class="row">
<div class="col-lg-12">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Search For Edit</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <form method="get" action="{{route('admin.cities')}}">
                                        	{{csrf_field()}}
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">City Name</label>
                                               <select id="default" name="country" class="form-control cityselect2">
                                               	<option value="">Select</option>
                                               	@foreach($countries as $country)
                                               		<option value="{{$country->country}}" @if(request()->input('country') == $country->country) selected @endif>{{$country->country}}</option>
                                               	@endforeach
                                               </select>

                                            </div>
                                          
                                            


                                            <button type="submit" class="btn btn-primary">Search</button>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
</div>


@if(!empty(request()->input('country')) and !empty($cities))
<div class="row mt-2 mb-2">
<div class="col-lg-12">
                                <div class="card">
<div id='alert'></div>
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">All Cities</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                 

                                        <table id="datatable_1" class="table table-bordered dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>City Name</th>
                                                    <th>Country</th>
                                                    <th>Iso</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	@foreach($cities as $k => $city)
                                                <tr id="row{{$city->id}}">
                                                    <td> <span class="text" id="text{{$k}}">{{$city->name}}</span> <span class="input" id="input{{$k}}"><input type="text" value="{{$city->name}}" id="name{{$k}}"></span></td>

                                                    <td><span class="text" id="text{{$k}}">{{$city->country}}</span><span id="input{{$k}}" class="input"><input type="text" value="{{$city->country}}" id="country{{$k}}"></span></td>

                                                    <td><span class="text" id="text{{$k}}">{{$city->iso}}</span><span class="input" id="input{{$k}}"><input type="text" value="{{$city->iso}}" id="iso{{$k}}"></span></td>

                                                    <td><button type="button" key="{{$k}}" id="editthis" class="btn btn-info btn-sm">Edit</button> 
                                                    	<button type="button" key="{{$k}}" rid="{{$city->id}}" id="updatethis" class="btn btn-success btn-sm">Update</button>

                                                    	<button onclick="return confirm('are you sure?')" type="button" id="del" key="{{$k}}" rid="{{$city->id}}" class="btn btn-danger btn-sm">Delete</button>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/jquery.dataTables.min.js"></script>
<script>
	var token = "{{ csrf_token()}}";
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

$("button#updatethis").click(function(){
    if(edited == 0){
            alert("edit a record first");
            return false;
        }
 	var id = $(this).attr("key");
 	var rid = $(this).attr("rid");
 	var name = $("input#name"+id).val();
 	var country = $("input#country"+id).val();
 	var iso = $("input#iso"+id).val();
 	$.post("{{url('admin/updatecity')}}", {_token:token,name:name,country:country,iso:iso,rid:rid}, function(data){
 		if(data == "success"){
 			$("span#success"+id).html("<div style='color:green'>Success</div>").delay(3000).fadeOut('slow');
            edited = 0;
 		}
 	});
 });

$("button#del").click(function(){
 	var id = $(this).attr("key");
 	var rid = $(this).attr("rid");
 	$.post("{{url('admin/delcity')}}", {_token:token,rid:rid}, function(data){
 		if(data == "success"){
 			$("tr#row"+rid).remove();
            $("div#alert").html("<div class='alert alert-success' style='color:green'>City has been Deleted Successfully</div>").delay(3000).fadeOut('slow');
 		}
 	});
 });

</script>
@endpush