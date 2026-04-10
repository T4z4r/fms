@extends('layouts.app')

@section('content')
    <div class="container">
        <h4><i class="bi bi-file-earmark-ruled text-primary"></i> Reports</h4>

        <form method="GET" class="mb-4 d-flex gap-2">
            <select name="year" class="form-select">
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <table class="excel-table table table-striped table-hover table-sm  ">
            <thead>
                <tr>
                    <th>Cost Centre</th>
                    <th>Total Budget</th>
                    <th>Total Actuals</th>
                    <th>Variance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($costCentres as $cc)
                    @php
                        $budgetTotal = $cc->budgets->sum('annual_budget');
                        $actualGroup = $actuals->get($cc->id);
                        $actualTotal = $actualGroup ? $actualGroup->sum('total') : 0;
                        $variance = $budgetTotal - $actualTotal;
                    @endphp
                    <tr>
                        <td>{{ $cc->name }}</td>
                        <td>£{{ number_format($budgetTotal, 2) }}</td>
                        <td>£{{ number_format($actualTotal, 2) }}</td>
                        <td class="{{ $variance >= 0 ? 'text-success' : 'text-danger' }}">
                            £{{ number_format($variance, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
