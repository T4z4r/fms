<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Actual;
use App\Models\CostCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function showImportForm()
    {
        $costCentres = CostCentre::active()->get();

        return view('import.actuals', compact('costCentres'));
    }

    public function importActuals(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'cost_centre_id' => 'required|exists:cost_centres,id',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $rows = [];
        if ($extension === 'csv') {
            $rows = $this->parseCsv($file);
        } else {
            $rows = $this->parseExcel($file);
        }

        $costCentreId = $request->cost_centre_id;
        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            if (empty($row['code']) || empty($row['month']) || empty($row['amount'])) {
                $errors[] = "Row {$rowNum}: Missing required fields.";

                continue;
            }

            $account = Account::where('code', $row['code'])
                ->where('cost_centre_id', $costCentreId)
                ->first();

            if (! $account) {
                $errors[] = "Row {$rowNum}: Account code '{$row['code']}' not found.";

                continue;
            }

            $month = (int) $row['month'];
            if ($month < 1 || $month > 12) {
                $errors[] = "Row {$rowNum}: Invalid month '{$row['month']}'.";

                continue;
            }

            $year = $row['year'] ?? now()->year;
            $amount = (float) $row['amount'];

            Actual::updateOrCreate(
                [
                    'cost_centre_id' => $costCentreId,
                    'account_id' => $account->id,
                    'month' => $month,
                    'year' => $year,
                ],
                ['amount' => DB::raw('amount + '.$amount)]
            );

            $imported++;
        }

        if (count($errors) > 0) {
            return back()->with('warning', "Imported {$imported} records. ".count($errors).' errors: '.implode('; ', array_slice($errors, 5)));
        }

        return back()->with('success', "Successfully imported {$imported} actual records.");
    }

    private function parseCsv($file)
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            $rowData = array_combine($headers, $row);
            $data[] = [
                'code' => trim($rowData['code'] ?? ''),
                'month' => (int) trim($rowData['month'] ?? ''),
                'year' => isset($rowData['year']) ? (int) trim($rowData['year']) : now()->year,
                'amount' => trim($rowData['amount'] ?? ''),
            ];
        }
        fclose($handle);

        return $data;
    }

    private function parseExcel($file)
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $headers = array_map('strtolower', array_map('trim', array_shift($rows)));
        $data = [];

        foreach ($rows as $row) {
            $rowData = array_combine($headers, $row);
            if ($rowData) {
                $data[] = [
                    'code' => trim($rowData['code'] ?? ''),
                    'month' => (int) trim($rowData['month'] ?? ''),
                    'year' => isset($rowData['year']) ? (int) trim($rowData['year']) : now()->year,
                    'amount' => trim($rowData['amount'] ?? ''),
                ];
            }
        }

        return $data;
    }
}
