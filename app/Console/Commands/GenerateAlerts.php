<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class GenerateAlerts extends Command
{
    protected $signature = 'alerts:generate {--year= : The year to generate alerts for} {--cost-centre= : The cost centre ID to generate alerts for}';

    protected $description = 'Generate financial alerts based on budget vs actuals';

    public function handle(AlertService $alertService): int
    {
        $year = $this->option('year') ?? now()->year;
        $costCentreId = $this->option('cost-centre');

        $this->info("Generating alerts for year {$year}...");

        $alertService->generateAlerts($year, $costCentreId);

        $this->info('Alerts generated successfully.');

        return Command::SUCCESS;
    }
}
