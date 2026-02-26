<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Contracts\CategoryServiceInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    /**
     * Inject CategoryServiceInterface
     */
    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get all categories with pagination.
     * Returns JSON response.
     */
    public function index(Request $request)
    {
        $perPage = 15;
        $categories = $this->categoryService->getAllCategories($request, $perPage);
        return CategoryResource::collection($categories)->additional(['message' => 'Categories retrieved successfully']);
    }

    /**
     * Store a newly created category.
     * Returns JSON response.
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->only(['name', 'description']);
        $result = $this->categoryService->createCategory($data);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $result['data'] ?? null
        ], 201);
    }

    /**
     * Get a specific category by ID.
     * Returns JSON response.
     */
    public function show($id)
    {
        $category = $this->categoryService->getCategory($id);
        return (new CategoryResource($category))->additional(['message' => 'Category retrieved successfully']);
    }

    /**
     * Update a specific category.
     * Returns JSON response.
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = $this->categoryService->getCategory($id);
        $data = $request->only(['name', 'description']);
        $result = $this->categoryService->updateCategory($category, $data);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $result['data'] ?? $category
        ]);
    }

    /**
     * Soft delete a category.
     * Returns JSON response.
     */
    public function destroy($id)
    {
        $result = $this->categoryService->deleteCategory($id);
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Get trashed (soft deleted) categories.
     * Returns JSON response.
     */
    public function trashed(Request $request)
    {
        $categories = $this->categoryService->getTrashed(15);
        return response()->json([
            'message' => 'Trashed categories retrieved successfully',
            'data' => $categories
        ]);
    }

    /**
     * Restore a soft deleted category.
     * Returns JSON response.
     */
    public function restore($id)
    {
        $result = $this->categoryService->restoreCategory($id);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data']
        ]);
    }

    /**
     * Force delete a category permanently.
     * Returns JSON response.
     */
    public function forceDelete($id)
    {
        $result = $this->categoryService->forceDeleteCategory($id);

        return response()->json(['message' => $result['message']]);
    }
}
