<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Actual;
use App\Models\Budget;
use Illuminate\Support\Collection;

class FinancialAnalysisService
{
    public function analyzeCostCentre(int $costCentreId, int $year): array
    {
        $budgets = Budget::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->with('account')
            ->get();

        $actuals = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->with('account')
            ->get();

        $monthlyActuals = $actuals->groupBy('month');
        $monthlyBudgets = $this->getMonthlyBudgets($costCentreId, $year);

        return [
            'summary' => $this->getSummary($budgets, $actuals),
            'monthly_trend' => $this->analyzeMonthlyTrend($monthlyActuals, $monthlyBudgets),
            'anomalies' => $this->detectAnomalies($monthlyActuals, $monthlyBudgets),
            'recommendations' => $this->generateRecommendations($budgets, $actuals),
            'account_analysis' => $this->analyzeByAccount($budgets, $actuals),
            'forecast' => $this->generateForecast($actuals, $year),
            'insights' => $this->generateInsights($budgets, $actuals, $year),
        ];
    }

    private function getMonthlyBudgets(int $costCentreId, int $year): Collection
    {
        return Budget::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->with('budgetLines')
            ->get()
            ->mapWithKeys(function ($budget) {
                $monthly = array_fill(1, 12, 0);
                foreach ($budget->budgetLines as $line) {
                    $monthly[$line->month] = $line->amount;
                }

                return [$budget->account_id => $monthly];
            });
    }

    private function getSummary($budgets, $actuals): array
    {
        $totalBudget = $budgets->sum('annual_budget');
        $totalActual = $actuals->sum('amount');
        $variance = $totalBudget - $totalActual;
        $utilization = $totalBudget > 0 ? ($totalActual / $totalBudget) * 100 : 0;

        return [
            'total_budget' => $totalBudget,
            'total_actual' => $totalActual,
            'variance' => $variance,
            'variance_percentage' => $totalBudget > 0 ? ($variance / $totalBudget) * 100 : 0,
            'utilization_rate' => $utilization,
            'status' => $this->getStatus($utilization),
        ];
    }

