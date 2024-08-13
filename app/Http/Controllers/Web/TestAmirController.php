<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\VirginFarmGlobalMail;
use Vanguard\Mail\VirginFarmsSystemMail;
use Vanguard\Models\Box;
use Vanguard\Models\OrderItem;
use Vanguard\Models\Product;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UnitOfMeasure;
use Vanguard\User;

class TestAmirController extends Controller
{

    private $boxes = [
        ["BOXES" => "MEDIUM", "MIN_CUBE" => 18, "MAX_CUBE" => 21],
        ["BOXES" => "MEDIUM L", "MIN_CUBE" => 22, "MAX_CUBE" => 25],
        ["BOXES" => "LARGE", "MIN_CUBE" => 27, "MAX_CUBE" => 30],
        ["BOXES" => "SUPER", "MIN_CUBE" => 40, "MAX_CUBE" => 45],
        ["BOXES" => "2 MED L", "MIN_CUBE" => 49, "MAX_CUBE" => 52],
        ["BOXES" => "1 MED L + 1 LARGE", "MIN_CUBE" => 53, "MAX_CUBE" => 56],
        ["BOXES" => "2 LARGE", "MIN_CUBE" => 58, "MAX_CUBE" => 60],
        ["BOXES" => "1 SUPER + 1 MED L", "MIN_CUBE" => 68, "MAX_CUBE" => 70],
        ["BOXES" => "1 SUPER + 1 LARGE", "MIN_CUBE" => 72, "MAX_CUBE" => 75],
        ["BOXES" => "2 SUPER", "MIN_CUBE" => 80, "MAX_CUBE" => 90]
    ];

    public function findBoxes($size)
    {
        list($boxes, $nextSize, $percentage) = $this->findBoxCombination($size, $this->boxes);

        return response()->json([
            'size' => $size,
            'boxes' => $boxes,
            'next_size' => $nextSize,
            'percentage' => $percentage
        ]);
    }

