@extends('layouts.app')

@php
function parseMarkdown($text) {
    $html = e($text);
    $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
    $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
    $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
    $html = preg_replace('/(<\/li>\n<li>)/', '$1', $html);
    $html = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $html);
    $html = preg_replace('/\n\n/', '</p><p>', $html);
    $html = '<p>' . $html . '</p>';
    $html = preg_replace('/<p><\/p>/', '', $html);
    $html = preg_replace('/<p>(<h[123]>)/', '$1', $html);
    $html = preg_replace('/(<\/h[123]>)<\/p>/', '$1', $html);
    return $html;
}
@endphp

@section('content')
<style>
.ai-commentary h1, .ai-commentary h2, .ai-commentary h3 { margin-top: 1rem; margin-bottom: 0.5rem; font-weight: 600; }
.ai-commentary h1 { font-size: 1.5rem; }
.ai-commentary h2 { font-size: 1.25rem; }
.ai-commentary h3 { font-size: 1.1rem; }
.ai-commentary p { margin-bottom: 0.75rem; }
.ai-commentary ul, .ai-commentary ol { margin-bottom: 0.75rem; padding-left: 1.5rem; }
.ai-commentary li { margin-bottom: 0.25rem; }
.ai-commentary code { background: #f4f4f4; padding: 0.125rem 0.25rem; border-radius: 3px; font-size: 0.9em; }
.ai-commentary pre { background: #f4f4f4; padding: 0.75rem; border-radius: 5px; overflow-x: auto; margin-bottom: 0.75rem; }
.ai-commentary pre code { background: none; padding: 0; }
.ai-commentary blockquote { border-left: 3px solid #ddd; padding-left: 0.75rem; margin-left: 0; color: #666; }
.ai-commentary table { width: 100%; margin-bottom: 0.75rem; border-collapse: collapse; }
.ai-commentary th, .ai-commentary td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
.ai-commentary th { background: #f4f4f4; }
.ai-commentary hr { border: none; border-top: 1px solid #ddd; margin: 1rem 0; }
.ai-commentary strong { font-weight: 600; }
</style>
<div class="container-fluid px-3">
    <h4 class="mb-4"><i class="bi bi-cpu"></i> AI Financial Analysis</h4>

    <form method="GET" class="row g-2 mb-4 filter-form-mobile">
        <div class="col-12 col-md-4">
            <label class="small">Cost Centre</label>
            <select name="cost_centre_id" class="form-select" onchange="this.form.submit()">
                <option value="">Select Cost Centre</option>
                @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}" {{ $selectedCostCentre?->id == $cc->id ? 'selected' : '' }}>
                        {{ $cc->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="small">Year</label>
            <select name="year" class="form-select" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>

    @if($selectedCostCentre && isset($analysis))
    
    @if(isset($aiCommentary))
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">AI Financial Commentary</h5>
            <button class="btn btn-sm btn-light" onclick="copyCommentary()">Copy</button>
        </div>
        <div class="card-body ai-commentary">
            {!! parseMarkdown($aiCommentary['full_commentary']) !!}
        </div>
    </div>
    <script>
        function copyCommentary() {
            const text = document.querySelector('.ai-commentary').innerText;
            navigator.clipboard.writeText(text);
        }
    </script>
    @endif

    <div class="row mb-4 g-2">
        <div class="col-6 col-md-3">
            <div class="card bg-{{ $analysis['summary']['status'] === 'over_budget' ? 'danger' : ($analysis['summary']['status'] === 'near_limit' ? 'warning' : 'success') }} text-white h-100">
                <div class="card-body">
                    <h6 class="small">Status</h6>
                    <h5 class="mb-0">{{ ucwords(str_replace('_', ' ', $analysis['summary']['status'])) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="small">Total Budget</h6>
                    <h5 class="mb-0">£{{ number_format($analysis['summary']['total_budget'], 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="small">Total Spent</h6>
                    <h5 class="mb-0">£{{ number_format($analysis['summary']['total_actual'], 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="small">Utilization</h6>
                    <h5 class="mb-0">{{ round($analysis['summary']['utilization_rate']) }}%</h5>
                </div>
            </div>
        </div>
    </div>

    @if(count($analysis['insights']) > 0)
    <div class="card mb-4 border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">AI Insights</h5>
        </div>
        <div class="card-body">
            @foreach($analysis['insights'] as $insight)
            <div class="mb-3">
                <strong>{{ $insight['title'] }}</strong>
                <p class="mb-0">{{ $insight['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row mb-4 g-3">
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Trend</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive-mobile">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Budget</th>
                                    <th>Actual</th>
                                    <th>Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analysis['monthly_trend'] as $month)
                                <tr class="{{ $month['variance_percentage'] < -20 ? 'table-danger' : '' }}">
                                    <td>{{ $month['month_name'] }}</td>
                                    <td>£{{ number_format($month['budget']) }}</td>
                                    <td>£{{ number_format($month['actual']) }}</td>
                                    <td class="{{ $month['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $month['variance'] >= 0 ? '+' : '' }}£{{ number_format($month['variance']) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Account Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive-mobile">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Budget</th>
                                    <th>Actual</th>
                                    <th>Utilization</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analysis['account_analysis'] as $account)
                                <tr class="{{ $account['utilization'] > 100 ? 'table-danger' : ($account['utilization'] > 80 ? 'table-warning' : '') }}">
                                    <td>{{ $account['account_name'] }}</td>
                                    <td>£{{ number_format($account['budget']) }}</td>
                                    <td>£{{ number_format($account['actual']) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $account['utilization'] > 100 ? 'danger' : ($account['utilization'] > 80 ? 'warning' : 'success') }}">
                                            {{ round($account['utilization']) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(count($analysis['anomalies']) > 0)
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning">
            <h5>Detected Anomalies</h5>
        </div>
        <div class="card-body">
            @foreach($analysis['anomalies'] as $anomaly)
            <div class="alert alert-{{ $anomaly['severity'] === 'high' ? 'danger' : 'warning' }}">
                <strong>{{ DateTime::createFromFormat('!m', $anomaly['month'])->format('F') }}</strong>: {{ $anomaly['message'] }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(count($analysis['recommendations']) > 0)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5>AI Recommendations</h5>
        </div>
        <div class="card-body">
            @foreach($analysis['recommendations'] as $rec)
            <div class="d-flex align-items-start mb-2">
                <span class="badge bg-{{ $rec['priority'] === 'high' ? 'danger' : 'info' }} me-2">{{ $rec['priority'] }}</span>
                <span>{{ $rec['message'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($analysis['forecast']['projected_total']))
    <div class="card mb-4">
        <div class="card-header">
            <h5>Year-End Forecast</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6 col-md-4">
                    <p class="small mb-1"><strong>Monthly Average:</strong></p>
                    <p class="mb-0">£{{ number_format($analysis['forecast']['monthly_average'], 2) }}</p>
                </div>
                <div class="col-6 col-md-4">
                    <p class="small mb-1"><strong>Projected Remaining:</strong></p>
                    <p class="mb-0">£{{ number_format($analysis['forecast']['projected_spending'], 2) }}</p>
                </div>
                <div class="col-12 col-md-4">
                    <p class="small mb-1"><strong>Projected Total:</strong></p>
                    <p class="mb-0">£{{ number_format($analysis['forecast']['projected_total'], 2) }}</p>
                </div>
            </div>
            <p class="text-muted small mb-0">Confidence: {{ ucfirst($analysis['forecast']['confidence']) }}</p>
        </div>
    </div>
    @endif

    @else
    <div class="alert alert-info">
        Select a cost centre to view AI-powered financial analysis.
    </div>
    @endif
</div>
@endsection