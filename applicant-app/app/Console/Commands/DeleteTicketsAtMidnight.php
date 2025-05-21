<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteTicketsAtMidnight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:delete-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all tickets from the queue system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $count = Ticket::count();
            Ticket::truncate();
            Log::info("Successfully deleted all tickets ({$count} tickets) at midnight.");
            $this->info("Successfully deleted {$count} tickets.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error("Failed to delete tickets: " . $e->getMessage());
            $this->error("Failed to delete tickets: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
