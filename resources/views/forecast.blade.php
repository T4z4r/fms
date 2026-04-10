@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Spending Forecast</h4>
    </div>

    <form method="GET" class="mb-4 filter-form-mobile">
        <select name="year" class="form-select">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
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

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Forecast Methodology</h5>
        </div>
        <div class="card-body">
            <p class="mb-0 small">Based on last 3 months spending average × remaining months = projected year-end spending</p>
        </div>
    </div>

    <div class="table-responsive-mobile">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Annual Budget</th>
                    <th>Current Spending</th>
                    <th>Monthly Avg</th>
                    <th>Forecast</th>
                    <th>Projected Total</th>
                    <th>Variance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forecasts as $item)
                <tr>
                    <td>{{ $item['account']->name }} ({{ $item['account']->code }})</td>
                    <td>£{{ number_format($item['budget'], 2) }}</td>
                    <td>£{{ number_format($item['current_spending'], 2) }}</td>
                    <td>£{{ number_format($item['monthly_average'], 2) }}</td>
                    <td>£{{ number_format($item['forecast'], 2) }}</td>
                    <td>£{{ number_format($item['projected_total'], 2) }}</td>
                    <td class="{{ $item['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                        £{{ number_format($item['variance'], 2) }}
                        <small>({{ $item['variance'] >= 0 ? 'Under' : 'Over' }})</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No budget data for selected filters.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection