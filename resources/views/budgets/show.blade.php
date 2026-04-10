@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('budgets.index') }}" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <h4><i class="bi bi-calculator"></i> Budget Details</h4>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Cost Centre</h5>
                    <p>{{ $budget->costCentre?->name }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Account</h5>
                    <p>{{ $budget->account?->name }} ({{ $budget->account?->code }})</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Year</h5>
                    <p>{{ $budget->year }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Annual Budget</h5>
                    <p>${{ number_format($budget->annual_budget, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3>Monthly Breakdown</h3>
    <form action="{{ route('budgets.lines', $budget) }}" method="POST">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @for($m = 1; $m <= 12; $m++)
                <tr>
                    <td>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</td>
                    <td>
                        <input type="number" name="lines[{{ $m - 1 }}]" class="form-control" 
                               value="{{ $budget->budgetLines[$m-1]->amount ?? 0 }}" step="0.01">
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Update Lines</button>
    </form>
</div>
@endsection