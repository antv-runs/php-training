<?php

namespace App\Services;

use App\Contracts\CategoryServiceInterface;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService implements CategoryServiceInterface
{
    /**
     * Get all categories
     */
    public function getAllCategories($perPage = 15)
    {
        return Category::latest()->paginate($perPage);
    }

    /**
     * Get category by ID
     */
    public function getCategory($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;

        $query = Category::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $original . '-' . $i++;
            $query = Category::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data)
    {
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        return Category::create($data);
    }

    /**
     * Update category
     */
    public function updateCategory(Category $category, array $data)
    {
        $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
        $category->update($data);
        return $category;
    }

    /**
     * Delete category
     */
    public function deleteCategory($id)
    {
        $category = $this->getCategory($id);
        $category->delete();
        return true;
    }

    /**
     * Validate category data
     */
    public function validateCategory(array $data)
    {
        return validator($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ])->validated();
    }
}
