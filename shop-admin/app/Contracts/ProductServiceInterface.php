<?php

namespace App\Contracts;

interface ProductServiceInterface
{
    /**
     * Get all products with category
     */
    public function getAllProducts($perPage = 10);

    /**
     * Get all categories
     */
    public function getCategories();

    /**
     * Create a new product
     */
    public function createProduct(array $data);

    /**
     * Get product by ID
     */
    public function getProduct($id);

    /**
     * Update product
     */
    public function updateProduct($product, array $data);

    /**
     * Delete product
     */
    public function deleteProduct($id);

    /**
     * Validate product data
     */
    public function validateProduct(array $data, $id = null);
}
