@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{smart_asset('assets/libs/simple-datatables/style.css')}}" rel="stylesheet" type="text/css" />
<link href="{{smart_asset('assets/libs/mobius1-selectr/selectr.min.css')}}" rel="stylesheet" type="text/css" />
  <style>
  span.input{display:none;}

  .form-check-input {
    margin-left: 0rem;
}

  /* Custom Select2 Styles */
  .custom-select2 {
    position: relative;
    display: inline-block;
    width: 100%;
}

.custom-select2 .select2-display {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 24px 8px 12px;
    cursor: pointer;
    min-height: 38px;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
    font-size: 14px;
    color: #333;
}

.custom-select2 .select2-display:hover {
    border-color: #aaa;
}

.custom-select2 .select2-display.open {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.custom-select2 .select2-display::after {
    content: '▼';
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: #666;
    pointer-events: none;
}

.custom-select2 .select2-display.open::after {
    transform: translateY(-50%) rotate(180deg);
}

.custom-select2 .select2-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.custom-select2 .select2-dropdown.open {
    display: block;
}

.custom-select2 .select2-search {
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.custom-select2 .select2-search input {
    width: 100%;
    padding: 6px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.custom-select2 .select2-search input:focus {
    outline: none;
    border-color: #80bdff;
}

.custom-select2 .select2-options {
    list-style: none;
    margin: 0;
    padding: 0;
}

.custom-select2 .select2-option {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
    color: #333;
    transition: background 0.15s ease;
}

.custom-select2 .select2-option:hover {
    background: #f0f0f0;
}

.custom-select2 .select2-option.selected {
    background: #007bff;
    color: white;
}

.custom-select2 .select2-option.hidden {
    display: none;
}

.custom-select2 .select2-placeholder {
    color: #999;
}

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


                                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
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
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="exampleInputEmail1" class="form-label">Country Name</label>
                                                        <select id="default" name="country" class="form-control cityselect2">
                                                            <option value="">Select</option>
                                                            @foreach($countries as $country)
                                                                <option value="{{$country->country}}" @if(request()->input('country') == $country->country) selected @endif>{{$country->country}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="cityNameSearch" class="form-label">City Name</label>
                                                        <input type="text" class="form-control" id="cityNameSearch" name="city_name" value="{{request()->input('city_name')}}" placeholder="Search by city name...">
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                                            @if(request()->input('country') || request()->input('city_name'))
                                                <a href="{{route('admin.cities')}}" class="btn btn-secondary"><i class="fa-solid fa-rotate-right"></i> Clear</a>
                                            @endif
                                            
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">All Cities</h5>
                                            <div class="d-flex align-items-center">
                                                <form method="GET" action="{{ route('admin.cities') }}" class="form-inline mr-3" id="searchForm">
                                                    <input type="hidden" name="country" value="{{ request('country') }}">
                                                    <input type="hidden" name="perPage" value="{{ request('perPage', $cities->perPage()) }}">
                                                    <input type="text" 
                                                           name="search" 
                                                           id="filterCityName" 
                                                           class="form-control form-control-sm" 
                                                           placeholder="Filter by city name..." 
                                                           value="{{ request('search') }}"
                                                           style="width: 250px;">
                                                </form>
                                                <form method="GET" action="{{ route('admin.cities') }}" class="form-inline">
                                                    <input type="hidden" name="country" value="{{ request('country') }}">
                                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                                    <label class="mr-2">Per page</label>
                                                    @php($pp = request('perPage', $cities->perPage()))
                                                    <select name="perPage" class="form-control" onchange="this.form.submit()">
                                                        <option value="10" {{ $pp==10 ? 'selected' : '' }}>10</option>
                                                        <option value="25" {{ $pp==25 ? 'selected' : '' }}>25</option>
                                                        <option value="50" {{ $pp==50 ? 'selected' : '' }}>50</option>
                                                        <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                 

                                        <table class="table table-bordered dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>City Name</th>
                                                    <th>Slug</th>
                                                    <th>Country</th>
                                                    <th>Iso</th>
                                                    <th>In Sitemap</th>
                                                    <th>Featured</th>
                                                    <th>Priority</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	@foreach($cities as $k => $city)
                                                <tr id="row{{$city->id}}">
                                                    <td> <span class="text" id="text{{$k}}">{{$city->name}}</span> <span class="input" id="input{{$k}}"><input type="text" value="{{$city->name}}" id="name{{$k}}"></span></td>

                                                    <td><span class="text" id="text{{$k}}">{{$city->slug ?? 'N/A'}}</span><span class="input" id="input{{$k}}"><input type="text" value="{{$city->slug}}" id="slug{{$k}}" placeholder="auto-generated"></span></td>

                                                    <td>{{$city->country}}</td>

                                                    <td><span class="text" id="text{{$k}}">{{$city->iso}}</span><span class="input" id="input{{$k}}"><input type="text" value="{{$city->iso}}" id="iso{{$k}}"></span></td>

                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input sitemap-toggle" type="checkbox" 
                                                                   id="sitemap{{$city->id}}" 
                                                                   data-city-id="{{$city->id}}"
                                                                   {{$city->include_in_sitemap ? 'checked' : ''}}>
                                                            <label class="form-check-label" for="sitemap{{$city->id}}">
                                                                <span class="badge bg-{{$city->include_in_sitemap ? 'success' : 'secondary'}}" id="badge{{$city->id}}">
                                                                    {{$city->include_in_sitemap ? 'Yes' : 'No'}}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input featured-toggle" type="checkbox" 
                                                                   id="featured{{$city->id}}" 
                                                                   data-city-id="{{$city->id}}"
                                                                   {{$city->is_featured ? 'checked' : ''}}>
                                                            <label class="form-check-label" for="featured{{$city->id}}">
                                                                <span class="badge bg-{{$city->is_featured ? 'warning' : 'secondary'}}" id="featuredBadge{{$city->id}}">
                                                                    {{$city->is_featured ? 'Featured' : 'No'}}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <input type="number" 
                                                               class="form-control form-control-sm priority-input" 
                                                               style="width: 80px;"
                                                               data-city-id="{{$city->id}}"
                                                               value="{{$city->feature_priority}}"
                                                               min="1"
                                                               max="999"
                                                               title="1 = Main Featured City, Lower = Higher Priority"
                                                               {{!$city->is_featured ? 'disabled' : ''}}>
                                                        <small class="text-muted">1=Main</small>
                                                    </td>

                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="fa-solid fa-gear"></i> Actions
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="javascript:void(0)" key="{{$k}}" id="editthis"><i class="fa-solid fa-pen"></i> Edit</a>
                                                                <a class="dropdown-item" href="javascript:void(0)" key="{{$k}}" rid="{{$city->id}}" id="updatethis"><i class="fa-solid fa-floppy-disk"></i> Update</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="return confirm('are you sure?')" id="del" key="{{$k}}" rid="{{$city->id}}"><i class="fa-solid fa-trash-can"></i> Delete</a>
                                                            </div>
                                                        </div>
                                                        <span id="success{{$k}}"></span>
                                                    </td>
                                                </tr>
                                                	@endforeach
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <p class="mb-0">
                                                    Showing {{ $cities->firstItem() }} to {{ $cities->lastItem() }} of {{ $cities->total() }} cities
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="float-end">
                                                    {{ $cities->appends(request()->query())->links() }}
                                                </div>
                                            </div>
                                        </div>
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
 

jQuery.fn.clickToggle = function(a, b) {
  return this.on("click", function(ev) { [b, a][this.$_io ^= 1].call(this, ev) })
};
    $(document).on("click", "#editthis", function() {
        var id = $(this).attr("key");
        if ($("span#input" + id).is(':visible')) {
            $("span#text" + id).show();
            $("span#input" + id).hide();
            edited = 0;
        } else {
            $("span#text" + id).hide();
            $("span#input" + id).show();
            edited = 1;
        }
    });

$(document).on("click", "#updatethis", function(){
    if(edited == 0){
            alert("edit a record first");
            return false;
        }
 	var id = $(this).attr("key");
 	var rid = $(this).attr("rid");
 	var name = $("input#name"+id).val();
 	var slug = $("input#slug"+id).val();
 	var iso = $("input#iso"+id).val();
 	$.post("{{url('admin/updatecity')}}", {_token:token,name:name,slug:slug,iso:iso,rid:rid}, function(data){
 		if(data == "success"){
 			$("span#success"+id).html("<div style='color:green'>Success</div>").delay(3000).fadeOut('slow');
            edited = 0;
            // Hide input fields and show text after successful update
            $("span#text" + id).show();
            $("span#input" + id).hide();
 		}
 	});
 });

$(document).on("click", "#del", function(){
 	var id = $(this).attr("key");
 	var rid = $(this).attr("rid");
 	$.post("{{url('admin/delcity')}}", {_token:token,rid:rid}, function(data){
 		if(data == "success"){
 			$("tr#row"+rid).remove();
            $("div#alert").html("<div class='alert alert-success' style='color:green'>City has been Deleted Successfully</div>").delay(3000).fadeOut('slow');
 		}
 	});
 });

// Handle sitemap checkbox toggle
$(".sitemap-toggle").change(function(){
    var cityId = $(this).data('city-id');
    var isChecked = $(this).is(':checked');
    
    $.post("{{url('admin/toggle-city-sitemap')}}", {
        _token: token,
        city_id: cityId,
        include_in_sitemap: isChecked ? 1 : 0
    }, function(data){
        if(data == "success"){
            var badge = $("#badge" + cityId);
            if(isChecked){
                badge.removeClass('bg-secondary').addClass('bg-success').text('Yes');
            } else {
                badge.removeClass('bg-success').addClass('bg-secondary').text('No');
            }
        }
    });
});

// Handle featured checkbox toggle
$(".featured-toggle").change(function(){
    var cityId = $(this).data('city-id');
    var isChecked = $(this).is(':checked');
    var priorityInput = $(".priority-input[data-city-id='" + cityId + "']");
    
    $.post("{{url('admin/toggle-city-featured')}}", {
        _token: token,
        city_id: cityId,
        is_featured: isChecked ? 1 : 0
    }, function(data){
        if(data == "success"){
            var badge = $("#featuredBadge" + cityId);
            if(isChecked){
                badge.removeClass('bg-secondary').addClass('bg-warning').text('Featured');
                priorityInput.prop('disabled', false);
            } else {
                badge.removeClass('bg-warning').addClass('bg-secondary').text('No');
                priorityInput.prop('disabled', true);
            }
        }
    });
});

// Handle priority input change
let priorityTimeout;
$(".priority-input").on('input', function(){
    var cityId = $(this).data('city-id');
    var priority = $(this).val();
    var input = $(this);
    
    clearTimeout(priorityTimeout);
    
    priorityTimeout = setTimeout(function(){
        $.post("{{url('admin/update-feature-priority')}}", {
            _token: token,
            city_id: cityId,
            feature_priority: priority
        }, function(response){
            if(response.success){
                input.css('border-color', '#28a745');
                setTimeout(function(){
                    input.css('border-color', '');
                }, 1000);
            } else {
                input.css('border-color', '#dc3545');
            }
        });
    }, 500); // Wait 500ms after user stops typing
});

// Initialize Select2 for country dropdowns
$(document).ready(function() {
    // Initialize custom select2 for country dropdowns
    initializeCustomSelect2();
});

function initializeCustomSelect2() {
    $('.cityselect2, .cityselect3').each(function() {
        const $select = $(this);
        if ($select.data('customSelect2')) return; // Already initialized
        
        const selectId = $select.attr('id') || 'select_' + Math.random().toString(36).substr(2, 9);
        $select.attr('id', selectId);
        
        // Hide original select
        $select.hide();
        
        // Get selected value
        const selectedValue = $select.val();
        const selectedText = $select.find('option:selected').text() || 'Select a country';
        
        // Create custom select2 HTML
        const customHtml = `
            <div class="custom-select2" data-select-id="${selectId}">
                <div class="select2-display">
                    <span class="select2-selected-text ${!selectedValue ? 'select2-placeholder' : ''}">${selectedText}</span>
                </div>
                <div class="select2-dropdown">
                    <div class="select2-search">
                        <input type="text" class="select2-search-input" placeholder="Search countries...">
                    </div>
                    <ul class="select2-options">
                        ${$select.find('option').map(function() {
                            const val = $(this).val();
                            const text = $(this).text();
                            const isSelected = val === selectedValue;
                            return `<li class="select2-option ${isSelected ? 'selected' : ''}" data-value="${val}">${text}</li>`;
                        }).get().join('')}
                    </ul>
                </div>
            </div>
        `;
        
        $select.after(customHtml);
        $select.data('customSelect2', true);
        
        const $customSelect = $select.next('.custom-select2');
        const $display = $customSelect.find('.select2-display');
        const $dropdown = $customSelect.find('.select2-dropdown');
        const $searchInput = $customSelect.find('.select2-search-input');
        const $selectedText = $customSelect.find('.select2-selected-text');
        
        // Toggle dropdown
        $display.on('click', function(e) {
            e.stopPropagation();
            
            // Close other dropdowns
            $('.custom-select2 .select2-dropdown').not($dropdown).removeClass('open');
            $('.custom-select2 .select2-display').not($display).removeClass('open');
            
            $dropdown.toggleClass('open');
            $display.toggleClass('open');
            
            if ($dropdown.hasClass('open')) {
                $searchInput.val('').focus();
                $customSelect.find('.select2-option').removeClass('hidden');
            }
        });
        
        // Search functionality
        $searchInput.on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $customSelect.find('.select2-option').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(searchTerm)) {
                    $(this).removeClass('hidden');
                } else {
                    $(this).addClass('hidden');
                }
            });
        });
        
        // Select option - NO HOVER, ONLY CLICK
        $customSelect.find('.select2-option').on('click', function(e) {
            e.stopPropagation();
            const value = $(this).data('value');
            const text = $(this).text();
            
            // Update original select
            $select.val(value).trigger('change');
            
            // Update display
            $selectedText.text(text).removeClass('select2-placeholder');
            
            // Update selected state
            $customSelect.find('.select2-option').removeClass('selected');
            $(this).addClass('selected');
            
            // Close dropdown
            $dropdown.removeClass('open');
            $display.removeClass('open');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest($customSelect).length) {
                $dropdown.removeClass('open');
                $display.removeClass('open');
            }
        });
        
        // Prevent dropdown close when clicking inside search
        $searchInput.on('click', function(e) {
            e.stopPropagation();
        });
    });
}

// Real-time city name filter with debouncing
let searchTimeout;
$("#filterCityName").on('keyup', function(){
    var searchValue = $(this).val();
    
    clearTimeout(searchTimeout);
    
    // If search is empty and was previously empty, don't submit
    if(searchValue === '' && '{{ request("search") }}' === ''){
        return;
    }
    
    // Debounce: wait 500ms after user stops typing
    searchTimeout = setTimeout(function(){
        $("#searchForm").submit();
    }, 500);
});

// Clear search on ESC key
$("#filterCityName").on('keydown', function(e){
    if(e.key === 'Escape'){
        $(this).val('');
        if('{{ request("search") }}' !== ''){
            $("#searchForm").submit();
        }
    }
});

</script>
@endpush