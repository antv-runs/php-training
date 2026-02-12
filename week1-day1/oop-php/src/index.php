<?php

use App\Container\Container;
use App\Models\Product;
use App\Models\VIPCustomer;
use App\Services\OrderService;

$container = new Container();

$product1 = new Product("Laptop", 1500);
$product2 = new Product("Mouse", 50);

$customer = new VIPCustomer("An", "an@gmail.com");

$orderService = $container->make(OrderService::class);
$orderService->createOrder($customer, [$product1, $product2]);
