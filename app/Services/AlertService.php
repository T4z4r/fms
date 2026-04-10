<?php

namespace App\Services;

use App\Models\Actual;
use App\Models\Alert;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;

class AlertService
{
    private const OVERSPEND_PERCENTAGE_THRESHOLD = 10;

    private const OVERSPEND_AMOUNT_THRESHOLD = 10000;

    public function generateAlerts(int $year, ?int $costCentreId = null): void
    {
        $this->generateOverspendAlerts($year, $costCentreId);
        $this->generateBudgetUtilizationAlerts($year, $costCentreId);
    }

    private function generateOverspendAlerts(int $year, ?int $costCentreId): void
    {
        $query = Budget::query()->where('year', $year);

        if ($costCentreId) {
            $query->where('cost_centre_id', $costCentreId);
        }

        $budgets = $query->with(['account', 'costCentre'])->get();

        foreach ($budgets as $budget) {
            $actualSpending = Actual::where('cost_centre_id', $budget->cost_centre_id)
                ->where('account_id', $budget->account_id)
                ->where('year', $year)
                ->sum('amount');

            $variance = $actualSpending - $budget->annual_budget;
            $variancePercentage = $budget->annual_budget > 0
                ? ($variance / $budget->annual_budget) * 100
                : 0;

            if ($variance > 0) {
                $overspendAmount = $variance;
                $overspendPercentage = $variancePercentage;

                if ($overspendPercentage >= self::OVERSPEND_PERCENTAGE_THRESHOLD || $overspendAmount >= self::OVERSPEND_AMOUNT_THRESHOLD) {
                    $this->createAlert(
                        type: 'overspend',
                        message: sprintf(
                            '%s (%s) overspent by £%.2f (%.1f%% over budget)',
                            $budget->account->name,
                            $budget->account->code,
                            $overspendAmount,
                            $overspendPercentage
                        ),
                        costCentreId: $budget->cost_centre_id,
                        accountId: $budget->account_id,
                        year: $year,
                        severity: $overspendPercentage >= 20 || $overspendAmount >= 20000 ? 'high' : 'medium'
                    );
                }
            }
        }
    }

    private function generateBudgetUtilizationAlerts(int $year, ?int $costCentreId): void
    {
        $currentMonth = now()->month;
        $expectedUtilization = ($currentMonth / 12) * 100;

        $query = Budget::query()->where('year', $year);

        if ($costCentreId) {
            $query->where('cost_centre_id', $costCentreId);
        }

        $budgets = $query->with(['account', 'costCentre'])->get();

        foreach ($budgets as $budget) {
            $actualSpending = Actual::where('cost_centre_id', $budget->cost_centre_id)
                ->where('account_id', $budget->account_id)
                ->where('year', $year)
                ->sum('amount');

            $utilizationPercentage = $budget->annual_budget > 0
                ? ($actualSpending / $budget->annual_budget) * 100
                : 0;

            if ($utilizationPercentage > 90 && $utilizationPercentage < 100) {
                $this->createAlert(
                    type: 'near_limit',
                    message: sprintf(
                        '%s (%s) is at %.1f%% of budget - approaching limit',
                        $budget->account->name,
                        $budget->account->code,
                        $utilizationPercentage
                    ),
                    costCentreId: $budget->cost_centre_id,
                    accountId: $budget->account_id,
                    year: $year,
                    severity: 'low'
                );
            } elseif ($utilizationPercentage >= 100) {
                $this->createAlert(
                    type: 'exceeded',
                    message: sprintf(
                        '%s (%s) has exceeded annual budget',
                        $budget->account->name,
                        $budget->account->code
                    ),
                    costCentreId: $budget->cost_centre_id,
                    accountId: $budget->account_id,
                    year: $year,
                    severity: 'high'
                );
            }
        }
    }

    public function getActiveAlerts(?int $costCentreId = null, ?int $year = null): Collection
    {
        $query = Alert::query()->where('is_read', false);

        if ($costCentreId) {
            $query->where('cost_centre_id', $costCentreId);
        }

        if ($year) {
            $query->where('year', $year);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getCostCentreAlerts(int $costCentreId, int $year): array
    {
        $budgets = Budget::where('cost_centre_id', $costCentreId)
            ->where('year', $year)
            ->with('account')
            ->get();

        $alerts = [];

        foreach ($budgets as $budget) {
            $actualSpending = Actual::where('cost_centre_id', $costCentreId)
                ->where('account_id', $budget->account_id)
                ->where('year', $year)
                ->sum('amount');

            $variance = $actualSpending - $budget->annual_budget;
            $variancePercentage = $budget->annual_budget > 0
                ? ($variance / $budget->annual_budget) * 100
                : 0;

            if ($variance > 0 && ($variancePercentage >= self::OVERSPEND_PERCENTAGE_THRESHOLD || $variance >= self::OVERSPEND_AMOUNT_THRESHOLD)) {
                $alerts[] = [
                    'account' => $budget->account->name,
                    'code' => $budget->account->code,
                    'budget' => $budget->annual_budget,
                    'actual' => $actualSpending,
                    'variance' => $variance,
                    'variance_percentage' => $variancePercentage,
                    'severity' => $variancePercentage >= 20 || $variance >= 20000 ? 'high' : 'medium',
                ];
            }
        }

        return $alerts;
    }

    private function createAlert(string $type, string $message, ?int $costCentreId = null, ?int $accountId = null, ?int $year = null, string $severity = 'low'): void
    {
        $existingAlert = Alert::where('type', $type)
            ->where('cost_centre_id', $costCentreId)
            ->where('account_id', $accountId)
            ->where('year', $year ?? now()->year)
            ->where('is_read', false)
            ->exists();

        if (! $existingAlert) {
            Alert::create([
                'type' => $type,
                'message' => $message,
                'cost_centre_id' => $costCentreId,
                'account_id' => $accountId,
                'year' => $year ?? now()->year,
                'is_read' => false,
            ]);
        }
    }

    public function markAsRead(int $alertId): void
    {
        Alert::findOrFail($alertId)->update(['is_read' => true]);
    }

    public function clearAllAlerts(?int $costCentreId = null): void
    {
        $query = Alert::query();

        if ($costCentreId) {
            $query->where('cost_centre_id', $costCentreId);
        }

        $query->delete();
    }
}
