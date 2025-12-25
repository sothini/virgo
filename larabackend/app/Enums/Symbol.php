<?php

namespace App\Enums;

enum Symbol: string
{
    case BTC = 'BTC';
    case ETH = 'ETH';
    case SOL = 'SOL';
    case ADA = 'ADA';
    case DOT = 'DOT';

    /**
     * Get all symbol values as an array
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all symbols as an array of associative arrays with name and value
     *
     * @return array<array{name: string, value: string}>
     */
    public static function toArray(): array
    {
        return array_map(
            fn(self $case) => ['name' => $case->name, 'value' => $case->value],
            self::cases()
        );
    }
}

