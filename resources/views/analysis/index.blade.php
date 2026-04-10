@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">AI Financial Analysis</h1>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Cost Centre</label>
            <select name="cost_centre_id" class="form-select" onchange="this.form.submit()">
                <option value="">Select Cost Centre</option>
                @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}" {{ $selectedCostCentre?->id == $cc->id ? 'selected' : '' }}>
                        {{ $cc->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Year</label>
            <select name="year" class="form-select" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>

    @if($selectedCostCentre && isset($analysis))
    
    @if(isset($aiCommentary))
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">AI Financial Commentary</h5>
        </div>
        <div class="card-body" style="white-space: pre-line;">{!! nl2br(e($aiCommentary['full_commentary'])) !!}</div>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-{{ $analysis['summary']['status'] === 'over_budget' ? 'danger' : ($analysis['summary']['status'] === 'near_limit' ? 'warning' : 'success') }} text-white">
                <div class="card-body">
                    <h6>Status</h6>
                    <h4>{{ ucwords(str_replace('_', ' ', $analysis['summary']['status'])) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6>Total Budget</h6>
                    <h4>${{ number_format($analysis['summary']['total_budget'], 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6>Total Spent</h6>
                    <h4>${{ number_format($analysis['summary']['total_actual'], 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6>Utilization</h6>
                    <h4>{{ round($analysis['summary']['utilization_rate']) }}%</h4>
                </div>
            </div>
        </div>
    </div>

    @if(count($analysis['insights']) > 0)
    <div class="card mb-4 border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">AI Insights</h5>
        </div>
        <div class="card-body">
            @foreach($analysis['insights'] as $insight)
            <div class="mb-3">
                <strong>{{ $insight['title'] }}</strong>
                <p class="mb-0">{{ $insight['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Monthly Trend</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Budget</th>
                                <th>Actual</th>
                                <th>Variance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analysis['monthly_trend'] as $month)
                            <tr class="{{ $month['variance_percentage'] < -20 ? 'table-danger' : '' }}">
                                <td>{{ $month['month_name'] }}</td>
                                <td>${{ number_format($month['budget']) }}</td>
                                <td>${{ number_format($month['actual']) }}</td>
                                <td class="{{ $month['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $month['variance'] >= 0 ? '+' : '' }}${{ number_format($month['variance']) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Account Analysis</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Budget</th>
                                <th>Actual</th>
                                <th>Utilization</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analysis['account_analysis'] as $account)
                            <tr class="{{ $account['utilization'] > 100 ? 'table-danger' : ($account['utilization'] > 80 ? 'table-warning' : '') }}">
                                <td>{{ $account['account_name'] }}</td>
                                <td>${{ number_format($account['budget']) }}</td>
                                <td>${{ number_format($account['actual']) }}</td>
                                <td>
                                    <span class="badge bg-{{ $account['utilization'] > 100 ? 'danger' : ($account['utilization'] > 80 ? 'warning' : 'success') }}">
                                        {{ round($account['utilization']) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(count($analysis['anomalies']) > 0)
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning">
            <h5>Detected Anomalies</h5>
        </div>
        <div class="card-body">
            @foreach($analysis['anomalies'] as $anomaly)
            <div class="alert alert-{{ $anomaly['severity'] === 'high' ? 'danger' : 'warning' }}">
                <strong>{{ DateTime::createFromFormat('!m', $anomaly['month'])->format('F') }}</strong>: {{ $anomaly['message'] }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(count($analysis['recommendations']) > 0)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5>AI Recommendations</h5>
        </div>
        <div class="card-body">
            @foreach($analysis['recommendations'] as $rec)
            <div class="d-flex align-items-start mb-2">
                <span class="badge bg-{{ $rec['priority'] === 'high' ? 'danger' : 'info' }} me-2">{{ $rec['priority'] }}</span>
                <span>{{ $rec['message'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($analysis['forecast']['projected_total']))
    <div class="card mb-4">
        <div class="card-header">
            <h5>Year-End Forecast</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Monthly Average:</strong> ${{ number_format($analysis['forecast']['monthly_average'], 2) }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Projected Remaining:</strong> ${{ number_format($analysis['forecast']['projected_spending'], 2) }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Projected Total:</strong> ${{ number_format($analysis['forecast']['projected_total'], 2) }}</p>
                </div>
            </div>
            <p class="text-muted">Confidence: {{ ucfirst($analysis['forecast']['confidence']) }}</p>
        </div>
    </div>
    @endif

    @else
    <div class="alert alert-info">
        Select a cost centre to view AI-powered financial analysis.
    </div>
    @endif
</div>
@endsection