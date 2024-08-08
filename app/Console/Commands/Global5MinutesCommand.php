<?php

namespace Vanguard\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Vanguard\Models\Cart;
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
        if (now()->hour % 6 == 0) {
            // Delete old records
            ProductQuantity::query()->whereDate('date_out', '<', now()->toDateString())->delete();

            // Update supplier ID
            DB::statement("UPDATE table_products p SET p.supplier_id = 1 WHERE p.supplier_id = 3 AND NOT EXISTS (SELECT 1 FROM table_qty q WHERE q.item_no = p.item_no)");

            Log::info('Job running every 6 hours to update supplier ID and delete old records.');
        }

        $this->emptyCartIf1HourPassed();

        #Cart::where('user_id', auth()->id())->delete();
    }

    public function emptyCartIf1HourPassed()
    {
        // Get all unique user IDs who have items in the cart
        $userIds = Cart::select('user_id')->distinct()->pluck('user_id');

        // Iterate over each user ID
        foreach ($userIds as $userId) {
            // Get the first item in the cart for the current user
            $firstCartItem = Cart::where('user_id', $userId)->orderBy('created_at', 'asc')->first();

            // Check if the first cart item exists and was created more than an hour ago
            if ($firstCartItem && $firstCartItem->created_at->lt(Carbon::now()->subHour())) {
                // Delete all cart items for the current user
                Cart::where('user_id', $userId)->delete();
            }

        }

        Log::info('Deleted cart items for users whose first cart item was created more than an hour ago.');
    }
}
