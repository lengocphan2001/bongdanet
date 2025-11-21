<?php

namespace App\Console\Commands;

use App\Jobs\FetchMatchesDataJob;
use Illuminate\Console\Command;

class WarmMatchesCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:warm-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm the cache by pre-fetching matches data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching FetchMatchesDataJob to warm cache...');
        
        FetchMatchesDataJob::dispatch();
        
        $this->info('Job dispatched successfully!');
        
        return Command::SUCCESS;
    }
}

