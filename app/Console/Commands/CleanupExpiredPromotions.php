<?php

namespace App\Console\Commands;

use App\Models\Backend\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanupExpiredPromotions extends Command
{
    protected $signature = 'promotions:cleanup-expired';
    protected $description = 'Marks listings with expired promotions as not featured.';

    public function handle()
    {
        $this->info('Starting cleanup of expired promotions...');

        $updatedCount = Listing::where('is_featured', true)
            ->where('promoted_until', '<=', Carbon::now())
            ->update(['is_featured' => false, 'promoted_until' => null]);

        $this->info("Cleanup complete. {$updatedCount} listings had their promotions expired.");
        return Command::SUCCESS;
    }
}
