<div>
@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
        <a class="back-link" href="{{ route('user.account') }}">
            <i class="fa fa-angle-left fa-fw"></i>
            <span class="hidden-xs">Back to Account</span>
        </a>
        <div class="title">
            <h1>
                <a href="{{ route('user.statistics') }}">Statistics</a>
            </h1>
        </div>
    </div>
</div>
@endsection

<style>
.content-wrapper.no-sidebar {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.my-nav.my-listings-nav {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 25px;
    padding: 15px 0;
    border-bottom: 1px solid #333;
}

.my-listing-nav-link-single {
    background-color: #333;
    color: #dca623;
    border: 1px solid #444;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
}

.my-listing-nav-link-single:hover {
    background-color: #444;
    color: #dca623;
    text-decoration: none;
}

.my-listing-new-link {
    background-color: #dca623;
    color: #000;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
}

.my-listing-new-link:hover {
    background-color: #c99520;
    color: #000;
    text-decoration: none;
}

.block {
    background-color: #1a1a1a61;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.block h2 {
    color: #fff;
    font-size: 30px;
    font-weight: 400;
    margin-top: 0;
    padding-bottom: 10px;
}

.block h2.border-bottom {
    border-bottom: 1px solid #444;
    margin-bottom: 15px;
}

.my-stat-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.my-stat {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    font-size: 22px;
}

.my-stat i.fa-envelope2,
.my-stat i.fa-envelope {
    color: #ffffffff;
}

.my-stat i.fa-phone {
    color: #ffffffff;
}

.my-stat i.fa-info-circle {
    color: #f4b827;
    font-size: 22px;
    margin-left: 5px;
    cursor: help;
}

.help-tooltip {
    position: relative;
    cursor: help;
}

.help-tooltip[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    margin-bottom: 5px;
}

.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
    max-width: 100%;
    margin-bottom: 30px;
    background-color: #0000000f;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 15px;
}

.chart-title {
    color: #fff;
    text-align: center;
    font-size: 20px;
    font-weight: 600;
    margin: 30px 0 15px 0;
}

@media (max-width: 768px) {
    .my-nav.my-listings-nav {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .my-stat-list {
        flex-direction: column;
        gap: 15px;
    }
    
    .chart-container {
        height: 300px;
    }
}
</style>

<div class="container-fluid">
    <div class="content-wrapper no-sidebar">
        <div id="content">

            {{-- Stats Cards --}}
            <div class="row">
                <div class="col-lg-6">
                    <div class="block">
                        <h2 class="no-margin-top border-bottom">Last 30 days</h2>
                        <ul class="list-inline my-stat-list">
                            <li class="my-stat">
                                <i class="fa fa-envelope"></i> {{ $last30DaysMessages }}
                            </li>
                            <li class="my-stat help-tooltip1" title="Phone number views (not actual phone calls)">
                                <i class="fa fa-phone"></i> {{ $last30DaysPhoneViews }}<i class="text-primary"><i class="fa fa-info-circle ml-1 fa-x fa-fw"></i></i>
                            </li>
                            <li class="my-stat">{{ $last30DaysViews }} views</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="block">
                        <h2 class="no-margin-top border-bottom">All time</h2>
                        <ul class="list-inline my-stat-list">
                            <li class="my-stat">
                                <i class="fa fa-envelope"></i> {{ $allTimeMessages }}
                            </li>
                            <li class="my-stat help-tooltip" title="Phone number views (not actual phone calls)">
                                <i class="fa fa-phone"></i> {{ $allTimePhoneViews }}<i class="text-primary"><i class="fa fa-info-circle ml-1 fa-x fa-fw"></i></i>
                            </li>
                            <li class="my-stat">{{ $allTimeViews }} views</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Contacts Chart --}}
            <h2 class="chart-title">
                Contacts from {{ $chartStartDate->format('j M Y') }} to {{ $chartEndDate->format('j M Y') }}
            </h2>
            <div class="chart-container" wire:ignore>
                <canvas id="contactsChart"></canvas>
            </div>

            {{-- Views Chart --}}
            <h2 class="chart-title">Profile views</h2>
            <div class="chart-container" wire:ignore>
                <canvas id="viewsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    function initCharts() {
        console.log('Initializing charts...');
        
        // Contacts Chart
        const contactsCtx = document.getElementById('contactsChart');
        console.log('contactsCtx:', contactsCtx);
        
        if (contactsCtx) {
            try {
                const contactsChartData = {!! json_encode($contactsChartData) !!};
                console.log('Contacts Chart Data:', contactsChartData);
                if (contactsChartData && contactsChartData.data) {
                    new Chart(contactsCtx.getContext('2d'), contactsChartData);
                    console.log('Contacts chart created successfully');
                }
            } catch (e) {
                console.error('Error creating contacts chart:', e);
            }
        }

        // Views Chart
        const viewsCtx = document.getElementById('viewsChart');
        console.log('viewsCtx:', viewsCtx);
        
        if (viewsCtx) {
            try {
                const viewsChartData = {!! json_encode($viewsChartData) !!};
                console.log('Views Chart Data:', viewsChartData);
                if (viewsChartData && viewsChartData.data) {
                    new Chart(viewsCtx.getContext('2d'), viewsChartData);
                    console.log('Views chart created successfully');
                }
            } catch (e) {
                console.error('Error creating views chart:', e);
            }
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        // Small delay to ensure canvas is ready
        setTimeout(initCharts, 100);
    }
})();
</script>
</div>
