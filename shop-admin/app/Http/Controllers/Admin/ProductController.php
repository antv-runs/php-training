<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        Product::create($request->all());

        return redirect()->route('admin.products.index')
             ->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

    return redirect()->route('admin.products.index')
             ->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        Product::destroy($id);

    return redirect()->route('admin.products.index')
             ->with('success', 'Product deleted successfully');
    }
}
