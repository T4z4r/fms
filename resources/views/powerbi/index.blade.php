@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Power BI Analytics</h4>
            <div>
                <button class="btn btn-outline-primary btn-sm" onclick="exportToPDF()">
                    <i class="bi bi-file-pdf"></i> PDF
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()">
                    <i class="bi bi-file-excel"></i> Excel
                </button>
            </div>
        </div>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
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
            <div class="col-md-2">
                <label>Year</label>
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label>Quarter</label>
                <select name="quarter" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ $quarter == 'all' ? 'selected' : '' }}>All Quarters</option>
                    <option value="Q1" {{ $quarter == 'Q1' ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                    <option value="Q2" {{ $quarter == 'Q2' ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                    <option value="Q3" {{ $quarter == 'Q3' ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                    <option value="Q4" {{ $quarter == 'Q4' ? 'selected' : '' }}>Q4 (Oct-Dec)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>View Mode</label>
                 <select name="view_mode" class="form-select" onchange="this.form.submit()">
                     <option value="interactive" {{ $viewMode == 'interactive' ? 'selected' : '' }}>Interactive</option>
                     <option value="comparison" {{ $viewMode == 'comparison' ? 'selected' : '' }}>Comparison</option>
                     <option value="trend" {{ $viewMode == 'trend' ? 'selected' : '' }}>Trend Analysis</option>
                     <option value="forecast" {{ $viewMode == 'forecast' ? 'selected' : '' }}>Forecast</option>
                     <option value="advanced" {{ $viewMode == 'advanced' ? 'selected' : '' }}>Advanced Analytics</option>
                     <option value="risk" {{ $viewMode == 'risk' ? 'selected' : '' }}>Risk Analysis</option>
                 </select>
            </div>
            <div class="col-md-2">
                <label>Chart Type</label>
                <select name="chart_type" class="form-select" onchange="this.form.submit()">
                    <option value="line" {{ $chartType == 'line' ? 'selected' : '' }}>Line</option>
                    <option value="column" {{ $chartType == 'column' ? 'selected' : '' }}>Column</option>
                    <option value="bar" {{ $chartType == 'bar' ? 'selected' : '' }}>Bar</option>
                    <option value="area" {{ $chartType == 'area' ? 'selected' : '' }}>Area</option>
                    <option value="spline" {{ $chartType == 'spline' ? 'selected' : '' }}>Spline</option>
                    <option value="pie" {{ $chartType == 'pie' ? 'selected' : '' }}>Pie</option>
                </select>
            </div>
        </form>

        @if ($selectedCostCentreId)
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h6 class="card-title">Total Budget</h6>
                            <h3 class="mb-0">£{{ number_format($kpiData['totalBudget'], 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h6 class="card-title">Total Actual</h6>
                            <h3 class="mb-0">£{{ number_format($kpiData['totalActual'], 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white {{ $kpiData['variance'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                        <div class="card-body">
                            <h6 class="card-title">Variance</h6>
                            <h3 class="mb-0">£{{ number_format($kpiData['variance'], 0) }}</h3>
                            <small>{{ $kpiData['variancePercentage'] }}%</small>
                        </div>
                    </div>
                </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6 class="card-title">YoY Growth</h6>
                        <h3 class="mb-0">{{ $kpiData['yoyGrowth'] }}%</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Daily Velocity</h6>
                        <h3 class="mb-0">£{{ number_format($spendingVelocity['avgDailyVelocity'], 2) }}</h3>
                        <small>Projected: £{{ number_format($spendingVelocity['projectedYearEnd'], 0) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <h6 class="card-title">Budget Accuracy</h6>
                        <h3 class="mb-0">{{ $budgetPrediction['accuracyScore'] }}%</h3>
                        <small>Predicted Variance: £{{ number_format($budgetPrediction['predictedVariance'], 0) }}</small>
                    </div>
                </div>
            </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Budget vs Actual Trend</h5>
                        </div>
                        <div class="card-body">
                            <div id="monthly-trend-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Department Comparison</h5>
                        </div>
                        <div class="card-body">
                            <div id="department-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Account Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div id="account-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Yearly Comparison</h5>
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
                        <div class="card-header">
                            <h5 class="mb-0">Monthly Variance Trend</h5>
                        </div>
                        <div class="card-body">
                            <div id="variance-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quarterly Comparison</h5>
                        </div>
                        <div class="card-body">
                            <div id="quarterly-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Top 5 Spending Accounts</h5>
                        </div>
                        <div class="card-body">
                            <div id="top-accounts-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Full Year Forecast (Budget vs Actual vs Projected)</h5>
                        </div>
                        <div class="card-body">
                            <div id="forecast-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($topAccounts) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Top Spending Accounts Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Account</th>
                                        <th>Actual Spending</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topAccounts as $index => $account)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $account['name'] }}</td>
                                        <td>£{{ number_format($account['actual'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($forecastData) && count($forecastData) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Forecast Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="alert alert-info">
                                        <strong>Avg Monthly Spend:</strong><br>
                                        £{{ number_format($forecastData['avgMonthlySpend'], 2) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-warning">
                                        <strong>Remaining Budget:</strong><br>
                                        £{{ number_format($forecastData['remainingBudget'], 2) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-primary">
                                        <strong>Projected Spend:</strong><br>
                                        £{{ number_format($forecastData['projectedSpend'], 2) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-{{ $forecastData['projectedVariance'] >= 0 ? 'success' : 'danger' }}">
                                        <strong>Projected Variance:</strong><br>
                                        £{{ number_format($forecastData['projectedVariance'], 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Advanced Analytics Sections -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-3"><i class="bi bi-graph-up"></i> Advanced Business Analytics</h4>
                </div>
            </div>

            <!-- Spending Velocity Analysis -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Spending Velocity Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="alert alert-success">
                                        <strong>Average Daily Velocity:</strong><br>
                                        £{{ number_format($spendingVelocity['avgDailyVelocity'], 2) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-info">
                                        <strong>Days Analyzed:</strong><br>
                                        {{ $spendingVelocity['totalDaysAnalyzed'] }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-warning">
                                        <strong>Projected Year End:</strong><br>
                                        £{{ number_format($spendingVelocity['projectedYearEnd'], 0) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-secondary">
                                        <strong>Acceleration:</strong><br>
                                        {{ $spendingVelocity['acceleration'] }}%
                                    </div>
                                </div>
                            </div>
                            <div id="spending-velocity-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Utilization Analysis -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Budget Utilization Rate per Account</h5>
                        </div>
                        <div class="card-body">
                            <div id="budget-utilization-chart"></div>
                            <div class="table-responsive mt-3">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Budget</th>
                                            <th>Actual</th>
                                            <th>Utilization Rate</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($budgetUtilization as $util)
                                        <tr>
                                            <td>{{ $util['account'] }}</td>
                                            <td>£{{ number_format($util['budget'], 2) }}</td>
                                            <td>£{{ number_format($util['actual'], 2) }}</td>
                                            <td>{{ $util['utilizationRate'] }}%</td>
                                            <td>
                                                @if($util['status'] == 'overspend')
                                                    <span class="badge bg-danger">Overspend</span>
                                                @elseif($util['status'] == 'warning')
                                                    <span class="badge bg-warning">Warning</span>
                                                @else
                                                    <span class="badge bg-success">Good</span>
                                                @endif
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

            <!-- Seasonal Trend Analysis -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Seasonal Trend Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div id="seasonal-trends-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Seasonal Indices</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Quarter</th>
                                            <th>Current Index</th>
                                            <th>Previous Index</th>
                                            <th>Change</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($seasonalTrends['seasonalIndices'] as $index)
                                        <tr>
                                            <td>{{ $index['quarter'] }}</td>
                                            <td>{{ $index['currentIndex'] }}%</td>
                                            <td>{{ $index['previousIndex'] }}%</td>
                                            <td class="{{ $index['change'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $index['change'] >= 0 ? '+' : '' }}{{ $index['change'] }}%
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <strong>Total Current Year:</strong> £{{ number_format($seasonalTrends['totalCurrentYear'], 0) }}<br>
                                <strong>Total Previous Year:</strong> £{{ number_format($seasonalTrends['totalPreviousYear'], 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Month-over-Month Growth -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Month-over-Month Growth Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div id="mom-growth-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Accuracy Prediction -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Budget Accuracy Prediction</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="alert alert-primary">
                                        <strong>Current Budget:</strong><br>
                                        £{{ number_format($budgetPrediction['currentBudget'], 0) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-success">
                                        <strong>Current Actual:</strong><br>
                                        £{{ number_format($budgetPrediction['currentActual'], 0) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-warning">
                                        <strong>Predicted Actual:</strong><br>
                                        £{{ number_format($budgetPrediction['predictedActual'], 0) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-info">
                                        <strong>Accuracy Score:</strong><br>
                                        {{ $budgetPrediction['accuracyScore'] }}%
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Budget</th>
                                            <th>Actual</th>
                                            <th>Accuracy</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($budgetPrediction['historicalAccuracy'] as $hist)
                                        <tr>
                                            <td>{{ $hist['year'] }}</td>
                                            <td>£{{ number_format($hist['budget'], 0) }}</td>
                                            <td>£{{ number_format($hist['actual'], 0) }}</td>
                                            <td>{{ $hist['accuracy'] }}%</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Analysis -->
            @if(count($riskAnalysis) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Risk Analysis & Overspend Indicators</h5>
                        </div>
                        <div class="card-body">
                            @foreach($riskAnalysis as $risk)
                            <div class="alert alert-{{ $risk['riskLevel'] == 'critical' ? 'danger' : ($risk['riskLevel'] == 'high' ? 'warning' : 'info') }} mb-2">
                                <strong>{{ ucfirst($risk['riskLevel']) }} Risk:</strong> {{ $risk['description'] }}
                                @if(isset($risk['utilization']))
                                    <br><small>Utilization: {{ $risk['utilization'] }}% | Available Monthly: £{{ number_format($risk['availableMonthly'], 2) }}</small>
                                @endif
                                @if(isset($risk['account']))
                                    <br><small>Account: {{ $risk['account'] }}</small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Category Breakdown -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Category Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <div id="category-breakdown-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Category Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Total</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categoryBreakdown as $cat)
                                        <tr>
                                            <td>{{ $cat['category'] }}</td>
                                            <td>£{{ number_format($cat['total'], 0) }}</td>
                                            <td>{{ $cat['percentage'] }}%</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rolling Averages & Cumulative Spending -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Rolling Averages Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div id="rolling-averages-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Cumulative Spending Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div id="cumulative-spending-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Anomaly Detection -->
            @if(count($anomalyDetection['monthlyAnomalies']) > 0 || count($anomalyDetection['accountAnomalies']) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Anomaly Detection</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Monthly Anomalies</h6>
                                    @if(count($anomalyDetection['monthlyAnomalies']) > 0)
                                        @foreach($anomalyDetection['monthlyAnomalies'] as $anomaly)
                                        <div class="alert alert-{{ $anomaly['severity'] == 'critical' ? 'danger' : 'warning' }} mb-2">
                                            <strong>{{ $anomaly['month'] }}:</strong> £{{ number_format($anomaly['amount'], 0) }}
                                            <small>(Z-score: {{ $anomaly['zScore'] }})</small>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No monthly anomalies detected.</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6>Account Anomalies</h6>
                                    @if(count($anomalyDetection['accountAnomalies']) > 0)
                                        @foreach($anomalyDetection['accountAnomalies'] as $anomaly)
                                        <div class="alert alert-{{ $anomaly['severity'] == 'critical' ? 'danger' : 'warning' }} mb-2">
                                            <strong>{{ $anomaly['account'] }} - {{ $anomaly['month'] }}:</strong> £{{ number_format($anomaly['amount'], 0) }}
                                            <small>(Z-score: {{ $anomaly['zScore'] }})</small>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No account anomalies detected.</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <strong>Statistics:</strong> Mean: £{{ number_format($anomalyDetection['stats']['mean'], 2) }} |
                                    Std Dev: £{{ number_format($anomalyDetection['stats']['stdDev'], 2) }} |
                                    Threshold: {{ $anomalyDetection['stats']['threshold'] }}σ
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <script>
                (function() {
                    var chartInstances = {};

                    window.renderPowerBICharts = function() {
                        var monthlyCategories = @json($monthlyData['categories']);
                        var monthlyBudget = @json($monthlyData['budget']);
                        var monthlyActual = @json($monthlyData['actual']);

                        if (chartInstances['monthly-trend-chart']) {
                            chartInstances['monthly-trend-chart'].destroy();
                        }
                        chartInstances['monthly-trend-chart'] = Highcharts.chart('monthly-trend-chart', {
                            chart: {
                                type: 'line'
                            },
                            title: {
                                text: 'Monthly Budget vs Actual'
                            },
                            subtitle: {
                                text: 'Interactive drill-down available'
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
                                    color: '#0d6efd',
                                    marker: {
                                        enabled: true,
                                        radius: 4
                                    }
                                },
                                {
                                    name: 'Actual',
                                    data: monthlyActual,
                                    color: '#198754',
                                    marker: {
                                        enabled: true,
                                        radius: 4
                                    }
                                }
                            ],
                            plotOptions: {
                                line: {
                                    dataLabels: {
                                        enabled: true
                                    },
                                    enableMouseTracking: true
                                }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'line'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'column'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'area'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Spline',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'spline'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Pie',
                                        onclick: function() {
                                            var self = this;
                                            var pieData = monthlyBudget.map(function(b, i) {
                                                return {
                                                    name: monthlyCategories[i],
                                                    y: b
                                                };
                                            });
                                            self.update({
                                                chart: {
                                                    type: 'pie'
                                                },
                                                series: [{
                                                    name: 'Amount',
                                                    data: pieData
                                                }]
                                            });
                                        }
                                    }
                                ]
                            },
                            credits: {
                                enabled: false
                            }
                        });

                        var departmentData = @json($departmentComparison);
                        if (chartInstances['department-chart']) {
                            chartInstances['department-chart'].destroy();
                        }
                        chartInstances['department-chart'] = Highcharts.chart('department-chart', {
                            chart: {
                                type: 'bar'
                            },
                            title: {
                                text: 'Budget vs Actual by Department'
                            },
                            xAxis: {
                                categories: departmentData.map(function(d) {
                                    return d.name;
                                })
                            },
                            yAxis: {
                                title: {
                                    text: 'Amount (£)'
                                }
                            },
                            series: [{
                                    name: 'Budget',
                                    data: departmentData.map(function(d) {
                                        return d.budget;
                                    }),
                                    color: '#0d6efd'
                                },
                                {
                                    name: 'Actual',
                                    data: departmentData.map(function(d) {
                                        return d.actual;
                                    }),
                                    color: '#198754'
                                }
                            ],
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true
                                    }
                                }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'bar'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'column'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'line'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'area'
                                                }
                                            });
                                        }
                                    }
                                ]
                            },
                            credits: {
                                enabled: false
                            }
                        });

                        var accountData = @json($accountData);
                        if (chartInstances['account-chart']) {
                            chartInstances['account-chart'].destroy();
                        }
                        chartInstances['account-chart'] = Highcharts.chart('account-chart', {
                            chart: {
                                type: 'pie'
                            },
                            title: {
                                text: 'Account Distribution'
                            },
                            subtitle: {
                                text: 'Click to drill down'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        format: '<b>{point.name}</b>: {point.percentage:.1f}%'
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Amount',
                                colorByPoint: true,
                                data: accountData.map(function(a, index) {
                                    var colors = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#20c997', '#e83e8c', '#6610f2', '#fd7e14'];
                                    return {
                                        name: a.name,
                                        y: a.budget + a.actual,
                                        color: colors[index % colors.length]
                                    };
                                })
                            }],
                            exporting: {
                                menuItems: [{
                                        text: 'Pie',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'pie'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'column'
                                                },
                                                series: [{
                                                    name: 'Budget',
                                                    data: accountData.map(function(a) {
                                                        return a.budget;
                                                    })
                                                }, {
                                                    name: 'Actual',
                                                    data: accountData.map(function(a) {
                                                        return a.actual;
                                                    })
                                                }]
                                            });
                                        }
                                    },
                                    {
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'bar'
                                                }
                                            });
                                        }
                                    }
                                ]
                            },
                            credits: {
                                enabled: false
                            }
                        });

                        var yearlyData = @json($yearlyComparison);
                        if (chartInstances['yearly-chart']) {
                            chartInstances['yearly-chart'].destroy();
                        }
                        chartInstances['yearly-chart'] = Highcharts.chart('yearly-chart', {
                            chart: {
                                type: 'column'
                            },
                            title: {
                                text: 'Yearly Budget vs Actual'
                            },
                            xAxis: {
                                categories: yearlyData.map(function(d) {
                                    return d.year;
                                })
                            },
                            yAxis: {
                                title: {
                                    text: 'Amount (£)'
                                }
                            },
                            series: [{
                                    name: 'Budget',
                                    data: yearlyData.map(function(d) {
                                        return d.budget;
                                    }),
                                    color: '#0d6efd'
                                },
                                {
                                    name: 'Actual',
                                    data: yearlyData.map(function(d) {
                                        return d.actual;
                                    }),
                                    color: '#198754'
                                }
                            ],
                            plotOptions: {
                                column: {
                                    grouping: true,
                                    shadow: false,
                                    borderWidth: 0
                                }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'column'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'line'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'bar'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'area'
                                                }
                                            });
                                        }
                                    }
                                ]
                            },
                            credits: {
                                enabled: false
                            }
                        });

                        var varianceData = @json($varianceData);
                        if (chartInstances['variance-chart']) {
                            chartInstances['variance-chart'].destroy();
                        }
                        chartInstances['variance-chart'] = Highcharts.chart('variance-chart', {
                            chart: {
                                type: 'column'
                            },
                            title: {
                                text: 'Monthly Variance (Budget - Actual)'
                            },
                            xAxis: {
                                categories: varianceData.map(function(v) {
                                    return v.month;
                                })
                            },
                            yAxis: {
                                title: {
                                    text: 'Variance (£)'
                                }
                            },
                            series: [{
                                name: 'Variance',
                                data: varianceData.map(function(v) {
                                    return {
                                        y: v.variance,
                                        color: v.variance >= 0 ? '#198754' : '#dc3545'
                                    };
                                })
                            }],
                            plotOptions: {
                                column: {
                                    colorByPoint: false,
                                    dataLabels: {
                                        enabled: true,
                                        formatter: function() {
                                            return '£' + Highcharts.numberFormat(this.y, 0);
                                        }
                                    }
                                }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'column'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'bar'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'line'
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({
                                                chart: {
                                                    type: 'area'
                                                }
                                            });
                                        }
                                    }
                                ]
                            },
                            credits: {
                                enabled: false
                            }
                        });

                        var quarterlyData = @json($quarterlyData);
                        if (chartInstances['quarterly-chart']) {
                            chartInstances['quarterly-chart'].destroy();
                        }
                        chartInstances['quarterly-chart'] = Highcharts.chart('quarterly-chart', {
                            chart: {
                                type: 'column'
                            },
                            title: {
                                text: 'Quarterly Budget vs Actual'
                            },
                            xAxis: {
                                categories: quarterlyData.map(function(q) {
                                    return q.quarter;
                                })
                            },
                            yAxis: {
                                title: {
                                    text: 'Amount (£)'
                                }
                            },
                            series: [{
                                    name: 'Budget',
                                    data: quarterlyData.map(function(q) {
                                        return q.budget;
                                    }),
                                    color: '#0d6efd'
                                },
                                {
                                    name: 'Actual',
                                    data: quarterlyData.map(function(q) {
                                        return q.actual;
                                    }),
                                    color: '#198754'
                                }
                            ],
                            plotOptions: {
                                column: {
                                    grouping: true
                                }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({ chart: { type: 'bar' } });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({ chart: { type: 'line' } });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        var topAccountsData = @json($topAccounts);
                        if (chartInstances['top-accounts-chart']) {
                            chartInstances['top-accounts-chart'].destroy();
                        }
                        chartInstances['top-accounts-chart'] = Highcharts.chart('top-accounts-chart', {
                            chart: {
                                type: 'bar'
                            },
                            title: {
                                text: 'Top 5 Spending Accounts'
                            },
                            xAxis: {
                                categories: topAccountsData.map(function(a) {
                                    return a.name;
                                })
                            },
                            yAxis: {
                                title: {
                                    text: 'Amount (£)'
                                }
                            },
                            series: [{
                                name: 'Actual Spending',
                                data: topAccountsData.map(function(a) {
                                    return a.actual;
                                }),
                                color: '#dc3545'
                            }],
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        formatter: function() {
                                            return '£' + Highcharts.numberFormat(this.y, 0);
                                        }
                                    }
                                }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({ chart: { type: 'bar' } });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Pie',
                                        onclick: function() {
                                            var pieData = topAccountsData.map(function(a) {
                                                return { name: a.name, y: a.actual };
                                            });
                                            this.update({
                                                chart: { type: 'pie' },
                                                series: [{ name: 'Amount', data: pieData }]
                                            });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        var forecastData = @json($forecastData);
                        var months = monthlyCategories;
                        var projectedData = [];
                        var cumulativeActual = 0;
                        var cumulativeBudget = 0;
                        for(var i = 0; i < 12; i++) {
                            cumulativeActual += monthlyActual[i] || 0;
                            cumulativeBudget += monthlyBudget[i] || 0;
                            projectedData.push(null);
                        }
                        for(var i = monthlyActual.length; i < 12; i++) {
                            projectedData[i] = (forecastData.avgMonthlySpend || 0);
                        }
                        if (chartInstances['forecast-chart']) {
                            chartInstances['forecast-chart'].destroy();
                        }
                        chartInstances['forecast-chart'] = Highcharts.chart('forecast-chart', {
                            chart: {
                                type: 'line'
                            },
                            title: {
                                text: 'Budget vs Actual vs Forecast'
                            },
                            subtitle: {
                                text: 'Full Year Analysis with Projection'
                            },
                            xAxis: {
                                categories: months
                            },
                            yAxis: {
                                title: {
                                    text: 'Amount (£)'
                                }
                            },
                            series: [
                                {
                                    name: 'Budget',
                                    data: monthlyBudget,
                                    color: '#0d6efd',
                                    dashStyle: 'Solid',
                                    marker: { enabled: true }
                                },
                                {
                                    name: 'Actual',
                                    data: monthlyActual,
                                    color: '#198754',
                                    dashStyle: 'Solid',
                                    marker: { enabled: true }
                                },
                                {
                                    name: 'Projected',
                                    data: projectedData,
                                    color: '#ffc107',
                                    dashStyle: 'ShortDash',
                                    marker: { enabled: true, symbol: 'diamond' }
                                }
                            ],
                            plotOptions: {
                                line: {
                                    dataLabels: {
                                        enabled: true
                                    }
                                }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({ chart: { type: 'line' } });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({ chart: { type: 'area' } });
                                        }
                                    },
                                    {
                                        text: 'Spline',
                                        onclick: function() {
                                            this.update({ chart: { type: 'spline' } });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        // Spending Velocity Chart
                        var spendingVelocityData = @json($spendingVelocity);
                        if (chartInstances['spending-velocity-chart']) {
                            chartInstances['spending-velocity-chart'].destroy();
                        }
                        chartInstances['spending-velocity-chart'] = Highcharts.chart('spending-velocity-chart', {
                            chart: { type: 'area' },
                            title: { text: 'Spending Velocity Trend' },
                            xAxis: { categories: monthlyCategories },
                            yAxis: { title: { text: 'Daily Velocity (£)' } },
                            series: [{
                                name: 'Daily Velocity',
                                data: Array.from({length: monthlyCategories.length}, (_, i) =>
                                    i < now.getMonth() + 1 ? (monthlyActual[i] / (i === now.getMonth() ? now.getDate() : new Date(now.getFullYear(), i + 1, 0).getDate())) : null
                                ).filter(v => v !== null),
                                color: '#17a2b8'
                            }],
                            exporting: {
                                menuItems: [{
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({ chart: { type: 'area' } });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({ chart: { type: 'line' } });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Spline',
                                        onclick: function() {
                                            this.update({ chart: { type: 'spline' } });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        // Budget Utilization Chart
                        var budgetUtilizationData = @json($budgetUtilization);
                        if (chartInstances['budget-utilization-chart']) {
                            chartInstances['budget-utilization-chart'].destroy();
                        }
                        chartInstances['budget-utilization-chart'] = Highcharts.chart('budget-utilization-chart', {
                            chart: { type: 'bar' },
                            title: { text: 'Budget Utilization by Account' },
                            xAxis: { categories: budgetUtilizationData.map(d => d.account) },
                            yAxis: { title: { text: 'Utilization Rate (%)' }, max: 150 },
                            series: [{
                                name: 'Utilization Rate',
                                data: budgetUtilizationData.map(d => ({
                                    y: d.utilizationRate,
                                    color: d.status === 'overspend' ? '#dc3545' : (d.status === 'warning' ? '#ffc107' : '#198754')
                                }))
                            }],
                            plotOptions: {
                                bar: { dataLabels: { enabled: true, format: '{y}%' } }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({ chart: { type: 'bar' } });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({ chart: { type: 'line' } });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({ chart: { type: 'area' } });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        // Seasonal Trends Chart
                        var seasonalData = @json($seasonalTrends);
                        if (chartInstances['seasonal-trends-chart']) {
                            chartInstances['seasonal-trends-chart'].destroy();
                        }
                        chartInstances['seasonal-trends-chart'] = Highcharts.chart('seasonal-trends-chart', {
                            chart: { type: 'column' },
                            title: { text: 'Seasonal Spending Comparison' },
                            xAxis: { categories: seasonalData.seasonalData.map(d => d.quarter) },
                            yAxis: { title: { text: 'Amount ($)' } },
                            series: [{
                                name: 'Current Year',
                                data: seasonalData.seasonalData.map(d => d.amount),
                                color: '#0d6efd'
                            }, {
                                name: 'Previous Year',
                                data: Array(seasonalData.seasonalData.length).fill(seasonalData.totalPreviousYear / 4),
                                color: '#6c757d',
                                dashStyle: 'ShortDash'
                            }],
                            exporting: {
                                menuItems: [{
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({ chart: { type: 'bar' } });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({ chart: { type: 'line' } });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({ chart: { type: 'area' } });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        // Month-over-Month Growth Chart
                        var momGrowthData = @json($momGrowth);
                        if (chartInstances['mom-growth-chart']) {
                            chartInstances['mom-growth-chart'].destroy();
                        }
                        chartInstances['mom-growth-chart'] = Highcharts.chart('mom-growth-chart', {
                            chart: { type: 'column' },
                            title: { text: 'Month-over-Month Growth' },
                            xAxis: { categories: momGrowthData.map(d => d.month) },
                            yAxis: { title: { text: 'Growth (%)' } },
                            series: [{
                                name: 'Growth Rate',
                                data: momGrowthData.map(d => ({
                                    y: d.growth,
                                    color: d.status === 'high_growth' ? '#198754' : (d.status === 'decline' ? '#dc3545' : '#ffc107')
                                }))
                            }],
                            plotOptions: {
                                column: { dataLabels: { enabled: true, format: '{y}%' } }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Bar',
                                        onclick: function() {
                                            this.update({ chart: { type: 'bar' } });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({ chart: { type: 'line' } });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({ chart: { type: 'area' } });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        // Category Breakdown Chart
                        var categoryData = @json($categoryBreakdown);
                        if (chartInstances['category-breakdown-chart']) {
                            chartInstances['category-breakdown-chart'].destroy();
                        }
                        chartInstances['category-breakdown-chart'] = Highcharts.chart('category-breakdown-chart', {
                            chart: { type: 'pie' },
                            title: { text: 'Spending by Category' },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f}%' }
                                }
                            },
                            series: [{
                                name: 'Amount',
                                colorByPoint: true,
                                data: categoryData.map((cat, index) => ({
                                    name: cat.category,
                                    y: cat.total,
                                    color: ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#17a2b8'][index % 5]
                                }))
                            }],
                            exporting: {
                                menuItems: [{
                                        text: 'Pie',
                                        onclick: function() {
                                            this.update({ chart: { type: 'pie' } });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            var columnData = categoryData.map(cat => cat.total);
                                            var categories = categoryData.map(cat => cat.category);
                                            this.update({
                                                chart: { type: 'column' },
                                                xAxis: { categories: categories },
                                                series: [{ name: 'Amount', data: columnData }]
                                            });
                                        }
                                    },
                                    {
                                        text: 'Bar',
                                        onclick: function() {
                                            var barData = categoryData.map(cat => cat.total);
                                            var categories = categoryData.map(cat => cat.category);
                                            this.update({
                                                chart: { type: 'bar' },
                                                xAxis: { categories: categories },
                                                series: [{ name: 'Amount', data: barData }]
                                            });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        // Rolling Averages Chart
                        var rollingData = @json($rollingAverages);
                        if (chartInstances['rolling-averages-chart']) {
                            chartInstances['rolling-averages-chart'].destroy();
                        }
                        chartInstances['rolling-averages-chart'] = Highcharts.chart('rolling-averages-chart', {
                            chart: { type: 'line' },
                            title: { text: 'Rolling Averages Analysis' },
                            xAxis: { categories: monthlyCategories },
                            yAxis: { title: { text: 'Amount ($)' } },
                            series: [{
                                name: 'Monthly Data',
                                data: rollingData.monthlyData,
                                color: '#6c757d',
                                lineWidth: 1
                            }, {
                                name: '3-Month Average',
                                data: rollingData.rolling3Month,
                                color: '#0d6efd',
                                lineWidth: 2
                            }, {
                                name: '6-Month Average',
                                data: rollingData.rolling6Month,
                                color: '#198754',
                                lineWidth: 2
                            }],
                            exporting: {
                                menuItems: [{
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({ chart: { type: 'line' } });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({ chart: { type: 'area' } });
                                        }
                                    },
                                    {
                                        text: 'Spline',
                                        onclick: function() {
                                            this.update({ chart: { type: 'spline' } });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });

                        // Cumulative Spending Chart
                        var cumulativeData = @json($cumulativeSpending);
                        if (chartInstances['cumulative-spending-chart']) {
                            chartInstances['cumulative-spending-chart'].destroy();
                        }
                        chartInstances['cumulative-spending-chart'] = Highcharts.chart('cumulative-spending-chart', {
                            chart: { type: 'area' },
                            title: { text: 'Cumulative Spending vs Budget' },
                            xAxis: { categories: cumulativeData.map(d => d.month) },
                            yAxis: { title: { text: 'Amount ($)' } },
                            series: [{
                                name: 'Cumulative Budget',
                                data: cumulativeData.map(d => d.cumulativeBudget),
                                color: '#0d6efd',
                                fillOpacity: 0.3
                            }, {
                                name: 'Cumulative Actual',
                                data: cumulativeData.map(d => d.cumulativeSpend),
                                color: '#198754',
                                fillOpacity: 0.3
                            }],
                            plotOptions: {
                                area: { stacking: 'normal' }
                            },
                            exporting: {
                                menuItems: [{
                                        text: 'Area',
                                        onclick: function() {
                                            this.update({ chart: { type: 'area' } });
                                        }
                                    },
                                    {
                                        text: 'Line',
                                        onclick: function() {
                                            this.update({ chart: { type: 'line' } });
                                        }
                                    },
                                    {
                                        text: 'Column',
                                        onclick: function() {
                                            this.update({ chart: { type: 'column' } });
                                        }
                                    },
                                    {
                                        text: 'Spline',
                                        onclick: function() {
                                            this.update({ chart: { type: 'spline' } });
                                        }
                                    }
                                ]
                            },
                            credits: { enabled: false }
                        });
                    };

                    function exportToPDF() {
                        var charts = document.querySelectorAll('[id$="-chart"]');
                        charts.forEach(function(chartDiv) {
                            if (chartInstances[chartDiv.id]) {
                                chartInstances[chartDiv.id].exportChart({
                                    type: 'application/pdf'
                                }, {
                                    chart: {
                                        backgroundColor: '#ffffff'
                                    }
                                });
                            }
                        });
                    }

                    function exportToExcel() {
                        var monthlyData = @json($monthlyData);
                        var yearlyData = @json($yearlyComparison);
                        var varianceData = @json($varianceData);
                        var budgetUtilizationData = @json($budgetUtilization);
                        var seasonalData = @json($seasonalTrends);
                        var momGrowthData = @json($momGrowth);
                        var categoryData = @json($categoryBreakdown);
                        var rollingData = @json($rollingAverages);
                        var cumulativeData = @json($cumulativeSpending);
                        var anomalyData = @json($anomalyDetection);

                        var csvContent = "data:text/csv;charset=utf-8,";

                        // Basic Data
                        csvContent += "Month,Budget,Actual\n";
                        monthlyData.categories.forEach(function(month, index) {
                            csvContent += month + "," + monthlyData.budget[index] + "," + monthlyData.actual[index] + "\n";
                        });

                        csvContent += "\nYear,Budget,Actual\n";
                        yearlyData.forEach(function(row) {
                            csvContent += row.year + "," + row.budget + "," + row.actual + "\n";
                        });

                        csvContent += "\nMonth,Variance,Percentage\n";
                        varianceData.forEach(function(row) {
                            csvContent += row.month + "," + row.variance + "," + row.percentage + "\n";
                        });

                        // Budget Utilization
                        csvContent += "\nAccount,Budget,Actual,Utilization Rate,Status\n";
                        budgetUtilizationData.forEach(function(row) {
                            csvContent += '"' + row.account + '",' + row.budget + "," + row.actual + "," + row.utilizationRate + "," + row.status + "\n";
                        });

                        // Seasonal Data
                        csvContent += "\nQuarter,Current Amount,Previous Amount,Index,Previous Index,Change\n";
                        seasonalData.seasonalData.forEach(function(season, index) {
                            var indices = seasonalData.seasonalIndices[index];
                            csvContent += season.quarter + "," + season.amount + "," +
                                (seasonalData.totalPreviousYear / 4) + "," +
                                indices.currentIndex + "," + indices.previousIndex + "," + indices.change + "\n";
                        });

                        // Month-over-Month Growth
                        csvContent += "\nMonth,Current,Previous,Growth,Status\n";
                        momGrowthData.forEach(function(row) {
                            csvContent += row.month + "," + row.current + "," + row.previous + "," + row.growth + "," + row.status + "\n";
                        });

                        // Category Breakdown
                        csvContent += "\nCategory,Total,Percentage\n";
                        categoryData.forEach(function(row) {
                            csvContent += row.category + "," + row.total + "," + row.percentage + "\n";
                        });

                        // Rolling Averages
                        csvContent += "\nMonth,Actual,3-Month Avg,6-Month Avg\n";
                        monthlyData.categories.forEach(function(month, index) {
                            csvContent += month + "," + rollingData.monthlyData[index] + "," +
                                (rollingData.rolling3Month[index] || '') + "," +
                                (rollingData.rolling6Month[index] || '') + "\n";
                        });

                        // Cumulative Spending
                        csvContent += "\nMonth,Monthly Spend,Cumulative Spend,Cumulative Budget,Variance,Efficiency\n";
                        cumulativeData.forEach(function(row) {
                            csvContent += row.month + "," + row.monthlySpend + "," + row.cumulativeSpend + "," +
                                row.cumulativeBudget + "," + row.variance + "," + row.efficiency + "\n";
                        });

                        // Anomalies
                        if (anomalyData.monthlyAnomalies.length > 0) {
                            csvContent += "\nMonthly Anomalies\nMonth,Amount,Z-Score,Deviation,Type,Severity\n";
                            anomalyData.monthlyAnomalies.forEach(function(row) {
                                csvContent += row.month + "," + row.amount + "," + row.zScore + "," +
                                    (row.deviation || '') + "," + row.type + "," + row.severity + "\n";
                            });
                        }

                        if (anomalyData.accountAnomalies.length > 0) {
                            csvContent += "\nAccount Anomalies\nAccount,Month,Amount,Z-Score,Type,Severity\n";
                            anomalyData.accountAnomalies.forEach(function(row) {
                                csvContent += '"' + row.account + '",' + row.month + "," + row.amount + "," + row.zScore + "," +
                                    row.type + "," + row.severity + "\n";
                            });
                        }

                        var encodedUri = encodeURI(csvContent);
                        var link = document.createElement("a");
                        link.setAttribute("href", encodedUri);
                        link.setAttribute("download", "powerbi_advanced_report.csv");
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }

                    window.exportToPDF = exportToPDF;
                    window.exportToExcel = exportToExcel;

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', window.renderPowerBICharts);
                    } else {
                        window.renderPowerBICharts();
                    }
                })();
            </script>
        @else
            <div class="alert alert-info">
                Select a cost centre to view Power BI analytics.
            </div>
        @endif
    </div>
@endsection