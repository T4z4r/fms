<?php

namespace App\Http\Controllers;

use App\Models\Actual;
use App\Models\Alert;
use App\Models\Budget;
use App\Services\AlertService;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index(Request $request)
    {
        $query = Alert::with(['costCentre', 'account'])
            ->orderBy('created_at', 'desc');

        if ($request->unread) {
            $query->where('is_read', false);
        }

        $alerts = $query->paginate(20);

        return view('alerts.index', compact('alerts'));
    }

    public function markRead(Alert $alert)
    {
        $alert->update(['is_read' => true]);

        return back()->with('success', 'Alert marked as read.');
    }

    public function generate(Request $request)
    {
        $year = $request->year ?? now()->year;
        $costCentreId = $request->cost_centre_id;

        $this->alertService->generateAlerts($year, $costCentreId);

        return back()->with('success', 'Alerts generated successfully.');
    }

    public static function generateBudgetAlerts(): void
    {
        $year = now()->year;
        $month = now()->month;

        $actuals = Actual::where('year', $year)
            ->where('month', $month)
            ->with(['costCentre', 'account'])
            ->get()
            ->groupBy(['cost_centre_id', 'account_id']);

        foreach ($actuals as $costCentreId => $accounts) {
            foreach ($accounts as $accountId => $actualGroup) {
                $actualTotal = $actualGroup->sum('amount');

                $budget = Budget::where('cost_centre_id', $costCentreId)
                    ->where('account_id', $accountId)
                    ->where('year', $year)
                    ->first();

                if ($budget && $budget->annual_budget > 0) {
                    $monthlyBudget = $budget->annual_budget / 12;
                    $threshold = $monthlyBudget * 1.1;

                    if ($actualTotal > $threshold) {
                        $percentage = round(($actualTotal - $monthlyBudget) / $monthlyBudget * 100);
                        $costCentre = $actualGroup->first()->costCentre;
                        $account = $actualGroup->first()->account;

                        Alert::updateOrCreate(
                            [
                                'type' => 'over_budget',
                                'cost_centre_id' => $costCentreId,
                                'account_id' => $accountId,
                                'year' => $year,
                                'month' => $month,
                            ],
                            [
                                'message' => "{$costCentre->name} - {$account->name} exceeded monthly budget by {$percentage}%",
                                'is_read' => false,
                            ]
                        );
                    }
                }
            }
        }
    }
}
