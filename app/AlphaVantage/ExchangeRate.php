<?php

namespace App\AlphaVantage;

class ExchangeRate
{
    public function __construct(
        public readonly int $commodity_id,
        public readonly float $rate,
        public readonly string $datetime
    )
    {}

    public function toArray(): array
    {
        return [
            'commodity_id' => $this->commodity_id,
            'rate' => $this->rate,
            'datetime' => $this->datetime
        ];
    }
}