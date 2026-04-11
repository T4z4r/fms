<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PowerBiExcelExportService
{
    public function download(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $overviewSheet = $this->addOverviewSheet($spreadsheet, $data);

        $monthlyTrendSheet = $this->addTabularSheet($spreadsheet, 'Monthly Trend', [
            'Month', 'Budget', 'Actual',
        ], collect($data['monthlyData']['categories'] ?? [])->map(function ($month, $index) use ($data) {
            return [
                $month,
                $data['monthlyData']['budget'][$index] ?? 0,
                $data['monthlyData']['actual'][$index] ?? 0,
            ];
        })->all());

        $accountDistributionSheet = $this->addTabularSheet($spreadsheet, 'Account Distribution', [
            'Account', 'Budget', 'Actual',
        ], array_map(fn ($row) => [
            $row['name'] ?? '',
            $row['budget'] ?? 0,
            $row['actual'] ?? 0,
        ], $data['accountData'] ?? []));

        $yearlyComparisonSheet = $this->addTabularSheet($spreadsheet, 'Yearly Comparison', [
            'Year', 'Budget', 'Actual',
        ], array_map(fn ($row) => [
            $row['year'] ?? '',
            $row['budget'] ?? 0,
            $row['actual'] ?? 0,
        ], $data['yearlyComparison'] ?? []));

        $varianceTrendSheet = $this->addTabularSheet($spreadsheet, 'Variance Trend', [
            'Month', 'Variance', 'Percentage',
        ], array_map(fn ($row) => [
            $row['month'] ?? '',
            $row['variance'] ?? 0,
            $row['percentage'] ?? 0,
        ], $data['varianceData'] ?? []));

        $departmentsSheet = $this->addTabularSheet($spreadsheet, 'Departments', [
            'Cost Centre', 'Budget', 'Actual',
        ], array_map(fn ($row) => [
            $row['name'] ?? '',
            $row['budget'] ?? 0,
            $row['actual'] ?? 0,
        ], $data['departmentComparison'] ?? []));

        $quarterlySheet = $this->addTabularSheet($spreadsheet, 'Quarterly', [
            'Quarter', 'Budget', 'Actual',
        ], array_map(fn ($row) => [
            $row['quarter'] ?? '',
            $row['budget'] ?? 0,
            $row['actual'] ?? 0,
        ], $data['quarterlyData'] ?? []));

        $this->addTabularSheet($spreadsheet, 'Top Accounts', [
            'Account', 'Actual',
        ], array_map(fn ($row) => [
            $row['name'] ?? '',
            $row['actual'] ?? 0,
        ], $data['topAccounts'] ?? []));

        $this->addKeyValueSheet($spreadsheet, 'Forecast', [
            ['Metric', 'Value'],
            ['Average Monthly Spend', $data['forecastData']['avgMonthlySpend'] ?? 0],
            ['Remaining Budget', $data['forecastData']['remainingBudget'] ?? 0],
            ['Projected Spend', $data['forecastData']['projectedSpend'] ?? 0],
            ['Projected Variance', $data['forecastData']['projectedVariance'] ?? 0],
        ]);

        $this->addKeyValueSheet($spreadsheet, 'Budget Prediction', [
            ['Metric', 'Value'],
            ['Current Budget', $data['budgetPrediction']['currentBudget'] ?? 0],
            ['Current Actual', $data['budgetPrediction']['currentActual'] ?? 0],
            ['Predicted Actual', $data['budgetPrediction']['predictedActual'] ?? 0],
            ['Current Variance', $data['budgetPrediction']['currentVariance'] ?? 0],
            ['Predicted Variance', $data['budgetPrediction']['predictedVariance'] ?? 0],
            ['Accuracy Score', $data['budgetPrediction']['accuracyScore'] ?? 0],
        ]);

        $this->addTabularSheet($spreadsheet, 'Prediction History', [
            'Year', 'Budget', 'Actual', 'Accuracy',
        ], array_map(fn ($row) => [
            $row['year'] ?? '',
            $row['budget'] ?? 0,
            $row['actual'] ?? 0,
            $row['accuracy'] ?? 0,
        ], $data['budgetPrediction']['historicalAccuracy'] ?? []));

        $this->addKeyValueSheet($spreadsheet, 'Velocity', [
            ['Metric', 'Value'],
            ['Average Daily Velocity', $data['spendingVelocity']['avgDailyVelocity'] ?? 0],
            ['Days Analyzed', $data['spendingVelocity']['totalDaysAnalyzed'] ?? 0],
            ['Projected Year End', $data['spendingVelocity']['projectedYearEnd'] ?? 0],
            ['Acceleration %', $data['spendingVelocity']['acceleration'] ?? 0],
        ]);

        $this->addTabularSheet($spreadsheet, 'Utilization', [
            'Account', 'Budget', 'Actual', 'Utilization Rate', 'Status',
        ], array_map(fn ($row) => [
            $row['account'] ?? '',
            $row['budget'] ?? 0,
            $row['actual'] ?? 0,
            $row['utilizationRate'] ?? 0,
            $row['status'] ?? '',
        ], $data['budgetUtilization'] ?? []));

        $seasonalRows = [];
        foreach (($data['seasonalTrends']['seasonalData'] ?? []) as $index => $season) {
            $indices = $data['seasonalTrends']['seasonalIndices'][$index] ?? [];
            $seasonalRows[] = [
                $season['quarter'] ?? '',
                $season['amount'] ?? 0,
                $indices['currentIndex'] ?? 0,
                $indices['previousIndex'] ?? 0,
                $indices['change'] ?? 0,
            ];
        }
        $this->addTabularSheet($spreadsheet, 'Seasonality', [
            'Quarter', 'Current Amount', 'Current Index', 'Previous Index', 'Change',
        ], $seasonalRows);

        $this->addTabularSheet($spreadsheet, 'MoM Growth', [
            'Month', 'Current', 'Previous', 'Growth', 'Status',
        ], array_map(fn ($row) => [
            $row['month'] ?? '',
            $row['current'] ?? 0,
            $row['previous'] ?? 0,
            $row['growth'] ?? 0,
            $row['status'] ?? '',
        ], $data['momGrowth'] ?? []));

        $categoryRows = [];
        foreach (($data['categoryBreakdown'] ?? []) as $category) {
            $categoryRows[] = [
                $category['category'] ?? '',
                $category['total'] ?? 0,
                $category['percentage'] ?? 0,
                collect($category['accounts'] ?? [])->pluck('account')->implode(', '),
            ];
        }
        $this->addTabularSheet($spreadsheet, 'Categories', [
            'Category', 'Total', 'Percentage', 'Accounts',
        ], $categoryRows);

        $this->addTabularSheet($spreadsheet, 'Rolling Averages', [
            'Month', 'Actual', '3-Month Avg', '6-Month Avg',
        ], collect(range(0, count($data['rollingAverages']['monthlyData'] ?? []) - 1))->map(function ($index) use ($data) {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            return [
                $months[$index] ?? 'N/A',
                $data['rollingAverages']['monthlyData'][$index] ?? 0,
                $data['rollingAverages']['rolling3Month'][$index] ?? 0,
                $data['rollingAverages']['rolling6Month'][$index] ?? 0,
            ];
        })->all());

        $this->addTabularSheet($spreadsheet, 'Cumulative Spend', [
            'Month', 'Monthly Spend', 'Cumulative Spend', 'Cumulative Budget', 'Variance', 'Efficiency',
        ], array_map(fn ($row) => [
            $row['month'] ?? '',
            $row['monthlySpend'] ?? 0,
            $row['cumulativeSpend'] ?? 0,
            $row['cumulativeBudget'] ?? 0,
            $row['variance'] ?? 0,
            $row['efficiency'] ?? 0,
        ], $data['cumulativeSpending'] ?? []));

        $this->addTabularSheet($spreadsheet, 'Risk Analysis', [
            'Type', 'Account', 'Utilization', 'Remaining Budget', 'Available Monthly', 'Risk Level', 'Description', 'Change',
        ], array_map(fn ($row) => [
            $row['type'] ?? '',
            $row['account'] ?? '',
            $row['utilization'] ?? 0,
            $row['remainingBudget'] ?? 0,
            $row['availableMonthly'] ?? 0,
            $row['riskLevel'] ?? '',
            $row['description'] ?? '',
            $row['change'] ?? 0,
        ], $data['riskAnalysis'] ?? []));

        $this->addKeyValueSheet($spreadsheet, 'Anomaly Stats', [
            ['Metric', 'Value'],
            ['Mean', $data['anomalyDetection']['stats']['mean'] ?? 0],
            ['Std Dev', $data['anomalyDetection']['stats']['stdDev'] ?? 0],
            ['Threshold', $data['anomalyDetection']['stats']['threshold'] ?? 0],
        ]);

        $this->addTabularSheet($spreadsheet, 'Monthly Anomalies', [
            'Month', 'Amount', 'Z-Score', 'Deviation', 'Type', 'Severity',
        ], array_map(fn ($row) => [
            $row['month'] ?? '',
            $row['amount'] ?? 0,
            $row['zScore'] ?? 0,
            $row['deviation'] ?? 0,
            $row['type'] ?? '',
            $row['severity'] ?? '',
        ], $data['anomalyDetection']['monthlyAnomalies'] ?? []));

        $this->addTabularSheet($spreadsheet, 'Account Anomalies', [
            'Account', 'Month', 'Amount', 'Z-Score', 'Type', 'Severity',
        ], array_map(fn ($row) => [
            $row['account'] ?? '',
            $row['month'] ?? '',
            $row['amount'] ?? 0,
            $row['zScore'] ?? 0,
            $row['type'] ?? '',
            $row['severity'] ?? '',
        ], $data['anomalyDetection']['accountAnomalies'] ?? []));

        $this->addLineChart($monthlyTrendSheet, 'Monthly Budget vs Actual', 'Month', 'Amount', ['Budget', 'Actual']);
        $this->addColumnChart($yearlyComparisonSheet, 'Yearly Comparison', 'Year', 'Amount', ['Budget', 'Actual']);
        $this->addColumnChart($varianceTrendSheet, 'Monthly Variance', 'Month', 'Variance', ['Variance']);
        $this->addColumnChart($departmentsSheet, 'Department Comparison', 'Cost Centre', 'Amount', ['Budget', 'Actual']);
        $this->addColumnChart($quarterlySheet, 'Quarterly Budget vs Actual', 'Quarter', 'Amount', ['Budget', 'Actual']);
        $this->addPieChart($accountDistributionSheet, 'Actual Spend by Account', 'Account', 'Actual');

        $this->addLineChart(
            $monthlyTrendSheet,
            'Monthly Budget vs Actual',
            'Month',
            'Amount',
            ['Budget', 'Actual'],
            $overviewSheet,
            'A16',
            'H31'
        );
        $this->addColumnChart(
            $varianceTrendSheet,
            'Monthly Variance',
            'Month',
            'Variance',
            ['Variance'],
            $overviewSheet,
            'J16',
            'Q31'
        );
        $this->addColumnChart(
            $departmentsSheet,
            'Department Comparison',
            'Cost Centre',
            'Amount',
            ['Budget', 'Actual'],
            $overviewSheet,
            'A34',
            'H49'
        );
        $this->addPieChart(
            $accountDistributionSheet,
            'Actual Spend by Account',
            'Account',
            'Actual',
            $overviewSheet,
            'J34',
            'Q49'
        );
        $this->addColumnChart(
            $yearlyComparisonSheet,
            'Yearly Comparison',
            'Year',
            'Amount',
            ['Budget', 'Actual'],
            $overviewSheet,
            'A52',
            'H67'
        );
        $this->addColumnChart(
            $quarterlySheet,
            'Quarterly Budget vs Actual',
            'Quarter',
            'Amount',
            ['Budget', 'Actual'],
            $overviewSheet,
            'J52',
            'Q67'
        );

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $filename = sprintf(
            'powerbi-analytics-%s-%s.xlsx',
            str($data['selectedCostCentreName'] ?? 'report')->slug(),
            $data['year']
        );

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function addOverviewSheet(Spreadsheet $spreadsheet, array $data): Worksheet
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Overview');
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        foreach (range(1, 17) as $columnIndex) {
            $sheet->getColumnDimension($this->columnLetter($columnIndex))->setWidth(14);
        }

        $sheet->mergeCells('A1:Q3');
        $sheet->setCellValue('A1', ($data['selectedCostCentreName'] ?? 'Analytics').' Dashboard');
        $sheet->getStyle('A1:Q3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->mergeCells('A4:Q5');
        $sheet->setCellValue(
            'A4',
            sprintf(
                'Year %s | %s | %s view | Generated %s',
                $data['year'],
                $data['quarterLabel'],
                ucfirst((string) $data['viewMode']),
                now()->format('Y-m-d H:i')
            )
        );
        $sheet->getStyle('A4:Q5')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => 'CBD5E1']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $cards = [
            ['A7:D10', 'Total Budget', $data['kpiData']['totalBudget'] ?? 0, '16A34A'],
            ['E7:H10', 'Total Actual', $data['kpiData']['totalActual'] ?? 0, '0F766E'],
            ['I7:L10', 'Variance', $data['kpiData']['variance'] ?? 0, ($data['kpiData']['variance'] ?? 0) >= 0 ? '15803D' : 'B91C1C'],
            ['M7:Q10', 'Burn Rate %', ($data['kpiData']['burnRate'] ?? 0).'%', '1D4ED8'],
        ];

        foreach ($cards as [$range, $label, $value, $color]) {
            [$start, $end] = explode(':', $range);
            $sheet->mergeCells($range);
            $sheet->setCellValue($start, $label."\n".$this->displayValue($value));
            $sheet->getStyle($range)->applyFromArray([
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                ],
            ]);
        }

        $summaryRows = [
            ['Metric', 'Value'],
            ['Cost Centre', $data['selectedCostCentreName'] ?? 'N/A'],
            ['Year', $data['year']],
            ['Quarter', $data['quarterLabel']],
            ['View Mode', ucfirst((string) $data['viewMode'])],
            ['Variance %', $data['kpiData']['variancePercentage'] ?? 0],
            ['YoY Growth %', $data['kpiData']['yoyGrowth'] ?? 0],
            ['Budget To Date', $data['kpiData']['budgetToDate'] ?? 0],
            ['Spent To Date', $data['kpiData']['spentToDate'] ?? 0],
        ];

        foreach ($summaryRows as $rowIndex => $row) {
            $excelRow = $rowIndex + 12;
            $sheet->setCellValue("A{$excelRow}", $row[0]);
            $sheet->setCellValue("B{$excelRow}", $this->normalizeValue($row[1]));
        }

        $sheet->getStyle('A12:B12')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
            ],
        ]);
        $sheet->getStyle('A12:B20')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        $sheet->setCellValue('D12', 'Dashboard Summary');
        $sheet->getStyle('D12:Q12')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
        ]);

        $sheet->setCellValue('A31', 'Operational Performance');
        $sheet->setCellValue('A49', 'Portfolio Mix');
        $sheet->setCellValue('A67', 'Historical Trends');
        $sheet->getStyle('A31:Q31')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
        ]);
        $sheet->getStyle('A49:Q49')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
        ]);
        $sheet->getStyle('A67:Q67')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
        ]);

        return $sheet;
    }

    private function addKeyValueSheet(Spreadsheet $spreadsheet, string $title, array $rows): Worksheet
    {
        return $this->addTabularSheet($spreadsheet, $title, array_shift($rows), $rows);
    }

    private function addTabularSheet(Spreadsheet $spreadsheet, string $title, array $headers, array $rows): Worksheet
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle(substr($title, 0, 31));

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1).'1', $header);
        }

        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $coordinate = Coordinate::stringFromColumnIndex($columnIndex + 1).($rowIndex + 2);
                $sheet->setCellValue($coordinate, $this->normalizeValue($value));
            }
        }

        $lastColumn = $sheet->getHighestColumn();
        $lastRow = max(1, count($rows) + 1);

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");

        foreach (range(1, count($headers)) as $columnIndex) {
            $sheet->getColumnDimensionByColumn($columnIndex)->setAutoSize(true);
        }

        return $sheet;
    }

    private function addLineChart(
        Worksheet $sheet,
        string $title,
        string $xAxisTitle,
        string $yAxisTitle,
        array $seriesHeaders,
        ?Worksheet $targetSheet = null,
        string $topLeftPosition = 'F2',
        string $bottomRightPosition = 'N20'
    ): void
    {
        $this->addChart($sheet, $title, DataSeries::TYPE_LINECHART, $xAxisTitle, $yAxisTitle, $seriesHeaders, null, $targetSheet, $topLeftPosition, $bottomRightPosition);
    }

    private function addColumnChart(
        Worksheet $sheet,
        string $title,
        string $xAxisTitle,
        string $yAxisTitle,
        array $seriesHeaders,
        ?Worksheet $targetSheet = null,
        string $topLeftPosition = 'F2',
        string $bottomRightPosition = 'N20'
    ): void
    {
        $this->addChart($sheet, $title, DataSeries::TYPE_BARCHART, $xAxisTitle, $yAxisTitle, $seriesHeaders, DataSeries::DIRECTION_COL, $targetSheet, $topLeftPosition, $bottomRightPosition);
    }

    private function addPieChart(
        Worksheet $sheet,
        string $title,
        string $labelHeader,
        string $valueHeader,
        ?Worksheet $targetSheet = null,
        string $topLeftPosition = 'E2',
        string $bottomRightPosition = 'M20'
    ): void
    {
        if ($sheet->getHighestDataRow() < 2) {
            return;
        }

        $lastRow = $sheet->getHighestDataRow();
        $sheetName = $this->quotedSheetName($sheet);
        $headerMap = $this->headerMap($sheet);
        $labelColumn = $this->columnLetter($headerMap[$labelHeader] ?? 1);
        $valueColumn = $this->columnLetter($headerMap[$valueHeader] ?? 2);

        $labels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$sheetName}!\${$labelColumn}\$2:\${$labelColumn}\${$lastRow}", null, max(0, $lastRow - 1)),
        ];

        $values = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "{$sheetName}!\${$valueColumn}\$2:\${$valueColumn}\${$lastRow}", null, max(0, $lastRow - 1)),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($values) - 1),
            [],
            $labels,
            $values
        );

        $chart = new Chart(
            str($title)->slug()->toString(),
            new Title($title),
            new Legend(Legend::POSITION_RIGHT, null, false),
            new PlotArea(null, [$series]),
            true,
            0,
            null,
            null
        );

        $chart->setTopLeftPosition($topLeftPosition);
        $chart->setBottomRightPosition($bottomRightPosition);
        ($targetSheet ?? $sheet)->addChart($chart);
    }

    private function addChart(
        Worksheet $sheet,
        string $title,
        string $chartType,
        string $xAxisTitle,
        string $yAxisTitle,
        array $seriesHeaders,
        ?string $direction = null,
        ?Worksheet $targetSheet = null,
        string $topLeftPosition = 'F2',
        string $bottomRightPosition = 'N20'
    ): void {
        if ($sheet->getHighestDataRow() < 2) {
            return;
        }

        $headerMap = $this->headerMap($sheet);
        $categoryColumnIndex = $headerMap[$xAxisTitle] ?? 1;
        $lastRow = $sheet->getHighestDataRow();
        $sheetName = $this->quotedSheetName($sheet);

        $categories = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                "{$sheetName}!$".$this->columnLetter($categoryColumnIndex)."\$2:$".$this->columnLetter($categoryColumnIndex)."\${$lastRow}",
                null,
                max(0, $lastRow - 1)
            ),
        ];

        $seriesValues = [];
        $seriesLabels = [];

        foreach ($seriesHeaders as $seriesIndex => $header) {
            $columnIndex = $headerMap[$header] ?? null;

            if (! $columnIndex) {
                continue;
            }

            $columnLetter = $this->columnLetter($columnIndex);
            $seriesLabels[] = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                "{$sheetName}!$".$columnLetter."\$1",
                null,
                1
            );
            $seriesValues[] = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                "{$sheetName}!$".$columnLetter."\$2:$".$columnLetter."\${$lastRow}",
                null,
                max(0, $lastRow - 1)
            );
        }

        if ($seriesValues === []) {
            return;
        }

        $series = new DataSeries(
            $chartType,
            $direction ? DataSeries::GROUPING_CLUSTERED : null,
            range(0, count($seriesValues) - 1),
            $seriesLabels,
            $categories,
            $seriesValues,
            $direction
        );

        $plotArea = new PlotArea(null, [$series]);
        $chart = new Chart(
            str($title)->slug()->toString(),
            new Title($title),
            new Legend(Legend::POSITION_RIGHT, null, false),
            $plotArea,
            true,
            0,
            new Title($xAxisTitle),
            new Title($yAxisTitle)
        );

        $chart->setTopLeftPosition($topLeftPosition);
        $chart->setBottomRightPosition($bottomRightPosition);
        ($targetSheet ?? $sheet)->addChart($chart);
    }

    private function headerMap(Worksheet $sheet): array
    {
        $map = [];
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        for ($columnIndex = 1; $columnIndex <= $highestColumnIndex; $columnIndex++) {
            $value = (string) $sheet->getCell($this->columnLetter($columnIndex).'1')->getValue();
            if ($value !== '') {
                $map[$value] = $columnIndex;
            }
        }

        return $map;
    }

    private function quotedSheetName(Worksheet $sheet): string
    {
        return "'".str_replace("'", "''", $sheet->getTitle())."'";
    }

    private function columnLetter(int $columnIndex): string
    {
        return Coordinate::stringFromColumnIndex($columnIndex);
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }

    private function displayValue(mixed $value): string
    {
        if (is_numeric($value)) {
            return number_format((float) $value, 2);
        }

        return (string) $value;
    }
}
