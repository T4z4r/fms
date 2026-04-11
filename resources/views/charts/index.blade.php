@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="mb-4"><i class="bi bi-pie-chart text-primary"></i> Financial Charts</h4>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label>Cost Centre</label>
                <select name="cost_centre_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Select Cost Centre</option>
                    @foreach ($costCentres as $cc)
                        <option value="{{ $cc->id }}" {{ $selectedCostCentreId == $cc->id ? 'selected' : '' }}>
                            {{ $cc->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Year</label>
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>

        @if ($selectedCostCentreId)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <h5 class="mb-0">Monthly Budget vs Actual Trend</h5>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Monthly trend chart type">
                                <button type="button" class="btn btn-outline-primary active" data-chart-target="monthly-trend-chart" data-chart-type="line">Line</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="monthly-trend-chart" data-chart-type="column">Column</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="monthly-trend-chart" data-chart-type="area">Area</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="monthly-trend-chart" data-chart-type="spline">Spline</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="monthly-trend-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <h5 class="mb-0">Account Distribution</h5>
                            <div class="btn-group btn-group-sm flex-wrap" role="group" aria-label="Account chart type">
                                <button type="button" class="btn btn-outline-primary active" data-chart-target="account-chart" data-chart-type="column">Column</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="account-chart" data-chart-type="bar">Bar</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="account-chart" data-chart-type="line">Line</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="account-chart" data-chart-type="area">Area</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="account-chart" data-chart-type="pie">Pie</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="account-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <h5 class="mb-0">Yearly Comparison</h5>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Yearly comparison chart type">
                                <button type="button" class="btn btn-outline-primary active" data-chart-target="yearly-chart" data-chart-type="column">Column</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="yearly-chart" data-chart-type="line">Line</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="yearly-chart" data-chart-type="bar">Bar</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="yearly-chart" data-chart-type="area">Area</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="yearly-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <h5 class="mb-0">Monthly Variance Trend</h5>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Monthly variance chart type">
                                <button type="button" class="btn btn-outline-primary active" data-chart-target="variance-chart" data-chart-type="column">Column</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="variance-chart" data-chart-type="bar">Bar</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="variance-chart" data-chart-type="line">Line</button>
                                <button type="button" class="btn btn-outline-primary" data-chart-target="variance-chart" data-chart-type="area">Area</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="variance-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                (function() {
                    var chartInstances = {};
                    var chartState = {
                        'monthly-trend-chart': 'line',
                        'account-chart': 'column',
                        'yearly-chart': 'column',
                        'variance-chart': 'column'
                    };

                    var monthlyCategories = @json($monthlyData['categories']);
                    var monthlyBudget = @json($monthlyData['budget']);
                    var monthlyActual = @json($monthlyData['actual']);
                    var accountData = @json($accountData);
                    var yearlyData = @json($yearlyComparison);
                    var varianceData = @json($varianceData);

                    function setActiveChartButton(chartId, chartType) {
                        document.querySelectorAll('[data-chart-target="' + chartId + '"]').forEach(function(button) {
                            var isActive = button.getAttribute('data-chart-type') === chartType;
                            button.classList.toggle('active', isActive);
                        });
                    }

                    function renderMonthlyTrendChart(chartType) {
                        chartInstances['monthly-trend-chart'] = Highcharts.chart('monthly-trend-chart', {
                            chart: {
                                type: chartType
                            },
                            title: {
                                text: 'Monthly Budget vs Actual'
                            },
                            xAxis: {
                                categories: monthlyCategories
                            },
                            yAxis: {
                                title: {
                                    text: 'Amount (£)'
                                }
                            },
                            series: [{
                                name: 'Budget',
                                data: monthlyBudget,
                                color: '#0d6efd'
                            }, {
                                name: 'Actual',
                                data: monthlyActual,
                                color: '#198754'
                            }],
                            credits: {
                                enabled: false
                            }
                        });
                    }

                    function renderAccountChart(chartType) {
                        var isPieChart = chartType === 'pie';
                        var options = {
                            chart: {
                                type: chartType
                            },
                            title: {
                                text: 'Budget vs Actual by Account'
                            },
                            credits: {
                                enabled: false
                            }
                        };

                        if (isPieChart) {
                            options.series = [{
                                name: 'Amount',
                                data: accountData.map(function(account) {
                                    return {
                                        name: account.name,
                                        y: account.budget + account.actual
                                    };
                                })
                            }];
                        } else {
                            options.xAxis = {
                                categories: accountData.map(function(account) {
                                    return account.name;
                                })
                            };
                            options.yAxis = {
                                title: {
                                    text: 'Amount (£)'
                                }
                            };
                            options.series = [{
                                name: 'Budget',
                                data: accountData.map(function(account) {
                                    return account.budget;
                                }),
                                color: '#0d6efd'
                            }, {
                                name: 'Actual',
                                data: accountData.map(function(account) {
                                    return account.actual;
                                }),
                                color: '#198754'
                            }];
                        }

                        chartInstances['account-chart'] = Highcharts.chart('account-chart', options);
                    }

                    function renderYearlyChart(chartType) {
                        chartInstances['yearly-chart'] = Highcharts.chart('yearly-chart', {
                            chart: {
                                type: chartType
                            },
                            title: {
                                text: 'Yearly Budget vs Actual'
                            },
                            xAxis: {
                                categories: yearlyData.map(function(item) {
                                    return item.year;
                                })
                            },
                            yAxis: {
                                title: {
                                    text: 'Amount (£)'
                                }
                            },
                            series: [{
                                name: 'Budget',
                                data: yearlyData.map(function(item) {
                                    return item.budget;
                                }),
                                color: '#0d6efd'
                            }, {
                                name: 'Actual',
                                data: yearlyData.map(function(item) {
                                    return item.actual;
                                }),
                                color: '#198754'
                            }],
                            credits: {
                                enabled: false
                            }
                        });
                    }

                    function renderVarianceChart(chartType) {
                        chartInstances['variance-chart'] = Highcharts.chart('variance-chart', {
                            chart: {
                                type: chartType
                            },
                            title: {
                                text: 'Monthly Variance (Budget - Actual)'
                            },
                            xAxis: {
                                categories: varianceData.map(function(item) {
                                    return item.month;
                                })
                            },
                            yAxis: {
                                title: {
                                    text: 'Variance (£)'
                                }
                            },
                            series: [{
                                name: 'Variance',
                                data: varianceData.map(function(item) {
                                    return {
                                        y: item.variance,
                                        color: item.variance >= 0 ? '#198754' : '#dc3545'
                                    };
                                })
                            }],
                            credits: {
                                enabled: false
                            }
                        });
                    }

                    function renderChart(chartId) {
                        if (chartInstances[chartId]) {
                            chartInstances[chartId].destroy();
                        }

                        if (chartId === 'monthly-trend-chart') {
                            renderMonthlyTrendChart(chartState[chartId]);
                        }

                        if (chartId === 'account-chart') {
                            renderAccountChart(chartState[chartId]);
                        }

                        if (chartId === 'yearly-chart') {
                            renderYearlyChart(chartState[chartId]);
                        }

                        if (chartId === 'variance-chart') {
                            renderVarianceChart(chartState[chartId]);
                        }

                        setActiveChartButton(chartId, chartState[chartId]);
                    }

                    window.setChartType = function(chartId, chartType) {
                        chartState[chartId] = chartType;
                        renderChart(chartId);
                    };

                    function bindChartTypeSwitchers() {
                        document.querySelectorAll('[data-chart-target]').forEach(function(button) {
                            if (button.dataset.chartSwitcherBound === 'true') {
                                return;
                            }

                            button.dataset.chartSwitcherBound = 'true';
                            button.addEventListener('click', function() {
                                window.setChartType(
                                    button.getAttribute('data-chart-target'),
                                    button.getAttribute('data-chart-type')
                                );
                            });
                        });
                    }

                    window.renderCharts = function() {
                        bindChartTypeSwitchers();
                        renderChart('monthly-trend-chart');
                        renderChart('account-chart');
                        renderChart('yearly-chart');
                        renderChart('variance-chart');
                    };

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', window.renderCharts);
                    } else {
                        window.renderCharts();
                    }
                })();
            </script>
        @else
            <div class="alert alert-info">
                Select a cost centre to view financial charts.
            </div>
        @endif
    </div>
@endsection
