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
        // Only run the job between 00:00 and 01:00 hours
        if (now()->hour >= 0 && now()->hour < 1) {
            ProductQuantity::query()
                ->where('date_out', '<', now()->subDay()->format('Y-m-d'))
                ->delete();
        }

        // Time check every 10 minutes within the 00:00 - 01:00 window
        $productQuantities = ProductQuantity::query()
            ->where('date_out', '=', now()->format('Y-m-d'))
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '<=', now()->format('H:i:s'));
            })
            ->get();

        // Iterate through the product quantities to check and remove associated cart records
        foreach ($productQuantities as $productQuantity) {
            Cart::query()->where('product_qty_id', $productQuantity->id)->delete();
            $productQuantity->delete();
        }


        $this->emptyCartIf1HourPassed();

        Log::info('Job running every 10 minutes to update supplier ID and delete old records. 8 november 2024');
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

        #Log::info('Deleted cart items for users whose first cart item was created more than an hour ago.');
    }
}
