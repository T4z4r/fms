<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\CostCentre;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $query = Budget::with(['costCentre', 'account']);

        if ($request->cost_centre_id) {
            $query->where('cost_centre_id', $request->cost_centre_id);
        }
        if ($request->year) {
            $query->where('year', $request->year);
        }

        $budgets = $query->paginate(10);
        $costCentres = CostCentre::active()->get();

        return view('budgets.index', compact('budgets', 'costCentres'));
    }

    public function create()
    {
        $costCentres = CostCentre::active()->get();
        $accounts = Account::with('costCentre')->get();

        return view('budgets.create', compact('costCentres', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_centre_id' => 'required|exists:cost_centres,id',
            'account_id' => 'required|exists:accounts,id',
            'annual_budget' => 'required|numeric|min:0',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $budget = Budget::create($validated);

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    public function show(Budget $budget)
    {
        $budget->load(['costCentre', 'account', 'budgetLines']);

        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $costCentres = CostCentre::active()->get();
        $accounts = Account::with('costCentre')->get();

        return view('budgets.edit', compact('budget', 'costCentres', 'accounts'));
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'cost_centre_id' => 'required|exists:cost_centres,id',
            'account_id' => 'required|exists:accounts,id',
            'annual_budget' => 'required|numeric|min:0',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }

    public function lines(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'lines' => 'required|array|size:12',
            'lines.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['lines'] as $month => $amount) {
            BudgetLine::updateOrCreate(
                ['budget_id' => $budget->id, 'month' => $month + 1],
                ['amount' => $amount ?? 0]
            );
        }

        return redirect()->route('budgets.show', $budget)->with('success', 'Budget lines updated successfully.');
    }
}
