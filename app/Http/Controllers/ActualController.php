<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Actual;
use App\Models\CostCentre;
use Illuminate\Http\Request;

class ActualController extends Controller
{
    public function index(Request $request)
    {
        $query = Actual::with(['costCentre', 'account']);

        if ($request->cost_centre_id) {
            $query->where('cost_centre_id', $request->cost_centre_id);
        }
        if ($request->year) {
            $query->where('year', $request->year);
        }
        if ($request->month) {
            $query->where('month', $request->month);
        }

        $actuals = $query->paginate(10);
        $costCentres = CostCentre::active()->get();

        return view('actuals.index', compact('actuals', 'costCentres'));
    }

    public function create()
    {
        $costCentres = CostCentre::active()->get();
        $accounts = Account::with('costCentre')->get();

        return view('actuals.create', compact('costCentres', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_centre_id' => 'required|exists:cost_centres,id',
            'account_id' => 'required|exists:accounts,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric',
        ]);

        $actual = Actual::updateOrCreate(
            [
                'cost_centre_id' => $validated['cost_centre_id'],
                'account_id' => $validated['account_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            ['amount' => $validated['amount']]
        );

        return redirect()->route('actuals.index')->with('success', 'Actual saved successfully.');
    }

    public function show(Actual $actual)
    {
        $actual->load(['costCentre', 'account', 'details']);

        return view('actuals.show', compact('actual'));
    }

    public function edit(Actual $actual)
    {
        $costCentres = CostCentre::active()->get();
        $accounts = Account::with('costCentre')->get();

        return view('actuals.edit', compact('actual', 'costCentres', 'accounts'));
    }

    public function update(Request $request, Actual $actual)
    {
        $validated = $request->validate([
            'cost_centre_id' => 'required|exists:cost_centres,id',
            'account_id' => 'required|exists:accounts,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric',
        ]);

        $actual->update($validated);

        return redirect()->route('actuals.index')->with('success', 'Actual updated successfully.');
    }

    public function destroy(Actual $actual)
    {
        $actual->delete();

        return redirect()->route('actuals.index')->with('success', 'Actual deleted successfully.');
    }

    public function addDetail(Request $request, Actual $actual)
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $actual->details()->create($validated);

        return redirect()->route('actuals.show', $actual)->with('success', 'Transaction detail added.');
    }
}
