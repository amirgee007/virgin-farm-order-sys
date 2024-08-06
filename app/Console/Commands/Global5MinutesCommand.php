<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Global5MinutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:global5-minutes-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute all auto functions here....!!!';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Job running every 5 minutes plz double check and then make code here.');
    }
}
