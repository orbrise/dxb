<div class="table-responsive">
    <!-- Bulk Delete Controls -->
    <div class="row mb-3">
        <div class="col-sm-12 col-md-6">
            
        </div>
        <div class="col-sm-12 col-md-6 text-end">
            <div id="bulk-actions" style="display: none;">
                <span id="selected-count" class="text-muted me-2">0 selected</span>
                <button type="button" id="bulk-delete-btn" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
            </div>
        </div>
    </div>

    <table class="table table-bordered dt-responsive nowrap table-striped">
    <thead>
        <tr>
            <th style="width: 40px;">
                <input type="checkbox" id="select-all" class="form-check-input">
            </th>
            <th>ID</th>
            <th>Date</th>
            <th>Name</th>
            <th>Picture</th>
            <th>City</th>
            <th>Package</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($profiles as $profile)
        <tr data-profile-id="{{ $profile->id }}">
            <td onclick="event.stopPropagation();">
                <input type="checkbox" class="form-check-input profile-checkbox" value="{{ $profile->id }}">
            </td>
            <td class="clickable-cell"><b>{{$profile->id}}</b></td>
            <td class="clickable-cell">{{date("d m Y H:i:s", strtotime($profile->created_at))}}</td>
            <td class="clickable-cell" style="color: #186dde;">{{$profile->name}}</td>
            <td class="clickable-cell">@if(!empty($profile->singleimg->image))<img
                style="width: 85px;max-height: 130px;object-fit: cover;" 
                class=" img-fluid"
                 src="{{smart_asset("userimages/".$profile->user_id."/".$profile->id."/".$profile->singleimg->image)}}">@endif</td>
            <td class="clickable-cell" style="color: #186dde;">{{$profile->gcity->name}}</td>
            <td class="clickable-cell">
       
               @if(!$profile->getpackage)
@elseif($profile->getpackage->id == 19)
<span class="badge bg-warning"><i class="fas fa-star"></i> Basic</span>
@elseif($profile->getpackage->id == 20)
<span class="badge bg-info"><i class="fas fa-star"></i> Featured</span>
@elseif($profile->getpackage->id == 21)
<span class="badge bg-success"><i class="fas fa-star"></i> VIP</span>
@else
<span class="badge bg-warning"><i class="fas fa-star"></i> Basic</span>
@endif
          </td>
            <td class="clickable-cell">{{$profile->ggender->name}}</td>
            <td class="clickable-cell">{{$profile->countrycode}} {{$profile->phone}}</td>
          
            <td onclick="event.stopPropagation();">
                <a href="{{ route('admin.profiles.edit', $profile->id) }}" class="btn btn-info btn-sm">Edit</a>
                <form action="{{ route('admin.profiles.destroy', $profile->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                   
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $profiles->links('vendor.pagination.custom') }}
</div>