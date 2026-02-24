<div class="table-responsive">
    <!-- Bulk Delete Controls -->
    <div class="row mb-3">

        <div class="col-sm-12 col-md-6 text-start">
            <div id="bulk-actions" style="display: none;">
                <span id="selected-count" class="text-muted me-2">0 selected</span>
                <button type="button" id="bulk-archive-btn" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-archive"></i> Archive Selected
                </button>
                <button type="button" id="bulk-repost-btn" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-undo"></i> Repost Selected
                </button>
                <button type="button" id="bulk-delete-btn" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
            </div>
        </div>
    </div>

    <table class="table table-bordered dt-responsive table-striped w-100" style="table-layout: auto;">
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
            <th>Country</th>
            <th>Package</th>
            <th>Auction</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody> 
        @foreach($profiles as $profile)
        <tr data-profile-id="{{ $profile->id }}" data-gender="{{ strtolower($profile->ggender->name ?? '') }}" data-city="{{ strtolower($profile->gcity->name ?? '') }}" data-slug="{{ $profile->slug }}">
            <td onclick="event.stopPropagation();">
                <input type="checkbox" class="form-check-input profile-checkbox" value="{{ $profile->id }}">
            </td>
            <td class="clickable-cell"><b>{{$profile->id}}</b></td>
            <td class="clickable-cell">{{date("d m Y H:i:s", strtotime($profile->created_at))}}</td>
            <td class="clickable-cell" style="color: #186dde;">{{$profile->name}}</td>
            <td class="clickable-cell">@if(!empty($profile->singleimg->image))<img
                style="width: 70px; height: 90px; object-fit: contain; background: black; border-radius: 4px;" 
                class="img-fluid"
                 src="{{smart_asset("userimages/".$profile->user_id."/".$profile->id."/".$profile->singleimg->image)}}">@endif</td>
            <td class="clickable-cell" style="color: #186dde;">{{$profile->gcity->name}}</td>
            <td class="clickable-cell">{{$profile->gcity->country ?? 'N/A'}}</td>
            <td class="clickable-cell">
       
               @if(!$profile->getpackage)
                <span class="badge bg-secondary">N/A</span>
@elseif(strtolower($profile->getpackage->name) == 'basic')
<span class="badge bg-warning"><i class="fas fa-star"></i> Basic</span>
@elseif(strtolower($profile->getpackage->name) == 'featured')
<span class="badge bg-info"><i class="fas fa-star"></i> Featured</span>
@elseif(strtolower($profile->getpackage->name) == 'vip')
<span class="badge bg-success"><i class="fas fa-star"></i> VIP</span>
@else
<span class="badge bg-primary"><i class="fas fa-star"></i> {{ $profile->getpackage->name }}</span>
@endif
          </td>
            <td class="clickable-cell">
                @if($profile->activeAuction)
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-gavel"></i> Spot #{{ $profile->activeAuction->spot_number }}
                    </span>
                    <br>
                    <small class="text-success">
                        <i class="fas fa-clock"></i> {{ $profile->auction_days_remaining }} {{ $profile->auction_days_remaining == 1 ? 'day' : 'days' }} left
                    </small>
                @else
                    <span class="badge bg-secondary">N/A</span>
                @endif
            </td>
            <td class="clickable-cell">{{$profile->ggender->name}}</td>
            <td class="clickable-cell">{{$profile->countrycode}} {{$profile->phone}}</td>
            <td class="clickable-cell">
                @if($profile->isArchived())
                    <span class="badge bg-secondary">
                        <i class="fas fa-archive"></i> Archived
                    </span>
                    @if($profile->archive_reason)
                        <br><small class="text-muted">{{ $profile->archive_reason }}</small>
                    @endif
                @elseif(!$profile->is_active)
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle"></i> Inactive
                    </span>
                @else
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                @endif
            </td>
            <td onclick="event.stopPropagation();">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cog"></i> Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('admin.profiles.edit', $profile->id) }}" class="dropdown-item">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @if($profile->isArchived())
                            <li>
                                <button type="button" class="dropdown-item repost-btn" data-profile-id="{{ $profile->id }}">
                                    <i class="fas fa-undo"></i> Repost
                                </button>
                            </li>
                        @else
                            <li>
                                <button type="button" class="dropdown-item archive-btn" data-profile-id="{{ $profile->id }}">
                                    <i class="fas fa-archive"></i> Archive
                                </button>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.profiles.destroy', $profile->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fa-solid fa-trash-can"></i> Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $profiles->links('vendor.pagination.custom') }}
</div>