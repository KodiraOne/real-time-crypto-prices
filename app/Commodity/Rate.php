<?php

namespace App\Commodity;

class Rate
{
    public function __construct(
        public readonly float $rate,
        public readonly float $change
    ){}
}