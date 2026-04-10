<?php

namespace App\Http\Controllers;

use App\Models\Actual;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\CostCentre;
use App\Services\AiCommentaryService;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private AlertService $alertService,
        private AiCommentaryService $commentaryService
    ) {}

    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $costCentreId = $request->cost_centre_id;

        $costCentres = CostCentre::active()->get();

        $currentMonth = now()->month;

        $budgets = Budget::where('year', $year)
            ->when($costCentreId, fn ($q) => $q->where('cost_centre_id', $costCentreId))
            ->with('account')
            ->get();

        $actuals = Actual::where('year', $year)
            ->when($costCentreId, fn ($q) => $q->where('cost_centre_id', $costCentreId))
            ->with('account')
            ->get();

        $totalBudget = $budgets->sum('annual_budget');
        $totalActual = $actuals->sum('amount');
        $totalVariance = $totalBudget - $totalActual;

        $ytdBudget = $this->calculateYtdBudget($budgets, $currentMonth);
        $ytdActual = $actuals->filter(fn ($a) => $a->month <= $currentMonth)->sum('amount');
        $ytdVariance = $ytdBudget - $ytdActual;

        $monthlyData = $this->getMonthlyTrend($year, $costCentreId, $currentMonth);

        $accountSummary = $this->getAccountSummary($budgets, $actuals);

        $alerts = $this->alertService->getActiveAlerts($costCentreId, $year);

        $quickInsight = $this->commentaryService->generateQuickInsight($costCentreId ?? CostCentre::first()?->id, $year);

        $forecast = $this->generateForecast($actuals, $year, $totalBudget);

        return view('dashboard', compact(
            'costCentres', 'year', 'costCentreId',
            'totalBudget', 'totalActual', 'totalVariance',
            'ytdBudget', 'ytdActual', 'ytdVariance',
            'monthlyData', 'accountSummary', 'alerts', 'quickInsight', 'forecast'
        ));
    }

    private function calculateYtdBudget($budgets, int $month): float
    {
        $monthlyBudgets = BudgetLine::whereIn('budget_id', $budgets->pluck('id'))
            ->where('month', '<=', $month)
            ->sum('amount');

        if ($monthlyBudgets > 0) {
            return $monthlyBudgets;
        }

        return $budgets->sum('annual_budget') / 12 * $month;
    }

    private function getMonthlyTrend(int $year, ?int $costCentreId, int $currentMonth): array
    {
        $monthlyActuals = DB::table('actuals')
            ->select('month', DB::raw('SUM(amount) as total'))
            ->where('year', $year)
            ->when($costCentreId, fn ($q) => $q->where('cost_centre_id', $costCentreId))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyBudget = DB::table('budget_lines')
            ->select('budget_lines.month', DB::raw('SUM(budget_lines.amount) as total'))
            ->join('budgets', 'budget_lines.budget_id', '=', 'budgets.id')
            ->where('budgets.year', $year)
            ->when($costCentreId, fn ($q) => $q->where('budgets.cost_centre_id', $costCentreId))
            ->groupBy('budget_lines.month')
            ->orderBy('budget_lines.month')
            ->pluck('total', 'month')
            ->toArray();

        $trend = [];
        $cumulativeActual = 0;
        $cumulativeBudget = 0;

        $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($month = 1; $month <= 12; $month++) {
            $actual = $monthlyActuals[$month] ?? 0;
            $budget = $monthlyBudget[$month] ?? ($month <= $currentMonth ? ($monthlyBudget[1] ?? 0) : 0);

            $cumulativeActual += $actual;
            $cumulativeBudget += $budget > 0 ? $budget : ($monthlyBudget[1] ?? 0);

            $trend[] = [
                'month' => $month,
                'month_name' => $monthNames[$month],
                'budget' => $budget,
                'actual' => $actual,
                'variance' => $budget - $actual,
                'cumulative_budget' => $cumulativeBudget,
                'cumulative_actual' => $cumulativeActual,
                'cumulative_variance' => $cumulativeBudget - $cumulativeActual,
            ];
        }

        return $trend;
    }

    private function getAccountSummary($budgets, $actuals): array
    {
        $summary = [];

        foreach ($budgets as $budget) {
            $accountActuals = $actuals->where('account_id', $budget->account_id);
            $actualTotal = $accountActuals->sum('amount');
            $variance = $budget->annual_budget - $actualTotal;
            $variancePct = $budget->annual_budget > 0 ? ($variance / $budget->annual_budget) * 100 : 0;

            $summary[] = [
                'account_id' => $budget->account_id,
                'account_name' => $budget->account->name,
                'account_code' => $budget->account->code,
                'budget' => $budget->annual_budget,
                'actual' => $actualTotal,
                'variance' => $variance,
                'variance_percentage' => $variancePct,
                'status' => $this->getAccountStatus($variancePct),
            ];
        }

        usort($summary, fn ($a, $b) => abs($b['variance_percentage']) <=> abs($a['variance_percentage']));

        return $summary;
    }

    private function getAccountStatus(float $variancePct): string
    {
        return match (true) {
            $variancePct < -20 => 'critical',
            $variancePct < -10 => 'warning',
            $variancePct > 20 => 'underspent',
            default => 'on_track',
        };
    }

    private function generateForecast($actuals, int $year, float $totalBudget): array
    {
        $currentMonth = now()->month;

        if ($currentMonth < 3) {
            return ['message' => 'Insufficient data for forecast'];
        }

        $lastThreeMonths = $actuals->filter(fn ($a) => $a->month >= $currentMonth - 2);

        if ($lastThreeMonths->isEmpty()) {
            return ['message' => 'No recent data for forecast'];
        }

        $monthlyAverage = $lastThreeMonths->sum('amount') / 3;
        $remainingMonths = 12 - $currentMonth;

        $currentSpending = $actuals->sum('amount');
        $forecastSpending = $monthlyAverage * $remainingMonths;
        $projectedTotal = $currentSpending + $forecastSpending;

        $projectedVariance = $totalBudget - $projectedTotal;
        $projectedVariancePct = $totalBudget > 0 ? ($projectedVariance / $totalBudget) * 100 : 0;

        return [
            'monthly_average' => $monthlyAverage,
            'remaining_months' => $remainingMonths,
            'current_spending' => $currentSpending,
            'forecast_spending' => $forecastSpending,
            'projected_total' => $projectedTotal,
            'projected_variance' => $projectedVariance,
            'projected_variance_percentage' => $projectedVariancePct,
            'confidence' => $this->calculateForecastConfidence($lastThreeMonths),
        ];
    }

    private function calculateForecastConfidence($lastThreeMonths): string
    {
        if ($lastThreeMonths->count() < 3) {
            return 'low';
        }

        $values = $lastThreeMonths->pluck('amount')->toArray();
        $mean = array_sum($values) / count($values);

        if ($mean === 0) {
            return 'low';
        }

        $variance = array_sum(array_map(fn ($v) => pow($v - $mean, 2), $values)) / count($values);
        $stdDev = sqrt($variance);
        $cv = ($stdDev / $mean) * 100;

        return match (true) {
            $cv < 10 => 'high',
            $cv < 25 => 'medium',
            default => 'low',
        };
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
            $budget = $budgetGroup->first();

            $lastThreeMonths = Actual::where('account_id', $accountId)
                ->where('year', $year)
                ->whereIn('month', [now()->month - 2, now()->month - 1, now()->month])
                ->sum('amount');

            $monthlyAverage = $lastThreeMonths / 3;
            $remainingMonths = 12 - now()->month;
            $forecast = $monthlyAverage * $remainingMonths;

            $forecasts[] = [
                'account' => $budget->account,
                'budget' => $budget->annual_budget,
                'current_spending' => $lastThreeMonths,
                'monthly_average' => $monthlyAverage,
                'forecast' => $forecast,
                'projected_total' => $lastThreeMonths + $forecast,
                'variance' => $budget->annual_budget - ($lastThreeMonths + $forecast),
            ];
        }

        return view('forecast', compact('forecasts', 'costCentres', 'year', 'costCentreId'));
    }
}
