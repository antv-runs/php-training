<!DOCTYPE html>
<html>
<head>
    <title>Sửa sản phẩm</title>
</head>
<body>

<h1>Sửa sản phẩm</h1>

<form action="{{ route('products.update', $product->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div>
        <label>Tên:</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}">
        @error('name') <p style="color:red">{{ $message }}</p> @enderror
    </div>

    <div>
        <label>Giá:</label>
        <input type="text" name="price" value="{{ old('price', $product->price) }}">
        @error('price') <p style="color:red">{{ $message }}</p> @enderror
    </div>

    <div>
        <label>Mô tả:</label>
        <textarea name="description">{{ old('description', $product->description) }}</textarea>
    </div>

    <button type="submit">Cập nhật</button>
</form>

<a href="{{ route('products.index') }}">Quay lại</a>

</body>
</html>
