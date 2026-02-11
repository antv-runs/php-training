<?php

namespace App\Services;

use App\Contracts\DiscountInterface;

class DiscountService
{
    public function calculate(float $total, DiscountInterface $customer): float 
    {
        $percent = $customer->getDiscountPercent();
        return ($total * $percent) / 100;
    }
}