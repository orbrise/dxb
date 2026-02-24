<div class="report-details">
    <dl class="row">
        <dt class="col-sm-4">Report ID:</dt>
        <dd class="col-sm-8">#{{ $report->id }}</dd>

        <dt class="col-sm-4">Reported Profile:</dt>
        <dd class="col-sm-8">
            @if($report->profile)
                <a href="{{ route('admin.profiles.edit', $report->profile->id) }}" target="_blank">
                    {{ $report->profile->name }} (ID: {{ $report->profile->id }})
                </a>
            @else
                <span class="text-muted">Profile Deleted</span>
            @endif
        </dd>

        <dt class="col-sm-4">Reporter:</dt>
        <dd class="col-sm-8">
            @if($report->user)
                {{ $report->user->email }}<br>
                <small class="text-muted">User ID: {{ $report->user->id }}</small>
            @else
                <span class="text-muted">User Deleted</span>
            @endif
        </dd>

        <dt class="col-sm-4">Report Type:</dt>
        <dd class="col-sm-8">
            <span class="badge badge-info">{{ ucfirst($report->report_type) }}</span>
        </dd>

        <dt class="col-sm-4">Status:</dt>
        <dd class="col-sm-8">
            @if($report->status == 'pending')
                <span class="badge badge-warning">Pending</span>
            @elseif($report->status == 'reviewed')
                <span class="badge badge-info">Reviewed</span>
            @else
                <span class="badge badge-success">Resolved</span>
            @endif
        </dd>

        <dt class="col-sm-4">Description:</dt>
        <dd class="col-sm-8">{{ $report->description }}</dd>

        <dt class="col-sm-4">Reported At:</dt>
        <dd class="col-sm-8">{{ $report->created_at->format('Y-m-d H:i:s') }}</dd>

        <dt class="col-sm-4">Last Updated:</dt>
        <dd class="col-sm-8">{{ $report->updated_at->format('Y-m-d H:i:s') }}</dd>
    </dl>
</div>
