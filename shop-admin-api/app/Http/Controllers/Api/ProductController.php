<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\ExportProductRequest;
use App\Http\Resources\ProductResource;
use App\Contracts\ProductServiceInterface;
use App\Contracts\FileUploadServiceInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseController
{
    private $productService;
    private $fileUploadService;

    public function __construct(ProductServiceInterface $productService, FileUploadServiceInterface $fileUploadService)
    {
        $this->productService = $productService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get all products with pagination.
     * Returns JSON response.
     *
     * @OA\Get(
     *     path="/api/products",
     *     summary="Product list",
     *     tags={"Products"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $products = $this->productService->getAllProducts($request, $perPage);
        return $this->success(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","price"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="price", type="number", format="float"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        // Handle image upload - delegated to FileUploadService
        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUploadService->uploadProductImage($request->file('image'));
        }

        $product = $this->productService->createProduct($data);

        return $this->success(
            new ProductResource($product['data']),
            'Product created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Get a specific product by ID or slug.
     * Returns JSON response.
     *
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Product detail",
     *     tags={"Products"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $product = $this->productService->getProduct($id);
        return $this->success(
            new ProductResource($product),
            'Product retrieved successfully'
        );
    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}",
     *     summary="Update product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="price", type="number"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated successfully")
     * )
     */
    public function update(ProductRequest $request, $id)
    {
        $product = $this->productService->getProduct($id);
        $data = $request->validated();

        // Handle image upload - delegated to FileUploadService
        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUploadService->replaceFile($product->image, $request->file('image'));
        }

        $result = $this->productService->updateProduct($product, $data);

        return $this->success(
            new ProductResource($result['data'] ?? $product),
            'Product updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Soft delete product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $this->productService->deleteProduct($id);

        return $this->success(
            null,
            'Product deleted successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/products/trashed",
     *     summary="Get trashed products",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(response=200, description="Trashed list")
     * )
     */
    public function trashed(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);

        $products = $this->productService->getTrashed($perPage);

        return $this->success(
            $products,
            'Trashed products retrieved successfully'
        );
    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}/restore",
     *     summary="Restore product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product restored")
     * )
     */
    public function restore($id)
    {
        $result = $this->productService->restoreProduct($id);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        return $this->success(
            new ProductResource($result['data']),
            $result['message']
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}/force-delete",
     *     summary="Force delete product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product permanently deleted")
     * )
     */
    public function forceDelete($id)
    {
        $this->productService->forceDeleteProduct($id);
        return $this->success(
            null,
            'Product permanently deleted'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products/export",
     *     summary="Export products to CSV/Excel",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"format"},
     *                 @OA\Property(property="format", type="string", enum={"csv", "excel"}, description="Export format"),
     *                 @OA\Property(property="search", type="string", description="Search term"),
     *                 @OA\Property(property="category_id", type="integer", description="Filter by category"),
     *                 @OA\Property(property="status", type="string", enum={"active", "deleted", "all"}, description="Filter by status")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=202, description="Export job queued"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function export(ExportProductRequest $request)
    {
        $data = $request->validated();

        // Get current authenticated user
        $user = auth()->user();

        // Dispatch export job
        $result = $this->productService->exportProducts(
            $user->id,
            [
                'search' => $data['search'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'status' => $data['status'] ?? 'active',
            ],
            $data['format']
        );

        return response()->json([
            'message' => 'Export job queued. You will receive an email with the download link shortly.',
            'format' => $request->input('format'),
        ], 202);
    }
}
