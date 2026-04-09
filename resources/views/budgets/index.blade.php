@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Budgets</h1>
        <a href="{{ route('budgets.create') }}" class="btn btn-primary">Add Budget</a>
    </div>

    <form method="GET" class="mb-4 d-flex gap-2">
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
                <th>Annual Budget</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgets as $budget)
            <tr>
                <td>{{ $budget->costCentre?->name }}</td>
                <td>{{ $budget->account?->name }} ({{ $budget->account?->code }})</td>
                <td>{{ $budget->year }}</td>
                <td>${{ number_format($budget->annual_budget, 2) }}</td>
                <td>
                    <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $budgets->links() }}
</div>
@endsection