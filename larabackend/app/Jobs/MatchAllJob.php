<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MatchAllJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
       
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $orders = Order::where('status',OrderStatus::OPEN)->cursor();

        foreach($orders as $order)
        {
            dispatch_sync( new MatchOrderJob($order->id));
        }
    }
}
