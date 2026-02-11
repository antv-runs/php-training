<?php
require_once "../app/Models/Product.php";

class ProductController {
    public function index() {
        $model = new ProductModel();

        $product = $model->getProduct(1);
        $colors = $model->getColors(1);
        $sizes = $model->getSizes(1);

        require "../views/product/index.php";
    }
}