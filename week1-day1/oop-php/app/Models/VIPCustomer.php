<?php

namespace App\Models;

class VIPCustomer extends Customer
{
    public function getDiscountPercent()
    {
        return 10;
    }
}