@extends('layouts.app')

@section('content')
<div class="container">
    <h4><i class="bi bi-pencil"></i> Edit Cost Centre</h4>
    <form action="{{ route('cost-centres.update', $costCentre) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $costCentre->name }}" required>
        </div>
        <div class="mb-3">
            <label>Owner</label>
            <select name="owner" class="form-control">
                <option value="">Select Owner</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}" {{ $costCentre->owner == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" {{ $costCentre->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $costCentre->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('cost-centres.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection