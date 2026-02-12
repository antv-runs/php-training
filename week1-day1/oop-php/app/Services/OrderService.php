<?php

namespace App\Services;

use App\Contracts\DiscountInterface;
use App\Models\Customer;

class OrderService
{
    private DiscountService $discountService;
    private InvoiceService $invoiceService;

    public function __construct()
    {
        $this->discountService = new DiscountService();
        $this->invoiceService = new InvoiceService();
    }

    public function createOrder(Customer $customer, array $products) 
    {
        $total = 0;

        foreach ($products as $product) {
            $total += $product->getPrice();
        }

        $discount = $this->discountService->calculate($total, $customer);
        $final = $total - $discount;

        $this->invoiceService->print(
            $customer->getName(),
            $total,
            $discount,
            $final
        );
    }
}