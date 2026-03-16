<div>
@push('css')
<style>
/* Sub-header bar */
.stats-subheader {
    background: #131616;
    padding: 12px 0;
}
.stats-subheader .ev-container {
    display: flex;
    align-items: center;
    position: relative;
}
.stats-subheader .back-link {
    color: #C1F11D;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.stats-subheader .back-link:hover {
    color: #d4f84d;
}
.stats-subheader .page-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    white-space: nowrap;
}

/* Stats page */
.stats-page {
    background: #0a0a0a;
    min-height: 100vh;
    padding: 30px 0 60px;
}

/* Profile nav tabs */
.stats-profile-nav {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 28px;
}
.stats-profile-tab {
    background: #1a1a1a;
    color: #fff;
    border: 1px solid #2a2a2a;
    padding: 10px 20px;
    border-radius: 22px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}
.stats-profile-tab:hover {
    background: #222;
    border-color: #C1F11D;
    color: #fff;
    text-decoration: none;
}
.stats-add-profile {
    background: #C1F11D;
    color: #000;
    border: none;
    padding: 10px 20px;
    border-radius: 22px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}
.stats-add-profile:hover {
    background: #d4f84d;
    color: #000;
    text-decoration: none;
}

/* Stats cards */
.stats-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 32px;
}
@media (max-width: 768px) {
    .stats-cards {
        grid-template-columns: 1fr;
    }
}
.stats-card {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    padding: 24px;
}
.stats-card-title {
    color: #fff;
    font-size: 22px;
    font-weight: 400;
    margin: 0 0 16px 0;
    padding-bottom: 14px;
    border-bottom: 1px solid #2a2a2a;
}
.stats-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
}
.stat-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    font-size: 18px;
}
.stat-item svg {
    color: #fff;
    opacity: 0.8;
}
.stat-info-icon {
    color: #C1F11D;
    cursor: help;
    position: relative;
}
.stat-info-icon svg {
    color: #C1F11D;
    opacity: 1;
}

/* Chart sections */
.chart-section-title {
    color: #fff;
    text-align: center;
    font-size: 18px;
    font-weight: 500;
    margin: 36px 0 16px 0;
}
.chart-box {
    position: relative;
    height: 400px;
    width: 100%;
    background: #111;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
}
@media (max-width: 768px) {
    .chart-box {
        height: 300px;
    }
    .stats-profile-nav {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush

{{-- Sub-header --}}
<div class="stats-subheader">
    <div class="ev-container">
        <a class="back-link" href="{{ route('user.account') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            My profile
        </a>
        <h1 class="page-title">My Statistics</h1>
    </div>
</div>

{{-- Main content --}}
<div class="stats-page">
    <div class="ev-container">

        {{-- Stats Cards --}}
        <div class="stats-cards">
            <div class="stats-card">
                <h2 class="stats-card-title">Last 30 days</h2>
                <div class="stats-row">
                    <span class="stat-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        {{ $last30DaysPhoneViews }}
                    </span>
                    <span class="stat-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        {{ $last30DaysMessages }}
                    </span>
                    <span class="stat-info-icon" title="Phone number views (not actual phone calls)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </span>
                    <span class="stat-item">{{ $last30DaysViews }} views</span>
                </div>
            </div>

            <div class="stats-card">
                <h2 class="stats-card-title">All time</h2>
                <div class="stats-row">
                    <span class="stat-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        {{ $allTimePhoneViews }}
                    </span>
                    <span class="stat-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        {{ $allTimeMessages }}
                    </span>
                    <span class="stat-info-icon" title="Phone number views (not actual phone calls)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </span>
                    <span class="stat-item">{{ $allTimeViews }} views</span>
                </div>
            </div>
        </div>

        {{-- Contacts Chart --}}
        <h2 class="chart-section-title">
            Contacts from {{ $chartStartDate->format('d M Y') }} to {{ $chartEndDate->format('d M Y') }}
        </h2>
        <div class="chart-box" wire:ignore>
            <canvas id="contactsChart"></canvas>
        </div>

        {{-- Views Chart --}}
        <h2 class="chart-section-title">Profile views</h2>
        <div class="chart-box" wire:ignore>
            <canvas id="viewsChart"></canvas>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    function initCharts() {
        const contactsCtx = document.getElementById('contactsChart');
        if (contactsCtx) {
            try {
                const contactsChartData = {!! json_encode($contactsChartData) !!};
                if (contactsChartData && contactsChartData.data) {
                    new Chart(contactsCtx.getContext('2d'), contactsChartData);
                }
            } catch (e) {
                console.error('Error creating contacts chart:', e);
            }
        }

        const viewsCtx = document.getElementById('viewsChart');
        if (viewsCtx) {
            try {
                const viewsChartData = {!! json_encode($viewsChartData) !!};
                if (viewsChartData && viewsChartData.data) {
                    new Chart(viewsCtx.getContext('2d'), viewsChartData);
                }
            } catch (e) {
                console.error('Error creating views chart:', e);
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        setTimeout(initCharts, 100);
    }
})();
</script>
@endpush
</div>
