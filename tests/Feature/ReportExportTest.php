<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Actual;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\CostCentre;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_download_a_branded_pdf_report(): void
    {
        $user = User::factory()->create();
        $costCentre = CostCentre::create([
            'name' => 'Operations',
            'owner' => $user->id,
            'status' => 'active',
        ]);
        $account = Account::create([
            'code' => 'OPS-100',
            'name' => 'Operations Spend',
            'cost_centre_id' => $costCentre->id,
        ]);

        Budget::create([
            'cost_centre_id' => $costCentre->id,
            'account_id' => $account->id,
            'annual_budget' => 120000,
            'year' => 2026,
        ]);

        Actual::create([
            'cost_centre_id' => $costCentre->id,
            'account_id' => $account->id,
            'month' => 4,
            'amount' => 45000,
            'year' => 2026,
        ]);

        Setting::set('company_name', 'Northwind Finance');
        Setting::set('company_currency', 'USD');

        $response = $this->actingAs($user)->get(route('reports.export.pdf', ['year' => 2026]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=financial-report-2026.pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_authenticated_user_can_download_powerbi_excel_export(): void
    {
        $user = User::factory()->create();
        $costCentre = CostCentre::create([
            'name' => 'Operations',
            'owner' => $user->id,
            'status' => 'active',
        ]);
        $account = Account::create([
            'code' => 'OPS-100',
            'name' => 'Operations Spend',
            'cost_centre_id' => $costCentre->id,
        ]);

        $budget = Budget::create([
            'cost_centre_id' => $costCentre->id,
            'account_id' => $account->id,
            'annual_budget' => 120000,
            'year' => 2026,
        ]);

        BudgetLine::create([
            'budget_id' => $budget->id,
            'month' => 4,
            'amount' => 10000,
        ]);

        Actual::create([
            'cost_centre_id' => $costCentre->id,
            'account_id' => $account->id,
            'month' => 4,
            'amount' => 8500,
            'year' => 2026,
        ]);

        $response = $this->actingAs($user)->get(route('powerbi.export.excel', [
            'year' => 2026,
            'cost_centre_id' => $costCentre->id,
            'quarter' => 'Q2',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString(
            'attachment; filename=powerbi-analytics-operations-2026.xlsx',
            (string) $response->headers->get('content-disposition')
        );
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }
}
