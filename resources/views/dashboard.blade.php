@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Financial Dashboard</h4>
    </div>

    <form method="GET" class="mb-4 filter-form-mobile">
        <select name="year" class="form-select">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <select name="cost_centre_id" class="form-select">
            <option value="">All Cost Centres</option>
            @foreach($costCentres as $cc)
                <option value="{{ $cc->id }}" {{ $costCentreId == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="row mb-4 g-2">
        <div class="col-6 col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title small">Annual Budget</h6>
                    <h5 class="mb-0">£{{ number_format($totalBudget, 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="card-title small">YTD Budget</h6>
                    <h5 class="mb-0">£{{ number_format($ytdBudget, 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title small">YTD Actual</h6>
                    <h5 class="mb-0">£{{ number_format($ytdActual, 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card {{ $ytdVariance >= 0 ? 'bg-success' : 'bg-danger' }} text-white h-100">
                <div class="card-body">
                    <h6 class="card-title small">YTD Variance</h6>
                    <h5 class="mb-0">£{{ number_format($ytdVariance, 0) }}</h5>
                </div>
            </div>
        </div>
    </div>

    @if(isset($quickInsight))
    <div class="alert alert-info mb-4">
        <strong>AI Insight:</strong> {{ $quickInsight }}
    </div>
    @endif

    @if($alerts->count() > 0)
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Active Alerts ({{ $alerts->count() }})</h5>
                <a href="{{ route('alerts') }}" class="btn btn-sm btn-dark">View All</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($alerts->take(4) as $alert)
                <div class="col-12 col-md-6">
                    <div class="alert alert-{{ $alert->severity === 'high' ? 'danger' : 'warning' }} mb-0">
                        <small>{{ $alert->type }}</small>
                        <p class="mb-0 small">{{ $alert->message }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="row mb-4 g-3">
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Budget vs Actual Trend</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive-mobile">
                        <table class="table table-sm table-bordered table-scrollable">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Budget</th>
                                    <th class="text-end">Actual</th>
                                    <th class="text-end">Variance</th>
                                    <th class="text-end">YTD Budget</th>
                                    <th class="text-end">YTD Actual</th>
                                    <th class="text-end">YTD Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyData as $data)
                                <tr class="{{ $data['month'] > now()->month ? 'table-secondary' : '' }}">
                                    <td>{{ $data['month_name'] }}</td>
                                    <td class="text-end">£{{ number_format($data['budget'], 2) }}</td>
                                    <td class="text-end">£{{ number_format($data['actual'], 2) }}</td>
                                    <td class="text-end {{ $data['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        £{{ number_format($data['variance'], 2) }}
                                    </td>
                                    <td class="text-end">£{{ number_format($data['cumulative_budget'], 2) }}</td>
                                    <td class="text-end">£{{ number_format($data['cumulative_actual'], 2) }}</td>
                                    <td class="text-end {{ $data['cumulative_variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        £{{ number_format($data['cumulative_variance'], 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Forecast</h5>
                </div>
                <div class="card-body">
                    @if(isset($forecast['message']))
                        <p class="text-muted">{{ $forecast['message'] }}</p>
                    @else
                        <ul class="list-unstyled small">
                            <li class="mb-2"><strong>Monthly Avg:</strong> £{{ number_format($forecast['monthly_average'], 2) }}</li>
                            <li class="mb-2"><strong>Remaining Months:</strong> {{ $forecast['remaining_months'] }}</li>
                            <li class="mb-2"><strong>Current Spending:</strong> £{{ number_format($forecast['current_spending'], 2) }}</li>
                            <li class="mb-2"><strong>Forecast Spending:</strong> £{{ number_format($forecast['forecast_spending'], 2) }}</li>
                            <li class="mb-2"><strong>Projected Total:</strong> £{{ number_format($forecast['projected_total'], 2) }}</li>
                            <li class="mb-2">
                                <strong>Projected Variance:</strong> 
                                <span class="{{ $forecast['projected_variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    £{{ number_format($forecast['projected_variance'], 2) }}
                                </span>
                            </li>
                            <li><strong>Confidence:</strong> 
                                <span class="badge bg-{{ $forecast['confidence'] === 'high' ? 'success' : ($forecast['confidence'] === 'medium' ? 'warning' : 'danger') }}">
                                    {{ strtoupper($forecast['confidence']) }}
                                </span>
                            </li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Budget vs Actuals by Account</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive-mobile">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th class="text-end">Budget</th>
                            <th class="text-end">Actual</th>
                            <th class="text-end">Variance</th>
                            <th class="text-end">Variance %</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accountSummary as $item)
                        <tr>
                            <td>{{ $item['account_name'] }} ({{ $item['account_code'] }})</td>
                            <td class="text-end">£{{ number_format($item['budget'], 2) }}</td>
                            <td class="text-end">£{{ number_format($item['actual'], 2) }}</td>
                            <td class="text-end {{ $item['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                £{{ number_format($item['variance'], 2) }}
                            </td>
                            <td class="text-end {{ $item['variance_percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($item['variance_percentage'], 1) }}%
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $item['status'] === 'critical' ? 'danger' : 
                                    ($item['status'] === 'warning' ? 'warning' : 
                                    ($item['status'] === 'underspent' ? 'info' : 'success'))
                                }}">
                                    {{ str_replace('_', ' ', strtoupper($item['status'])) }}
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
@endsection
