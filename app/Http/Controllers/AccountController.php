<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CostCentre;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::with('costCentre');

        if ($request->cost_centre_id) {
            $query->where('cost_centre_id', $request->cost_centre_id);
        }

        $accounts = $query->paginate(10);
        $costCentres = CostCentre::active()->get();

        return view('accounts.index', compact('accounts', 'costCentres'));
    }

    public function create()
    {
        $costCentres = CostCentre::active()->get();

        return view('accounts.create', compact('costCentres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:accounts,code,NULL,id,cost_centre_id,'.$request->cost_centre_id,
            'name' => 'required|string|max:255',
            'cost_centre_id' => 'required|exists:cost_centres,id',
        ]);

        Account::create($validated);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        $account->load(['costCentre', 'budgets', 'actuals']);

        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        $costCentres = CostCentre::active()->get();

        return view('accounts.edit', compact('account', 'costCentres'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:accounts,code,'.$account->id.',id,cost_centre_id,'.$request->cost_centre_id,
            'name' => 'required|string|max:255',
            'cost_centre_id' => 'required|exists:cost_centres,id',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
