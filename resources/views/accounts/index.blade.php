@extends('layouts.app')

@section('content')
    <div class="container page-shell">
        <div class="page-header">
            <h4 class="page-title"><i class="bi bi-wallet2 text-primary"></i> Accounts</h4>
            <div class="page-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#accountModal"
                    data-mode="create">Add Account</button>
            </div>
        </div>

        <form method="GET" class="mb-4 responsive-filter-form">
            <select name="cost_centre_id" class="form-select">
                <option value="">All Cost Centres</option>
                @foreach ($costCentres as $cc)
                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                @endforeach
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
                                <th>Code</th>
                                <th>Name</th>
                                <th>Cost Centre</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                                <tr>
                                    <td>{{ $account->code }}</td>
                                    <td>{{ $account->name }}</td>
                                    <td>{{ $account->costCentre?->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="{{ route('accounts.show', $account) }}"
                                                class="btn btn-sm btn-primary">View</a>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#accountModal" data-mode="edit"
                                                data-id="{{ $account->id }}" data-code="{{ $account->code }}"
                                                data-name="{{ $account->name }}"
                                                data-cost_centre_id="{{ $account->cost_centre_id }}">Edit</button>
                                            <form action="{{ route('accounts.destroy', $account) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    data-confirm="Are you sure you want to delete this account?">Delete</button>
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
        <div class="pagination-shell mt-3">
            {{ $accounts->links() }}
        </div>
    </div>

    <div class="modal fade" id="accountModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountModalTitle">Add Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="accountForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="_method" id="accountMethod">
                        <div class="mb-3">
                            <label>Code</label>
                            <input type="text" name="code" id="accountCode" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" id="accountName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Cost Centre</label>
                            <select name="cost_centre_id" id="accountCostCentre" class="form-control" required>
                                <option value="">Select Cost Centre</option>
                                @foreach ($costCentres as $cc)
                                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                                @endforeach
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
            var accountModal = document.getElementById('accountModal');
            accountModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var mode = button.getAttribute('data-mode');
                var modalTitle = document.getElementById('accountModalTitle');
                var form = document.getElementById('accountForm');
                var methodInput = document.getElementById('accountMethod');

                if (mode === 'create') {
                    modalTitle.textContent = 'Add Account';
                    form.action = '{{ route('accounts.store') }}';
                    methodInput.value = '';
                    document.getElementById('accountCode').value = '';
                    document.getElementById('accountName').value = '';
                    document.getElementById('accountCostCentre').value = '';
                } else {
                    modalTitle.textContent = 'Edit Account';
                    var id = button.getAttribute('data-id');
                    form.action = '/accounts/' + id;
                    methodInput.value = 'PUT';
                    document.getElementById('accountCode').value = button.getAttribute('data-code');
                    document.getElementById('accountName').value = button.getAttribute('data-name');
                    document.getElementById('accountCostCentre').value = button.getAttribute(
                        'data-cost_centre_id');
                }
            });
        });
    </script>
@endsection
