<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width:80px;">ID</th>
                <th>Reported Profile</th>
                <th>Reporter</th>
                <th>Report Type</th>
                <th>Description</th>
                <th style="width:150px;">Status</th>
                <th style="width:150px;">Date</th>
                <th style="width:220px; text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                @php
                    $typeSlug = strtolower($report->report_type ?? 'other');
                    $typeIcon = match($typeSlug) {
                        'fake'          => 'fas fa-user-slash',
                        'spam'          => 'fas fa-exclamation-triangle',
                        'inappropriate' => 'fas fa-ban',
                        default         => 'fas fa-flag',
                    };
                @endphp
                <tr>
                    <td><span class="pr-id-chip">#{{ $report->id }}</span></td>
                    <td>
                        @if($report->profile)
                            <div class="pr-profile-cell">
                                <span class="pr-avatar">{{ strtoupper(mb_substr($report->profile->name, 0, 1)) }}</span>
                                <a href="{{ route('admin.profiles.edit', $report->profile->id) }}" target="_blank" class="pr-profile-link">
                                    {{ $report->profile->name }}
                                </a>
                            </div>
                        @else
                            <div class="pr-profile-cell">
                                <span class="pr-avatar deleted"><i class="fas fa-user-slash" style="font-size:12px;"></i></span>
                                <span class="pr-deleted">Profile Deleted</span>
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($report->user)
                            <span class="pr-email">{{ $report->user->email }}</span>
                        @else
                            <span class="pr-deleted">User Deleted</span>
                        @endif
                    </td>
                    <td>
                        <span class="pr-type pr-type-{{ $typeSlug }}">
                            <i class="{{ $typeIcon }}"></i> {{ ucfirst($report->report_type) }}
                        </span>
                    </td>
                    <td>
                        <div class="pr-description view-details-btn"
                             data-report-id="{{ $report->id }}"
                             title="Click to view full details">
                            {{ Str::limit($report->description, 80) }}
                        </div>
                    </td>
                    <td>
                        <select class="form-control form-control-sm status-dropdown"
                                data-report-id="{{ $report->id }}">
                            <option value="pending"  {{ $report->status == 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $report->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </td>
                    <td>
                        @if($report->created_at)
                            <div class="pr-date">
                                {{ $report->created_at->format('M d, Y') }}
                                <small>{{ $report->created_at->format('H:i') }}</small>
                            </div>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div class="btn-group">
                            @if($report->profile)
                                <a href="/{{ strtolower($report->profile->ggender->name ?? 'female') }}-escorts-in-{{ strtolower(str_replace(' ', '-', $report->profile->gcity->name ?? 'dubai')) }}/{{ $report->profile->id }}/{{ $report->profile->slug }}"
                                   target="_blank"
                                   class="btn btn-sm btn-info"
                                   title="View Profile">
                                    <i class="fa fa-eye"></i>
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-info" disabled title="Profile Deleted">
                                    <i class="fa fa-eye"></i>
                                </button>
                            @endif
                            @if($report->profile)
                                <button type="button" class="btn btn-sm btn-warning archive-btn"
                                        data-report-id="{{ $report->id }}"
                                        title="Archive Profile">
                                    <i class="fa fa-archive"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-profile-btn"
                                        data-report-id="{{ $report->id }}"
                                        title="Delete Profile">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif
                            <button type="button" class="btn btn-sm btn-secondary view-details-btn"
                                    data-report-id="{{ $report->id }}"
                                    title="View Report Details">
                                <i class="fa fa-info-circle"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-dark delete-report-btn"
                                    data-report-id="{{ $report->id }}"
                                    title="Delete Report">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                            <i class="fas fa-flag"></i>
                        </div>
                        <p style="color:#64748b; margin:0; font-size:15px;">No reports found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div id="pagination-container">
    {{ $reports->appends(request()->query())->links() }}
</div>
