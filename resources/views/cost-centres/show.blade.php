@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('cost-centres.index') }}" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <h4><i class="bi bi-diagram-3"></i> {{ $costCentre->name }}</h4>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Owner</h5>
                    <p>{{ $costCentre->owner?->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Status</h5>
                    <span class="badge bg-{{ $costCentre->status === 'active' ? 'success' : 'secondary' }}">
                        {{ $costCentre->status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <h3>Accounts ({{ $costCentre->accounts->count() }})</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($costCentre->accounts as $account)
            <tr>
                <td>{{ $account->code }}</td>
                <td>{{ $account->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Budgets ({{ $costCentre->budgets->count() }})</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Account</th>
                <th>Year</th>
                <th>Annual Budget</th>
            </tr>
        </thead>
        <tbody>
            @foreach($costCentre->budgets as $budget)
            <tr>
                <td>{{ $budget->account->name }}</td>
                <td>{{ $budget->year }}</td>
                <td>${{ number_format($budget->annual_budget, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection