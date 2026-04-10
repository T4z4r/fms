<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Budget;
use App\Models\CostCentre;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;

        $marketing = CostCentre::create(['name' => 'Marketing', 'owner' => 1, 'status' => 'active']);
        $operations = CostCentre::create(['name' => 'Operations', 'owner' => 1, 'status' => 'active']);
        $hr = CostCentre::create(['name' => 'HR', 'owner' => 1, 'status' => 'active']);

        $accounts = [
            ['cost_centre' => $marketing, 'code' => '5001', 'name' => 'Advertising', 'budget' => 60000],
            ['cost_centre' => $marketing, 'code' => '5002', 'name' => 'Events', 'budget' => 30000],
            ['cost_centre' => $marketing, 'code' => '5003', 'name' => 'Software', 'budget' => 12000],
            ['cost_centre' => $operations, 'code' => '6001', 'name' => 'Repairs', 'budget' => 80000],
            ['cost_centre' => $operations, 'code' => '6002', 'name' => 'Utilities', 'budget' => 40000],
            ['cost_centre' => $operations, 'code' => '6003', 'name' => 'Staff Costs', 'budget' => 150000],
            ['cost_centre' => $hr, 'code' => '7001', 'name' => 'Training', 'budget' => 15000],
            ['cost_centre' => $hr, 'code' => '7002', 'name' => 'Recruitment', 'budget' => 20000],
            ['cost_centre' => $hr, 'code' => '7003', 'name' => 'Salaries', 'budget' => 120000],
        ];

        foreach ($accounts as $data) {
            $account = Account::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'cost_centre_id' => $data['cost_centre']->id,
            ]);

            Budget::create([
                'cost_centre_id' => $data['cost_centre']->id,
                'account_id' => $account->id,
                'annual_budget' => $data['budget'],
                'year' => $year,
            ]);
        }
    }
}
