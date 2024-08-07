<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Vanguard\Models\ProductQuantity;

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
        ProductQuantity::query()->whereDate('date_out', '<', now()->toDateString())->delete();

        Log::info('Job running every 5 minutes plz double check and then make code here.');
    }
}
