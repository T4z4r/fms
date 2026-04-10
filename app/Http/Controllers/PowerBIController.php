<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Actual;
use App\Models\CostCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PowerBIController extends Controller
{
    private function getQuarterMonths($quarter)
    {
        if ($quarter === 'all') {
            return range(1, 12);
        }

        $quarters = [
            'Q1' => [1, 2, 3],
            'Q2' => [4, 5, 6],
            'Q3' => [7, 8, 9],
            'Q4' => [10, 11, 12],
        ];

        return $quarters[$quarter] ?? range(1, 12);
    }

    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $quarter = $request->quarter ?? 'all';
        $viewMode = $request->view_mode ?? 'interactive';
        $chartType = $request->chart_type ?? 'line';
        $costCentres = CostCentre::active()->get();
        $selectedCostCentreId = $request->cost_centre_id ?? $costCentres->first()?->id;

        if (! $selectedCostCentreId) {
            return view('powerbi.index', [
                'costCentres' => $costCentres,
                'selectedCostCentreId' => null,
                'year' => $year,
                'quarter' => $quarter,
                'viewMode' => $viewMode,
                'chartType' => $chartType,
                'kpiData' => [],
                'monthlyData' => ['categories' => [], 'budget' => [], 'actual' => []],
                'accountData' => [],
                'yearlyComparison' => [],
                'varianceData' => [],
                'departmentComparison' => [],
                'quarterlyData' => [],
                'topAccounts' => [],
                'forecastData' => [],
            ]);
        }

        $quarterMonths = $this->getQuarterMonths($quarter);

        $kpiData = $this->getKPIData($selectedCostCentreId, $year, $quarterMonths);
        $monthlyData = $this->getMonthlyTrend($selectedCostCentreId, $year, $quarterMonths);
        $accountData = $this->getAccountDistribution($selectedCostCentreId, $year, $quarterMonths);
        $yearlyComparison = $this->getYearlyComparison($selectedCostCentreId, $year);
        $varianceData = $this->getVarianceTrend($selectedCostCentreId, $year, $quarterMonths);
        $departmentComparison = $this->getDepartmentComparison($year, $quarterMonths);
        $quarterlyData = $this->getQuarterlyData($selectedCostCentreId, $year);
        $topAccounts = $this->getTopSpendingAccounts($selectedCostCentreId, $year, $quarterMonths);
        $forecastData = $this->getForecastData($selectedCostCentreId, $year, $quarterMonths);

        // Advanced Analytics
        $spendingVelocity = $this->getSpendingVelocity($selectedCostCentreId, $year, $quarterMonths);
        $budgetUtilization = $this->getBudgetUtilizationRate($selectedCostCentreId, $year, $quarterMonths);
        $seasonalTrends = $this->getSeasonalTrendAnalysis($selectedCostCentreId, $year);
        $momGrowth = $this->getMonthOverMonthGrowth($selectedCostCentreId, $year, $quarterMonths);
        $budgetPrediction = $this->getBudgetAccuracyPrediction($selectedCostCentreId, $year);
        $riskAnalysis = $this->getRiskAnalysis($selectedCostCentreId, $year, $quarterMonths);
        $categoryBreakdown = $this->getCategoryBreakdown($selectedCostCentreId, $year, $quarterMonths);
        $rollingAverages = $this->getRollingAverages($selectedCostCentreId, $year, $quarterMonths);
        $cumulativeSpending = $this->getCumulativeSpending($selectedCostCentreId, $year, $quarterMonths);
        $anomalyDetection = $this->getAnomalyDetection($selectedCostCentreId, $year, $quarterMonths);

        return view('powerbi.index', [
            'costCentres' => $costCentres,
            'selectedCostCentreId' => $selectedCostCentreId,
            'year' => $year,
            'quarter' => $quarter,
            'viewMode' => $viewMode,
            'chartType' => $chartType,
            'kpiData' => $kpiData,
            'monthlyData' => $monthlyData,
            'accountData' => $accountData,
            'yearlyComparison' => $yearlyComparison,
            'varianceData' => $varianceData,
            'departmentComparison' => $departmentComparison,
            'quarterlyData' => $quarterlyData,
            'topAccounts' => $topAccounts,
            'forecastData' => $forecastData,
            // Advanced Analytics
            'spendingVelocity' => $spendingVelocity,
            'budgetUtilization' => $budgetUtilization,
            'seasonalTrends' => $seasonalTrends,
            'momGrowth' => $momGrowth,
            'budgetPrediction' => $budgetPrediction,
            'riskAnalysis' => $riskAnalysis,
            'categoryBreakdown' => $categoryBreakdown,
            'rollingAverages' => $rollingAverages,
            'cumulativeSpending' => $cumulativeSpending,
            'anomalyDetection' => $anomalyDetection,
        ]);
    }

    private function getKPIData($costCentreId, $year, $quarterMonths = null)
    {
        $quarterMonths = $quarterMonths ?? range(1, 12);

        $totalBudget = DB::table('budget_lines')
            ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
            ->where('budgets.cost_centre_id', $costCentreId)
            ->where('budgets.year', $year)
            ->whereIn('budget_lines.month', $quarterMonths)
            ->sum('budget_lines.amount');

        $totalActual = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->whereIn('month', $quarterMonths)
            ->sum('amount');

        $prevYear = $year - 1;
        $prevYearActual = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $prevYear)
            ->sum('amount');

        $variance = $totalBudget - $totalActual;
        $variancePercentage = $totalBudget > 0 ? round(($variance / $totalBudget) * 100, 1) : 0;
        $yoyGrowth = $prevYearActual > 0 ? round((($totalActual - $prevYearActual) / $prevYearActual) * 100, 1) : 0;

        $months = range(1, now()->month);
        $spentToDate = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->whereIn('month', $months)
            ->sum('amount');

        $budgetToDate = DB::table('budget_lines')
            ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
            ->where('budgets.cost_centre_id', $costCentreId)
            ->where('budgets.year', $year)
            ->whereIn('budget_lines.month', $months)
            ->sum('budget_lines.amount');

        return [
            'totalBudget' => (float) $totalBudget,
            'totalActual' => (float) $totalActual,
            'variance' => (float) $variance,
            'variancePercentage' => $variancePercentage,
            'yoyGrowth' => $yoyGrowth,
            'budgetToDate' => (float) $budgetToDate,
            'spentToDate' => (float) $spentToDate,
            'burnRate' => $budgetToDate > 0 ? round(($spentToDate / $budgetToDate) * 100, 1) : 0,
        ];
    }

    private function getMonthlyTrend($costCentreId, $year, $quarterMonths = null)
    {
        $quarterMonths = $quarterMonths ?? range(1, 12);
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $budgetData = [];
        $actualData = [];

        foreach ($quarterMonths as $m) {
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

        $filteredMonths = array_map(function ($m) use ($months) {
            return $months[$m - 1];
        }, $quarterMonths);

        return [
            'categories' => $filteredMonths,
            'budget' => $budgetData,
            'actual' => $actualData,
        ];
    }

    private function getAccountDistribution($costCentreId, $year, $quarterMonths = null)
    {
        $quarterMonths = $quarterMonths ?? range(1, 12);
        $accounts = Account::where('cost_centre_id', $costCentreId)->get();
        $data = [];

        foreach ($accounts as $account) {
            $budget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $costCentreId)
                ->where('budgets.account_id', $account->id)
                ->where('budgets.year', $year)
                ->whereIn('budget_lines.month', $quarterMonths)
                ->sum('budget_lines.amount');

            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('account_id', $account->id)
                ->where('year', $year)
                ->whereIn('month', $quarterMonths)
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

    private function getVarianceTrend($costCentreId, $year, $quarterMonths = null)
    {
        $quarterMonths = $quarterMonths ?? range(1, 12);
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];

        foreach ($quarterMonths as $m) {
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

    private function getDepartmentComparison($year, $quarterMonths = null)
    {
        $quarterMonths = $quarterMonths ?? range(1, 12);
        $costCentres = CostCentre::active()->get();
        $data = [];

        foreach ($costCentres as $cc) {
            $budget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $cc->id)
                ->where('budgets.year', $year)
                ->whereIn('budget_lines.month', $quarterMonths)
                ->sum('budget_lines.amount');

            $actual = Actual::where('cost_centre_id', $cc->id)
                ->where('year', $year)
                ->whereIn('month', $quarterMonths)
                ->sum('amount');

            if ($budget > 0 || $actual > 0) {
                $data[] = [
                    'name' => $cc->name,
                    'budget' => (float) $budget,
                    'actual' => (float) $actual,
                ];
            }
        }

        return $data;
    }

    private function getQuarterlyData($costCentreId, $year)
    {
        $quarters = [
            ['months' => [1, 2, 3], 'name' => 'Q1'],
            ['months' => [4, 5, 6], 'name' => 'Q2'],
            ['months' => [7, 8, 9], 'name' => 'Q3'],
            ['months' => [10, 11, 12], 'name' => 'Q4'],
        ];
        $data = [];

        foreach ($quarters as $q) {
            $budget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $costCentreId)
                ->where('budgets.year', $year)
                ->whereIn('budget_lines.month', $q['months'])
                ->sum('budget_lines.amount');

            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->whereIn('month', $q['months'])
                ->sum('amount');

            $data[] = [
                'quarter' => $q['name'],
                'budget' => (float) $budget,
                'actual' => (float) $actual,
            ];
        }

        return $data;
    }

    private function getTopSpendingAccounts($costCentreId, $year, $quarterMonths = null)
    {
        $quarterMonths = $quarterMonths ?? range(1, 12);
        $accounts = Account::where('cost_centre_id', $costCentreId)->get();
        $data = [];

        foreach ($accounts as $account) {
            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('account_id', $account->id)
                ->where('year', $year)
                ->whereIn('month', $quarterMonths)
                ->sum('amount');

            if ($actual > 0) {
                $data[] = [
                    'name' => $account->name,
                    'actual' => (float) $actual,
                ];
            }
        }

        usort($data, function ($a, $b) {
            return $b['actual'] - $a['actual'];
        });

        return array_slice($data, 0, 5);
    }

    private function getForecastData($costCentreId, $year, $quarterMonths = null)
    {
        $remainingMonths = range(now()->month + 1, 12);
        $forecastData = [];

        $avgMonthlySpend = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year - 1)
            ->sum('amount') / 12;

        $remainingBudget = DB::table('budget_lines')
            ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
            ->where('budgets.cost_centre_id', $costCentreId)
            ->where('budgets.year', $year)
            ->whereIn('budget_lines.month', $remainingMonths)
            ->sum('budget_lines.amount');

        $projectedSpend = $avgMonthlySpend * count($remainingMonths);
        $projectedVariance = $remainingBudget - $projectedSpend;

        return [
            'avgMonthlySpend' => (float) $avgMonthlySpend,
            'remainingBudget' => (float) $remainingBudget,
            'projectedSpend' => (float) $projectedSpend,
            'projectedVariance' => (float) $projectedVariance,
        ];
    }

    private function getSpendingVelocity($costCentreId, $year, $quarterMonths = null)
    {
        $quarterMonths = $quarterMonths ?? range(1, 12);
        $monthlySpend = [];
        $currentMonth = now()->month;
        $filteredMonths = array_filter($quarterMonths, function ($m) use ($currentMonth) {
            return $m <= $currentMonth;
        });
        foreach ($filteredMonths as $m) {
            $spend = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->where('month', $m)
                ->sum('amount');
            $monthlySpend[] = (float) $spend;
        }

        $velocity = 0;
        $totalDays = 0;
        $currentMonth = now()->month;
        $currentDay = now()->day;

        for ($m = 1; $m <= $currentMonth; $m++) {
            $daysInMonth = $m == $currentMonth ? $currentDay : cal_days_in_month(CAL_GREGORIAN, $m, $year);
            $totalDays += $daysInMonth;
            if (isset($monthlySpend[$m - 1])) {
                $velocity += ($monthlySpend[$m - 1] / $daysInMonth);
            }
        }

        $avgVelocity = $totalDays > 0 ? $velocity / $totalDays : 0;

        // Project year-end spend
        $remainingDays = 0;
        for ($m = $currentMonth + 1; $m <= 12; $m++) {
            $remainingDays += cal_days_in_month(CAL_GREGORIAN, $m, $year);
        }
        $projectedYearEnd = ($avgVelocity * $remainingDays) + array_sum($monthlySpend);

        return [
            'avgDailyVelocity' => round($avgVelocity, 2),
            'totalDaysAnalyzed' => $totalDays,
            'projectedYearEnd' => round($projectedYearEnd, 2),
            'acceleration' => $this->calculateVelocityAcceleration($monthlySpend),
        ];
    }

    private function calculateVelocityAcceleration($monthlySpend)
    {
        $accelerations = [];
        for ($i = 1; $i < count($monthlySpend); $i++) {
            $prev = $monthlySpend[$i - 1] ?? 0;
            $curr = $monthlySpend[$i];
            if ($prev > 0) {
                $accelerations[] = (($curr - $prev) / $prev) * 100;
            }
        }

        return $accelerations ? round(array_sum($accelerations) / count($accelerations), 2) : 0;
    }

    private function getBudgetUtilizationRate($costCentreId, $year)
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

            if ($budget > 0) {
                $utilizationRate = round(($actual / $budget) * 100, 2);
                $data[] = [
                    'account' => $account->name,
                    'budget' => (float) $budget,
                    'actual' => (float) $actual,
                    'utilizationRate' => $utilizationRate,
                    'status' => $utilizationRate > 100 ? 'overspend' : ($utilizationRate > 90 ? 'warning' : 'good'),
                ];
            }
        }

        usort($data, function ($a, $b) {
            return $b['utilizationRate'] - $a['utilizationRate'];
        });

        return $data;
    }

    private function getSeasonalTrendAnalysis($costCentreId, $year)
    {
        $quarters = [
            ['months' => [1, 2, 3], 'name' => 'Q1'],
            ['months' => [4, 5, 6], 'name' => 'Q2'],
            ['months' => [7, 8, 9], 'name' => 'Q3'],
            ['months' => [10, 11, 12], 'name' => 'Q4'],
        ];

        $seasonalData = [];
        $prevYearSeasonal = [];

        // Get current year seasonal data
        foreach ($quarters as $q) {
            $spend = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->whereIn('month', $q['months'])
                ->sum('amount');

            $seasonalData[] = [
                'quarter' => $q['name'],
                'amount' => (float) $spend,
            ];
        }

        // Get previous year for comparison
        foreach ($quarters as $q) {
            $spend = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year - 1)
                ->whereIn('month', $q['months'])
                ->sum('amount');

            $prevYearSeasonal[] = (float) $spend;
        }

        // Calculate seasonal indices
        $totalCurrent = array_sum(array_column($seasonalData, 'amount'));
        $totalPrev = array_sum($prevYearSeasonal);

        $avgQuarterCurrent = $totalCurrent / 4;
        $avgQuarterPrev = $totalPrev / 4;

        $seasonalIndices = [];
        foreach ($seasonalData as $i => $data) {
            $index = $avgQuarterCurrent > 0 ? (($data['amount'] / $avgQuarterCurrent) * 100) : 0;
            $prevIndex = $avgQuarterPrev > 0 ? (($prevYearSeasonal[$i] / $avgQuarterPrev) * 100) : 0;

            $seasonalIndices[] = [
                'quarter' => $data['quarter'],
                'currentIndex' => round($index, 2),
                'previousIndex' => round($prevIndex, 2),
                'change' => round($index - $prevIndex, 2),
            ];
        }

        return [
            'seasonalData' => $seasonalData,
            'seasonalIndices' => $seasonalIndices,
            'totalCurrentYear' => $totalCurrent,
            'totalPreviousYear' => $totalPrev,
        ];
    }

    private function getMonthOverMonthGrowth($costCentreId, $year)
    {
        $growthData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($m = 1; $m <= 12; $m++) {
            $currentSpend = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->where('month', $m)
                ->sum('amount');

            $prevMonth = $m - 1;
            $prevYear = $year;
            if ($prevMonth == 0) {
                $prevMonth = 12;
                $prevYear = $year - 1;
            }

            $prevSpend = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $prevYear)
                ->where('month', $prevMonth)
                ->sum('amount');

            $growth = $prevSpend > 0 ? round((($currentSpend - $prevSpend) / $prevSpend) * 100, 2) : 0;

            $growthData[] = [
                'month' => $months[$m - 1],
                'current' => (float) $currentSpend,
                'previous' => (float) $prevSpend,
                'growth' => $growth,
                'status' => $growth > 10 ? 'high_growth' : ($growth < -10 ? 'decline' : 'stable'),
            ];
        }

        return $growthData;
    }

    private function getBudgetAccuracyPrediction($costCentreId, $year)
    {
        // Get historical data for the last 3 years
        $historicalData = [];
        for ($y = $year - 3; $y < $year; $y++) {
            $budget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $costCentreId)
                ->where('budgets.year', $y)
                ->sum('budget_lines.amount');

            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $y)
                ->sum('amount');

            $historicalData[] = [
                'year' => $y,
                'budget' => (float) $budget,
                'actual' => (float) $actual,
                'accuracy' => $budget > 0 ? round((1 - abs($budget - $actual) / $budget) * 100, 2) : 0,
            ];
        }

        $currentBudget = DB::table('budget_lines')
            ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
            ->where('budgets.cost_centre_id', $costCentreId)
            ->where('budgets.year', $year)
            ->sum('budget_lines.amount');

        $currentActual = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->sum('amount');

        // Calculate prediction based on historical accuracy
        $avgAccuracy = array_sum(array_column($historicalData, 'accuracy')) / count($historicalData);
        $predictedActual = $currentBudget * ($avgAccuracy / 100);

        $variance = $currentBudget - $currentActual;
        $predictedVariance = $currentBudget - $predictedActual;

        return [
            'historicalAccuracy' => $historicalData,
            'currentBudget' => (float) $currentBudget,
            'currentActual' => (float) $currentActual,
            'predictedActual' => round($predictedActual, 2),
            'currentVariance' => (float) $variance,
            'predictedVariance' => round($predictedVariance, 2),
            'accuracyScore' => round($avgAccuracy, 2),
        ];
    }

    private function getRiskAnalysis($costCentreId, $year)
    {
        $risks = [];
        $currentMonth = now()->month;

        // Overspend risk by account
        $accounts = Account::where('cost_centre_id', $costCentreId)->get();
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

            if ($budget > 0) {
                $utilization = ($actual / $budget) * 100;
                $remainingBudget = $budget - $actual;
                $monthlyBudget = $budget / 12;
                $remainingMonths = 12 - $currentMonth + 1;
                $availableMonthly = $remainingBudget / $remainingMonths;

                $riskLevel = 'low';
                $riskDescription = 'Budget on track';

                if ($utilization > 100) {
                    $riskLevel = 'critical';
                    $riskDescription = 'Already overspent';
                } elseif ($utilization > 90) {
                    $riskLevel = 'high';
                    $riskDescription = 'High risk of overspend';
                } elseif ($utilization > 75) {
                    $riskLevel = 'medium';
                    $riskDescription = 'Moderate overspend risk';
                }

                $risks[] = [
                    'type' => 'account_overspend',
                    'account' => $account->name,
                    'utilization' => round($utilization, 2),
                    'remainingBudget' => (float) $remainingBudget,
                    'availableMonthly' => round($availableMonthly, 2),
                    'riskLevel' => $riskLevel,
                    'description' => $riskDescription,
                ];
            }
        }

        // Seasonal risk analysis
        $seasonalData = $this->getSeasonalTrendAnalysis($costCentreId, $year);
        $currentQuarter = ceil($currentMonth / 3);
        $currentQuarterData = array_filter($seasonalData['seasonalData'], function ($q) use ($currentQuarter) {
            return $q['quarter'] == 'Q'.$currentQuarter;
        });

        if (! empty($currentQuarterData)) {
            $currentQ = reset($currentQuarterData);
            $prevQIndex = $seasonalData['seasonalIndices'][$currentQuarter - 1] ?? null;
            if ($prevQIndex && $prevQIndex['change'] > 20) {
                $risks[] = [
                    'type' => 'seasonal_spike',
                    'description' => 'Unusual seasonal spending pattern detected',
                    'riskLevel' => 'medium',
                    'change' => $prevQIndex['change'],
                ];
            }
        }

        usort($risks, function ($a, $b) {
            $levels = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];

            return $levels[$b['riskLevel']] - $levels[$a['riskLevel']];
        });

        return $risks;
    }

    private function getCategoryBreakdown($costCentreId, $year)
    {
        // Assuming accounts can be categorized by their names or we need to add category field
        // For now, we'll categorize by account name patterns
        $accounts = Account::where('cost_centre_id', $costCentreId)->get();
        $categories = [
            'Personnel' => [],
            'Facilities' => [],
            'Technology' => [],
            'Marketing' => [],
            'Other' => [],
        ];

        foreach ($accounts as $account) {
            $actual = Actual::where('cost_centre_id', $costCentreId)
                ->where('account_id', $account->id)
                ->where('year', $year)
                ->sum('amount');

            $category = $this->categorizeAccount($account->name);
            if ($actual > 0) {
                $categories[$category][] = [
                    'account' => $account->name,
                    'amount' => (float) $actual,
                ];
            }
        }

        $breakdown = [];
        foreach ($categories as $cat => $accounts) {
            if (! empty($accounts)) {
                $total = array_sum(array_column($accounts, 'amount'));
                $breakdown[] = [
                    'category' => $cat,
                    'total' => $total,
                    'percentage' => 0, // Will calculate after getting grand total
                    'accounts' => $accounts,
                ];
            }
        }

        $grandTotal = array_sum(array_column($breakdown, 'total'));
        foreach ($breakdown as &$cat) {
            $cat['percentage'] = $grandTotal > 0 ? round(($cat['total'] / $grandTotal) * 100, 2) : 0;
        }

        usort($breakdown, function ($a, $b) {
            return $b['total'] - $a['total'];
        });

        return $breakdown;
    }

    private function categorizeAccount($accountName)
    {
        $name = strtolower($accountName);

        if (str_contains($name, 'salary') || str_contains($name, 'wage') || str_contains($name, 'payroll')) {
            return 'Personnel';
        } elseif (str_contains($name, 'rent') || str_contains($name, 'facility') || str_contains($name, 'office')) {
            return 'Facilities';
        } elseif (str_contains($name, 'software') || str_contains($name, 'hardware') || str_contains($name, 'it') || str_contains($name, 'tech')) {
            return 'Technology';
        } elseif (str_contains($name, 'marketing') || str_contains($name, 'advertising') || str_contains($name, 'promo')) {
            return 'Marketing';
        } else {
            return 'Other';
        }
    }

    private function getRollingAverages($costCentreId, $year)
    {
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $spend = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->where('month', $m)
                ->sum('amount');
            $monthlyData[] = (float) $spend;
        }

        $rolling3Month = [];
        $rolling6Month = [];

        for ($i = 0; $i < count($monthlyData); $i++) {
            // 3-month rolling average
            $start3 = max(0, $i - 2);
            $slice3 = array_slice($monthlyData, $start3, $i - $start3 + 1);
            $rolling3Month[] = count($slice3) > 0 ? array_sum($slice3) / count($slice3) : 0;

            // 6-month rolling average
            $start6 = max(0, $i - 5);
            $slice6 = array_slice($monthlyData, $start6, $i - $start6 + 1);
            $rolling6Month[] = count($slice6) > 0 ? array_sum($slice6) / count($slice6) : 0;
        }

        return [
            'monthlyData' => $monthlyData,
            'rolling3Month' => array_map(function ($v) {
                return round($v, 2);
            }, $rolling3Month),
            'rolling6Month' => array_map(function ($v) {
                return round($v, 2);
            }, $rolling6Month),
        ];
    }

    private function getCumulativeSpending($costCentreId, $year)
    {
        $cumulative = [];
        $runningTotal = 0;

        $budgetTotal = DB::table('budget_lines')
            ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
            ->where('budgets.cost_centre_id', $costCentreId)
            ->where('budgets.year', $year)
            ->sum('budget_lines.amount');

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($m = 1; $m <= 12; $m++) {
            $monthlySpend = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->where('month', $m)
                ->sum('amount');

            $runningTotal += $monthlySpend;

            $monthlyBudget = DB::table('budget_lines')
                ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                ->where('budgets.cost_centre_id', $costCentreId)
                ->where('budgets.year', $year)
                ->where('budget_lines.month', $m)
                ->sum('budget_lines.amount');

            $cumulativeBudget = 0;
            for ($bm = 1; $bm <= $m; $bm++) {
                $cumulativeBudget += DB::table('budget_lines')
                    ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
                    ->where('budgets.cost_centre_id', $costCentreId)
                    ->where('budgets.year', $year)
                    ->where('budget_lines.month', $bm)
                    ->sum('budget_lines.amount');
            }

            $cumulative[] = [
                'month' => $months[$m - 1],
                'monthlySpend' => (float) $monthlySpend,
                'cumulativeSpend' => (float) $runningTotal,
                'cumulativeBudget' => (float) $cumulativeBudget,
                'variance' => (float) ($cumulativeBudget - $runningTotal),
                'efficiency' => $cumulativeBudget > 0 ? round(($runningTotal / $cumulativeBudget) * 100, 2) : 0,
            ];
        }

        return $cumulative;
    }

    private function getAnomalyDetection($costCentreId, $year)
    {
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $spend = Actual::where('cost_centre_id', $costCentreId)
                ->where('year', $year)
                ->where('month', $m)
                ->sum('amount');
            $monthlyData[] = (float) $spend;
        }

        $anomalies = [];
        $mean = array_sum($monthlyData) / count($monthlyData);
        $variance = 0;

        foreach ($monthlyData as $value) {
            $variance += pow($value - $mean, 2);
        }
        $variance /= count($monthlyData);
        $stdDev = sqrt($variance);

        $threshold = 2; // 2 standard deviations

        foreach ($monthlyData as $i => $value) {
            $zScore = $stdDev > 0 ? abs($value - $mean) / $stdDev : 0;

            if ($zScore > $threshold) {
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                $anomalies[] = [
                    'month' => $months[$i],
                    'amount' => $value,
                    'zScore' => round($zScore, 2),
                    'deviation' => round($value - $mean, 2),
                    'type' => $value > $mean ? 'high_spend' : 'low_spend',
                    'severity' => $zScore > 3 ? 'critical' : 'warning',
                ];
            }
        }

        // Account-level anomalies
        $accounts = Account::where('cost_centre_id', $costCentreId)->get();
        foreach ($accounts as $account) {
            $accountSpend = [];
            for ($m = 1; $m <= 12; $m++) {
                $spend = Actual::where('cost_centre_id', $costCentreId)
                    ->where('account_id', $account->id)
                    ->where('year', $year)
                    ->where('month', $m)
                    ->sum('amount');
                $accountSpend[] = (float) $spend;
            }

            $accountMean = array_sum($accountSpend) / count($accountSpend);
            $accountVariance = 0;
            foreach ($accountSpend as $value) {
                $accountVariance += pow($value - $accountMean, 2);
            }
            $accountVariance /= count($accountSpend);
            $accountStdDev = sqrt($accountVariance);

            foreach ($accountSpend as $i => $value) {
                $zScore = $accountStdDev > 0 ? abs($value - $accountMean) / $accountStdDev : 0;
                if ($zScore > $threshold && $value > 0) {
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $anomalies[] = [
                        'account' => $account->name,
                        'month' => $months[$i],
                        'amount' => $value,
                        'zScore' => round($zScore, 2),
                        'type' => 'account_anomaly',
                        'severity' => $zScore > 3 ? 'critical' : 'warning',
                    ];
                }
            }
        }

        usort($anomalies, function ($a, $b) {
            $severity = ['critical' => 3, 'warning' => 2, 'info' => 1];

            return $severity[$b['severity']] - $severity[$a['severity']];
        });

        return [
            'monthlyAnomalies' => array_filter($anomalies, function ($a) {
                return ! isset($a['account']);
            }),
            'accountAnomalies' => array_filter($anomalies, function ($a) {
                return isset($a['account']);
            }),
            'stats' => [
                'mean' => round($mean, 2),
                'stdDev' => round($stdDev, 2),
                'threshold' => $threshold,
            ],
        ];
    }
}
