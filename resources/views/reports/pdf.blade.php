<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $companyName }} Financial Report {{ $year }}</title>
    <style>
        @page {
            margin: 28px;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            color: #212529;
            background: #fff;
        }

        .container {
            width: 100%;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .text-muted {
            color: #6c757d;
        }

        .text-success {
            color: #198754;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: 700;
        }

        .small {
            font-size: 11px;
        }

        .card {
            border: 1px solid #dee2e6;
            margin-bottom: 1rem;
        }

        .card-header {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            font-weight: 700;
        }

        .card-body {
            padding: 12px;
        }

        .summary-table,
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td,
        .report-table th,
        .report-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: top;
        }

        .summary-table td {
            width: 25%;
        }

        .report-table thead th {
            background: #f8f9fa;
            text-align: left;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
        }

        .badge-success {
            background: #198754;
        }

        .badge-danger {
            background: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="mb-4">
            <h1 class="fw-bold" style="margin: 0 0 6px;">{{ $companyName }} Budget Performance Report</h1>
            <div class="text-muted">Financial Management System</div>
            <div class="small text-muted" style="margin-top: 6px;">
                Reporting year: {{ $year }} | Generated: {{ $generatedAt->format('M d, Y H:i') }} | Currency: {{ $currencyCode }}
            </div>
        </div>

        <div class="card">
            <div class="card-header">Executive Summary</div>
            <div class="card-body">
                <table class="summary-table">
                    <tr>
                        <td>
                            <div class="text-muted small">Budgeted</div>
                            <div class="fw-bold">{{ $currencySymbol }}{{ number_format($totals['budget_total'], 2) }}</div>
                        </td>
                        <td>
                            <div class="text-muted small">Actuals</div>
                            <div class="fw-bold">{{ $currencySymbol }}{{ number_format($totals['actual_total'], 2) }}</div>
                        </td>
                        <td>
                            <div class="text-muted small">Net Variance</div>
                            <div class="fw-bold {{ $totals['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $currencySymbol }}{{ number_format($totals['variance'], 2) }}
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small">Portfolio Health</div>
                            <div class="fw-bold">{{ $underBudgetCount }}/{{ $costCentreCount }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Cost Centre Breakdown</div>
            <div class="card-body">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Cost Centre</th>
                            <th>Owner</th>
                            <th>Budget</th>
                            <th>Actuals</th>
                            <th>Variance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td class="fw-bold">{{ $row['name'] }}</td>
                                <td>{{ $row['owner'] ?: 'Unassigned' }}</td>
                                <td>{{ $currencySymbol }}{{ number_format($row['budget_total'], 2) }}</td>
                                <td>{{ $currencySymbol }}{{ number_format($row['actual_total'], 2) }}</td>
                                <td class="{{ $row['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $currencySymbol }}{{ number_format($row['variance'], 2) }}
                                </td>
                                <td>
                                    <span class="badge {{ $row['variance'] >= 0 ? 'badge-success' : 'badge-danger' }}">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted">No report data is available for {{ $year }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-end text-muted small">
            {{ $companyName }} | Financial report generated by {{ config('app.name', 'FMS') }}
        </div>
    </div>
</body>

</html>
