<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Enums\Symbol;
use App\Interfaces\IOrderRepository;
use App\Jobs\MatchOrderJob;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRepository implements IOrderRepository
{
   
    public function getOpenOrdersBySymbol(?string $symbol): Collection
    {
        $query = Order::with('user')
        ->whereNot('user_id',Auth::id())
        ->where('status',OrderStatus::OPEN);

        if ($symbol) {
            $query->where('symbol', strtoupper($symbol));
        }

        return $query->orderBy('created_at', 'desc')->get();
    }


    public function getMyOrders(): Collection
    {
        $query = Order::where('user_id',Auth::id());
        return $query->orderBy('created_at', 'desc')->get();
    }

   
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail(Auth::id());
            $side = $data['side'];
            $symbol = strtoupper($data['symbol']);
            $price = $data['price'];
            $amount = $data['amount'];

            if ($side === 'buy') {
                // Calculate base order value
                $orderValue = $price * $amount;
                
                // Calculate maximum possible commission (1.5% of order value)
                // We use the buyer's price as worst case since trades execute at seller's price (which is <= buyer's price)
                $maxCommission = $orderValue * 0.015;
                
                // Lock total amount: order value + commission
                $requiredUsd = $orderValue + $maxCommission;
                
                if ($user->balance < $requiredUsd) {
                    throw new \Exception('Insufficient USD balance. Required: ' . $requiredUsd . ' (including 1.5% commission), Available: ' . $user->balance);
                }

                // Lock USD by deducting from balance (includes commission)
                $user->balance -= $requiredUsd;
                $user->save();
            } else {
                // For sell orders, lock assets (amount)
                $asset = Asset::where('user_id', $user->id)
                    ->where('symbol', $symbol)
                    ->first();

                if (!$asset) {
                    throw new \Exception('Asset not found for symbol: ' . $symbol);
                }

                $availableAmount = $asset->amount - $asset->locked_amount;
                
                if ($availableAmount < $amount) {
                    throw new \Exception('Insufficient asset balance. Required: ' . $amount . ', Available: ' . $availableAmount);
                }

                // Lock assets by moving from amount to locked_amount
                $asset->amount -= $amount;
                $asset->locked_amount += $amount;
                $asset->save();
            }

            // Create the order
            $order = Order::create([
                'user_id' => Auth::id(),
                'symbol' => $symbol,
                'side' => $side,
                'price' => $price,
                'amount' => $amount,
                'status' => OrderStatus::OPEN,
            ]);

            // Dispatch matching job after transaction commits
            DB::afterCommit(function () use ($order) {
                MatchOrderJob::dispatchSync($order->id);
            });

            return $order;
        });
    }

    
    public function cancelOrder(int $id): Order
    {
        return DB::transaction(function () use ($id) {
            $order = Order::findOrFail($id);

            if ($order->status !== OrderStatus::OPEN) {
                throw new \Exception('Order is not open and cannot be cancelled.');
            }

            $user = $order->user;
            $side = $order->side;
            $symbol = $order->symbol;
            $price = $order->price;
            $amount = $order->amount;

            if ($side === 'buy') {
                // For buy orders, unlock USD (price * amount * 1.015 including commission) back to balance
                // This matches what was locked when the order was created
                $unlockUsd = $price * $amount * 1.015;
                $user->balance += $unlockUsd;
                $user->save();
            } else {
                // For sell orders, unlock assets (amount) from locked_amount back to amount
                $asset = Asset::where('user_id', $user->id)
                    ->where('symbol', $symbol)
                    ->first();

                if (!$asset) {
                    throw new \Exception('Asset not found for symbol: ' . $symbol);
                }

                if ($asset->locked_amount < $amount) {
                    throw new \Exception('Locked amount is less than order amount. This should not happen.');
                }

                // Unlock assets by moving from locked_amount back to amount
                $asset->locked_amount -= $amount;
                $asset->amount += $amount;
                $asset->save();
            }

            // Update order status to cancelled
            $order->status = OrderStatus::CANCELLED;
            $order->save();

            return $order;
        });
    }

    public function getSymbols() : array {

        return Symbol::toArray();
       
    }
}

