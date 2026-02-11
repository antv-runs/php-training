<?php

namespace App\Models;

use App\Contracts\DiscountInterface;

class Customer extends User implements DiscountInterface
{
    public function getDiscountPercent()
    {
        return 0;
    }
}