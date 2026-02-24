<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Reported Profile</th>
                <th>Reporter</th>
                <th>Report Type</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>
                        @if($report->profile)
                            <a href="{{ route('admin.profiles.edit', $report->profile->id) }}" target="_blank">
                                {{ $report->profile->name }}
                            </a>
                        @else
                            <span class="text-muted">Profile Deleted</span>
                        @endif
                    </td>
                    <td>
                        @if($report->user)
                            {{ $report->user->email }}
                        @else
                            <span class="text-muted">User Deleted</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-info">{{ ucfirst($report->report_type) }}</span>
                    </td>
                    <td>
                        <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: pointer; color: #007bff; text-decoration: underline;" 
                             class="view-details-btn"
                             data-report-id="{{ $report->id }}"
                             title="Click to view full details">
                            {{ Str::limit($report->description, 50) }}
                        </div>
                    </td>
                    <td>
                        <select class="form-control form-control-sm status-dropdown" 
                                data-report-id="{{ $report->id }}"
                                style="width: 120px;">
                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $report->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </td>
                    <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <div class="btn-group">
                            @if($report->profile)
                                <a href="/{{ strtolower($report->profile->ggender->name ?? 'female') }}-escorts-in-{{ strtolower(str_replace(' ', '-', $report->profile->gcity->name ?? 'dubai')) }}/{{ $report->profile->id }}/{{ $report->profile->slug }}" 
                                   target="_blank"
                                   class="btn btn-sm btn-info" 
                                   title="View Profile">
                                    <i class="fa fa-eye"></i>
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-info" disabled
                                        title="Profile Deleted">
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
                    <td colspan="8" class="text-center">No reports found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-3" id="pagination-container">
    {{ $reports->appends(request()->query())->links() }}
</div>
