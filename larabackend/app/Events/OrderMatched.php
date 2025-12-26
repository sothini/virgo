<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $buyOrder;
    public Order $sellOrder;
    public string $symbol;
    public float $amount;
    public float $price;
    public float $tradeValue;
    public float $commission;
    public int $buyerId;
    public int $sellerId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Order $buyOrder,
        Order $sellOrder,
        string $symbol,
        float $amount,
        float $price,
        float $tradeValue,
        float $commission
    ) {
        $this->buyOrder = $buyOrder;
        $this->sellOrder = $sellOrder;
        $this->symbol = $symbol;
        $this->amount = $amount;
        $this->price = $price;
        $this->tradeValue = $tradeValue;
        $this->commission = $commission;
        $this->buyerId = $buyOrder->user_id;
        $this->sellerId = $sellOrder->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->buyerId),
            new PrivateChannel('user.' . $this->sellerId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.matched';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'buy_order' => [
                'id' => $this->buyOrder->id,
                'symbol' => $this->buyOrder->symbol,
                'side' => $this->buyOrder->side,
                'price' => $this->buyOrder->price,
                'amount' => $this->buyOrder->amount,
                'status' => $this->buyOrder->status->value,
            ],
            'sell_order' => [
                'id' => $this->sellOrder->id,
                'symbol' => $this->sellOrder->symbol,
                'side' => $this->sellOrder->side,
                'price' => $this->sellOrder->price,
                'amount' => $this->sellOrder->amount,
                'status' => $this->sellOrder->status->value,
            ],
            'trade' => [
                'symbol' => $this->symbol,
                'amount' => $this->amount,
                'price' => $this->price,
                'trade_value' => $this->tradeValue,
                'commission' => $this->commission,
            ],
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

