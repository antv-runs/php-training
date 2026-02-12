<?php

class Router
{
    public static function dispatch()
    {
        $url = $_GET['url'] ?? 'products';

        if ($url === 'products') {
            require_once __DIR__ . '/../Controllers/ProductController.php';
            $controller = new ProductController();
            $controller->index();
        } else {
            echo "404 Not Found";
        }
    }
}