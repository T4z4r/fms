@extends('layouts.app')

@section('content')
<div class="container">
    <h4><i class="bi bi-pencil"></i> Edit Budget</h4>
    <form action="{{ route('budgets.update', $budget) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Cost Centre</label>
            <select name="cost_centre_id" class="form-control" required>
                @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}" {{ $budget->cost_centre_id == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Account</label>
            <select name="account_id" class="form-control" required>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ $budget->account_id == $account->id ? 'selected' : '' }}>
                        {{ $account->name }} ({{ $account->code }}) - {{ $account->costCentre?->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Annual Budget</label>
            <input type="number" name="annual_budget" class="form-control" step="0.01" value="{{ $budget->annual_budget }}" required>
        </div>
        <div class="mb-3">
            <label>Year</label>
            <select name="year" class="form-control" required>
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $budget->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('budgets.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection