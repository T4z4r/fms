<?php

namespace App\Services;

use App\Models\Actual;
use App\Models\Budget;

class AiCommentaryService
{
    public function generateVarianceCommentary(int $costCentreId, int $year): array
    {
        $budgets = Budget::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->with('account')
            ->get();

        $actuals = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->get();

        $month = now()->month;
        $ytdActuals = $actuals->filter(fn ($a) => $a->month <= $month)->sum('amount');
        $ytdBudgets = $this->calculateYtdBudget($budgets, $month);

        $totalBudget = $budgets->sum('annual_budget');
        $totalActual = $actuals->sum('amount');
        $variance = $totalBudget - $totalActual;
        $variancePercentage = $totalBudget > 0 ? ($variance / $totalBudget) * 100 : 0;

        $commentary = [];

        $commentary[] = $this->generateExecutiveSummary($totalBudget, $totalActual, $variance, $variancePercentage, $year);
        $commentary[] = $this->generateYtdCommentary($ytdBudgets, $ytdActuals, $month);
        $commentary[] = $this->generateAccountCommentary($budgets, $actuals);
        $commentary[] = $this->generateTrendCommentary($actuals, $month);
        $commentary[] = $this->generateRecommendation($totalBudget, $totalActual, $variancePercentage);

        return [
            'executive_summary' => $commentary[0],
            'ytd_analysis' => $commentary[1],
            'account_analysis' => $commentary[2],
            'trend_analysis' => $commentary[3],
            'recommendations' => $commentary[4],
            'full_commentary' => implode("\n\n", array_filter($commentary)),
        ];
    }

    private function generateExecutiveSummary(float $budget, float $actual, float $variance, float $variancePct, int $year): string
    {
        $status = $variance >= 0 ? 'under' : 'over';
        $absVariancePct = abs($variancePct);

        $summary = sprintf(
            "## %d Financial Summary\n\nFor the fiscal year %d, the total budget allocation was £%.2f against actual expenditure of £%.2f. ",
            $year, $year, $budget, $actual
        );

        if ($absVariancePct < 5) {
            $summary .= 'The variance of £'.number_format(abs($variance), 2).' ('.number_format($absVariancePct, 1).'%) indicates spending is **on track** and closely aligned with the budget. ';
        } elseif ($absVariancePct < 15) {
            $summary .= sprintf(
                'The variance of £%s (%s%%) shows %s spending. %s',
                number_format(abs($variance), 2),
                number_format($absVariancePct, 1),
                $status === 'under' ? 'moderate savings' : 'moderate overspend',
                $status === 'over' ? 'Monitoring is recommended.' : 'Funds are available for reallocation.'
            );
        } else {
            $summary .= sprintf(
                'CRITICAL: The variance of £%s (%s%%) indicates significant %s. Immediate action is required.',
                number_format(abs($variance), 2),
                number_format($absVariancePct, 1),
                $status === 'over' ? 'overspending' : 'underspending'
            );
        }

        return $summary;
    }

    private function generateYtdCommentary(float $ytdBudget, float $ytdActual, int $month): string
    {
        $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        $currentMonth = $monthNames[$month];
        $ytdVariance = $ytdBudget - $ytdActual;
        $expectedPct = ($month / 12) * 100;
        $actualPct = $ytdBudget > 0 ? ($ytdActual / $ytdBudget) * 100 : 0;

        $commentary = sprintf(
            "## Year-to-Date Analysis (as of %s)\n\n",
            $currentMonth
        );

        $commentary .= sprintf(
            "YTD Budget: £%s | YTD Actual: £%s | YTD Variance: £%s\n\n",
            number_format($ytdBudget, 2),
            number_format($ytdActual, 2),
            number_format($ytdVariance, 2)
        );

        if ($actualPct > $expectedPct + 10) {
            $commentary .= "Spending is running **ahead of schedule**. At this point in the year, approximately {$expectedPct}% of the budget should have been utilized, but actual utilization stands at ".number_format($actualPct, 1).'%. ';
        } elseif ($actualPct < $expectedPct - 10) {
            $commentary .= "Spending is running **behind schedule**. At this point in the year, approximately {$expectedPct}% of the budget should have been utilized, but actual utilization is only ".number_format($actualPct, 1).'%. ';
        } else {
            $commentary .= 'Spending is **on schedule**. Actual utilization ('.number_format($actualPct, 1).'%) is closely aligned with expected ('.number_format($expectedPct, 1).'%). ';
        }

        return $commentary;
    }

