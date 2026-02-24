<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Contracts\ProductServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * Inject ProductServiceInterface
     */
    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $perPage = 10;
        $products = $this->productService->getAllProducts($request, $perPage);
        $categories = $this->productService->getCategories();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = $this->productService->getCategories();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->productService->validateProduct($request->all());

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $this->productService->createProduct($data);

        return redirect()->route('admin.products.index')
             ->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = $this->productService->getProduct($id);
        $categories = $this->productService->getCategories();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = $this->productService->getProduct($id);
        $data = $this->productService->validateProduct($request->all(), $id);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $this->productService->updateProduct($product, $data);

        return redirect()->route('admin.products.index')
             ->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);

        return redirect()->route('admin.products.index')
             ->with('success', 'Product deleted successfully');
    }

    /**
     * Show trashed products
     */
    public function trashed()
    {
        $products = $this->productService->getTrashed(10);
        return view('admin.products.trashed', compact('products'));
    }

    /**
     * Restore product
     */
    public function restore($id)
    {
        $result = $this->productService->restoreProduct($id);

        if (!$result['success']) {
            return redirect()->route('admin.products.trashed')->with('error', $result['message']);
        }

        return redirect()->route('admin.products.trashed')->with('success', $result['message']);
    }

    /**
     * Force delete product
     */
    public function forceDelete($id)
    {
        $result = $this->productService->forceDeleteProduct($id);

        return redirect()->route('admin.products.trashed')->with('success', $result['message']);
    }
}
