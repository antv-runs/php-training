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

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $result['data'] ?? $product
        ]);
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
        $result = $this->productService->deleteProduct($id);

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/products/trashed",
     *     summary="Get trashed products",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Trashed list")
     * )
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

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data']
        ]);
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
        $result = $this->productService->forceDeleteProduct($id);

        return response()->json(['message' => $result['message']]);
    }
}
