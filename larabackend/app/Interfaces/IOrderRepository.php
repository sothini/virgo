<?php

namespace App\Interfaces;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface IOrderRepository
{
    public function getOpenOrdersBySymbol(?string $symbol): Collection;
    public function getMyOrders(): Collection;
    public function createOrder(array $data): Order;
    public function cancelOrder(int $id): Order;
    public function getSymbols(): array;
}

