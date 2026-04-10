<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Actual;
use App\Models\CostCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartsController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;

        $costCentres = CostCentre::active()->get();
        $selectedCostCentreId = $request->cost_centre_id ?? $costCentres->first()?->id;

        if (! $selectedCostCentreId) {
            return view('charts.index', [
                'costCentres' => $costCentres,
                'selectedCostCentreId' => null,
                'year' => $year,
                'monthlyData' => ['categories' => [], 'budget' => [], 'actual' => []],
                'accountData' => [],
                'yearlyComparison' => [],
                'varianceData' => [],
            ]);
        }

        $monthlyData = $this->getMonthlyTrend($selectedCostCentreId, $year);
        $accountData = $this->getAccountDistribution($selectedCostCentreId, $year);
        $yearlyComparison = $this->getYearlyComparison($selectedCostCentreId, $year);
        $varianceData = $this->getVarianceTrend($selectedCostCentreId, $year);

        return view('charts.index', [
            'costCentres' => $costCentres,
            'selectedCostCentreId' => $selectedCostCentreId,
            'year' => $year,
            'monthlyData' => $monthlyData,
            'accountData' => $accountData,
            'yearlyComparison' => $yearlyComparison,
            'varianceData' => $varianceData,
        ]);
    }

    private function getMonthlyTrend($costCentreId, $year)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $budgetData = [];
        $actualData = [];

        for ($m = 1; $m <= 12; $m++) {
            $budget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $costCentreId)
                ->where('budgets.year', $year)
                ->where('budget_lines.month', $m)
                ->sum('budget_lines.amount');

            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->where('month', $m)
                ->sum('amount');

            $budgetData[] = (float) $budget;
            $actualData[] = (float) $actual;
        }

        return [
            'categories' => $months,
            'budget' => $budgetData,
            'actual' => $actualData,
        ];
    }

    private function getAccountDistribution($costCentreId, $year)
    {
        $accounts = Account::where('cost_centre_id', $costCentreId)->get();
        $data = [];

        foreach ($accounts as $account) {
            $budget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $costCentreId)
                ->where('budgets.account_id', $account->id)
                ->where('budgets.year', $year)
                ->sum('budget_lines.amount');

            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('account_id', $account->id)
                ->where('year', $year)
                ->sum('amount');

            if ($budget > 0 || $actual > 0) {
                $data[] = [
                    'name' => $account->name,
                    'budget' => (float) $budget,
                    'actual' => (float) $actual,
                ];
            }
        }

        return $data;
    }

    private function getYearlyComparison($costCentreId, $year)
    {
        $data = [];

        for ($y = $year - 4; $y <= $year; $y++) {
            $budget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $costCentreId)
                ->where('budgets.year', $y)
                ->sum('budget_lines.amount');

            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $y)
                ->sum('amount');

            $data[] = [
                'year' => (string) $y,
                'budget' => (float) $budget,
                'actual' => (float) $actual,
            ];
        }

        return $data;
    }

    private function getVarianceTrend($costCentreId, $year)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $budget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $costCentreId)
                ->where('budgets.year', $year)
                ->where('budget_lines.month', $m)
                ->sum('budget_lines.amount');

            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->where('month', $m)
                ->sum('amount');

            $variance = $budget - $actual;

            $data[] = [
                'month' => $months[$m - 1],
                'variance' => (float) $variance,
                'percentage' => $budget > 0 ? round(($variance / $budget) * 100, 1) : 0,
            ];
        }

        return $data;
    }
}
