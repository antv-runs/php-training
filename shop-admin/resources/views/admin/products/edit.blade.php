@extends('admin.layouts.master')

@section('title', 'Sửa sản phẩm')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Sửa sản phẩm</h2>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium">Tên:</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('name') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Giá:</label>
                <input type="text" name="price" value="{{ old('price', $product->price) }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('price') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Mô tả:</label>
                <textarea name="description" class="mt-1 block w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Cập nhật</button>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600">Quay lại</a>
            </div>
        </form>
    </div>
@endsection
