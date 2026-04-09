@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Actual Details</h1>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Cost Centre</h5>
                    <p>{{ $actual->costCentre?->name }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Account</h5>
                    <p>{{ $actual->account?->name }} ({{ $actual->account?->code }})</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Period</h5>
                    <p>{{ DateTime::createFromFormat('!m', $actual->month)->format('F') }} {{ $actual->year }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Amount</h5>
                    <p>${{ number_format($actual->amount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3>Transaction Details</h3>
    <form action="{{ route('actuals.details', $actual) }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="description" class="form-control" placeholder="Description">
            </div>
            <div class="col-md-3">
                <input type="date" name="transaction_date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($actual->details as $detail)
            <tr>
                <td>{{ $detail->description ?? 'N/A' }}</td>
                <td>{{ $detail->transaction_date }}</td>
            </tr>
            @empty
            <tr><td colspan="2">No details yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection