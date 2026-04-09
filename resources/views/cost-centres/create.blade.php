@extends('layouts.app')

@section('content')
<div class="container">
    <h1>New Cost Centre</h1>
    <form action="{{ route('cost-centres.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Owner</label>
            <select name="owner" class="form-control">
                <option value="">Select Owner</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('cost-centres.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection