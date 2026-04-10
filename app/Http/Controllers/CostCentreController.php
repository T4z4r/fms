<?php

namespace App\Http\Controllers;

use App\Models\CostCentre;
use App\Models\User;
use Illuminate\Http\Request;

class CostCentreController extends Controller
{
    public function index()
    {
        $costCentres = CostCentre::with('owner')->paginate(10);
        $users = User::all();

        return view('cost-centres.index', compact('costCentres', 'users'));
    }

    public function create()
    {
        return view('cost-centres.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        CostCentre::create($validated);

        return redirect()->route('cost-centres.index')->with('success', 'Cost Centre created successfully.');
    }

    public function show(CostCentre $costCentre)
    {
        $costCentre->load(['accounts', 'budgets', 'actuals', 'owner']);

        return view('cost-centres.show', compact('costCentre'));
    }

    public function edit(CostCentre $costCentre)
    {
        return view('cost-centres.edit', compact('costCentre'));
    }

    public function update(Request $request, CostCentre $costCentre)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $costCentre->update($validated);

        return redirect()->route('cost-centres.index')->with('success', 'Cost Centre updated successfully.');
    }

    public function destroy(CostCentre $costCentre)
    {
        $costCentre->delete();

        return redirect()->route('cost-centres.index')->with('success', 'Cost Centre deleted successfully.');
    }
}