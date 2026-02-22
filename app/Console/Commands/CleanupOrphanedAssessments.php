<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupOrphanedAssessments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-orphaned-assessments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup guest assessments that are older than 24 hours and haven\'t been claimed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoff = now()->subHours(24);
        
        $deletedCount = \App\Models\Assessment::whereNull('user_id')
            ->where('created_at', '<', $cutoff)
            ->delete();
            
        $this->info("Successfully deleted {$deletedCount} orphaned guest assessments.");
    }
}
