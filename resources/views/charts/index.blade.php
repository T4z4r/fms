@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <h4 class="mb-4"><i class="bi bi-pie-chart"></i> Financial Charts</h4>

    <form method="GET" class="row g-2 mb-4 filter-form-mobile">
        <div class="col-12 col-md-4">
            <label class="small">Cost Centre</label>
            <select name="cost_centre_id" class="form-select" onchange="this.form.submit()">
                <option value="">Select Cost Centre</option>
                @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}" {{ $selectedCostCentreId == $cc->id ? 'selected' : '' }}>
                        {{ $cc->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="small">Year</label>
            <select name="year" class="form-select" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>

    @if($selectedCostCentreId)
    <div class="row mb-4 g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Budget vs Actual Trend</h5>
                </div>
                <div class="card-body">
                    <div id="monthly-trend-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Account Distribution</h5>
                </div>
                <div class="card-body">
                    <div id="account-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card h-100">
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
        <div class="col-12">
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

    <script>
    (function() {
        var chartInstances = {};

        window.renderCharts = function() {
            var monthlyCategories = @json($monthlyData['categories']);
            var monthlyBudget = @json($monthlyData['budget']);
            var monthlyActual = @json($monthlyData['actual']);

            if (chartInstances['monthly-trend-chart']) {
                chartInstances['monthly-trend-chart'].destroy();
            }
            chartInstances['monthly-trend-chart'] = Highcharts.chart('monthly-trend-chart', {
                chart: { type: 'line' },
                title: { text: 'Monthly Budget vs Actual' },
                xAxis: { categories: monthlyCategories },
                yAxis: { title: { text: 'Amount ($)' } },
                series: [
                    { name: 'Budget', data: monthlyBudget, color: '#0d6efd' },
                    { name: 'Actual', data: monthlyActual, color: '#198754' }
                ],
                exporting: {
                    menuItems: [
                        { text: 'Line', onclick: function() { this.update({ chart: { type: 'line' } }); } },
                        { text: 'Column', onclick: function() { this.update({ chart: { type: 'column' } }); } },
                        { text: 'Area', onclick: function() { this.update({ chart: { type: 'area' } }); } },
                        { text: 'Spline', onclick: function() { this.update({ chart: { type: 'spline' } }); } }
                    ]
                },
                credits: { enabled: false }
            });

            var accountData = @json($accountData);
            if (chartInstances['account-chart']) {
                chartInstances['account-chart'].destroy();
            }
            chartInstances['account-chart'] = Highcharts.chart('account-chart', {
                chart: { type: 'column' },
                title: { text: 'Budget vs Actual by Account' },
                xAxis: { categories: accountData.map(function(a) { return a.name; }) },
                yAxis: { title: { text: 'Amount ($)' } },
                series: [
                    { name: 'Budget', data: accountData.map(function(a) { return a.budget; }), color: '#0d6efd' },
                    { name: 'Actual', data: accountData.map(function(a) { return a.actual; }), color: '#198754' }
                ],
                exporting: {
                    menuItems: [
                        { text: 'Column', onclick: function() { this.update({ chart: { type: 'column' } }); } },
                        { text: 'Bar', onclick: function() { this.update({ chart: { type: 'bar' } }); } },
                        { text: 'Line', onclick: function() { this.update({ chart: { type: 'line' } }); } },
                        { text: 'Area', onclick: function() { this.update({ chart: { type: 'area' } }); } },
                        { text: 'Pie', onclick: function() { 
                            this.update({ 
                                chart: { type: 'pie' },
                                series: [{
                                    name: 'Amount',
                                    data: accountData.map(function(a) { return { name: a.name, y: a.budget + a.actual }; })
                                }]
                            }); 
                        } }
                    ]
                },
                credits: { enabled: false }
            });

            var yearlyData = @json($yearlyComparison);
            if (chartInstances['yearly-chart']) {
                chartInstances['yearly-chart'].destroy();
            }
            chartInstances['yearly-chart'] = Highcharts.chart('yearly-chart', {
                chart: { type: 'column' },
                title: { text: 'Yearly Budget vs Actual' },
                xAxis: { categories: yearlyData.map(function(d) { return d.year; }) },
                yAxis: { title: { text: 'Amount ($)' } },
                series: [
                    { name: 'Budget', data: yearlyData.map(function(d) { return d.budget; }), color: '#0d6efd' },
                    { name: 'Actual', data: yearlyData.map(function(d) { return d.actual; }), color: '#198754' }
                ],
                exporting: {
                    menuItems: [
                        { text: 'Column', onclick: function() { this.update({ chart: { type: 'column' } }); } },
                        { text: 'Line', onclick: function() { this.update({ chart: { type: 'line' } }); } },
                        { text: 'Bar', onclick: function() { this.update({ chart: { type: 'bar' } }); } },
                        { text: 'Area', onclick: function() { this.update({ chart: { type: 'area' } }); } }
                    ]
                },
                credits: { enabled: false }
            });

            var varianceData = @json($varianceData);
            if (chartInstances['variance-chart']) {
                chartInstances['variance-chart'].destroy();
            }
            chartInstances['variance-chart'] = Highcharts.chart('variance-chart', {
                chart: { type: 'column' },
                title: { text: 'Monthly Variance (Budget - Actual)' },
                xAxis: { categories: varianceData.map(function(v) { return v.month; }) },
                yAxis: { title: { text: 'Variance ($)' } },
                series: [{
                    name: 'Variance',
                    data: varianceData.map(function(v) { return { y: v.variance, color: v.variance >= 0 ? '#198754' : '#dc3545' }; })
                }],
                exporting: {
                    menuItems: [
                        { text: 'Column', onclick: function() { this.update({ chart: { type: 'column' } }); } },
                        { text: 'Bar', onclick: function() { this.update({ chart: { type: 'bar' } }); } },
                        { text: 'Line', onclick: function() { this.update({ chart: { type: 'line' } }); } },
                        { text: 'Area', onclick: function() { this.update({ chart: { type: 'area' } }); } }
                    ]
                },
                credits: { enabled: false }
            });
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