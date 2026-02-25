<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
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
     */
    public function index(Request $request)
    {
        $perPage = 10;
        $products = $this->productService->getAllProducts($request, $perPage);
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products
        ]);
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
     * Get a specific product by ID.
     * Returns JSON response.
     */
    public function show($id)
    {
        $product = $this->productService->getProduct($id);
        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product
        ]);
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
