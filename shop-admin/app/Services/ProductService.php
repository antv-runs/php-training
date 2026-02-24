<?php

namespace App\Services;

use App\Contracts\ProductServiceInterface;
use App\Models\Product;
use App\Models\Category;

class ProductService implements ProductServiceInterface
{
    /**
     * Get all products with category
     */
    public function getAllProducts($perPage = 10)
    {
        return Product::with('category')->latest()->paginate($perPage);
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        return Category::all();
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data)
    {
        return Product::create($data);
    }

    /**
     * Get product by ID
     */
    public function getProduct($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Update product
     */
    public function updateProduct(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    /**
     * Delete product
     */
    public function deleteProduct($id)
    {
        return Product::destroy($id);
    }

    /**
     * Validate product data
     */
    public function validateProduct(array $data, $id = null)
    {
        $rules = [
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'category_id' => 'nullable|exists:categories,id'
        ];

        return validator($data, $rules)->validate();
    }
}
