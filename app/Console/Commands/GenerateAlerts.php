<?php

namespace App\Console\Commands;

use App\Http\Controllers\AlertController;
use Illuminate\Console\Command;

class GenerateAlerts extends Command
{
    protected $signature = 'alerts:generate';

    protected $description = 'Generate budget alerts for over-budget items';

    public function handle(): int
    {
        $this->info('Generating budget alerts...');
        AlertController::generateBudgetAlerts();
        $this->info('Alerts generated successfully.');

        return Command::SUCCESS;
    }
}
