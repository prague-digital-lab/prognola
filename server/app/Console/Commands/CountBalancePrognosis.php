<?php

namespace App\Console\Commands;

use App\Jobs\Balance\CountBalance;
use App\Models\Workspace;
use Illuminate\Console\Command;

class CountBalancePrognosis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach (Workspace::all() as $workspace) {
            $this->info("Dispatching counting balance prognosis for workspace $workspace->name.");
            CountBalance::dispatch($workspace);
        }
    }
}
