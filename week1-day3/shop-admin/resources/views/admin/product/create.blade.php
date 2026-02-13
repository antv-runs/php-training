@extends('admin')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')

@section('content')
<form method="POST" action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if(isset($product)) @method('PUT') @endif
    <div class="mb-2">
        <label>Name:</label>
        <input type="text" class="form-control" name="name" value="{{ $product->name ?? '' }}" required>
    </div>
    <div class="mb-2">
        <label>Price</label>
        <input type="number" step="0.01" class="form-control" name="price" value="{{ $product->price ?? '' }} required">
    </div>
    <div class="mb-2">
        <label>Category:</label>
        <select name="category_id" class="form-control">
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ isset($product) && $product->category_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>
</form>
@endsection
