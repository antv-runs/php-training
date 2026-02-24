@extends('admin.layouts.master')

@section('content')

<h1 class="text-2xl font-semibold">Product List</h1>

@if(session('success'))
    <p class="text-green-600">{{ session('success') }}</p>
@endif

<a href="{{ route('admin.products.create') }}" class="inline-block px-3 py-2 bg-indigo-600 text-white rounded">Add Product</a>

<table class="min-w-full mt-4 table-auto">
    <thead>
    <tr>
        <th class="px-4 py-2 text-left">ID</th>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-left">Price</th>
        <th class="px-4 py-2 text-left">Category</th>
        <th class="px-4 py-2 text-left">Description</th>
        <th class="px-4 py-2 text-left">Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
    <tr class="border-t">
        <td class="px-4 py-2">{{ $product->id }}</td>
        <td class="px-4 py-2">{{ $product->name }}</td>
        <td class="px-4 py-2">{{ number_format($product->price) }}</td>
        <td class="px-4 py-2">{{ $product->category?->name ?? 'No category' }}</td>
        <td class="px-4 py-2">{{ $product->description }}</td>
        <td class="px-4 py-2">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-indigo-600 mr-2">Edit</a>

            <form action="{{ route('admin.products.destroy', $product->id) }}"
                  method="POST"
                  style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

{{ $products->links() }}

@endsection
