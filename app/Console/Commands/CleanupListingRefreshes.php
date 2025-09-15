<?php

namespace App\Console\Commands;

use App\Models\ListingRefresh;
use Illuminate\Console\Command;

class CleanupListingRefreshes extends Command
{
    protected $signature = 'listing:cleanup-refreshes {--days=90}';
    
    protected $description = 'Clean up old listing refresh records';
    
    public function handle(): int
    {
        $days = $this->option('days');
        
        $deleted = ListingRefresh::where('refreshed_at', '<', now()->subDays($days))
            ->delete();
        
        $this->info("Deleted {$deleted} old listing refresh records older than {$days} days.");
        
        return Command::SUCCESS;
    }
}