@extends('admin.layout.master')

@section('content')

   <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Dashboard</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">statistics, charts, events and reports</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="row justify-content-center mt-3">

<div class="col-md-3 col-sm-6 widget-holder widget-full-height">
                        <div class="widget-bg bg-primary text-inverse" style="background-color: #fb8c00 !important">
                            <div class="widget-body">
                            <a style="color:black; text-decoration:none" href="{{ route('admin.users') }}">
                                <div class="widget-counter">
                                    <h6>Total Users <small class="text-inverse"></small></h6>
                                    <h3 class="h1"><span class="counter">{{ $stats['total_users'] }}</span></h3><i class="material-icons list-icon">group</i>
                                </div>
                                </a>
                                <!-- /.widget-counter -->
                            </div>
                            <!-- /.widget-body -->
                        </div>
                        <!-- /.widget-bg -->
                    </div>

    <!--end col-->
    <div class="col-md-3 col-sm-6 widget-holder widget-full-height">
                        <div class="widget-bg bg-color-scheme text-inverse" style="background-color: #0acc95 !important">
                            <div class="widget-body clearfix">
                            <a style="color:black; text-decoration:none" href="{{ route('admin.profiles.index') }}">
                                <div class="widget-counter">
                                    <h6>Total Profiles <small class="text-inverse"></small></h6>
                                    <h3 class="h1"><span class="counter">{{ $stats['total_profiles'] }}</span></h3><i class="material-icons list-icon">face</i>
                                </div>
                                </a>
                                <!-- /.widget-counter -->
                            </div>
                            <!-- /.widget-body -->
                        </div>
                        <!-- /.widget-bg -->
                    </div>


    <div class="col-md-3 col-sm-6 widget-holder widget-full-height">
                        <div class="widget-bg" style="background-color: #186dde !important; color:white">
                            <div class="widget-body clearfix">
                             <a style="color:white" href="{{ url('admin/profiles?id=&start_date=&end_date=&title=&city=&status=1&premium=') }}">

                                <div class="widget-counter" style="color:white">
                                    <h6 style="color:white">Active Profiles <small></small></h6>
                                    <h3 class="h1" style="color:white"><span class="counter">{{ $stats['active_profiles'] }}</span></h3><i class="material-icons list-icon">wc</i>
                                </div>
                                </a>
                                <!-- /.widget-counter -->
                            </div>
                            <!-- /.widget-body -->
                        </div>
                        <!-- /.widget-bg -->
                    </div>


<div class="col-md-3 col-sm-6 widget-holder widget-full-height">
                        <div class="widget-bg" style="background-color: #2f3d4a !important; color:white">
                            <div class="widget-body clearfix">
                             <a style="color:white" href="{{ url('admin/profiles?id=&start_date=&end_date=&title=&city=&status=0&premium=') }}">
                                <div class="widget-counter">
                                    <h6 style="color:white">Inactive Profiles <small></small></h6>
                                    <h3 class="h1" style="color:white"><span class="counter">{{ $stats['inactive_profiles'] }}</span></h3><i class="material-icons list-icon">mood_bad</i>
                                </div>
                                </a>
                                <!-- /.widget-counter -->
                            </div>
                            <!-- /.widget-body -->
                        </div>
                        <!-- /.widget-bg -->
                    </div>
                  
</div>



<div class="row">
<div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Profile Statistics</h5>
            </div>
            <div class="card-body">
                <div id="profileChart" class="apex-charts"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">User Statistics</h5>
            </div>
            <div class="card-body">
                <div id="userChart" class="apex-charts"></div>
            </div>
        </div>
    </div>
    
</div>

