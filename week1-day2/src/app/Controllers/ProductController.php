<?php

require_once __DIR__ . '/../Models/ProductModel.php';

class ProductController 
{
    public function index()
    {
        $model = new ProductModel();
        $products = $model->getAll();

        require __DIR__ . '/../../views/products/index.php';
    }
}