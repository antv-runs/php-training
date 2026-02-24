<?php

namespace App\Contracts;

interface CategoryServiceInterface
{
    /**
     * Get all categories
     */
    public function getAllCategories($perPage = 15);

    /**
     * Get category by ID
     */
    public function getCategory($id);

    /**
     * Create a new category
     */
    public function createCategory(array $data);

    /**
     * Update category
     */
    public function updateCategory($category, array $data);

    /**
     * Delete category
     */
    public function deleteCategory($id);

    /**
     * Validate category data
     */
    public function validateCategory(array $data);
}
