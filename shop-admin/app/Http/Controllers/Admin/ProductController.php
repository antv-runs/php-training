<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Contracts\ProductServiceInterface;
use Illuminate\Http\Request;

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

    public function index()
    {
        $products = $this->productService->getAllProducts(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = $this->productService->getCategories();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->productService->validateProduct($request->all());
        $this->productService->createProduct($validated);

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
        $validated = $this->productService->validateProduct($request->all(), $id);
        $this->productService->updateProduct($product, $validated);

        return redirect()->route('admin.products.index')
             ->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);

        return redirect()->route('admin.products.index')
             ->with('success', 'Product deleted successfully');
    }
}
