@extends('layouts.app')

@section('content')
<div class="container">
    <h4><i class="bi bi-pencil"></i> Edit Actual</h4>
    <form action="{{ route('actuals.update', $actual) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Cost Centre</label>
            <select name="cost_centre_id" class="form-control" required>
                @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}" {{ $actual->cost_centre_id == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Account</label>
            <select name="account_id" class="form-control" required>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ $actual->account_id == $account->id ? 'selected' : '' }}>
                        {{ $account->name }} ({{ $account->code }}) - {{ $account->costCentre?->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Year</label>
            <select name="year" class="form-control" required>
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $actual->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label>Month</label>
            <select name="month" class="form-control" required>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $actual->month == $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label>Amount</label>
            <input type="number" name="amount" class="form-control" step="0.01" value="{{ $actual->amount }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('actuals.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection