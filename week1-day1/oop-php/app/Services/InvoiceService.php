<?php

namespace App\Services;

class InvoiceService 
{
    public function print(string $name, float $total, float $discount, float $final) 
    {
        echo "Customer: $name \n";
        echo "Total: " . formatMoney($total) . "\n";
        echo "Discount: " . formatMoney($discount) . "\n";
        echo "Final: " . formatMoney($final) . "\n";
    }
}