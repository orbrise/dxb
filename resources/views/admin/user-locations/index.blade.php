@extends('admin.layout.master')

@push('css')
<style>
/* ===== User Locations ===== */
.ul-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
.ul-stat {
    position: relative; overflow: hidden;
    border-radius: 14px; padding: 16px 18px; color: #fff;
    box-shadow: 0 6px 24px rgba(15,23,42,.08);
}
.ul-stat .ul-stat-num { font-size: 24px; font-weight: 700; line-height: 1.1; }
.ul-stat .ul-stat-label { font-size: 12px; opacity: .9; margin-top: 4px; text-transform: uppercase; letter-spacing: .05em; }
.ul-stat i { position: absolute; right: 14px; bottom: 10px; font-size: 38px; opacity: .18; }
.ul-stat.s1 { background: linear-gradient(135deg, #6366f1, #4f46e5); }
.ul-stat.s2 { background: linear-gradient(135deg, #10b981, #059669); }
.ul-stat.s3 { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.ul-stat.s4 { background: linear-gradient(135deg, #f59e0b, #d97706); }
@media (max-width: 767px) { .ul-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }

.ul-filters label {
    font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
    color: #64748b; margin: 0 0 6px 2px; display: block;
}
.ul-filters label i { color: #6366f1; margin-right: 4px; }
.ul-filters .form-control { height: 42px; border-radius: 9px; }
.ul-filters .ul-actions { display: flex; gap: 8px; align-items: flex-end; }

.ul-user {
    display: flex; flex-wrap: wrap; align-items: center; gap: 10px 18px;
    padding: 14px 18px; margin-bottom: 18px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
}
.ul-user-name { font-weight: 700; color: #1e293b; }
.ul-user-email { color: #64748b; font-size: 13px; }
.ul-user-countries { display: flex; flex-wrap: wrap; gap: 6px; margin-left: auto; }

.ul-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 999px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
}
.ul-chip.register       { background: #e0e7ff; color: #3730a3; }
.ul-chip.login          { background: #d1fae5; color: #065f46; }
.ul-chip.profile_create { background: #cffafe; color: #155e75; }
.ul-chip.profile_update { background: #fef3c7; color: #92400e; }
.ul-chip.country        { background: #f1f5f9; color: #334155; font-weight: 500; }

.ul-table td, .ul-table th { vertical-align: middle; font-size: 13px; }
.ul-table th { font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #64748b; white-space: nowrap; }
.ul-table a.ul-link { color: #4f46e5; }
.ul-table .ul-ip { font-family: SFMono-Regular, Consolas, monospace; font-size: 12px; }
.ul-table .ul-ua { color: #64748b; font-size: 12px; max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ul-table .ul-time { white-space: nowrap; }
.ul-table .ul-time small { display: block; color: #94a3b8; }
</style>
@include('admin._partials.settings-modern')
@endpush

@section('content')
@php
    // Short device label from the user agent, e.g. "Chrome · Windows".
    $device = function (?string $ua) {
        if (!$ua) return null;
        $browser = collect(['Edg' => 'Edge', 'OPR' => 'Opera', 'SamsungBrowser' => 'Samsung Internet', 'Chrome' => 'Chrome', 'Firefox' => 'Firefox', 'Safari' => 'Safari'])
            ->first(fn ($name, $needle) => str_contains($ua, $needle));
        $os = collect(['iPhone' => 'iPhone', 'iPad' => 'iPad', 'Android' => 'Android', 'Windows' => 'Windows', 'Mac OS' => 'Mac', 'Linux' => 'Linux'])
            ->first(fn ($name, $needle) => str_contains($ua, $needle));
        return collect([$browser, $os])->filter()->implode(' · ') ?: 'Other';
    };
@endphp
<div class="s-modern">
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">User Locations</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Where users registered, logged in, created and edited profiles from</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Users</a></li>
            <li class="breadcrumb-item active">User Locations</li>
        </ol>
    </div>
</div>

<div class="ul-stats">
    <div class="ul-stat s1"><div class="ul-stat-num">{{ number_format($stats->events ?? 0) }}</div><div class="ul-stat-label">Events</div><i class="fas fa-history"></i></div>
    <div class="ul-stat s2"><div class="ul-stat-num">{{ number_format($stats->users ?? 0) }}</div><div class="ul-stat-label">Users</div><i class="fas fa-users"></i></div>
    <div class="ul-stat s3"><div class="ul-stat-num">{{ number_format($stats->ips ?? 0) }}</div><div class="ul-stat-label">IP addresses</div><i class="fas fa-network-wired"></i></div>
    <div class="ul-stat s4"><div class="ul-stat-num">{{ number_format($stats->countries ?? 0) }}</div><div class="ul-stat-label">Countries</div><i class="fas fa-globe"></i></div>
</div>

@if($user)
    <div class="ul-user">
        <div>
            <div class="ul-user-name"><i class="fas fa-user-circle text-muted"></i> {{ $user->name ?: 'User #' . $user->id }}</div>
            <div class="ul-user-email">{{ $user->email }} · #{{ $user->id }}</div>
        </div>
        <div class="ul-user-countries">
            @forelse($userCountries as $c)
                <span class="ul-chip country" title="Last seen {{ \Carbon\Carbon::parse($c->last_seen)->format('d M Y, h:i A') }}">
                    <i class="fas fa-map-marker-alt"></i> {{ $c->country }} × {{ $c->times }}
                </span>
            @empty
                <span class="text-muted small">No locations recorded yet.</span>
            @endforelse
        </div>
        <a href="{{ route('admin.user-locations') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i> All users</a>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.user-locations') }}" class="ul-filters">
            @if($filters['user_id'])<input type="hidden" name="user_id" value="{{ $filters['user_id'] }}">@endif
            <div class="row">
                <div class="col-md-3 form-group">
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Name, email, IP or profile">
                </div>
                <div class="col-md-2 form-group">
                    <label><i class="fas fa-bolt"></i> Event</label>
                    <select name="event" class="form-control">
                        <option value="">All events</option>
                        @foreach($events as $key => $label)
                            <option value="{{ $key }}" @selected($filters['event'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label><i class="fas fa-globe"></i> Country</label>
                    <select name="country" class="form-control">
                        <option value="">All countries</option>
                        @foreach($countries as $c)
                            <option value="{{ $c }}" @selected($filters['country'] === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label><i class="fas fa-network-wired"></i> IP</label>
                    <input type="text" name="ip" value="{{ $filters['ip'] }}" class="form-control" placeholder="Exact IP">
                </div>
                <div class="col-md-3 form-group ul-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.user-locations', $filters['user_id'] ? ['user_id' => $filters['user_id']] : []) }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table ul-table mb-0">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Profile</th>
                        <th>Country</th>
                        <th>IP</th>
                        <th>Device</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ul-time">
                                @if($log->created_at)
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                                    <small>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</small>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <a class="ul-link" href="{{ route('admin.user-locations', ['user_id' => $log->user_id]) }}" title="All locations for this user">
                                    {{ $log->user_name ?: 'User #' . $log->user_id }}
                                </a>
                                <div class="text-muted small">{{ $log->user_email ?? 'deleted user' }}</div>
                            </td>
                            <td><span class="ul-chip {{ $log->event }}">{{ $events[$log->event] ?? $log->event }}</span></td>
                            <td>
                                @if($log->profile_id)
                                    <a class="ul-link" href="{{ route('admin.profiles.edit', $log->profile_id) }}">{{ $log->profile_name ?: 'Profile #' . $log->profile_id }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($log->country)
                                    <a class="ul-chip country" href="{{ request()->fullUrlWithQuery(['country' => $log->country, 'page' => null]) }}">{{ $log->country }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($log->ip)
                                    <a class="ul-link ul-ip" href="{{ route('admin.user-locations', ['ip' => $log->ip]) }}" title="Everyone seen on this IP">{{ $log->ip }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="ul-ua" title="{{ $log->user_agent }}">{{ $device($log->user_agent) ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No locations match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="mt-3 d-flex justify-content-end">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
</div>
@endsection
