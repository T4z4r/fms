<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Actual;
use App\Models\CostCentre;
use Illuminate\Database\Seeder;

class ActualSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;

        $costCentres = [
            'Marketing' => CostCentre::where('name', 'Marketing')->first(),
            'Operations' => CostCentre::where('name', 'Operations')->first(),
            'HR' => CostCentre::where('name', 'HR')->first(),
        ];

        $accounts = [
            'Marketing' => [
                '5001' => Account::where('code', '5001')->first(),
                '5002' => Account::where('code', '5002')->first(),
                '5003' => Account::where('code', '5003')->first(),
            ],
            'Operations' => [
                '6001' => Account::where('code', '6001')->first(),
                '6002' => Account::where('code', '6002')->first(),
                '6003' => Account::where('code', '6003')->first(),
            ],
            'HR' => [
                '7001' => Account::where('code', '7001')->first(),
                '7002' => Account::where('code', '7002')->first(),
                '7003' => Account::where('code', '7003')->first(),
            ],
        ];

        $actuals = [
            ['month' => 1, 'cost_centre' => 'Marketing', 'code' => '5001', 'amount' => 6140.17],
            ['month' => 1, 'cost_centre' => 'Marketing', 'code' => '5002', 'amount' => 2566.5],
            ['month' => 1, 'cost_centre' => 'Marketing', 'code' => '5003', 'amount' => 1230.14],
            ['month' => 1, 'cost_centre' => 'Operations', 'code' => '6001', 'amount' => 6332.2],
            ['month' => 1, 'cost_centre' => 'Operations', 'code' => '6002', 'amount' => 3753.06],
            ['month' => 1, 'cost_centre' => 'Operations', 'code' => '6003', 'amount' => 12652.76],
            ['month' => 1, 'cost_centre' => 'HR', 'code' => '7001', 'amount' => 1467.41],
            ['month' => 1, 'cost_centre' => 'HR', 'code' => '7002', 'amount' => 1536.51],
            ['month' => 1, 'cost_centre' => 'HR', 'code' => '7003', 'amount' => 11502.53],
            ['month' => 2, 'cost_centre' => 'Marketing', 'code' => '5001', 'amount' => 4317.27],
            ['month' => 2, 'cost_centre' => 'Marketing', 'code' => '5002', 'amount' => 2595.33],
            ['month' => 2, 'cost_centre' => 'Marketing', 'code' => '5003', 'amount' => 808.57],
            ['month' => 2, 'cost_centre' => 'Operations', 'code' => '6001', 'amount' => 7837.31],
            ['month' => 2, 'cost_centre' => 'Operations', 'code' => '6002', 'amount' => 3907.29],
            ['month' => 2, 'cost_centre' => 'Operations', 'code' => '6003', 'amount' => 11550.72],
            ['month' => 2, 'cost_centre' => 'HR', 'code' => '7001', 'amount' => 1440.69],
            ['month' => 2, 'cost_centre' => 'HR', 'code' => '7002', 'amount' => 1928.22],
            ['month' => 2, 'cost_centre' => 'HR', 'code' => '7003', 'amount' => 10018.65],
            ['month' => 3, 'cost_centre' => 'Marketing', 'code' => '5001', 'amount' => 5928.9],
            ['month' => 3, 'cost_centre' => 'Marketing', 'code' => '5002', 'amount' => 2959],
            ['month' => 3, 'cost_centre' => 'Marketing', 'code' => '5003', 'amount' => 1112.81],
            ['month' => 3, 'cost_centre' => 'Operations', 'code' => '6001', 'amount' => 5852.22],
            ['month' => 3, 'cost_centre' => 'Operations', 'code' => '6002', 'amount' => 2827.99],
            ['month' => 3, 'cost_centre' => 'Operations', 'code' => '6003', 'amount' => 13918.96],
            ['month' => 3, 'cost_centre' => 'HR', 'code' => '7001', 'amount' => 1372.26],
            ['month' => 3, 'cost_centre' => 'HR', 'code' => '7002', 'amount' => 1722.14],
            ['month' => 3, 'cost_centre' => 'HR', 'code' => '7003', 'amount' => 10796.3],
            ['month' => 4, 'cost_centre' => 'Marketing', 'code' => '5001', 'amount' => 6291.71],
            ['month' => 4, 'cost_centre' => 'Marketing', 'code' => '5002', 'amount' => 2965],
            ['month' => 4, 'cost_centre' => 'Marketing', 'code' => '5003', 'amount' => 910.12],
            ['month' => 4, 'cost_centre' => 'Operations', 'code' => '6001', 'amount' => 5766.17],
            ['month' => 4, 'cost_centre' => 'Operations', 'code' => '6002', 'amount' => 3200.45],
            ['month' => 4, 'cost_centre' => 'Operations', 'code' => '6003', 'amount' => 15152.98],
            ['month' => 4, 'cost_centre' => 'HR', 'code' => '7001', 'amount' => 1515.79],
            ['month' => 4, 'cost_centre' => 'HR', 'code' => '7002', 'amount' => 1459.5],
            ['month' => 4, 'cost_centre' => 'HR', 'code' => '7003', 'amount' => 11379.93],
            ['month' => 5, 'cost_centre' => 'Marketing', 'code' => '5001', 'amount' => 4824.34],
            ['month' => 5, 'cost_centre' => 'Marketing', 'code' => '5002', 'amount' => 2503.89],
            ['month' => 5, 'cost_centre' => 'Marketing', 'code' => '5003', 'amount' => 1020.41],
            ['month' => 5, 'cost_centre' => 'Operations', 'code' => '6001', 'amount' => 5894.17],
            ['month' => 5, 'cost_centre' => 'Operations', 'code' => '6002', 'amount' => 3879.21],
            ['month' => 5, 'cost_centre' => 'Operations', 'code' => '6003', 'amount' => 13099.82],
            ['month' => 5, 'cost_centre' => 'HR', 'code' => '7001', 'amount' => 1418.68],
            ['month' => 5, 'cost_centre' => 'HR', 'code' => '7002', 'amount' => 1968.85],
            ['month' => 5, 'cost_centre' => 'HR', 'code' => '7003', 'amount' => 10050.51],
            ['month' => 6, 'cost_centre' => 'Marketing', 'code' => '5001', 'amount' => 5990.09],
            ['month' => 6, 'cost_centre' => 'Marketing', 'code' => '5002', 'amount' => 2591.97],
            ['month' => 6, 'cost_centre' => 'Marketing', 'code' => '5003', 'amount' => 1273.08],
            ['month' => 6, 'cost_centre' => 'Operations', 'code' => '6001', 'amount' => 5717.73],
            ['month' => 6, 'cost_centre' => 'Operations', 'code' => '6002', 'amount' => 3818.96],
            ['month' => 6, 'cost_centre' => 'Operations', 'code' => '6003', 'amount' => 10447.36],
            ['month' => 6, 'cost_centre' => 'HR', 'code' => '7001', 'amount' => 1302.73],
            ['month' => 6, 'cost_centre' => 'HR', 'code' => '7002', 'amount' => 1989.73],
            ['month' => 6, 'cost_centre' => 'HR', 'code' => '7003', 'amount' => 8927.78],
        ];

        foreach ($actuals as $data) {
            Actual::create([
                'cost_centre_id' => $costCentres[$data['cost_centre']]->id,
                'account_id' => $accounts[$data['cost_centre']][$data['code']]->id,
                'year' => $year,
                'month' => $data['month'],
                'amount' => $data['amount'],
            ]);
        }
    }
}
