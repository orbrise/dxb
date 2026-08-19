@extends("admin.layout.master")

@section('content')
<style>
.col-md-2{
    padding-left: 4px;
    padding-right: 4px;
}
</style>
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Auctions</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage auctions effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Auctions</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>


<div class="container-fluid mb-3   ">
    <div class="row">
    <div class="col-12  mt-3">
       <div class="float-right">
                        <a href="{{ route('admin.auctions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New Auction Spot
                        </a>
                    </div>
                    </div>
        <div class="col-12">
     
            <div class="card mt-2">
            
                <div class="card-header">
                    <h5 class="card-title float-left    ">Auction Spots Management</h5>
                    
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="card-body border-bottom">
                        <form action="{{ route('admin.auctions.index') }}" method="GET" class="row">
                            <div class="col-md-2">
                            <div class="form-group">
                                <label for="city_search">City</label>
                                <div class="position-relative">
                                    <input type="text" id="city_search" class="form-control @error('city_id') is-invalid @enderror" 
                                           placeholder="Type to search cities..." autocomplete="off">
                                    <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id') }}" required>
                                    <div id="city_results" class="dropdown-menu w-100" style="display:none; max-height:250px; overflow-y:auto;"></div>
                                </div>
                               
                            </div>
                        </div>
                    
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>Ended</option>
                                    </select>
                                </div>
                            </div>
                    
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control">
                                        <option value="">All Genders</option>
                                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="shemale" {{ request('gender') == 'shemale' ? 'selected' : '' }}>Shemale</option>
                                    </select>
                                </div>
                            </div>
                    
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Spot Number</label>
                                    <select name="spot" class="form-control">
                                        <option value="">All Spots</option>
                                        @for($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}" {{ request('spot') == $i ? 'selected' : '' }}>
                                                Spot #{{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                    
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sort By</label>
                                    <select name="sort" class="form-control">
                                        <option value="end_date" {{ request('sort') == 'end_date' ? 'selected' : '' }}>End Date</option>
                                        <option value="current_price" {{ request('sort') == 'current_price' ? 'selected' : '' }}>Current Price</option>
                                        <option value="bid_count" {{ request('sort') == 'bid_count' ? 'selected' : '' }}>Bid Count</option>
                                    </select>
                                </div>
                            </div>
                    
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                        <a href="{{ route('admin.auctions.index') }}" class="btn btn-default">
                                            <i class="fas fa-sync"></i> 
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Spot #</th>
                                <th>City</th>
                                <th>Gender</th>
                                <th>Current Price</th>
                                <th>End Date</th>
                                <th>Time Left</th>
                                <th>Status</th>
                                <th>Bids</th>
                                <th>Winner</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auctions as $auction)
                            <tr>
                                <td>{{ $auction->id }}</td>
                                <td>{{ $auction->spot_number }}</td>
                                <td>{{ $auction->city->name }}</td>
                                <td>{{ ucfirst($auction->gender) }}</td>
                                <td>${{ number_format($auction->current_price, 2) }}</td>
                                <td>{{ $auction->end_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $auction->time_left }}</td>
                                <td>
                                    <span class="badge badge-{{ $auction->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($auction->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.auctions.bids', $auction) }}">
                                        {{ $auction->bid_count }} bids
                                    </a>
                                </td>
                                <td>
                                    @if($auction->winner_profile_id)
                                        {{ $auction->winnerProfile->name ?? 'Unknown' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="d-flex">
                                    <a href="{{ route('admin.auctions.edit', $auction) }}" class="btn btn-sm btn-info mr-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($auction->status === 'active')
                                        <form action="{{ route('admin.auctions.end', $auction) }}" method="POST" class="mr-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Are you sure you want to end this auction?')">
                                                <i class="fas fa-stop-circle"></i> End
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.auctions.reset', $auction) }}" method="POST" class="mr-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to reset this auction?')">
                                                <i class="fas fa-redo"></i> Reset
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.auctions.destroy', $auction) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this auction?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $auctions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    let searchTimeout;
    
    // Function to search cities
    function searchCities(query) {
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            $('#city_results').hide();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("admin.cities.search") }}',
                type: 'POST',
                data: {
                    search: query,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    // Clear previous results
                    $('#city_results').empty();
                    
                    if (data.length === 0) {
                        $('#city_results').append('<div class="dropdown-item">No cities found</div>');
                    } else {
                        // Add each city to the results
                        $.each(data, function(index, city) {
                            const cityName = city.name + (city.country ? ` (${city.country})` : '');
                            const item = $('<div class="dropdown-item city-item"></div>')
                                .text(cityName)
                                .data('id', city.id)
                                .data('name', city.name);
                            
                            $('#city_results').append(item);
                        });
                    }
                    
                    // Show the results dropdown
                    $('#city_results').show();
                },
                error: function() {
                    $('#city_results').html('<div class="dropdown-item text-danger">Error loading cities</div>').show();
                }
            });
        }, 300);
    }
    
    // Event listener for input changes
    $('#city_search').on('input', function() {
        searchCities($(this).val().trim());
    });
    
    // Event listener for clicking on a city item
    $(document).on('click', '.city-item', function() {
    const cityId = $(this).data('id');
    const cityName = $(this).data('name');
    
    $('#city_id').val(cityId);
    $('#city_search').val(cityName);
    $('#selected_city_name').text('Selected: ' + cityName).removeClass('text-muted').addClass('text-success');
    $('#city_results').hide();
});
    
    // Event listener for clicking outside the dropdown
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.position-relative').length) {
            $('#city_results').hide();
        }
    });
    
    // Event listener for focusing on the search input
    $('#city_search').on('focus', function() {
        if ($(this).val().trim().length >= 2) {
            searchCities($(this).val().trim());
        }
    });
    
    // If there's a previously selected city, show it in the input
    if ($('#city_id').val()) {
        const cityName = $('#selected_city_name').text().replace('Selected: ', '');
        $('#city_search').val(cityName);
    }
});
</script>
@endpush