    private function analyzeMonthlyTrend($monthlyActuals, $monthlyBudgets): array
    {
        $trend = [];

        for ($month = 1; $month <= 12; $month++) {
            $actual = $monthlyActuals[$month]?->sum('amount') ?? 0;
            $budget = $monthlyBudgets->sum(fn ($item) => $item[$month] ?? 0);

            $trend[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month)),
                'budget' => $budget,
                'actual' => $actual,
                'variance' => $budget - $actual,
                'variance_percentage' => $budget > 0 ? (($budget - $actual) / $budget) * 100 : 0,
            ];
        }

        return $trend;
    }

    private function detectAnomalies($monthlyActuals, $monthlyBudgets): array
    {
        $anomalies = [];
        $threshold = 1.5;

        for ($month = 1; $month <= 12; $month++) {
            $actual = $monthlyActuals[$month]?->sum('amount') ?? 0;
            $budget = $monthlyBudgets->sum(fn ($item) => $item[$month] ?? 0);

            if ($budget > 0) {
                $ratio = $actual / $budget;

                if ($ratio > $threshold) {
                    $anomalies[] = [
                        'month' => $month,
                        'type' => 'overspend',
                        'message' => sprintf('Spent %.0f%% of monthly budget (%.0f%% over threshold)', $ratio * 100, $threshold * 100),
                        'severity' => $ratio > 2 ? 'high' : 'medium',
                    ];
                } elseif ($ratio < 0.5 && $month < now()->month) {
                    $anomalies[] = [
                        'month' => $month,
                        'type' => 'underspend',
                        'message' => sprintf('Only spent %.0f%% of monthly budget', $ratio * 100),
                        'severity' => 'low',
                    ];
                }
            }
        }

        return $anomalies;
    }

    private function generateRecommendations($budgets, $actuals): array
    {
        $recommendations = [];

        foreach ($budgets as $budget) {
            $accountActuals = $actuals->where('account_id', $budget->account_id);
            $actualTotal = $accountActuals->sum('amount');
            $budgetTotal = $budget->annual_budget;

            $utilization = ($actualTotal / $budgetTotal) * 100;

            if ($utilization > 100) {
                $recommendations[] = [
                    'type' => 'budget_increase',
                    'account' => $budget->account->name,
                    'message' => "{$budget->account->name} exceeded budget by ".round($utilization - 100).'%. Consider increasing budget or reducing spending.',
                    'priority' => 'high',
                ];
            } elseif ($utilization < 50 && now()->month > 6) {
                $recommendations[] = [
                    'type' => 'budget_review',
                    'account' => $budget->account->name,
                    'message' => "{$budget->account->name} has only used ".round($utilization).'% of budget. Consider reallocating funds.',
                    'priority' => 'medium',
                ];
            }
        }

        return $recommendations;
    }

    private function analyzeByAccount($budgets, $actuals): array
    {
        return $budgets->map(function ($budget) use ($actuals) {
            $accountActuals = $actuals->where('account_id', $budget->account_id);
            $actualTotal = $accountActuals->sum('amount');

            return [
                'account_id' => $budget->account_id,
                'account_name' => $budget->account->name,
                'account_code' => $budget->account->code,
                'budget' => $budget->annual_budget,
                'actual' => $actualTotal,
                'variance' => $budget->annual_budget - $actualTotal,
                'utilization' => $budget->annual_budget > 0 ? ($actualTotal / $budget->annual_budget) * 100 : 0,
            ];
        })->toArray();
    }

    private function generateForecast($actuals, int $year): array
    {
        $currentMonth = now()->month;

        if ($currentMonth < 3) {
            return ['message' => 'Insufficient data for forecast (need at least 3 months)'];
        }

        $lastThreeMonths = $actuals->filter(fn ($a) => $a->month >= $currentMonth - 2);
        $monthlyAverage = $lastThreeMonths->sum('amount') / 3;
        $remainingMonths = 12 - $currentMonth;

        $forecast = $monthlyAverage * $remainingMonths;
        $currentTotal = $actuals->sum('amount');
        $projectedTotal = $currentTotal + $forecast;

        return [
            'monthly_average' => $monthlyAverage,
            'remaining_months' => $remainingMonths,
            'projected_spending' => $forecast,
            'projected_total' => $projectedTotal,
            'confidence' => $this->calculateConfidence($lastThreeMonths),
        ];
    }

    private function calculateConfidence($lastThreeMonths): string
    {
        if ($lastThreeMonths->count() < 3) {
            return 'low';
        }

        $values = $lastThreeMonths->pluck('amount')->toArray();
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn ($v) => pow($v - $mean, 2), $values)) / count($values);
        $stdDev = sqrt($variance);
        $cv = $mean > 0 ? ($stdDev / $mean) * 100 : 0;

        return match (true) {
            $cv < 10 => 'high',
            $cv < 25 => 'medium',
            default => 'low',
        };
    }

    private function generateInsights($budgets, $actuals, int $year): array
    {
        $insights = [];

        $spendingByMonth = $actuals->groupBy('month')->map(fn ($g) => $g->sum('amount'));

        if ($spendingByMonth->count() >= 2) {
            $firstHalf = $spendingByMonth->filter(fn ($_, $k) => $k <= 6)->sum();
            $secondHalf = $spendingByMonth->filter(fn ($_, $k) => $k > 6)->sum();

            if ($firstHalf > 0) {
                $growthRate = (($secondHalf - $firstHalf) / $firstHalf) * 100;

                $insights[] = [
                    'type' => 'spending_growth',
                    'title' => 'Spending Trend',
                    'description' => $growthRate > 0
                        ? 'Spending increased by '.round($growthRate).'% in second half of year'
                        : 'Spending decreased by '.round(abs($growthRate)).'% in second half of year',
                ];
            }
        }

        $topAccount = $actuals->groupBy('account_id')
            ->map(fn ($g) => $g->sum('amount'))
            ->sortDesc()
            ->keys()
            ->first();

        if ($topAccount) {
            $account = Account::find($topAccount);
            $topAmount = $actuals->where('account_id', $topAccount)->sum('amount');
            $total = $actuals->sum('amount');
            $percentage = $total > 0 ? ($topAmount / $total) * 100 : 0;

            if ($percentage > 30) {
                $insights[] = [
                    'type' => 'top_spender',
                    'title' => 'Top Spending Category',
                    'description' => "{$account->name} accounts for ".round($percentage).'% of total spending',
                ];
            }
        }

        $lastMonth = now()->month;
        $prevMonth = $lastMonth - 1;
        $lastMonthSpending = $actuals->where('month', $lastMonth)->sum('amount');
        $prevMonthSpending = $actuals->where('month', $prevMonth)->sum('amount');

        if ($prevMonthSpending > 0) {
            $change = (($lastMonthSpending - $prevMonthSpending) / $prevMonthSpending) * 100;

            $insights[] = [
                'type' => 'monthly_change',
                'title' => 'Month-over-Month Change',
                'description' => abs($change) > 10
                    ? sprintf('Spending %s by %.0f%% compared to last month', $change > 0 ? 'increased' : 'decreased', abs($change))
                    : 'Spending remained stable compared to last month',
            ];
        }

        return $insights;
    }

    private function getStatus(float $utilization): string
    {
        return match (true) {
            $utilization > 100 => 'over_budget',
            $utilization > 80 => 'near_limit',
            $utilization > 50 => 'on_track',
            default => 'under_budget',
        };
    }

    public function comparePeriods(int $costCentreId, int $year1, int $year2): array
    {
        $actual1 = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year1)->sum('amount');
        $actual2 = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year2)->sum('amount');

        $budget1 = Budget::where('cost_centre_id', $costCentreId)
            ->where('year', $year1)->sum('annual_budget');
        $budget2 = Budget::where('cost_centre_id', $costCentreId)
            ->where('year', $year2)->sum('annual_budget');

        return [
            'year1' => ['year' => $year1, 'actual' => $actual1, 'budget' => $budget1],
            'year2' => ['year' => $year2, 'actual' => $actual2, 'budget' => $budget2],
            'actual_change' => $actual1 > 0 ? (($actual2 - $actual1) / $actual1) * 100 : 0,
            'budget_change' => $budget1 > 0 ? (($budget2 - $budget1) / $budget1) * 100 : 0,
        ];
    }
}
