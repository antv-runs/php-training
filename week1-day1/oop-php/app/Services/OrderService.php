<?php

namespace App\Services;

use App\Contracts\DiscountInterface;

class OrderService
{
    private DiscountService $discountService;
    private InvoiceService $invoiceService;

    public function __construct(
        DiscountService $discountService,
        InvoiceService $invoiceService
    )
    {
        $this->discountService = $discountService;
        $this->invoiceService = $invoiceService;
    }

    public function createOrder(DiscountInterface $customer, array $products) 
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