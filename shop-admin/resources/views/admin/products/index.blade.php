@extends('admin.layouts.master')

@section('content')

<h1>Danh sách sản phẩm</h1>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<a href="{{ route('admin.products.create') }}" class="inline-block px-3 py-2 bg-indigo-600 text-white rounded">Thêm sản phẩm</a>

<table cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Giá</th>
        <th>Mô tả</th>
        <th>Hành động</th>
    </tr>

    @foreach($products as $product)
    <tr>
        <td>{{ $product->id }}</td>
        <td>{{ $product->name }}</td>
        <td>{{ number_format($product->price) }}</td>
        <td>{{ $product->description }}</td>
        <td>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-indigo-600 mr-2">Sửa</a>

        <form action="{{ route('admin.products.destroy', $product->id) }}"
            method="POST"
            style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Xóa</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

{{ $products->links() }}

@endsection