<div class="row justify-content-center mt-3 mb-3">
    <div class="col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">                      
                        <h5 class="card-title">Latest Profiles</h5>                      
                    </div><!--end col-->
                    <div class="col-auto">
                        <a href="{{ url('admin/profiles') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                   
                </div>  <!--end row-->                                  
            </div><!--end card-header-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-top-0">ID</th>
                                <th class="border-top-0">Title</th>
                                <th class="border-top-0">Gender</th>
                                <th class="border-top-0">Status</th>
                                <th class="border-top-0">Created At</th>
                            </tr><!--end tr-->
                        </thead>
                        <tbody>

                            @foreach($latest_profiles  as $profile)

                            <tr>                                                        
                                <td>
                                    {{$profile->id}}
                                </td>
                                <td><a href="/{{ strtolower($profile->ggender->name) }}-escorts-in-{{ strtolower($profile->gcity->name) }}/{{ $profile->id }}/{{ $profile->slug }}" target="_blank">{{$profile->name}}</a></td>

                                <td>{{$profile->ggender->name}}</td>                                  
                                <td><span class="badge {{ $profile->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $profile->is_active ? 'Active' : 'Inactive' }}
                                </span></td>
                                <td>{{ $profile->created_at->format('d M Y') }}</td>
                            </tr><!--end tr-->     
                            
                               @endforeach
                        
                                                      
                        </tbody>
                    </table> <!--end table-->                                               
                </div><!--end /div-->
            </div><!--end card-body--> 
        </div><!--end card--> 
    </div> <!--end col--> 
    <div class="col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                                      <div class="col">
                        <h5 class="card-title">Latest Users</h5>                      
                    
                </div>  <!--end row-->         
                
                <div class="col-auto">
                    <a href="{{ url('admin/users') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                </div>

            </div><!--end card-header-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-top-0">ID</th>
                                <th class="border-top-0">name</th>
                                <th class="border-top-0">Email</th>
                                <th class="border-top-0">Created At</th>
                            </tr><!--end tr-->
                        </thead>
                        <tbody>

                            @foreach($latest_users as $user)

                            <tr>                                                        
                                <td>
                                    {{$user->id}}
                                </td>
                                <td>{{$user->name}}</td>                                 
                                <td>{{$user->email }}</td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr><!--end tr-->     
                            
                               @endforeach
                        
                                                      
                        </tbody>
                    </table> <!--end table-->                                                 
                </div><!--end /div-->                           
            </div><!--end card-body--> 
        </div><!--end card--> 
    </div> <!--end col--> 
               
</div><!--end row-->



    
@endsection

@push('js')
<script src="{{smart_asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{smart_asset('assets/js/pages/index.init.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var userOptions = {
    series: [{
        name: 'Active Users',
        data: Object.values(@json($activeUserStats))
    }, {
        name: 'Inactive Users',
        data: Object.values(@json($inactiveUserStats))
    }],
    chart: {
        type: 'bar',
        height: 350,
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
                reset: true
            }
        }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '65%',
            endingShape: 'rounded',
            dataLabels: {
                position: 'top'
            }
        }
    },
    colors: ['#007bff', '#6c757d'],
    dataLabels: {
        enabled: true,
        offsetY: -20,
        style: {
            fontSize: '11px',
            colors: ["#304758"]
        },
        formatter: function (val) {
            return val > 0 ? val : '';
        }
    },
    xaxis: {
        categories: Object.keys(@json($activeUserStats)),
        labels: {
            rotate: 0,
            style: {
                fontSize: '11px'
            },
            hideOverlappingLabels: true,
            showDuplicates: false
        },
        tickAmount: 5,
        tickPlacement: 'between',
        title: {
            text: ''
        }
    },
    yaxis: {
        title: {
            text: 'Number of Users'
        },
        min: 0
    },
    title: {
        text: 'Daily User Registration (Active vs Inactive)',
        align: 'center',
        style: {
            fontSize: '16px'
        }
    },
    legend: {
        show: true,
        position: 'top'
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + " users"
            }
        }
    }
};
    
        var profileOptions = {
            series: [{
                name: 'Active Profiles',
                data: Object.values(@json($activeProfileStats))
            }, {
                name: 'Inactive Profiles',
                data: Object.values(@json($inactiveProfileStats))
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '65%',
                    endingShape: 'rounded',
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            colors: ['#007bff', '#6c757d'],
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '11px',
                    colors: ["#304758"]
                },
                formatter: function (val) {
                    return val > 0 ? val : '';
                }
            },
            xaxis: {
                categories: Object.keys(@json($activeProfileStats)),
                labels: {
                    rotate: 0,
                    style: {
                        fontSize: '11px'
                    },
                    hideOverlappingLabels: true,
                    showDuplicates: false
                },
                tickAmount: 5,
                tickPlacement: 'between',
                title: {
                    text: ''
                }
            },
            yaxis: {
                title: {
                    text: 'Number of Profiles'
                },
                min: 0
            },
            title: {
                text: 'Daily Profile Creation (Active vs Inactive)',
                align: 'center',
                style: {
                    fontSize: '16px'
                }
            },
            legend: {
                show: true,
                position: 'top'
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " profiles"
                    }
                }
            }
        };
    
        new ApexCharts(document.querySelector("#userChart"), userOptions).render();
        new ApexCharts(document.querySelector("#profileChart"), profileOptions).render();
    });
    </script>
@endpush