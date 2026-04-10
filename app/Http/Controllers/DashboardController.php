<?php

namespace App\Http\Controllers;

use App\Models\Actual;
use App\Models\Alert;
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

        $alerts = Alert::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('summary', 'costCentres', 'year', 'costCentreId', 'monthlyData', 'budgetMonthly', 'alerts'));
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

    public function forecast(Request $request)
    {
        $year = $request->year ?? now()->year;
        $costCentreId = $request->cost_centre_id;

        $costCentres = CostCentre::active()->get();

        $accounts = Budget::where('year', $year)
            ->when($costCentreId, fn ($q) => $q->where('cost_centre_id', $costCentreId))
            ->with('account')
            ->get()
            ->groupBy('account_id');

        $forecasts = [];
        foreach ($accounts as $accountId => $budgetGroup) {
            $lastThreeMonths = Actual::where('account_id', $accountId)
                ->where('year', $year)
                ->whereIn('month', [now()->month - 2, now()->month - 1, now()->month])
                ->sum('amount');

            $monthlyAverage = $lastThreeMonths / 3;
            $remainingMonths = 12 - now()->month;
            $forecast = $monthlyAverage * $remainingMonths;

            $forecasts[] = [
                'account' => $budgetGroup->first()->account,
                'budget' => $budgetGroup->first()->annual_budget,
                'current_spending' => $lastThreeMonths,
                'monthly_average' => $monthlyAverage,
                'forecast' => $forecast,
                'projected_total' => $lastThreeMonths + $forecast,
                'variance' => $budgetGroup->first()->annual_budget - ($lastThreeMonths + $forecast),
            ];
        }

        return view('forecast', compact('forecasts', 'costCentres', 'year', 'costCentreId'));
    }
}
