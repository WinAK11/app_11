@extends('layouts.admin')
@section('content')
    <style>
        .equal-height-row {
            display: flex;
            flex-wrap: wrap;
        }
        .equal-height-row > [class*='col-'] {
            display: flex;
            flex-direction: column;
        }
        .left-column .row {
            display: flex;
            /* flex-direction: column; */
            /* align-items: center; */
            flex-grow: 1;
        }
        .left-column .col-md-6 {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .wg-chart-default, .wg-box {
            flex-grow: 1;
        }
    </style>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="tf-section mb-30">
                <div class="row equal-height-row">
                    <div class="col-lg-6 col-md-12 left-column">
                        <div class="mb-3">
                            <button class="btn btn-primary" id="btn-year">This year</button>
                            <button class="btn btn-secondary" id="btn-current-month">This month</button>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-calendar"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Month Revenue</div>
                                            <h4 id="stat-month">{{ number_format($revenueComparison['month']['current'], 0, ',', ',') }} đ</h4>
                                            <p class="text-tiny {{ $revenueComparison['month']['percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $revenueComparison['month']['percentage'] >= 0 ? '▲' : '▼' }}
                                                {{ number_format(abs($revenueComparison['month']['percentage']), 2) }}% compared to last month
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-calendar"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Quarter Revenue</div>
                                            <h4 id="stat-quarter">{{ number_format($revenueComparison['quarter']['current'], 0, ',', ',') }} đ</h4>
                                            <p class="text-tiny {{ $revenueComparison['quarter']['percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $revenueComparison['quarter']['percentage'] >= 0 ? '▲' : '▼' }}
                                                {{ number_format(abs($revenueComparison['quarter']['percentage']), 2) }}% compared to last quarter
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-shopping-bag"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Total Orders</div>
                                            <h4 id="stat-total-orders">{{ $dashboardDatas[0]->Total }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-dollar-sign"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Total Amount</div>
                                            <h4 id="stat-total-amount">{{ number_format($dashboardDatas[0]->TotalAmount, 0, ',', ',') }} đ</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-shopping-bag"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Pending Orders</div>
                                            <h4 id="stat-pending-orders">{{ $dashboardDatas[0]->TotalOrdered }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-dollar-sign"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Pending Orders Amount</div>
                                            <h4 id="stat-pending-amount">{{ number_format($dashboardDatas[0]->TotalOrderedAmount, 0, ',', ',') }} đ</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-shopping-bag"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Delivered Orders</div>
                                            <h4 id="stat-delivered-orders">{{ $dashboardDatas[0]->TotalDelivered }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-dollar-sign"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Delivered Orders Amount</div>
                                            <h4 id="stat-delivered-amount">{{ number_format($dashboardDatas[0]->TotalDeliveredAmount, 0, ',', ',') }} đ</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-shopping-bag"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Canceled Orders</div>
                                            <h4 id="stat-canceled-orders">{{ $dashboardDatas[0]->TotalCanceled }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="wg-chart-default">
                                    <div class="flex items-center gap14">
                                        <div class="image ic-bg">
                                            <i class="icon-dollar-sign"></i>
                                        </div>
                                        <div>
                                            <div class="body-text mb-2">Canceled Orders Amount</div>
                                            <h4 id="stat-canceled-amount">{{ number_format($dashboardDatas[0]->TotalCanceledAmount, 0, ',', ',') }} đ</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 d-flex flex-column">
                        <div class="wg-box mb-4">
                            <div class="flex items-center justify-between">
                                <h5 id="chart-title">Monthly Revenue</h5>
                                <button id="back-to-monthly" class="btn btn-secondary" style="display: none;">Back</button>
                            </div>
                            <div id="revenue-chart"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="wg-box">
                                    <div class="flex items-center justify-between">
                                        <h5>Top Products</h5>
                                    </div>
                                    <div id="top-products-chart"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="wg-box">
                                    <div class="flex items-center justify-between">
                                        <h5>Top Categories</h5>
                                    </div>
                                    <div id="top-categories-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tf-section mb-30">
                <div class="wg-box">
                    <div class="flex items-center justify-between">
                        <h5>Recent orders</h5>
                        <div class="dropdown default">
                            <a class="btn btn-secondary dropdown-toggle" href="{{ route('admin.orders') }}">
                                <span class="view-all">View all</span>
                            </a>
                        </div>
                    </div>
                    <div class="wg-table table-all-user">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:70px">OrderNo</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Phone</th>
                                        <th class="text-center">Subtotal</th>
                                        <th class="text-center">Tax</th>
                                        <th class="text-center">Total</th>

                                        <th class="text-center">Status</th>
                                        <th class="text-center">Order Date</th>
                                        <th class="text-center">Total Items</th>
                                        <th class="text-center">Delivered On</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td class="text-center">{{ $order->id }}</td>
                                            <td class="text-center">{{ $order->name }}</td>
                                            <td class="text-center">{{ $order->phone }}</td>
                                            <td class="text-center">{{ number_format($order->subtotal, 0, ',', ',') }} đ</td>
                                            <td class="text-center">{{ number_format($order->tax, 0, ',', ',') }} đ</td>
                                            <td class="text-center">{{ number_format($order->total, 0, ',', ',') }} đ</td>

                                            <td class="text-center">
                                                @if ($order->status == 'delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif($order->status == 'canceled')
                                                    <span class="badge bg-danger">Canceled</span>
                                                @else
                                                    <span class="badge bg-warning">Ordered</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $order->created_at }}</td>
                                            <td class="text-center">{{ $order->orderItems->count() }}</td>
                                            <td class="text-center">{{ $order->delivered_date }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.order.details', ['order_id' => $order->id]) }}">
                                                    <div class="list-icon-function view-icon">
                                                        <div class="item eye">
                                                            <i class="icon-eye"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        (function($) {
            let monthlyChart;
            let weeklyChart;
            const chartElement = document.querySelector("#revenue-chart");
            const chartTitle = document.getElementById('chart-title');
            const backButton = document.getElementById('back-to-monthly');
            const monthlyData = @json($monthlyDatas);
            let selectedMonthIndex = null;

            function updateMonthlySummary(monthIndex) {
                updateLeftStats(monthIndex);
            }

            function updateLeftStats(monthIndex) {
                if (monthIndex == -1) {
                    let now = new Date();
                    let currentMonthIndex = now.getMonth();
                    const month = monthlyData[currentMonthIndex];

                    let prevMonth = monthlyData[currentMonthIndex - 1] || {TotalAmount: 0};
                    let monthDiff = month.TotalAmount - prevMonth.TotalAmount;
                    let monthPercentageChange = prevMonth.TotalAmount > 0 ? (monthDiff / prevMonth.TotalAmount) * 100 : (month.TotalAmount > 0 ? 100 : 0);

                    let currentQuarter = Math.floor(currentMonthIndex / 3);
                    let prevQuarterIndex = (currentQuarter - 1) * 3;
                    let quarterTotal = 0, prevQuarterTotal = 0;
                    for (let i = currentQuarter * 3; i < currentQuarter * 3 + 3; i++) {
                        if (monthlyData[i]) quarterTotal += Number(monthlyData[i].TotalAmount) || 0;
                        console.log(monthlyData[i].TotalAmount)
                    }
                    for (let i = prevQuarterIndex; i < prevQuarterIndex + 3; i++) {
                        if (monthlyData[i]) prevQuarterTotal += Number(monthlyData[i].TotalAmount) || 0;
                        console.log(monthlyData[i].TotalAmount)
                    }
                    let quarterDiff = quarterTotal - prevQuarterTotal;
                    let quarterPercentageChange = prevQuarterTotal > 0 ? (quarterDiff / prevQuarterTotal) * 100 : (quarterTotal > 0 ? 100 : 0);

                    document.getElementById('stat-month').innerText = Number(month.TotalAmount || 0).toLocaleString('vi-VN') + ' đ';
                    let monthCompareElem = document.getElementById('stat-month').nextElementSibling;
                    monthCompareElem.innerHTML =
                        `${monthPercentageChange >= 0 ? '▲' : '▼'} ${Math.abs(monthPercentageChange).toFixed(2)}% compared to last month`;
                    monthCompareElem.classList.remove('text-success', 'text-danger');
                    monthCompareElem.classList.add(monthPercentageChange >= 0 ? 'text-success' : 'text-danger');

                    document.getElementById('stat-quarter').innerText = Number(quarterTotal || 0).toLocaleString('vi-VN') + ' đ';
                    let quarterCompareElem = document.getElementById('stat-quarter').nextElementSibling;
                    quarterCompareElem.innerHTML =
                        `${quarterPercentageChange >= 0 ? '▲' : '▼'} ${Math.abs(quarterPercentageChange).toFixed(2)}% compared to last quarter`;
                    quarterCompareElem.classList.remove('text-success', 'text-danger');
                    quarterCompareElem.classList.add(quarterPercentageChange >= 0 ? 'text-success' : 'text-danger');

                    let totalOrders = 0, totalAmount = 0, totalOrdered = 0, totalOrderedAmount = 0,
                        totalDelivered = 0, totalDeliveredAmount = 0, totalCanceled = 0, totalCanceledAmount = 0;
                    monthlyData.forEach(m => {
                        totalOrders += Number(m.TotalOrders) || 0;
                        totalAmount += Number(m.TotalAmount) || 0;
                        totalOrdered += Number(m.TotalOrdered) || 0;
                        totalOrderedAmount += Number(m.TotalOrderedAmount) || 0;
                        totalDelivered += Number(m.TotalDelivered) || 0;
                        totalDeliveredAmount += Number(m.TotalDeliveredAmount) || 0;
                        totalCanceled += Number(m.TotalCanceled) || 0;
                        totalCanceledAmount += Number(m.TotalCanceledAmount) || 0;
                    });

                    document.getElementById('stat-total-orders').innerText = totalOrders;
                    document.getElementById('stat-total-amount').innerText = totalAmount.toLocaleString('vi-VN') + ' đ';
                    document.getElementById('stat-pending-orders').innerText = totalOrdered;
                    document.getElementById('stat-pending-amount').innerText = totalOrderedAmount.toLocaleString('vi-VN') + ' đ';
                    document.getElementById('stat-delivered-orders').innerText = totalDelivered;
                    document.getElementById('stat-delivered-amount').innerText = totalDeliveredAmount.toLocaleString('vi-VN') + ' đ';
                    document.getElementById('stat-canceled-orders').innerText = totalCanceled;
                    document.getElementById('stat-canceled-amount').innerText = totalCanceledAmount.toLocaleString('vi-VN') + ' đ';

                }
                else
                {
                    const month = monthlyData[monthIndex];

                    let prevMonth;
                    if (monthIndex === 0) {
                        prevMonth = { TotalAmount: 0 };
                    } else {
                        prevMonth = monthlyData[monthIndex - 1] || { TotalAmount: 0 };
                    }
                    let monthDiff = month.TotalAmount - prevMonth.TotalAmount;
                    let monthPercentageChange = prevMonth.TotalAmount > 0 ? (monthDiff / prevMonth.TotalAmount) * 100 : (month.TotalAmount > 0 ? 100 : 0);

                    let currentQuarter = Math.floor(monthIndex / 3);
                    let quarterTotal = 0, prevQuarterTotal = 0;

                    // Calculate current quarter total
                    for (let i = currentQuarter * 3; i < currentQuarter * 3 + 3; i++) {
                        if (monthlyData[i]) quarterTotal += Number(monthlyData[i].TotalAmount) || 0;
                    }

                    if (currentQuarter > 0) {
                        let prevQuarterIndex = (currentQuarter - 1) * 3;
                        for (let i = prevQuarterIndex; i < prevQuarterIndex + 3; i++) {
                            if (monthlyData[i]) prevQuarterTotal += Number(monthlyData[i].TotalAmount) || 0;
                        }
                    }

                    let quarterDiff = quarterTotal - prevQuarterTotal;
                    let quarterPercentageChange = prevQuarterTotal > 0 ? (quarterDiff / prevQuarterTotal) * 100 : (quarterTotal > 0 ? 100 : 0);

                    document.getElementById('stat-month').innerText = Number(month.TotalAmount || 0).toLocaleString('vi-VN') + ' đ';
                    let monthCompareElem = document.getElementById('stat-month').nextElementSibling;
                    monthCompareElem.innerHTML =
                        `${monthPercentageChange >= 0 ? '▲' : '▼'} ${Math.abs(monthPercentageChange).toFixed(2)}% compared to last month`;
                    monthCompareElem.classList.remove('text-success', 'text-danger');
                    monthCompareElem.classList.add(monthPercentageChange >= 0 ? 'text-success' : 'text-danger');

                    document.getElementById('stat-quarter').innerText = Number(quarterTotal || 0).toLocaleString('vi-VN') + ' đ';
                    let quarterCompareElem = document.getElementById('stat-quarter').nextElementSibling;
                    quarterCompareElem.innerHTML =
                        `${quarterPercentageChange >= 0 ? '▲' : '▼'} ${Math.abs(quarterPercentageChange).toFixed(2)}% compared to last quarter`;
                    quarterCompareElem.classList.remove('text-success', 'text-danger');
                    quarterCompareElem.classList.add(quarterPercentageChange >= 0 ? 'text-success' : 'text-danger');

                    document.getElementById('stat-total-orders').innerText = monthlyData[monthIndex].TotalOrders || 0;
                    document.getElementById('stat-total-amount').innerText = Number(monthlyData[monthIndex].TotalAmount || 0).toLocaleString('vi-VN') + ' đ';
                    document.getElementById('stat-pending-orders').innerText = monthlyData[monthIndex].TotalOrdered || 0;
                    document.getElementById('stat-pending-amount').innerText = Number(monthlyData[monthIndex].TotalOrderedAmount || 0).toLocaleString('vi-VN') + ' đ';
                    document.getElementById('stat-delivered-orders').innerText = monthlyData[monthIndex].TotalDelivered || 0;
                    document.getElementById('stat-delivered-amount').innerText = Number(monthlyData[monthIndex].TotalDeliveredAmount || 0).toLocaleString('vi-VN') + ' đ';
                    document.getElementById('stat-canceled-orders').innerText = monthlyData[monthIndex].TotalCanceled || 0;
                    document.getElementById('stat-canceled-amount').innerText = Number(monthlyData[monthIndex].TotalCanceledAmount || 0).toLocaleString('vi-VN') + ' đ';
                }
            }

            function renderMonthlyChart() {
                if (weeklyChart) {
                    weeklyChart.destroy();
                    weeklyChart = null;
                }

                const monthlyOptions = {
                    series: [{
                            name: 'Total',
                            data: [{{ $AmountM }}]
                        }, {
                            name: 'Pending',
                            data: [{{ $OrderedAmountM }}]
                        },
                        {
                            name: 'Delivered',
                            data: [{{ $DeliveredAmountM }}]
                        }, {
                            name: 'Canceled',
                            data: [{{ $CanceledAmountM }}]
                        }
                    ],
                    chart: {
                        type: 'bar',
                        height: 325,
                        toolbar: { show: false },
                        events: {
                            xAxisLabelClick: function(event, chartContext, config) {
                                selectedMonthIndex = config.labelIndex;
                                updateMonthlySummary(selectedMonthIndex);
                                fetchWeeklyData(selectedMonthIndex + 1, new Date().getFullYear());
                            },
                            dataPointSelection: function(event, chartContext, config) {
                                selectedMonthIndex = config.dataPointIndex;
                                updateMonthlySummary(selectedMonthIndex);
                                fetchWeeklyData(selectedMonthIndex + 1, new Date().getFullYear());
                            }
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '10px',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: { enabled: false },
                    legend: {
                        show: true,
                        markers: {
                            fillColors: ['#2377FC', '#FFA500', '#078407', '#FF0000']
                        }
                    },
                    colors: ['#2377FC', '#FFA500', '#078407', '#FF0000'],
                    stroke: { show: false },
                    xaxis: {
                        labels: { style: { colors: '#212529' } },
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    },
                    yaxis: {
                        show: true,
                        labels: {
                            style: { colors: '#212529' },
                            formatter: function(val) {
                                return val.toLocaleString('vi-VN') + " đ";
                            }
                        },
                        tickAmount: 5,
                        min: 0
                    },
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val.toLocaleString('vi-VN') + " đ"
                            }
                        }
                    }
                };

                monthlyChart = new ApexCharts(chartElement, monthlyOptions);
                monthlyChart.render();
            }

            function fetchWeeklyData(month, year) {
                fetch(`{{ route('admin.revenue.weekly') }}?month=${month}&year=${year}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    renderWeeklyChart(data.labels, data.total, data.pending, data.delivered, data.canceled, month, year);
                })
                .catch(error => console.error('Error fetching weekly data:', error));
            }

            function renderWeeklyChart(labels, total, pending, delivered, canceled, month, year) {
                if (monthlyChart) {
                    monthlyChart.destroy();
                    monthlyChart = null;
                }
                if (weeklyChart) {
                    weeklyChart.destroy();
                    weeklyChart = null;
                }

                chartTitle.innerText = `Weekly revenue ${month}/${year}`;
                backButton.style.display = 'block';

                const weeklyOptions = {
                    series: [
                        { name: 'Total', data: total },
                        { name: 'Pending', data: pending },
                        { name: 'Delivered', data: delivered },
                        { name: 'Canceled', data: canceled }
                    ],
                    chart: {
                        type: 'bar',
                        height: 325,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '10px',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: {
                        show: true,
                        markers: {
                            fillColors: ['#2377FC', '#FFA500', '#078407', '#FF0000']
                        }
                    },
                    colors: ['#2377FC', '#FFA500', '#078407', '#FF0000'],
                    stroke: { show: false },
                    xaxis: {
                        labels: { style: { colors: '#212529' } },
                        categories: labels,
                    },
                    yaxis: {
                        show: true,
                        labels: {
                            style: { colors: '#212529' },
                            formatter: function(val) {
                                return val.toLocaleString('vi-VN') + " đ";
                            }
                        },
                        tickAmount: 5,
                        min: 0
                    },
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val.toLocaleString('vi-VN') + " đ"
                            }
                        }
                    }
                };

                weeklyChart = new ApexCharts(chartElement, weeklyOptions);
                weeklyChart.render();
            }

            backButton.addEventListener('click', function() {
                chartTitle.innerText = 'Monthly Revenue';
                this.style.display = 'none';
                renderMonthlyChart();
            });

            document.getElementById('btn-year').addEventListener('click', function() {
                updateLeftStats(-1);
            });
            document.getElementById('btn-current-month').addEventListener('click', function() {
                let now = new Date();
                let currentMonthIndex = now.getMonth();
                updateLeftStats(currentMonthIndex);
            });

            $(window).on("load", function() {
                if ($("#revenue-chart").length > 0) {
                    renderMonthlyChart();
                    updateLeftStats(-1);
                }
            });

            // Top Products Pie Chart
            var topProductQty = @json($topProductQty);
            var topProductsOptions = {
                series: @json($topProductData),
                chart: {
                    type: 'pie',
                    height: 350
                },
                plotOptions: {
                    pie: {
                        donut: { labels: { show: false } }
                    }
                },
                labels: @json($topProductLabels),
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(val, opts) {
                            var idx = opts.dataPointIndex;
                            return topProductQty[idx] + " sold (" + val + "%)";
                        },
                        title: {
                            formatter: (seriesName) => seriesName,
                        }
                    }
                }
            };

            var topProductsChart = new ApexCharts(document.querySelector("#top-products-chart"), topProductsOptions);
            if ($("#top-products-chart").length > 0) {
                topProductsChart.render();
            }

            // Top Categories Pie Chart
            var topCategoryQty = @json($topCategoryQty);
            var topCategoriesOptions = {
                series: @json($topCategoryData),
                chart: {
                    type: 'pie',
                    height: 350
                },
                plotOptions: {
                    pie: {
                        donut: { labels: { show: false } }
                    }
                },
                labels: @json($topCategoryLabels),
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(val, opts) {
                            var idx = opts.dataPointIndex;
                            return topCategoryQty[idx] + " sold (" + val + "%)";
                        },
                        title: {
                            formatter: (seriesName) => seriesName,
                        }
                    }
                }
            };

            var topCategoriesChart = new ApexCharts(document.querySelector("#top-categories-chart"), topCategoriesOptions);
            if ($("#top-categories-chart").length > 0) {
                topCategoriesChart.render();
            }

        })(jQuery);
    </script>
@endpush
