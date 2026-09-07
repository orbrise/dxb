{{-- Bulk actions bar --}}
<div id="bulk-actions" class="p-bulk-bar" style="display: none;">
    <span id="selected-count">0 selected</span>
    <button type="button" id="bulk-archive-btn" class="btn btn-warning btn-sm">
        <i class="fas fa-archive"></i> Archive Selected
    </button>
    <button type="button" id="bulk-repost-btn" class="btn btn-success btn-sm">
        <i class="fas fa-undo"></i> Repost Selected
    </button>
    <button type="button" id="bulk-delete-btn" class="btn btn-danger btn-sm">
        <i class="fas fa-trash"></i> Delete Selected
    </button>
</div>

<div class="table-responsive">
    <table class="table dt-responsive w-100" style="table-layout: auto;">
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
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profiles as $profile)
            <tr data-profile-id="{{ $profile->id }}"
                data-gender="{{ strtolower($profile->ggender->name ?? '') }}"
                data-city="{{ strtolower($profile->gcity->name ?? '') }}"
                data-slug="{{ $profile->slug }}">
                <td onclick="event.stopPropagation();">
                    <input type="checkbox" class="form-check-input profile-checkbox" value="{{ $profile->id }}">
                </td>
                <td class="clickable-cell">
                    <span class="p-id-chip">#{{ $profile->id }}</span>
                </td>
                <td class="clickable-cell">
                    <div class="p-date-cell">{{ date("d M Y", strtotime($profile->created_at)) }}<br><small style="color:#94a3b8;">{{ date("H:i", strtotime($profile->created_at)) }}</small></div>
                </td>
                <td class="clickable-cell">
                    <div class="p-name-cell">
                        <span class="p-avatar">{{ strtoupper(mb_substr($profile->name, 0, 1)) }}</span>
                        <span class="p-name-link">{{ $profile->name }}</span>
                    </div>
                </td>
                <td class="clickable-cell">
                    @if(!empty($profile->singleimg->image))
                        <img class="p-picture-thumb"
                             src="{{ smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image) }}"
                             alt="{{ $profile->name }}">
                    @else
                        <span class="p-picture-empty">
                            <i class="fas fa-image"></i>
                            No image
                        </span>
                    @endif
                </td>
                <td class="clickable-cell">
                    <span class="p-city-cell">{{ $profile->gcity->name ?? '-' }}</span>
                </td>
                <td class="clickable-cell">{{ $profile->gcity->country ?? 'N/A' }}</td>
                <td class="clickable-cell">
                    @if(!$profile->getpackage)
                        <span class="p-badge p-badge-muted">N/A</span>
                    @elseif(strtolower($profile->getpackage->name) == 'basic')
                        <span class="p-badge p-badge-warning"><i class="fas fa-star"></i> Basic</span>
                    @elseif(strtolower($profile->getpackage->name) == 'featured')
                        <span class="p-badge p-badge-info"><i class="fas fa-star"></i> Featured</span>
                    @elseif(strtolower($profile->getpackage->name) == 'vip')
                        <span class="p-badge p-badge-success"><i class="fas fa-star"></i> VIP</span>
                    @else
                        <span class="p-badge p-badge-primary"><i class="fas fa-star"></i> {{ $profile->getpackage->name }}</span>
                    @endif
                </td>
                <td class="clickable-cell">
                    @if($profile->activeAuction)
                        <span class="p-badge p-badge-warning">
                            <i class="fas fa-gavel"></i> Spot #{{ $profile->activeAuction->spot_number }}
                        </span>
                        <div style="margin-top:4px;">
                            <span class="p-badge p-badge-success" style="font-size:11px;">
                                <i class="fas fa-clock"></i> {{ $profile->auction_days_remaining }} {{ $profile->auction_days_remaining == 1 ? 'day' : 'days' }} left
                            </span>
                        </div>
                    @else
                        <span class="p-badge p-badge-muted">N/A</span>
                    @endif
                </td>
                <td class="clickable-cell">{{ $profile->ggender->name ?? '-' }}</td>
                <td class="clickable-cell">
                    <span class="p-phone">{{ $profile->countrycode }} {{ $profile->phone }}</span>
                    @php
                        $waDigits = preg_replace('/\D/', '', ($profile->countrycode ?? '') . ($profile->phone ?? ''));
                    @endphp
                    @if($waDigits !== '')
                        <a href="https://wa.me/{{ $waDigits }}"
                           target="_blank"
                           rel="noopener"
                           title="Open in WhatsApp"
                           onclick="event.stopPropagation();"
                           class="p-whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    @endif
                </td>
                <td class="clickable-cell">
                    @if($profile->isArchived())
                        <span class="p-badge p-badge-archive">
                            <i class="fas fa-archive"></i> Archived
                        </span>
                        @if($profile->archive_reason)
                            <div style="margin-top:4px; color:#94a3b8; font-size:11px;">{{ $profile->archive_reason }}</div>
                        @endif
                    @elseif(!$profile->is_active)
                        <span class="p-badge p-badge-danger">
                            <i class="fas fa-times-circle"></i> Inactive
                        </span>
                    @else
                        <span class="p-badge p-badge-success">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    @endif
                </td>
                <td onclick="event.stopPropagation();" style="text-align:right;">
                    <div class="btn-group">
                        <button type="button" class="btn p-action-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cog"></i> Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
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

    @if($profiles->count() === 0)
        <div style="text-align:center; padding: 60px 20px;">
            <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px;">
                <i class="fas fa-inbox"></i>
            </div>
            <p style="color:#64748b; margin:0; font-size:15px;">No profiles found</p>
        </div>
    @endif

    <div class="p-pagination-wrap">
        {{ $profiles->links('vendor.pagination.custom') }}
    </div>
</div>
