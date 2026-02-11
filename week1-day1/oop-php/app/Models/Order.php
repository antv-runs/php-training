<?php

namespace App\Models;

class Order
{
    private $customer;
    private $products = [];

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function addProduct(Product $product) 
    {
        $this->products[] = $product;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function getCustomer()
    {
        return $this->customer;
    }
}