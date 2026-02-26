<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Contracts\ProductServiceInterface;
use App\Contracts\FileUploadServiceInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var FileUploadServiceInterface
     */
    private $fileUploadService;

    /**
     * Inject dependencies
     *
     * Dependencies: ProductServiceInterface, FileUploadServiceInterface
     * Follows Dependency Inversion - depends on abstractions not concretions
     */
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
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $perPage = 10;
        $products = $this->productService->getAllProducts($request, $perPage);
        return ProductResource::collection($products)->additional(['message' => 'Products retrieved successfully']);
    }

    /**
     * Store a newly created product.
     * Returns JSON response.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        // Handle image upload - delegated to FileUploadService
        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUploadService->uploadProductImage($request->file('image'));
        }

        $result = $this->productService->createProduct($data);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $result['data'] ?? null
        ], 201);
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
        return (new ProductResource($product))->additional(['message' => 'Product retrieved successfully']);
    }

    /**
     * Update a specific product.
     * Returns JSON response.
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

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $result['data'] ?? $product
        ]);
    }

    /**
     * Soft delete a product.
     * Returns JSON response.
     */
    public function destroy($id)
    {
        $result = $this->productService->deleteProduct($id);

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Get trashed (soft deleted) products.
     * Returns JSON response.
     */
    public function trashed(Request $request)
    {
        $products = $this->productService->getTrashed(10);
        return response()->json([
            'message' => 'Trashed products retrieved successfully',
            'data' => $products
        ]);
    }

    /**
     * Restore a soft deleted product.
     * Returns JSON response.
     */
    public function restore($id)
    {
        $result = $this->productService->restoreProduct($id);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data']
        ]);
    }

    /**
     * Force delete a product permanently.
     * Returns JSON response.
     */
    public function forceDelete($id)
    {
        $result = $this->productService->forceDeleteProduct($id);

        return response()->json(['message' => $result['message']]);
    }
}
