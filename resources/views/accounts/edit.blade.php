@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Account</h1>
    <form action="{{ route('accounts.update', $account) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Code</label>
            <input type="text" name="code" class="form-control" value="{{ $account->code }}" required>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
        </div>
        <div class="mb-3">
            <label>Cost Centre</label>
            <select name="cost_centre_id" class="form-control" required>
                @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}" {{ $account->cost_centre_id == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection