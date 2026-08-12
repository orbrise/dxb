<tr data-run-id="{{ $run->id }}" data-terminal="{{ $run->isTerminal() ? '1' : '0' }}">
    <td>{{ $run->id }}</td>
    <td>{{ $sources[$run->source]['label'] ?? $run->source }}</td>
    <td>{{ $run->city_slug }}</td>
    <td style="min-width:160px;">
        <div class="scraper-progress-bar">
            <div class="scraper-progress-fill js-progress-fill" style="width: {{ $run->progressPercent() }}%"></div>
        </div>
        <small class="js-progress-text">{{ $run->progress_current }} / {{ $run->progress_total ?: $run->requested_count }} ({{ $run->progressPercent() }}%)</small>
        <div><small class="text-muted js-stage">{{ $run->progress_stage }}</small></div>
    </td>
    <td>
        <span class="scraper-status-badge scraper-status-{{ $run->status }} js-status">{{ $run->status }}</span>
    </td>
    <td>
        <small>{{ optional($run->started_at ?: $run->created_at)->diffForHumans() }}</small>
    </td>
    <td>
        <button class="btn btn-sm btn-outline-secondary js-log-btn" data-run-id="{{ $run->id }}" title="View log">
            <i class="fa fa-file-text-o"></i> Log
        </button>
        <div class="scraper-log-viewer" id="scraper-log-{{ $run->id }}"></div>
    </td>
</tr>
