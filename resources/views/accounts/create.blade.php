@extends('layouts.app')

@section('content')
<div class="container">
    <h1>New Account</h1>
    <form action="{{ route('accounts.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Code</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Cost Centre</label>
            <select name="cost_centre_id" class="form-control" required>
                <option value="">Select Cost Centre</option>
                @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection