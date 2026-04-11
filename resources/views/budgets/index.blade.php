@extends('layouts.app')

@section('content')
    <div class="container page-shell">
        <div class="page-header">
            <h4 class="page-title"><i class="bi bi-calculator text-primary"></i> Budgets</h4>
            <div class="page-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#budgetModal"
                    data-mode="create">Add Budget</button>
            </div>
        </div>

        <form method="GET" class="mb-4 responsive-filter-form">
            <select name="cost_centre_id" class="form-select">
                <option value="">All Cost Centres</option>
                @foreach ($costCentres as $cc)
                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                @endforeach
            </select>
            <select name="year" class="form-select">
                <option value="">All Years</option>
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="excel-table table table-hover table-sm mb-0">
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
                            @foreach ($budgets as $budget)
                                <tr>
                                    <td>{{ $budget->costCentre?->name }}</td>
                                    <td>{{ $budget->account?->name }} ({{ $budget->account?->code }})</td>
                                    <td>{{ $budget->year }}</td>
                                    <td>£{{ number_format($budget->annual_budget, 2) }}</td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="{{ route('budgets.show', $budget) }}"
                                                class="btn btn-sm btn-primary">View</a>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#budgetModal" data-mode="edit"
                                                data-id="{{ $budget->id }}"
                                                data-cost_centre_id="{{ $budget->cost_centre_id }}"
                                                data-account_id="{{ $budget->account_id }}"
                                                data-annual_budget="{{ $budget->annual_budget }}"
                                                data-year="{{ $budget->year }}">Edit</button>
                                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    data-confirm="Are you sure you want to delete this budget?">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="pagination-shell mt-3">
            {{ $budgets->links() }}
        </div>
    </div>

    <div class="modal fade" id="budgetModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="budgetModalTitle">Add Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="budgetForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="_method" id="budgetMethod">
                        <div class="mb-3">
                            <label>Cost Centre</label>
                            <select name="cost_centre_id" id="budgetCostCentre" class="form-control" required>
                                <option value="">Select Cost Centre</option>
                                @foreach ($costCentres as $cc)
                                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Account</label>
                            <select name="account_id" id="budgetAccount" class="form-control" required>
                                <option value="">Select Account</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->code }}) -
                                        {{ $account->costCentre?->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Annual Budget</label>
                            <input type="number" name="annual_budget" id="budgetAmount" class="form-control" step="0.01"
                                required>
                        </div>
                        <div class="mb-3">
                            <label>Year</label>
                            <select name="year" id="budgetYear" class="form-control" required>
                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var budgetModal = document.getElementById('budgetModal');
            budgetModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var mode = button.getAttribute('data-mode');
                var modalTitle = document.getElementById('budgetModalTitle');
                var form = document.getElementById('budgetForm');
                var methodInput = document.getElementById('budgetMethod');
                var currentYear = new Date().getFullYear();

                if (mode === 'create') {
                    modalTitle.textContent = 'Add Budget';
                    form.action = '{{ route('budgets.store') }}';
                    methodInput.value = '';
                    document.getElementById('budgetCostCentre').value = '';
                    document.getElementById('budgetAccount').value = '';
                    document.getElementById('budgetAmount').value = '';
                    document.getElementById('budgetYear').value = currentYear;
                } else {
                    modalTitle.textContent = 'Edit Budget';
                    var id = button.getAttribute('data-id');
                    form.action = '/budgets/' + id;
                    methodInput.value = 'PUT';
                    document.getElementById('budgetCostCentre').value = button.getAttribute(
                        'data-cost_centre_id');
                    document.getElementById('budgetAccount').value = button.getAttribute('data-account_id');
                    document.getElementById('budgetAmount').value = button.getAttribute(
                        'data-annual_budget');
                    document.getElementById('budgetYear').value = button.getAttribute('data-year');
                }
            });
        });
    </script>
@endsection
