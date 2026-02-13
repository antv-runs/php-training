<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Hiển thị danh sách
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    // Hiển thị form tạo mới
    public function create()
    {
        return view('products.create');
    }

    // Lưu dữ liệu mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable'
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')
                         ->with('success', 'Thêm sản phẩm thành công');
    }

    // Hiển thị form chỉnh sửa
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    // Cập nhật dữ liệu
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable'
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index')
                         ->with('success', 'Cập nhật thành công');
    }

    // Xóa
    public function destroy($id)
    {
        Product::destroy($id);

        return redirect()->route('products.index')
                         ->with('success', 'Xóa thành công');
    }
}
