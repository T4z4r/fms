@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-speedometer2 text-primary"></i> Financial Dashboard</h4>
            <form method="GET" class="d-flex gap-2">
                <select name="year" class="form-select">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="cost_centre_id" class="form-select">
                    <option value="">All Cost Centres</option>
                    @foreach ($costCentres as $cc)
                        <option value="{{ $cc->id }}" {{ $costCentreId == $cc->id ? 'selected' : '' }}>
                            {{ $cc->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Annual Budget</h6>
                        <h3>£{{ number_format($totalBudget, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">YTD Budget</h6>
                        <h3>£{{ number_format($ytdBudget, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">YTD Actual</h6>
                        <h3>£{{ number_format($ytdActual, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card {{ $ytdVariance >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                    <div class="card-body">
                        <h6 class="card-title">YTD Variance</h6>
                        <h3>£{{ number_format($ytdVariance, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($quickInsight))
            <div class="alert alert-info mb-4">
                <strong>AI Insight:</strong> {{ $quickInsight }}
            </div>
        @endif

        @if ($alerts->count() > 0)
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Active Alerts ({{ $alerts->count() }})</h5>
                        <a href="{{ route('alerts') }}" class="btn btn-sm btn-dark">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($alerts->take(4) as $alert)
                            <div class="col-md-6 mb-2">
                                <div class="alert alert-{{ $alert->severity === 'high' ? 'danger' : 'warning' }} mb-0">
                                    <small>{{ $alert->type }}</small>
                                    <p class="mb-0">{{ $alert->message }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Monthly Budget vs Actual Trend</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="excel-table table table-sm">
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
                                    @foreach ($monthlyData as $data)
                                        <tr class="{{ $data['month'] > now()->month ? 'table-secondary' : '' }}">
                                            <td>{{ $data['month_name'] }}</td>
                                            <td class="text-end">£{{ number_format($data['budget'], 2) }}</td>
                                            <td class="text-end">£{{ number_format($data['actual'], 2) }}</td>
                                            <td
                                                class="text-end {{ $data['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                £{{ number_format($data['variance'], 2) }}
                                            </td>
                                            <td class="text-end">£{{ number_format($data['cumulative_budget'], 2) }}</td>
                                            <td class="text-end">£{{ number_format($data['cumulative_actual'], 2) }}</td>
                                            <td
                                                class="text-end {{ $data['cumulative_variance'] >= 0 ? 'text-success' : 'text-danger' }}">
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

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Forecast</h5>
                    </div>
                    <div class="card-body">
                        @if (isset($forecast['message']))
                            <p class="text-muted">{{ $forecast['message'] }}</p>
                        @else
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>Monthly Avg:</strong>
                                    £{{ number_format($forecast['monthly_average'], 2) }}</li>
                                <li class="mb-2"><strong>Remaining Months:</strong> {{ $forecast['remaining_months'] }}
                                </li>
                                <li class="mb-2"><strong>Current Spending:</strong>
                                    £{{ number_format($forecast['current_spending'], 2) }}</li>
                                <li class="mb-2"><strong>Forecast Spending:</strong>
                                    £{{ number_format($forecast['forecast_spending'], 2) }}</li>
                                <li class="mb-2"><strong>Projected Total:</strong>
                                    £{{ number_format($forecast['projected_total'], 2) }}</li>
                                <li class="mb-2">
                                    <strong>Projected Variance:</strong>
                                    <span
                                        class="{{ $forecast['projected_variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        £{{ number_format($forecast['projected_variance'], 2) }}
                                    </span>
                                </li>
                                <li><strong>Confidence:</strong>
                                    <span
                                        class="badge bg-{{ $forecast['confidence'] === 'high' ? 'success' : ($forecast['confidence'] === 'medium' ? 'warning' : 'danger') }}">
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
                <h5>Budget vs Actuals by Account</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="excel-table table">
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
                            @foreach ($accountSummary as $item)
                                <tr>
                                    <td>{{ $item['account_name'] }} ({{ $item['account_code'] }})</td>
                                    <td class="text-end">£{{ number_format($item['budget'], 2) }}</td>
                                    <td class="text-end">£{{ number_format($item['actual'], 2) }}</td>
                                    <td class="text-end {{ $item['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        £{{ number_format($item['variance'], 2) }}
                                    </td>
                                    <td
                                        class="text-end {{ $item['variance_percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($item['variance_percentage'], 1) }}%
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $item['status'] === 'critical'
                                                ? 'danger'
                                                : ($item['status'] === 'warning'
                                                    ? 'warning'
                                                    : ($item['status'] === 'underspent'
                                                        ? 'info'
                                                        : 'success')) }}">
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
