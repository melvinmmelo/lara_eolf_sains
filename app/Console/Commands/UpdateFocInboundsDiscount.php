<?php

namespace App\Console\Commands;

use App\Models\Inbound;
use Illuminate\Console\Command;

class UpdateFocInboundsDiscount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inbounds:update-foc-discount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update discount and discount_details for all FOC (is_foc = 1) inbounds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update FOC inbounds...');

        // Get all inbounds where is_foc = 1 and discount_details is empty or null
        $focInbounds = Inbound::where('is_foc', 1)
            ->where(function ($query) {
                $query->whereNull('discount_details')
                      ->orWhere('discount_details', '');
            })
            ->get();

        if ($focInbounds->isEmpty()) {
            $this->warn('No FOC inbounds found.');
            return 0;
        }

        $this->info("Found {$focInbounds->count()} FOC inbounds to update.");

        $progressBar = $this->output->createProgressBar($focInbounds->count());
        $progressBar->start();

        $updated = 0;

        foreach ($focInbounds as $inbound) {
            // Get the grand total (this calculates the total with service fee)
            $grandTotal = $inbound->grand_total;

            // Update discount to grand total and discount_details to 100%
            $inbound->update([
                'discount' => $grandTotal,
                'discount_details' => '100%'
            ]);

            // Log the activity
            activity()
                ->performedOn($inbound)
                ->withProperties([
                    'order_code' => $inbound->code,
                    'branch_code' => $inbound->branch_code,
                    'discount' => $grandTotal,
                    'discount_details' => '100%'
                ])
                ->log("FOC inbound discount updated via command");

            $updated++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("Successfully updated {$updated} FOC inbounds.");

        return 0;
    }
}
