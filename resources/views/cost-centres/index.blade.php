@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-diagram-3 text-primary"></i> Cost Centres</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#costCentreModal" data-mode="create">Add Cost
                Centre</button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="excel-table table table-hover table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($costCentres as $cc)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $cc->name }}</td>
                        <td>{{ $cc->owner?->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $cc->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $cc->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('cost-centres.show', $cc) }}" class="btn btn-sm btn-primary">View</a>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#costCentreModal"
                                data-mode="edit" data-id="{{ $cc->id }}" data-name="{{ $cc->name }}"
                                data-owner="{{ $cc->owner }}" data-status="{{ $cc->status }}">Edit</button>
                            <form action="{{ route('cost-centres.destroy', $cc) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    data-confirm="Are you sure you want to delete this cost centre?">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $costCentres->links() }}
    </div>

    <div class="modal fade" id="costCentreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="costCentreModalTitle">Add Cost Centre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="costCentreForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="_method" id="costCentreMethod">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" id="costCentreName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Owner</label>
                            <select name="owner" id="costCentreOwner" class="form-control">
                                <option value="">Select Owner</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" id="costCentreStatus" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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
            var costCentreModal = document.getElementById('costCentreModal');
            costCentreModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var mode = button.getAttribute('data-mode');
                var modalTitle = document.getElementById('costCentreModalTitle');
                var form = document.getElementById('costCentreForm');
                var methodInput = document.getElementById('costCentreMethod');

                if (mode === 'create') {
                    modalTitle.textContent = 'Add Cost Centre';
                    form.action = '{{ route('cost-centres.store') }}';
                    methodInput.value = '';
                    document.getElementById('costCentreName').value = '';
                    document.getElementById('costCentreOwner').value = '';
                    document.getElementById('costCentreStatus').value = 'active';
                } else {
                    modalTitle.textContent = 'Edit Cost Centre';
                    var id = button.getAttribute('data-id');
                    form.action = '/cost-centres/' + id;
                    methodInput.value = 'PUT';
                    document.getElementById('costCentreName').value = button.getAttribute('data-name');
                    document.getElementById('costCentreOwner').value = button.getAttribute('data-owner') ||
                        '';
                    document.getElementById('costCentreStatus').value = button.getAttribute(
                        'data-status') || 'active';
                }
            });
        });
    </script>
@endsection
