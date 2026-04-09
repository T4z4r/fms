@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dashboard</h1>
        <form method="GET" class="d-flex gap-2">
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
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Budget</h5>
                    <h3>${{ number_format(array_sum(array_column($summary, 'budget')), 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Actuals</h5>
                    <h3>${{ number_format(array_sum(array_column($summary, 'actual')), 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Variance</h5>
                    <h3 class="{{ array_sum(array_column($summary, 'variance')) >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format(array_sum(array_column($summary, 'variance')), 2) }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Budget vs Actuals by Account</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Budget</th>
                        <th>Actual</th>
                        <th>Variance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $item)
                    <tr>
                        <td>{{ $item['account']->name }} ({{ $item['account']->code }})</td>
                        <td>${{ number_format($item['budget'], 2) }}</td>
                        <td>${{ number_format($item['actual'], 2) }}</td>
                        <td class="{{ $item['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                            ${{ number_format($item['variance'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Monthly Actuals vs Budget</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Budget</th>
                        <th>Actual</th>
                    </tr>
                </thead>
                <tbody>
                    @for($m = 1; $m <= 12; $m++)
                    <tr>
                        <td>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</td>
                        <td>${{ number_format($budgetMonthly[$m] ?? 0, 2) }}</td>
                        <td>${{ number_format($monthlyData[$m] ?? 0, 2) }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection