@extends('admin.layouts.master')

@section('title', 'Add Product')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Add Product</h2>

        <form action="{{ route('admin.products.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <div>
                <label class="block font-medium">Name:</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('name') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Price:</label>
                <input type="text" name="price" value="{{ old('price') }}" class="mt-1 block w-full border rounded px-3 py-2">
                @error('price') <p class="text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium">Description:</label>
                <textarea name="description" class="mt-1 block w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600">Back</a>
            </div>
        </form>
    </div>
@endsection
