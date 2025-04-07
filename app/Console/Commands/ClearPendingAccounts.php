<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearPendingAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:clear-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all pending accounts from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::table('pending_accounts')->truncate();
            $this->info('All pending accounts have been cleared.');
        } catch (\Exception $e) {
            $this->error('Failed to clear pending accounts: ' . $e->getMessage());
        }
    }
}
