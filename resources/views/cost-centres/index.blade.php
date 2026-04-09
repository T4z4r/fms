@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Cost Centres</h1>
        <a href="{{ route('cost-centres.create') }}" class="btn btn-primary">Add Cost Centre</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Owner</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($costCentres as $cc)
            <tr>
                <td>{{ $cc->id }}</td>
                <td>{{ $cc->name }}</td>
                <td>{{ $cc->owner?->name ?? 'N/A' }}</td>
                <td>
                    <span class="badge bg-{{ $cc->status === 'active' ? 'success' : 'secondary' }}">
                        {{ $cc->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('cost-centres.show', $cc) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('cost-centres.edit', $cc) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('cost-centres.destroy', $cc) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $costCentres->links() }}
</div>
@endsection