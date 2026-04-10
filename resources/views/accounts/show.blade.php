@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('accounts.index') }}" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <h4><i class="bi bi-wallet2"></i> {{ $account->name }} ({{ $account->code }})</h4>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Cost Centre</h5>
                    <p>{{ $account->costCentre?->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3>Budgets ({{ $account->budgets->count() }})</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Year</th>
                <th>Annual Budget</th>
            </tr>
        </thead>
        <tbody>
            @foreach($account->budgets as $budget)
            <tr>
                <td>{{ $budget->year }}</td>
                <td>£{{ number_format($budget->annual_budget, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Actuals ({{ $account->actuals->count() }})</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($account->actuals as $actual)
            <tr>
                <td>{{ $actual->year }}</td>
                <td>{{ $actual->month }}</td>
                <td>£{{ number_format($actual->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection