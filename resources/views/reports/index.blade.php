@extends('layouts.app')

@section('content')
    <div class="container py-3 page-shell">
        <div class="page-header">
            <div>
                <h4 class="page-title mb-1">
                    <i class="bi bi-file-earmark-ruled text-primary"></i>
                    {{ $companyName }} Reports
                </h4>
                <p class="text-muted mb-0">Review budget versus actual spending by cost centre and export the report as PDF.
                </p>
            </div>

            <div class="page-actions">
                <a href="{{ route('reports.export.pdf', ['year' => $year]) }}" class="btn btn-outline-primary">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Budgeted</div>
                        <div class="fs-5 fw-semibold">{{ $currencySymbol }}{{ number_format($totals['budget_total'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Actuals</div>
                        <div class="fs-5 fw-semibold">{{ $currencySymbol }}{{ number_format($totals['actual_total'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Variance</div>
                        <div class="fs-5 fw-semibold {{ $totals['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $currencySymbol }}{{ number_format($totals['variance'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Cost Centres</div>
                        <div class="fs-5 fw-semibold">{{ $costCentreCount }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="responsive-filter-form">
                    <div class="col-sm-4 col-md-3">
                        <label for="year" class="form-label">Year</label>
                        <select id="year" name="year" class="form-select">
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header">
                <strong>Cost Centre Breakdown</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="excel-table table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Cost Centre</th>
                                <th>Owner</th>
                                <th>Total Budget</th>
                                <th>Total Actuals</th>
                                <th>Variance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="fw-semibold">{{ $row['name'] }}</td>
                                    <td>{{ $row['owner'] ?: 'Unassigned' }}</td>
                                    <td>{{ $currencySymbol }}{{ number_format($row['budget_total'], 2) }}</td>
                                    <td>{{ $currencySymbol }}{{ number_format($row['actual_total'], 2) }}</td>
                                    <td class="{{ $row['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $currencySymbol }}{{ number_format($row['variance'], 2) }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $row['variance'] >= 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No report data is available for
                                        {{ $year }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
