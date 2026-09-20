<?php

namespace App\Console\Commands;

use App\Services\RiskPredictionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('pharmacy:predict-risk {--lead-time=7 : Supplier lead time in days}')]
#[Description('Recalculate dual-risk prediction metrics (Stockout Risk and Expiry Risk) for hospital pharmacy inventory')]
class PredictPharmacyRiskCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(RiskPredictionService $service): int
    {
        $leadTime = (int) $this->option('lead-time');

        $this->info("Recalculating Dual-Risk scores with {$leadTime}-day supplier lead time...");

        $results = $service->recalculateAllRisks($leadTime);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Medicines Processed', $results['medicines_processed']],
                ['Batches Processed', $results['batches_processed']],
                ['High Stockout Risk Medicines', $results['high_stockout_count']],
                ['High Expiry Risk Batches', $results['high_expiry_count']],
            ]
        );

        $this->info('Dual-Risk prediction metrics successfully updated.');

        return Command::SUCCESS;
    }
}
