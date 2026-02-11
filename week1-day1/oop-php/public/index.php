<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\VIPCustomer;
use App\Services\OrderService;

require_once "helpers/functions.php";
require_once "interfaces/DiscountInterface.php";


echo "=== E-COMMERCE SYSTEM === \n";

$product1 = new Product("Laptop", 1500);
$product2 = new Product("Mouse", 50);

$customer = new VIPCustomer("An", "an@gmail.com");

$orderService = new OrderService();
$orderService->createOrder($customer, [$product1, $product2]);
