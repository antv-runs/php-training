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
     *
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Category list",
     *     tags={"Categories"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $perPage = 15;
        $categories = $this->categoryService->getAllCategories($request, $perPage);
        return CategoryResource::collection($categories)->additional(['message' => 'Categories retrieved successfully']);
    }

    /**
     * Store a newly created category.
     *
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create new category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Áo Nam"),
     *             @OA\Property(property="description", type="string", example="Danh mục áo nam")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
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
     *
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Category detail",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $category = $this->categoryService->getCategory($id);
        return (new CategoryResource($category))->additional(['message' => 'Category retrieved successfully']);
    }

    /**
     * Update a specific category.
     *
     * @OA\Patch(
     *     path="/api/categories/{id}",
     *     summary="Update category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Áo Thun Nam"),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
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
     *
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Soft delete category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Category deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function destroy($id)
    {
        $result = $this->categoryService->deleteCategory($id);
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Get trashed categories.
     *
     * @OA\Get(
     *     path="/api/categories/trashed",
     *     summary="Get soft deleted categories",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Trashed categories retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
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
     *
     * @OA\Patch(
     *     path="/api/categories/{id}/restore",
     *     summary="Restore category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Category restored successfully"),
     *     @OA\Response(response=400, description="Restore failed"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
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
     * Permanently delete a category.
     *
     * @OA\Delete(
     *     path="/api/categories/{id}/force-delete",
     *     summary="Force delete category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Category permanently deleted"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function forceDelete($id)
    {
        $result = $this->categoryService->forceDeleteCategory($id);

        return response()->json(['message' => $result['message']]);
    }
}
