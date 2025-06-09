@extends('admin.layout.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/vendor/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
@endpush

@section('content')
<section class="content-body">
    <div class="container-fluid position-relative">
        <div class="d-flex flex-wrap align-items-center justify-content-between text-head">
           <h2 class="mb-3 me-auto applaud">Admin Dashboard</h2>
        </div>

        <!-- ============================================
             Summary Cards (dynamic: 5 metrics total)
             ============================================ -->
        <div class="row">

            <!-- Total Payments -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-secondary text-secondary">
                                <i class="fal fa-credit-card"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-0">Total Payments</p>
                                <h4 class="mb-0">₹ {{ number_format($totalPayments, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Payment Collection -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-warning text-warning">
                                <i class="fal fa-calendar-day"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-0">Today's Collection</p>
                                <h4 class="mb-0">₹ {{ number_format($todayPayment, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- This Month's Payment Collection -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12 mt-4 mt-lg-0">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-info text-info">
                                <i class="fal fa-calendar-alt"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-0">Monthly Collection</p>
                                <h4 class="mb-0">₹ {{ number_format($monthPayment, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-primary text-primary">
                                <i class="fal fa-users"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-0">Total Users</p>
                                <h4 class="mb-0">{{ number_format($totalUsers) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Add-Ons -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-success text-success">
                                <i class="fal fa-puzzle-piece"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-0">Total Add-Ons</p>
                                <h4 class="mb-0">{{ number_format($totalProducts) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var subscriberChart   = echarts.init(document.getElementById('subscriberChart'));
            var notificationsChart = echarts.init(document.getElementById('notificationsChart'));

            var subscriberOption = {
                tooltip: { trigger: 'axis' },
                xAxis: { type: 'category', data: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] },
                yAxis: { type: 'value' },
                series: [{
                    name: 'Subscribers',
                    data: [120,200,150,80,70,110,130],
                    type: 'line',
                    smooth: true,
                    areaStyle: { color: 'rgba(0,123,255,0.2)' },
                    lineStyle: { color: 'rgba(0,123,255,1)', width: 3 },
                    itemStyle: { color: 'rgba(0,123,255,1)' },
                    symbolSize: 8
                }]
            };

            var notificationsOption = {
                tooltip: { trigger: 'axis' },
                xAxis: { type: 'category', data: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] },
                yAxis: { type: 'value' },
                series: [{
                    name: 'Notifications Sent',
                    data: [300,450,400,350,300,500,550],
                    type: 'bar',
                    itemStyle: { color: 'rgba(40,167,69,1)' },
                    barWidth: '50%'
                }]
            };

            subscriberChart.setOption(subscriberOption);
            notificationsChart.setOption(notificationsOption);

            window.addEventListener('resize', function() {
                subscriberChart.resize();
                notificationsChart.resize();
            });
        });
    </script>
@endpush