    private function findBoxCombination($size, $boxes)
    {
        // Sort boxes by MIN_CUBE in descending order to prioritize larger boxes
        usort($boxes, function($a, $b) {
            return $b['MIN_CUBE'] <=> $a['MIN_CUBE'];
        });

        $result = [];
        $remainingSize = $size;

        // Try to find the largest combination of boxes
        while ($remainingSize > 0) {
            $found = false;

            // Check for pairs of the largest boxes first to prevent using smaller boxes unnecessarily
            foreach ($boxes as $box) {
                while ($remainingSize >= $box['MIN_CUBE'] * 2) {
                    $result[] = $box['BOXES'];
                    $result[] = $box['BOXES'];
                    $remainingSize -= $box['MIN_CUBE'] * 2;
                    $found = true;
                }
                if ($found) {
                    break;
                }
            }

            if ($found) {
                continue;
            }

            // Check for the largest single box that can fit
            foreach ($boxes as $box) {
                if ($remainingSize >= $box['MIN_CUBE']) {
                    $result[] = $box['BOXES'];
                    $remainingSize -= $box['MIN_CUBE'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // If no suitable box found, break to avoid infinite loop
                break;
            }
        }

        $nextSize = $this->calculateNextSize($remainingSize, $size);
        $percentage = $this->calculatePercentage($size, $nextSize);

        return [$result, $nextSize, $percentage];
    }

    private function calculateNextSize($remainingSize, $size)
    {
        // Find the next minimum size required to reach a valid box size
        foreach ($this->boxes as $box) {
            if ($remainingSize < $box['MIN_CUBE']) {
                return $box['MIN_CUBE'] - $remainingSize;
            }
        }

        // If the size is larger than the largest box, suggest adding more to reach the largest box size
        foreach ($this->boxes as $box) {
            if ($size < $box['MIN_CUBE']) {
                return $box['MIN_CUBE'] - $size;
            }
        }

        return null;
    }

    private function calculatePercentage($currentSize, $nextSize)
    {
        if ($nextSize == 0) {
            return 100;
        }

        $totalSize = $currentSize + $nextSize;
        $percentage = ($currentSize / $totalSize) * 100;

        return round($percentage, 2);
    }

    public function index3(){

        auth()->loginUsingId(1);

        return view('test');
    }

    public function index2(Request $request){

        ini_set('max_execution_time', 300000); //300 seconds = 5 minutes
        ini_set('max_memory_limit', -1); //300 seconds = 5 minutes
        ini_set('memory_limit', '4096M');

        return $this->findBoxes($request->number);
        #current size before method callings is: 111.41
        # cart:931 current size and next max limit is: -66.41 18

        $request = Request::create('/', 'GET', ['selection' => 90.73+18 ]);

        $ok = (new CartController())->validateCartSelection($request);

        dd($ok);
        $user = User::first();
        $content = '<p>New user has been successfully registered on Virgin farms order system. Here are the details of the new user:</p>'
            . '<ul>'
            . '<li><strong>Full Name:</strong> ' . $user->first_name.' '.$user->last_name . '</li>'
            . '<li><strong>Last Name:</strong> ' . $user->last_name . '</li>'
            . '<li><strong>Company Name:</strong> ' . $user->company_name . '</li>'
            . '<li><strong>Phone No:</strong> ' . $user->phone . '</li>'
            . '<li><strong>Email:</strong> ' . $user->email . '</li>'
            . '<li><strong>Username:</strong> ' . $user->username . '</li>'
            . '<li><strong>Sales Representative:</strong> ' . $user->sales_rep . '</li>'
            . '<li><strong>Shipping Address:</strong> ' . $user->address . '</li>'
            . '<li><strong>Appt/Suite:</strong> ' . $user->apt_suit . '</li>'
            . '<li><strong>City:</strong> ' . $user->city . '</li>'
            . '<li><strong>State:</strong> ' . $user->state . '</li>'
            . '<li><strong>Zip:</strong> ' . $user->zip . '</li>'
            . '<li><strong>Shipping Method:</strong> ' . $user->ship_method . '</li>'
            . '</ul>';

        \Mail::to('amirseersol@gmail.com')
            ->cc('amirseersol@gmail.com')
            ->send(new VirginFarmGlobalMail('New User Registration Notification', $content));

        dd('ddd');

        $user_id = 9;
        $order_id = rand();
        $message = 'abccccccccccccccc adfsa fasdfareacasd';

        addNotification($user_id , $order_id , $message);
        dd($quantity);

        $user = User::first();
        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

        $content = 'User Changed his shipping address under profile page please check asap i.e user is '.$user->first_name;

        \Mail::raw($content, function ($message) use($salesRepEmail) {
            $message->to('christinah@virginfarms.com')
                ->bcc(['amirseersol@gmail.com' , $salesRepEmail])
                ->subject('Hi, plz check some user updated shipping address');});

//        \Mail::to('christinah@virginfarms.com')
//            ->bcc('amirseersol@gmail.com')
//            ->send(new VirginFarmGlobalMail('Hi, plz check some user updated shipping address', $content));

        dd('dd');

        dd($ranges);

        foreach ($ranges as $range) {
            #not in current ranges
            if ($currentSelection >= $range['min'] && $currentSelection <= $range['max']) {
                $max = $range['max'] + 1;
            }
        }

        return response()->json([
            'valid' => true,
            'nextMax' => $max
        ]);

        $ranges = Box::pluck('max_value', 'min_value')->toArray();

        $adjustedRanges = [];
        $startLimit = 1;

        foreach ($ranges as $min => $max) {

            if ($startLimit > $min - 1) {
                $startLimit = $max + 1;
                continue;
            }

            $adjustedRanges[] = [ 'min' => $startLimit, 'max' => $min - 1 ]; // Add the adjusted range to the array
            $startLimit = $max + 1;
        }
    }
}
