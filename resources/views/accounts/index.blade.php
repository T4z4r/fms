@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Accounts</h1>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">Add Account</a>
    </div>

    <form method="GET" class="mb-4 d-flex gap-2">
        <select name="cost_centre_id" class="form-select">
            <option value="">All Cost Centres</option>
            @foreach($costCentres as $cc)
                <option value="{{ $cc->id }}">{{ $cc->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Cost Centre</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
            <tr>
                <td>{{ $account->code }}</td>
                <td>{{ $account->name }}</td>
                <td>{{ $account->costCentre?->name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('accounts.show', $account) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $accounts->links() }}
</div>
@endsection