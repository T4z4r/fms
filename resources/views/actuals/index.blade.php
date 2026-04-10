@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-receipt"></i> Actuals</h4>
        <a href="{{ route('actuals.create') }}" class="btn btn-primary">Add Actual</a>
    </div>

    <form method="GET" class="mb-4 filter-form-mobile">
        <select name="cost_centre_id" class="form-select">
            <option value="">All Cost Centres</option>
            @foreach($costCentres as $cc)
                <option value="{{ $cc->id }}">{{ $cc->name }}</option>
            @endforeach
        </select>
        <select name="year" class="form-select">
            <option value="">All Years</option>
            @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
        <select name="month" class="form-select">
            <option value="">All Months</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Cost Centre</th>
                <th>Account</th>
                <th>Year</th>
                <th>Month</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actuals as $actual)
            <tr>
                <td>{{ $actual->costCentre?->name }}</td>
                <td>{{ $actual->account?->name }} ({{ $actual->account?->code }})</td>
                <td>{{ $actual->year }}</td>
                <td>{{ DateTime::createFromFormat('!m', $actual->month)->format('F') }}</td>
                <td>£{{ number_format($actual->amount, 2) }}</td>
                <td>
                    <a href="{{ route('actuals.show', $actual) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('actuals.edit', $actual) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('actuals.destroy', $actual) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" data-confirm="Are you sure you want to delete this actual?">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($actuals->isEmpty())
        <div class="text-center text-muted py-4">No actuals found.</div>
    @endif

    <div class="d-flex justify-content-center">
        {{ $actuals->links() }}
    </div>
</div>
@endsection