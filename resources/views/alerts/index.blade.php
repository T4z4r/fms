@extends('layouts.app')

@section('content')
    <div class="container page-shell">
        <div class="page-header">
            <h4 class="page-title"><i class="bi bi-bell text-primary"></i> Alerts</h4>
            <div class="page-actions">
                <form method="POST" action="{{ route('alerts.generate') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Generate Alerts</button>
                </form>
                <form method="GET" class="responsive-filter-form">
                    <div class="form-check d-flex align-items-center m-0">
                        <input type="checkbox" name="unread" value="1" {{ request('unread') ? 'checked' : '' }}
                            class="form-check-input me-2 mt-0" id="unread">
                        <label class="form-check-label" for="unread">Unread only</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="excel-table table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Message</th>
                                <th>Cost Centre</th>
                                <th>Account</th>
                                <th>Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alerts as $alert)
                                <tr class="{{ $alert->is_read ? '' : 'table-warning' }}">
                                    <td>
                                        <span class="badge bg-danger">{{ str_replace('_', ' ', $alert->type) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $severityClass = match ($alert->severity ?? 'low') {
                                                'high' => 'bg-danger',
                                                'medium' => 'bg-warning',
                                                default => 'bg-info',
                                            };
                                        @endphp
                                        <span class="badge {{ $severityClass }}">{{ $alert->severity ?? 'low' }}</span>
                                    </td>
                                    <td>{{ $alert->message }}</td>
                                    <td>{{ $alert->costCentre?->name ?? 'N/A' }}</td>
                                    <td>{{ $alert->account?->name ?? 'N/A' }}</td>
                                    <td>{{ $alert->month }}/{{ $alert->year }}</td>
                                    <td>
                                        @if ($alert->is_read)
                                            <span class="badge bg-secondary">Read</span>
                                        @else
                                            <span class="badge bg-warning">New</span>
                                        @endif
                                    </td>
                                    <td>
                                        @unless ($alert->is_read)
                                            <div class="table-actions">
                                                <form action="{{ route('alerts.markRead', $alert) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-primary">Mark Read</button>
                                                </form>
                                            </div>
                                        @endunless
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No alerts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="pagination-shell mt-3">
            {{ $alerts->links() }}
        </div>
    </div>
@endsection
