<?php

namespace App\Http\Controllers;

use App\Models\Actual;
use App\Models\Budget;
use App\Models\CostCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $costCentreId = $request->cost_centre_id;

        $costCentres = CostCentre::active()->get();

        $query = function ($year, $costCentreId) use ($costCentreId) {
            $budgets = Budget::where('year', $year)
                ->when($costCentreId, fn ($q) => $q->where('cost_centre_id', $costCentreId))
                ->with('account')
                ->get()
                ->groupBy('account_id');

            $actuals = Actual::where('year', $year)
                ->when($costCentreId, fn ($q) => $q->where('cost_centre_id', $costCentreId))
                ->with('account')
                ->get()
                ->groupBy('account_id');

            $summary = [];
            foreach ($budgets as $accountId => $budgetGroup) {
                $budgetTotal = $budgetGroup->sum('annual_budget');
                $actualTotal = $actuals[$accountId]->sum('amount') ?? 0;

                $summary[] = [
                    'account' => $budgetGroup->first()->account,
                    'budget' => $budgetTotal,
                    'actual' => $actualTotal,
                    'variance' => $budgetTotal - $actualTotal,
                ];
            }

            return $summary;
        };

        $summary = $query($year, $costCentreId);

        $monthlyData = DB::table('actuals')
            ->select('month', DB::raw('SUM(amount) as total'))
            ->where('year', $year)
            ->when($costCentreId, fn ($q) => $q->where('cost_centre_id', $costCentreId))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $budgetMonthly = DB::table('budgets')
            ->select('budget_lines.month', DB::raw('SUM(budget_lines.amount) as total'))
            ->join('budget_lines', 'budgets.id', '=', 'budget_lines.budget_id')
            ->where('budgets.year', $year)
            ->when($costCentreId, fn ($q) => $q->where('budgets.cost_centre_id', $costCentreId))
            ->groupBy('budget_lines.month')
            ->orderBy('budget_lines.month')
            ->pluck('total', 'month');

        return view('dashboard', compact('summary', 'costCentres', 'year', 'costCentreId', 'monthlyData', 'budgetMonthly'));
    }

    public function reports(Request $request)
    {
        $year = $request->year ?? now()->year;

        $costCentres = CostCentre::active()->with(['accounts', 'budgets' => fn ($q) => $q->where('year', $year)])->get();

        $actuals = Actual::where('year', $year)
            ->select('cost_centre_id', 'account_id', DB::raw('SUM(amount) as total'))
            ->groupBy('cost_centre_id', 'account_id')
            ->get()
            ->groupBy('cost_centre_id');

        return view('reports.index', compact('costCentres', 'actuals', 'year'));
    }
}
