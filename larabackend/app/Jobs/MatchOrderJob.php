<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Events\OrderMatched;
use App\Models\Asset;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchOrderJob implements ShouldQueue
{
    use Queueable;

    protected int $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            // Get the new order and lock it for update to prevent race conditions
            $newOrder = Order::where('id', $this->orderId)
                ->lockForUpdate()
                ->firstOrFail();

            // Skip if order is not open
            if ($newOrder->status !== OrderStatus::OPEN) {
                return;
            }

            $side = $newOrder->side;
            $symbol = $newOrder->symbol;
            $price = $newOrder->price;
            $amount = $newOrder->amount;

            // Find matching order based on rules
            $matchingOrder = null;

            if ($side === 'buy') {
                // New BUY → match with first SELL where sell.price <= buy.price
                $matchingOrder = Order::where('status', OrderStatus::OPEN)
                    ->where('side', 'sell')
                    ->where('symbol', $symbol)
                    ->where('price', '<=', $price)
                    ->where('amount', '=', $amount) // Full match only
                    ->orderBy('created_at', 'asc') // First SELL (oldest first)
                    ->lockForUpdate() // Lock for update to prevent race conditions
                    ->first();
            } else {
                // New SELL → match with first BUY where buy.price >= sell.price
                $matchingOrder = Order::where('status', OrderStatus::OPEN)
                    ->where('side', 'buy')
                    ->where('symbol', $symbol)
                    ->where('price', '>=', $price)
                    ->where('amount', '=', $amount) // Full match only
                    ->orderBy('created_at', 'asc') // First BUY (oldest first)
                    ->lockForUpdate() // Lock for update to prevent race conditions
                    ->first();
            }

            // If no match found, return
            if (!$matchingOrder) {
                return;
            }

            // Double-check both orders are still open (race condition protection)
            $newOrder->refresh();
            $matchingOrder->refresh();

            if ($newOrder->status !== OrderStatus::OPEN || $matchingOrder->status !== OrderStatus::OPEN) {
                return;
            }

            // Execute the trade
            $this->executeTrade($newOrder, $matchingOrder);
        });
    }

    /**
     * Execute the trade between two orders.
     */
    protected function executeTrade(Order $buyOrder, Order $sellOrder): void
    {
        // Ensure we have the correct order types
        if ($buyOrder->side !== 'buy') {
            [$buyOrder, $sellOrder] = [$sellOrder, $buyOrder];
        }

        $symbol = $buyOrder->symbol;
        $amount = $buyOrder->amount;
        $tradePrice = $sellOrder->price; // Use seller's price

        $buyer = $buyOrder->user;
        $seller = $sellOrder->user;

        // Calculate trade value (what seller receives)
        $tradeValue = $tradePrice * $amount;
        
        // Calculate commission: 1.5% of matched USD value (deducted from buyer only)
        $commission = $tradeValue * 0.015;
        
        // Calculate total amount buyer should pay (trade value + commission)
        $buyerTotalPayment = $tradeValue + $commission;
        
        // Calculate what buyer originally locked (includes commission: price * amount * 1.015)
        // This is the amount that was deducted from buyer's balance when order was created
        $buyerLocked = $buyOrder->price * $amount * 1.015;
        
        // Calculate net adjustment to buyer's balance
        // Since buyer locked the maximum (their price + commission), we refund the difference
        // if the trade executes at a lower price or with lower commission
        $buyerBalanceAdjustment = $buyerLocked - $buyerTotalPayment;

        // Transfer assets to buyer
        $buyerAsset = Asset::firstOrCreate(
            [
                'user_id' => $buyer->id,
                'symbol' => $symbol,
            ],
            [
                'amount' => 0,
                'locked_amount' => 0,
            ]
        );
        $buyerAsset->amount += $amount;
        $buyerAsset->save();

        // Transfer USD to seller (seller receives full trade value, no commission deduction)
        $seller->balance += $tradeValue;
        $seller->save();

        // Adjust buyer's balance (refund if overpaid, or deduct additional if underpaid)
        // Note: buyer already paid when order was created, so we adjust here
        if ($buyerBalanceAdjustment != 0) {
            $buyer->balance += $buyerBalanceAdjustment;
            $buyer->save();
        }

        // Release locked assets from seller
        $sellerAsset = Asset::where('user_id', $seller->id)
            ->where('symbol', $symbol)
            ->firstOrFail();

        if ($sellerAsset->locked_amount < $amount) {
            throw new \Exception('Locked amount mismatch. This should not happen.');
        }

        $sellerAsset->locked_amount -= $amount;
        $sellerAsset->save();

        // Mark both orders as filled
        $buyOrder->status = OrderStatus::FILLED;
        $buyOrder->save();

        $sellOrder->status = OrderStatus::FILLED;
        $sellOrder->save();

        // Create trade record with commission
        DB::table('trades')->insert([
            'buyer_order_id' => $buyOrder->id,
            'seller_order_id' => $sellOrder->id,
            'buyer_fee' => $commission, // Commission deducted from buyer only
            'seller_fee' => 0.00, // No commission for seller
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Trade executed', [
            'buy_order_id' => $buyOrder->id,
            'sell_order_id' => $sellOrder->id,
            'symbol' => $symbol,
            'amount' => $amount,
            'price' => $tradePrice,
            'trade_value' => $tradeValue,
            'commission' => $commission,
            'buyer_total_payment' => $buyerTotalPayment,
        ]);

        // Broadcast OrderMatched event to both parties via Pusher
        // Refresh orders to get latest status
        $buyOrder->refresh();
        $sellOrder->refresh();
        
        event(new OrderMatched(
            $buyOrder,
            $sellOrder,
            $symbol,
            $amount,
            $tradePrice,
            $tradeValue,
            $commission
        ));
    }
}
