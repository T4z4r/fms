<?php

namespace App\Http\Controllers;

use App\Models\CostCentre;
use App\Services\AiCommentaryService;
use App\Services\FinancialAnalysisService;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function __construct(
        private FinancialAnalysisService $analysis,
        private AiCommentaryService $commentary
    ) {}

    public function index(Request $request)
    {
        $costCentres = CostCentre::active()->get();

        $selectedCostCentre = $request->cost_centre_id
            ? CostCentre::find($request->cost_centre_id)
            : $costCentres->first();

        $year = $request->year ?? now()->year;

        if (! $selectedCostCentre) {
            return view('analysis.index', [
                'costCentres' => $costCentres,
                'selectedCostCentre' => null,
                'year' => $year,
            ]);
        }

        $analysis = $this->analysis->analyzeCostCentre($selectedCostCentre->id, $year);

        $aiCommentary = $this->commentary->generateVarianceCommentary($selectedCostCentre->id, $year);

        return view('analysis.index', [
            'costCentres' => $costCentres,
            'selectedCostCentre' => $selectedCostCentre,
            'year' => $year,
            'analysis' => $analysis,
            'aiCommentary' => $aiCommentary,
        ]);
    }

    public function compare(Request $request)
    {
        $request->validate([
            'cost_centre_id' => 'required|exists:cost_centres,id',
            'year1' => 'required|integer|min:2000',
            'year2' => 'required|integer|min:2000',
        ]);

        $comparison = $this->analysis->comparePeriods(
            $request->cost_centre_id,
            $request->year1,
            $request->year2
        );

        return view('analysis.compare', [
            'costCentre' => CostCentre::find($request->cost_centre_id),
            'comparison' => $comparison,
        ]);
    }
}