    private function generateAccountCommentary($budgets, $actuals): string
    {
        $accountAnalysis = [];

        foreach ($budgets as $budget) {
            $accountActuals = $actuals->where('account_id', $budget->account_id);
            $actualTotal = $accountActuals->sum('amount');
            $variance = $budget->annual_budget - $actualTotal;
            $variancePct = $budget->annual_budget > 0 ? ($variance / $budget->annual_budget) * 100 : 0;

            $accountAnalysis[] = [
                'name' => $budget->account->name,
                'code' => $budget->account->code,
                'budget' => $budget->annual_budget,
                'actual' => $actualTotal,
                'variance' => $variance,
                'variance_pct' => $variancePct,
            ];
        }

        usort($accountAnalysis, fn ($a, $b) => abs($b['variance_pct']) <=> abs($a['variance_pct']));

        $commentary = "## Account-Level Analysis\n\n";

        $topConcerns = array_filter($accountAnalysis, fn ($a) => $a['variance_pct'] < -15);
        $topSavings = array_filter($accountAnalysis, fn ($a) => $a['variance_pct'] > 15);

        if (! empty($topConcerns)) {
            $commentary .= "### Areas of Concern\n";
            foreach ($topConcerns as $account) {
                $commentary .= sprintf(
                    "- **%s (%s)**: £%s over budget (%.1f%%)\n",
                    $account['name'],
                    $account['code'],
                    number_format(abs($account['variance']), 2),
                    abs($account['variance_pct'])
                );
            }
            $commentary .= "\n";
        }

        if (! empty($topSavings)) {
            $commentary .= "### Areas of Savings\n";
            foreach ($topSavings as $account) {
                $commentary .= sprintf(
                    "- **%s (%s)**: £%s under budget (%.1f%%)\n",
                    $account['name'],
                    $account['code'],
                    number_format($account['variance'], 2),
                    $account['variance_pct']
                );
            }
            $commentary .= "\n";
        }

        return $commentary;
    }

    private function generateTrendCommentary($actuals, int $currentMonth): string
    {
        if ($currentMonth < 2) {
            return "## Trend Analysis\n\nInsufficient data for trend analysis. More months of data required.";
        }

        $monthlySpending = $actuals->groupBy('month')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortKeys();

        $commentary = "## Monthly Trend Analysis\n\n";

        if ($monthlySpending->count() >= 2) {
            $lastMonth = $currentMonth - 1;
            $prevMonth = $currentMonth - 2;

            $lastMonthSpending = $monthlySpending[$lastMonth] ?? 0;
            $prevMonthSpending = $monthlySpending[$prevMonth] ?? 0;

            if ($prevMonthSpending > 0) {
                $momChange = (($lastMonthSpending - $prevMonthSpending) / $prevMonthSpending) * 100;

                if (abs($momChange) < 5) {
                    $commentary .= 'Spending has remained **stable** month-over-month, with only a '.number_format(abs($momChange), 1).'% change. ';
                } elseif ($momChange > 0) {
                    $commentary .= sprintf(
                        'Spending increased by %.1f%% month-over-month. This trend should be monitored. ',
                        $momChange
                    );
                } else {
                    $commentary .= sprintf(
                        'Spending decreased by %.1f%% month-over-month, indicating better cost control. ',
                        abs($momChange)
                    );
                }
            }

            $avgMonthly = $monthlySpending->avg();
            $currentMonthSpending = $monthlySpending[$currentMonth] ?? 0;

            if ($currentMonthSpending > $avgMonthly * 1.2) {
                $commentary .= sprintf(
                    'Current month spending (£%s) is significantly above the monthly average (£%s). ',
                    number_format($currentMonthSpending, 2),
                    number_format($avgMonthly, 2)
                );
            }
        }

        return $commentary;
    }

    private function generateRecommendation(float $budget, float $actual, float $variancePct): string
    {
        $commentary = "## AI Recommendations\n\n";

        if ($variancePct < -20) {
            $commentary .= "1. **Urgent Budget Review**: Significant overspending detected. Review all large transactions and consider budget reallocation.\n";
            $commentary .= "2. **Cost Control Measures**: Implement immediate spending controls for high-variance accounts.\n";
            $commentary .= "3. **Forecast Adjustment**: Update full-year forecast to reflect current spending trajectory.\n";
        } elseif ($variancePct < -10) {
            $commentary .= "1. **Monitor High-Variance Accounts**: Identify accounts exceeding budget and review spending patterns.\n";
            $commentary .= "2. **Quarterly Review**: Schedule a budget review meeting to discuss reallocation options.\n";
        } elseif ($variancePct > 20) {
            $commentary .= "1. **Reallocate Funds**: Significant underspending indicates available funds for other priorities.\n";
            $commentary .= "2. **Next Year Planning**: Consider increasing budget allocations for underutilized accounts.\n";
        } else {
            $commentary .= "1. **Continue Monitoring**: Current spending is within acceptable variance. Maintain regular monitoring.\n";
            $commentary .= "2. **Year-End Planning**: Prepare for year-end budget reconciliation.\n";
        }

        return $commentary;
    }

    private function calculateYtdBudget($budgets, int $month): float
    {
        $total = 0;

        foreach ($budgets as $budget) {
            $monthlyAmount = $budget->annual_budget / 12;
            $total += $monthlyAmount * $month;
        }

        return $total;
    }

    public function generateQuickInsight(?int $costCentreId, int $year): string
    {
        if (! $costCentreId) {
            return 'Select a cost centre to view insights';
        }

        $budgets = Budget::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->get();

        $actuals = Actual::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->get();

        $totalBudget = $budgets->sum('annual_budget');
        $totalActual = $actuals->sum('amount');
        $variance = $totalBudget - $totalActual;
        $variancePct = $totalBudget > 0 ? ($variance / $totalBudget) * 100 : 0;

        $status = match (true) {
            $variancePct < -20 => 'CRITICAL - Significant overspending',
            $variancePct < -10 => 'WARNING - Moderate overspending',
            $variancePct > 20 => 'NOTICE - Significant underspending',
            default => 'ON TRACK - Spending within budget',
        };

        return sprintf(
            '**%s**: £%s spent of £%s budget (%.1f%% variance)',
            $status,
            number_format($totalActual, 0),
            number_format($totalBudget, 0),
            $variancePct
        );
    }
}
