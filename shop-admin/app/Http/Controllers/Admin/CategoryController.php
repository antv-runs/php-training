<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Placeholder: in future hook up Category model
        return view('admin.categories.index');
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        // validation and storing logic will be added later
        return redirect()->route('admin.categories.index')->with('success', 'Category created (placeholder)');
    }

    public function edit($id)
    {
        return view('admin.categories.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.categories.index')->with('success', 'Category updated (placeholder)');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted (placeholder)');
    }
}
