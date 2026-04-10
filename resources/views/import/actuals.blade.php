@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-upload"></i> Import Actuals</h4>
        <a href="{{ route('import.template') }}" class="btn btn-outline-primary">
            <i class="bi bi-download"></i> Download Template
        </a>
    </div>
    <p class="text-muted">Upload an Excel or CSV file with columns: code, month, year (optional), amount</p>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Example Format:</h5>
            <div class="table-responsive-mobile">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>code</th>
                            <th>month</th>
                            <th>year</th>
                            <th>amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ACC-001</td>
                            <td>1</td>
                            <td>2026</td>
                            <td>1500.00</td>
                        </tr>
                        <tr>
                            <td>ACC-001</td>
                            <td>2</td>
                            <td>2026</td>
                            <td>2000.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form action="{{ route('import.actuals') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Cost Centre</label>
            <select name="cost_centre_id" class="form-control" required>
                <option value="">Select Cost Centre</option>
                @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>File (Excel or CSV)</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning mt-3">{{ session('warning') }}</div>
    @endif
</div>
@endsection